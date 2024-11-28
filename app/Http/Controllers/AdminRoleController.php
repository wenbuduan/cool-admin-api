<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Imagick;

class AdminRoleController extends Controller
{
    public function getAllRole()
    {
        $roles = DB::table('admin_roles')
            ->select('id', 'name', 'key', 'sort_num', 'status')
            ->where('deleted', 0)
            ->where('status', 'ENABLED')
            ->get();
        return $this->jsonOk($roles);
    }

    public function getRoleList(Request $request)
    {
        $page_index = intval($request->page_index);
        $page_size = intval($request->page_size);
        $name = $request->name;
        $key = $request->key;
        $status = $request->status;

        $query = DB::table('admin_roles')
            ->select('id', 'name', 'key', 'sort_num', 'status', 'remark', 'create_time')
            ->where('deleted', 0);

        if (!is_null($name)) {
            $query->where('name', $name);
        }
        if (!is_null($key)) {
            $query->where('key', $key);
        }
        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $pages = $query
            ->orderBy($orderColumn ?? 'create_time', $orderType ?? 'desc')
            ->paginate($page_size, ['*'], 'page', $page_index);

        return $this->jsonOk([
            'total' => $pages->total(),
            'rows' => $pages->items(),
        ]);
    }

    public function getRoleInfo(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
        ]);

        $id = $request->id;

        $role_info = DB::table('admin_roles')
            ->select('id', 'name', 'key', 'sort_num', 'status', 'remark', 'create_time')
            ->where('id', $id)
            ->where('deleted', 0)
            ->first();

        if (!is_null($role_info)) {
            $role_info->menu_ids = DB::table('admin_role_menu')
                ->where('role_id', $role_info->id)
                ->pluck('menu_id');
        }

        return $this->jsonOk($role_info);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'key' => 'required|string',
            'sort_num' => 'required|int',
        ]);
        $body_entity = $request->json()->all();

        $role_info = [
            'name' => $body_entity['name'],
            'key' => $body_entity['key'],
            'sort_num' => $body_entity['sort_num'],
            //'data_scope' => $body_entity['data_scope'],
            //'dept_id_set' => $body_entity['dept_id_set'],
            'remark' => $body_entity['remark'],
            'status' => $body_entity['status'],
        ];

        DB::beginTransaction();
        try {
            $role_id = DB::table('admin_roles')->insertGetId($role_info);

            $menu_ids = $body_entity['menu_ids'];
            foreach ($menu_ids as $menu_id) {
                DB::table('admin_role_menu')->insert(['role_id' => $role_id, 'menu_id' => $menu_id]);
            }

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
        $this->validate($request, [
            'id' => 'required|int',
            'name' => 'required|string',
            'key' => 'required|string',
            'sort_num' => 'required|int',
        ]);

        $body_entity = $request->json()->all();
        $role_id = $body_entity['id'];

        $role_info = [
            'name' => $body_entity['name'],
            'key' => $body_entity['key'],
            'sort_num' => $body_entity['sort_num'],
            //'data_scope' => $body_entity['data_scope'],
            //'dept_id_set' => $body_entity['dept_id_set'],
            'remark' => $body_entity['remark'],
            'status' => $body_entity['status'],
        ];

        DB::beginTransaction();
        try {
            DB::table('admin_roles')
                ->where('id', $role_id)
                ->update($role_info);

            DB::table('admin_role_menu')
                ->where('role_id', $role_id)
                ->delete();

            $menu_ids = $body_entity['menu_ids'];
            foreach ($menu_ids as $menu_id) {
                DB::table('admin_role_menu')->insert(['role_id' => $role_id, 'menu_id' => $menu_id]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            Log::error($e);
            return $this->jsonError('系统异常');
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

        DB::table('admin_roles')
            ->where('id', $id)
            ->update([
                'status' => $status,
            ]);

        return $this->jsonOk();
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
        ]);

        $id = $request->id;

        DB::table('admin_roles')
            ->where('id', $id)
            ->update([
                'deleted' => 1,
            ]);

        return $this->jsonOk();
    }
}
