<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Imagick;
use Illuminate\Support\Facades\Validator;

class AdminMenuController extends Controller
{
    public function getUserInfo(Request $request)
    {
        $user_role_ids = DB::table('sys_user_roles')
            ->select('role_id')
            ->where('user_id', $this->getAdminId())
            ->pluck('role_id');
        $role_code = DB::table('sys_roles')
            ->whereIn('role_id', $user_role_ids)
            ->select('role_code')
            ->pluck('role_code');
        if (in_array('admin', $role_code->toArray())) {
            //超级管理员返回全部菜单项
            $menu_list = $this->getAllMenu();
        } else {
            //普通管理员返回权限菜单项
            $menu_list = $this->getMenuListByUserId($this->getAdminId());
        }
        $data = [
            "userId" => 40,
            "username" => "admin",
            "password" => "",
            "nickname" => "管理员",
            "avatar" => "https://cdn.eleadmin.com/20200610/avatar.jpg",
            "sex" => "2",
            "phone" => "12345678901",
            "email" => "eleadmin@eclouds.com",
            "emailVerified" => 0,
            "realName" => null,
            "idCard" => null,
            "birthday" => "2021-05-21",
            "introduction" => "遗其欲，则心静！",
            "organizationId" => 31,
            "status" => 0,
            "deleted" => 0,
            "tenantId" => 4,
            "createTime" => "2020-01-13 14:43:52",
            "updateTime" => "2023-04-10 15:08:51",
            "organizationName" => "XXX公司",
            "sexName" => "女",
            "roles" => [
                "roleId" => 10,
                "roleCode" => "admin",
                "roleName" => "管理员",
                "comments" => "管理员",
                "deleted" => 0,
                "tenantId" => 4,
                "createTime" => "2020-02-26 15:18:37",
                "updateTime" => "2020-03-21 15:15:54",
                "userId" => null
            ],

            "authorities" => $menu_list,
            // [
            //     [
            //         "id" => 336,
            //         "parent_id" => 0,
            //         "title" => "Dashboard",
            //         "path" => "/dashboard",
            //         "component" => null,
            //         "menuType" => 0,
            //         "sortNumber" => 0,
            //         "authority" => null,
            //         "icon" => "IconProHomeOutlined",
            //         "hide" => 0,
            //         "meta" => null,
            //         "deleted" => 0,
            //         "createTime" => "2021-02-02 20:00:34",
            //         "updateTime" => "2025-01-10 13:35:07",
            //         "children" => null,
            //         "checked" => null
            //     ],


            //     [

            //         "id" => 301,
            //         "parent_id" => 0,
            //         "title" => "系统管理",
            //         "path" => "/system",
            //         "component" => null,
            //         "menuType" => 0,
            //         "sortNumber" => 1,
            //         "authority" => null,
            //         "icon" => "IconProSettingOutlined",
            //         "hide" => 0,
            //         "meta" => "{\"lang\": {\"zh_TW\": \"系統管理\", \"en\": \"System\"}}",
            //         "deleted" => 0,
            //         "createTime" => "2020-02-26 12:51:23",
            //         "updateTime" => "2025-01-10 13:35:27",
            //         "children" => null,
            //         "checked" => null

            //     ],
            //     [

            //         "id" => 302,
            //         "parent_id" => 301,
            //         "title" => "用户管理",
            //         "path" => "/system/user",
            //         "component" => "/system/user",
            //         "menuType" => 0,
            //         "sortNumber" => 1,
            //         "authority" => null,
            //         "icon" => "IconProUserOutlined",
            //         "hide" => 0,
            //         "meta" => "{\"lang\": {\"zh_TW\": \"用戶管理\", \"en\": \"User\"}}",
            //         "deleted" => 0,
            //         "createTime" => "2020-02-26 12:51:55",
            //         "updateTime" => "2025-01-10 13:35:25",
            //         "children" => null,
            //         "checked" => null

            //     ]
            // ],

        ];
        return $this->jsonOk($data);
    }
    public function getMenuList(Request $request)
    {
        $query = DB::table('sys_permissions')
            ->when($request->has('title'), function ($query) use ($request) {
                $query->where('title', 'like', $request->title . '%');
            })
            ->when($request->has('path'), function ($query) use ($request) {
                $query->where('path', 'like', $request->path . '%');
            })
            ->when($request->has('authority'), function ($query) use ($request) {
                $query->where('authority', 'like', $request->authority . '%');
            })
            ->where('deleted', 0);
        // \dd($query->toSql());
        $list = $query->get();

        foreach ($list as &$item) {
            if (!$item->menuType) {
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
            $item->meta = json_decode($item->meta, true);
            // unset($item->meta);
        }

        return $this->jsonOk($list);
    }

    public function getMenuTree(Request $request)
    {
        $userId = $this->getAdminId();
        $userInfo = DB::table('sys_users')
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

    // public function getRoleMenuList(Request $request)
    // {
    //     $userId = $this->getAdminId();
    //     $userInfo = DB::table('sys_users')
    //         ->select('id', 'is_admin')
    //         ->where('id', $userId)
    //         ->first();

    //     if ($userInfo->is_admin) {
    //         //超级管理员返回全部菜单项
    //         $menu_list = $this->getAllMenu();
    //     } else {
    //         //普通管理员返回权限菜单项
    //         $menu_list = $this->getMenuListByUserId($userId);
    //     }

    //     // 传给前端的路由排除掉按钮和停用的菜单
    //     $menu_list = $menu_list->where('is_button', 0)
    //         ->where('status', 'ENABLED');

    //     $menu_items = [];
    //     foreach ($menu_list as $item) {
    //         $menu_items[] = (object)[
    //             'menuId' => $item->menuId,
    //             'parentId' => $item->parentId,
    //             'title' => $item->title,
    //             'path' => $item->path,
    //             'meta' => json_decode($item->meta, true),
    //             'checked' => true
    //         ];
    //     }
    //     return $this->jsonOk($menu_items);
    // }

    private function getAllMenu()
    {
        return DB::table('sys_permissions')
            ->where('deleted', 0)
            ->get();
    }

    private function getMenuListByUserId($userId)
    {
        $sql = "(
            select
                distinct m.*
            from
                sys_users u
                join sys_user_roles ur on u.id = ur.user_id
                join sys_roles r on ur.role_id = r.role_id and r.status = 'ENABLED' and r.deleted = 0
                join sys_role_permissions rm on r.role_id = rm.role_id
                join sys_permissions m on rm.menu_id = m.menuId and m.status = 'ENABLED' and m.deleted = 0
            where
                u.id = $userId
                and u.status = 'ENABLED'
                and u.deleted = 0
        ) a";
        $query = DB::table(DB::raw($sql))
            ->orderBy('menuId');

        return $query->get();
    }

    public function getMenuInfo(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int',
        ]);

