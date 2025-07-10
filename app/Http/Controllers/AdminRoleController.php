<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Imagick;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class AdminRoleController extends Controller
{
    public function getAllRole()
    {
        $roles = DB::table('sys_roles')
            ->select('role_id as roleId', 'role_name as roleName', 'role_code as roleCode', 'sort_num', 'status')
            ->where('deleted', 0)
            ->where('status', 'ENABLED')
            ->get();
        return $this->jsonOk($roles);
    }

    //获取角色菜单权限
    public function getMenusWithRole($roleId)
    {
        $menus = DB::table('sys_permissions')
            ->select('menuId', 'parentId', 'title')
            ->where('deleted', 0)
            ->where('status', 'ENABLED')
            // ->where('parent_id', 0)
            ->get();

        $roleInfo = DB::table('sys_roles')
            ->select('role_id', 'role_code')
            ->where('role_id', $roleId)
            ->first();
        if ($roleInfo->role_code == 'admin') {
            //超级管理员返回全部菜单项
            $checked_menus_ids = DB::table('sys_permissions')
                ->select('menuId')
                ->where('deleted', 0)
                ->pluck('menuId');
        } else {
            //普通管理员返回权限菜单项
            $checked_menus_ids = DB::table('sys_role_permissions')
                ->where('role_id', $roleId)
                ->pluck('menu_id');
        }
        foreach ($menus as $menu) {
            $menu->checked = in_array($menu->menuId, $checked_menus_ids->toArray());
        }

        $data = [
            'menus' => $menus,
            'checked_menu_ids' => $checked_menus_ids,
        ];

        return $this->jsonOk($data);
    }

    public function getRoleList(Request $request)
    {
        $page_index = intval($request->page);
        $page_size = intval($request->limit);
        $name = $request->role_name;
        $role_code = $request->role_code;
        $status = $request->status;

        $query = DB::table('sys_roles')
            ->select([
                'role_id as roleId',
                'role_name as roleName',
                'role_code as roleCode',
                'sort_num as sortNum',
                'status',
                'comments',
                'create_time'
            ])
            ->when($request->roleName, function ($query) use ($request) {
                $query->where('role_name', 'like', '%' . $request->roleName . '%');
            })
            ->when($request->roleCode, function ($query) use ($request) {
                $query->where('role_code', 'like', '%' . $request->roleCode . '%');
            })
            ->where('deleted', 0);

        if (!is_null($name)) {
            $query->where('role_name', 'like', '%' . $name . '%');
        }
        if (!is_null($role_code)) {
            $query->where('role_code', 'like', '%' . $role_code . '%');
        }
        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $pages = $query
            ->orderBy($request->sort ?? 'create_time', $request->order ?? 'desc')
            ->paginate($page_size, ['*'], 'page', $page_index);

        return $this->jsonOk([
            // 'total' => $pages->total(),
            'count' => $pages->count(),
            'list' => $pages->items(),
        ]);
    }

    public function getRoleInfo($roleId)
    {
        // $this->validate($request, [
        //     'roleId' => 'required|int',
        // ]);

        // $id = $request->id;
        $validator = Validator::make(['roleId' => $roleId], [
            'roleId' => 'required|int',
        ]);
        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first());
        }

        $role_info = DB::table('sys_permissions')
            ->select('role_id', 'role_name', 'role_code', 'sort_num', 'status', 'remark', 'create_time')
            ->where('role_id', $roleId)
            ->where('deleted', 0)
            ->first();

        if (!is_null($role_info)) {
            $role_info->menu_ids = DB::table('sys_role_permissions')
                ->where('role_id', $role_info->role_id)
                ->pluck('menu_id');
        }

        return $this->jsonOk($role_info);
    }

    public function create(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'roleName' => 'required|string',
            'roleCode' => 'required|string',
            'sortNum' => 'required|int',
        ]);
        $body_entity = $request->json()->all();
        // \dd($body_entity);

        $role_info = [
            'role_name' => $body_entity['roleName'],
            'role_code' => $body_entity['roleCode'],
            'sort_num' => $body_entity['sortNum'],
            'comments' => $body_entity['comments'],
            'status' => $body_entity['status'],
        ];

        DB::beginTransaction();
        try {
            $role_id = DB::table('sys_permissions')->insertGetId($role_info);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            Log::error($e);
            return $this->jsonError('系统异常');
        }

        return $this->jsonOk([
            'id' => $role_id,
        ]);
    }

    public function update(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'roleId' => 'required|int',
            'roleName' => 'required|string',
            'roleCode' => 'required|string',
            'sortNum' => 'required|int',
        ]);

        $body_entity = $request->json()->all();
        $role_id = $body_entity['roleId'];

        $role_info = [
            'role_name' => $body_entity['roleName'],
            'role_code' => $body_entity['roleCode'],
            'sort_num' => $body_entity['sortNum'],
            //'data_scope' => $body_entity['data_scope'],
            //'dept_id_set' => $body_entity['dept_id_set'],
            'comments' => $body_entity['comments'],
            'status' => $body_entity['status'],
        ];

        DB::beginTransaction();
        try {
            DB::table('sys_roles')
                ->where('role_id', $role_id)
                ->update($role_info);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            Log::error($e);
            return $this->jsonError($e->getMessage());
        }

        return $this->jsonOk();
    }

    public function updateMenu($roleId, Request $request)
    {
        $validator = Validator::make(['roleId' => $roleId], [
            'roleId' => 'required|int',
        ]);
        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first());
        }


        $menu_ids = $request->all();

        DB::table('sys_role_permissions')
            ->where('role_id', $roleId)
            ->delete();

        foreach ($menu_ids as $menu_id) {
            DB::table('sys_role_permissions')->insert(['role_id' => $roleId, 'menu_id' => $menu_id]);
        }

        return $this->jsonOk();
    }

    public function updateStatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
            'status' => 'required|string',
        ]);

        $id = $request->id;
        $status = $request->status;

        DB::table('sys_roles')
            ->where('id', $id)
            ->update([
                'status' => $status,
            ]);

        return $this->jsonOk();
    }

    public function delete($roleId)
    {
        DB::table('sys_roles')
            ->where('role_id', $roleId)
            ->update([
                'deleted' => 1,
            ]);

        return $this->jsonOk();
    }

    public function batchDelete(Request $request)
    {
        // \dd($request->all());
        if (empty($request->all())) {
            return $this->jsonError('请选择要删除的记录');
        }

        $ids = $request->all();

        DB::table('sys_roles')
            ->whereIn('role_id', $ids)
            ->update([
                'deleted' => 1,
            ]);

        return $this->jsonOk();
    }
}
