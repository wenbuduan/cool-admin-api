<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Imagick;

class AdminMenuController extends Controller
{
    public function getMenuList(Request $request)
    {
        $query = DB::table('admin_menus')
            ->where('deleted', 0);

        $list = $query->get();

        foreach ($list as &$item) {
            if (!$item->is_button) {
                $item->type_desc = match ($item->type) {
                    'PAGE' => '页面',
                    'CATALOG' => '目录',
                    'IFRAME' => '内嵌Iframe',
                    'REDIRECT' => '外链跳转',
                    'ACTION' => '操作',
                    default => '未知',
                };
            } else {
                $item->type = 0;
                $item->type_desc = '';
            }
            $item->meta = json_decode($item->meta_info, true);
            unset($item->meta_info);
        }

        return $this->jsonOk($list);
    }

    public function getMenuTree(Request $request)
    {
        $userId = $this->getAdminId();
        $userInfo = DB::table('admin_users')
            ->select('id', 'is_admin')
            ->where('id', $userId)
            ->first();

        if ($userInfo->is_admin) {
            //超级管理员返回全部菜单项
            $menu_list = $this->getAllMenu();
        } else {
            //普通管理员返回权限菜单项
            $menu_list = $this->getMenuListByUserId($userId);
        }

        // 传给前端的路由排除掉按钮和停用的菜单
        $menu_list = $menu_list->where('is_button', 0)
            ->where('status', 'ENABLED');

        $tree_items = [];
        foreach ($menu_list as $item) {
            $tree_items[] = (object)[
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'name' => $item->name,
                'path' => $item->path,
                'meta' => json_decode($item->meta_info, true),
            ];
        }

        $tree = buildTree($tree_items, 0);

        return $this->jsonOk($tree);
    }

    private function getAllMenu()
    {
        return DB::table('admin_menus')
            ->where('deleted', 0)
            ->get();
    }

    private function getMenuListByUserId($userId)
    {
        $sql = "(
            select
                distinct m.*
            from
                admin_users u
                join admin_user_role ur on u.id = ur.user_id
                join admin_roles r on ur.role_id = r.id and r.status = 'ENABLED' and r.deleted = 0
                join admin_role_menu rm on r.id = rm.role_id
                join admin_menus m on rm.menu_id = m.id and m.status = 'ENABLED' and m.deleted = 0
            where
                u.id = $userId
                and u.status = 'ENABLED'
                and u.deleted = 0
        ) a";
        $query = DB::table(DB::raw($sql))
            ->orderBy('id');

        return $query->get();
    }

    public function getMenuInfo(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
        ]);

        $id = $request->id;

        $menu_info = DB::table('admin_menus')
            ->where('id', $id)
            ->where('deleted', 0)
            ->first();

        if($menu_info != null) {
            $menu_info->meta = json_decode($menu_info->meta_info, true);
            unset($menu_info->meta_info);
        }

        return $this->jsonOk($menu_info);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'path' => 'required|string',
            'parent_id' => 'required|int',
        ]);

        $body_entity = $request->json()->all();

        $menu_info = [
            'name' => $body_entity['name'],
            'type' => $body_entity['type'],
            'router_name' => $body_entity['router_name'] ?? '',
            'parent_id' => $body_entity['parent_id'],
            'path' => $body_entity['path'],
            'is_button' => $body_entity['is_button'],
            'permission' => $body_entity['permission'] ?? '',
            'meta_info' => json_encode($body_entity['meta'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'status' => $body_entity['status'],
        ];
        $menu_id = DB::table('admin_menus')->insertGetId($menu_info);

        return $this->jsonOk([
            'id' => $menu_id,
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
            'name' => 'required|string',
            'path' => 'required|string',
            'parent_id' => 'required|int',
        ]);

        $body_entity = $request->json()->all();
        $id = $body_entity['id'];

        $menu_info = [
            'name' => $body_entity['name'],
            'type' => $body_entity['type'],
            'router_name' => $body_entity['router_name'] ?? '',
            'parent_id' => $body_entity['parent_id'],
            'path' => $body_entity['path'],
            'is_button' => $body_entity['is_button'],
            'permission' => $body_entity['permission'] ?? '',
            'meta_info' => json_encode($body_entity['meta'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'status' => $body_entity['status'],
        ];
        DB::table('admin_menus')
            ->where('id', $id)
            ->update($menu_info);

        return $this->jsonOk();
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
        ]);

        $id = $request->id;

        DB::table('admin_menus')
            ->where('id', $id)
            ->update([
                'deleted' => 1,
            ]);

        return $this->jsonOk();
    }
}