        $id = $request->id;

        $menu_info = DB::table('sys_permissions')
            ->where('id', $id)
            ->where('deleted', 0)
            ->first();

        if ($menu_info != null) {
            $menu_info->meta = json_decode($menu_info->meta_info, true);
            unset($menu_info->meta_info);
        }

        return $this->jsonOk($menu_info);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'path' => 'required|string',
            'parentId' => 'required|int',
        ]);

        $body_entity = $request->json()->all();
        //组件-内嵌-外链
        $type = ['PAGE', 'CATALOG', 'IFRAME', 'REDIRECT', 'ACTION'];
        $menu_info = [
            'title' => $body_entity['title'],
            'type' => $type[$body_entity['menuType']],
            'path' => $body_entity['path'] ?? '',
            'parentId' => $body_entity['parentId'],
            'component' => $body_entity['component'],
            'menuType' => $body_entity['menuType'],
            'authority' => $body_entity['authority'] ?? '',
            'meta' => json_encode($body_entity['meta'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'status' => $body_entity['status'],
            'sortNumber' => $body_entity['sortNumber'],
            'icon' => $body_entity['icon'] ?? '',
            'hide' => $body_entity['hide'] ?? 0,
        ];
        $menu_id = DB::table('sys_permissions')->insertGetId($menu_info);

        return $this->jsonOk([
            'id' => $menu_id,
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'menuId' => 'required|int',
            'title' => 'required|string',
            // 'path' => 'string',
            'parentId' => 'required|int',
        ]);

        $body_entity = $request->json()->all();
        $id = $body_entity['menuId'];
        $type = ['PAGE', 'CATALOG', 'IFRAME', 'REDIRECT', 'ACTION'];
        $menu_info = [
            'title' => $body_entity['title'],
            'type' => $type[$body_entity['menuType']],
            'path' => $body_entity['path'] ?? '',
            'parentId' => $body_entity['parentId'],
            'component' => $body_entity['component'],
            'menuType' => $body_entity['menuType'],
            'authority' => $body_entity['authority'] ?? '',
            'meta' => json_encode($body_entity['meta'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?? '',
            'status' => $body_entity['status'],
            'sortNumber' => $body_entity['sortNumber'],
            'icon' => $body_entity['icon'] ?? '',
            'hide' => $body_entity['hide'] ?? 0,
        ];

        DB::table('sys_permissions')
            ->where('menuId', $id)
            ->update($menu_info);

        return $this->jsonOk();
    }

    public function delete($menuId)
    {
        $validator = Validator::make(['menuId' => $menuId], [
            'menuId' => 'required|int',
        ]);
        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first());
        }


        // $this->validate($request, [
        //     'menuId' => 'required|int',
        // ]);

        // $menuId = $request->menuId;

        DB::table('sys_permissions')
            ->where('menuId', $menuId)
            ->update([
                'deleted' => 1,
            ]);

        return $this->jsonOk();
    }
}
