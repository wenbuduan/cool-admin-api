<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{

    public function getDictionaryByFilter(Request $request)
    {
        $request->validate([
            'dictCode' => 'required|string|max:255',
        ]);
        $dictCode = $request->input('dictCode');

        $dict_id = DB::table('sys_dictionary')
            ->where('dict_code', $dictCode)
            ->value('dict_id');

        $dictionary = DB::table('sys_dictionary_data')
            ->select([
                'dict_id as dictId',
                'dict_data_code as dictDataCode',
                'dict_data_name as dictDataName',
                'comments',
                'dict_data_id as dictDataId',
                'comments as dictName'
            ])
            ->where('dict_id', $dict_id)
            ->get();

        if (count($dictionary) > 0) {
            foreach ($dictionary as  $key => &$item) {
                $item->dictDataId = $key + 1;
            }
        }

        return $this->jsonOk($dictionary);
    }

    public function checkUserExistence(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $result = DB::table('sys_users')->where($field, $value)->get();
        if (count($result) > 0) {
            return $this->jsonOk('用户已存在');
        }
    }
    public function getAdminUserList(Request $request)
    {
        $page_index = intval($request->page);
        $page_size = intval($request->limit);
        $id = $request->id;
        $username = $request->username;
        $nickname = $request->nickname;
        $gender = $request->sex;

        // $sql = "(
        //     SELECT u.* FROM sys_users u where u.deleted = 0
        // ) a";
        $query = DB::table('sys_users')
            ->select(
                'id',
                'username',
                'nickname',
                'email',
                'phone',
                'gender',
                'avatar',
                'is_admin',
                'remark',
                'status',
                'introduction',
                'create_time'
            )->where('deleted', 0);

        $pages = $query
            ->when($id, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($username, function ($query) use ($username) {
                $query->where('username', 'like', '%' . $username . '%');
            })
            ->when($nickname, function ($query) use ($nickname) {
                $query->where('nickname', 'like', '%' . $nickname . '%');
            })
            ->when($gender, function ($query) use ($gender) {
                $query->where('gender', $gender);
            })
            ->orderBy($orderColumn ?? 'create_time', $orderType ?? 'desc')
            // ->toSql();
            ->paginate($page_size, ['*'], 'page', $page_index);

        foreach ($pages->items() as &$user_info) {
            $user_info->userId = $user_info->id;
            $user_info->role_ids = DB::table('sys_user_roles')
                ->where('user_id', $user_info->id)
                ->pluck('role_id');
            $user_info->roles = DB::table('sys_roles')
                ->select([
                    'role_id as roleId',
                    'role_name as roleName',
                    'role_code as roleCode',
                    'sort_num as sortNum',
                    'status',
                    'comments',
                    'create_time'
                ])
                ->whereIn('role_id', $user_info->role_ids)
                ->get();
            $user_info->sexName = DB::table('sys_dictionary_data')->where('dict_data_code', $user_info->gender)->value('dict_data_name');
            $user_info->sex = $user_info->gender;
        }


        return $this->jsonOk([
            'count' => $pages->total(),
            'list' => $pages->items(),
        ]);
    }

    public function create(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
            'phone' => 'required|string',
            'status' => 'required|integer',
        ]);
        $body_entity = $request->json()->all();

        $hashed_password = password_hash($body_entity['password'], PASSWORD_DEFAULT);

        $user_info = [
            'username' => $body_entity['username'],
            'nickname' => $body_entity['nickname'],
            'email' => $body_entity['email'] ?? null,
            'phone' => $body_entity['phone'] ?? null,
            'gender' => $body_entity['sex'] ?? 'UNKNOWN',
            'password' => $hashed_password,
            'remark' => $body_entity['remark'] ?? null,
            'status' => $body_entity['status'],
            'introduction' => $body_entity['introduction'] ?? '',
        ];

        DB::beginTransaction();
        try {
            $user_id = DB::table('sys_users')->insertGetId($user_info);

            $role_ids = $body_entity['roles'];
            foreach ($role_ids as $role_id) {
                DB::table('sys_user_roles')->insert(['user_id' => $user_id, 'role_id' => $role_id['roleId']]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            Log::error($e);
            return $this->jsonError($e->getMessage());
        }

        return $this->jsonOk([
            'id' => $user_id,
        ]);
    }

    public function update(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'userId' => 'required|int',
            'username' => 'required|string',
            'status' => 'required|integer|max:1|min:0',
        ]);

        $body_entity = $request->json()->all();
        $user_id = $body_entity['userId'];

        $user_info = [
            'username' => $body_entity['username'],
            'nickname' => $body_entity['nickname'],
            'email' => $body_entity['email'],
            'phone' => $body_entity['phone'],
            'gender' => $body_entity['sex'],
            'introduction' => $body_entity['introduction'] ?? '',
            'status' => $body_entity['status'],
        ];

        DB::beginTransaction();
        try {
            DB::table('sys_users')
                ->where('id', $user_id)
                ->update($user_info);

            DB::table('sys_user_roles')
                ->where('user_id', $user_id)
                ->delete();

            $role_ids = $body_entity['roles'];
            foreach ($role_ids as $role_id) {
                DB::table('sys_user_roles')->insert(['user_id' => $user_id, 'role_id' => $role_id['roleId']]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            Log::error($e);
            return $this->jsonError($e->getMessage());
        }

        return $this->jsonOk();
    }

    public function updateStatus(Request $request)
    {
        $this->validate($request, [
            'userId' => 'required|int',
            'status' => 'required|integer|max:1|min:0',
        ]);

        $userId = $request->userId;
        $status = $request->status;

        DB::table('sys_users')
            ->where('id', $userId)
            ->update([
                'status' => $status,
            ]);

        return $this->jsonOk();
    }

    //重置密码
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'userId' => 'required|int',
            'password' => 'required|string|max:20',
        ]);

        $userId = $request->userId;
        $password = $request->password;

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        DB::table('sys_users')
            ->where('id', $userId)
            ->update([
                'password' => $hashed_password,
            ]);

        return $this->jsonOk();
    }

    public function delete(Request $request)
    {
        // \dd($request->all());
        // $this->validate($request, [
        //     'id' => 'required|int',
        // ]);

        $user_ids = $request->all();

        DB::table('sys_users')
            ->whereIn('id', $user_ids)
            ->update([
                'deleted' => 1,
            ]);

        DB::table('sys_user_roles')
            ->whereIn('user_id', $user_ids)
            ->delete();

        return $this->jsonOk();
    }

    //获取自己的用户信息
    public function profile(Request $request)
    {
        $userId = $this->getAdminId();

        $userinfo = DB::table('sys_users')
            ->select('id', 'username', 'phone', 'gender', 'nickname', 'avatar', 'email', 'create_time')
            ->where('id', $userId)
            ->first();

        return $this->jsonOk($userinfo);
    }

    //修改自己的密码
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string|max:20',
            'new_password' => 'required|string|max:20',
        ]);

        $old_password = $request->old_password;
        $new_password = $request->new_password;

        $userId = $this->getAdminId();

        $userinfo = DB::table('sys_users')
            ->select('id', 'password')
            ->where('id', $userId)
            ->first();

        if (!password_verify($old_password, $userinfo->password)) {
            return $this->jsonError('旧密码错误');
        }

        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        DB::table('sys_users')
            ->where('id', $userId)
            ->update([
                'password' => $hashed_new_password,
            ]);

        return $this->jsonOk();
    }
}
