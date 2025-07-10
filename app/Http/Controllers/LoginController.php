<?php

namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    public function captchaImage(Request $request)
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build();

        $captcha_info = [
            'captcha_code' => $builder->getPhrase()
        ];
        $captcha_info_JSON = $this->toJson($captcha_info);

        $captcha_key = uniqid();
        $redis_captcha_key = 'login:captcha_key:' . $captcha_key;
        $ttl = 60 * 10; //10分钟有效
        Redis::setex($redis_captcha_key, $ttl, $captcha_info_JSON);

        return $this->jsonOk([
            'captcha_key' => $captcha_key,
            'captcha_code' => $captcha_info['captcha_code'],
            'captcha_img' => base64_encode($builder->get()),
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|max:20|regex:/^\w+$/',
            'password' => 'required|string|max:255|regex:/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', //只包含 Base64 字符集中的字符，并且长度是4的倍数（以=结尾）
            'captcha_key' => 'required|string|max:20|regex:/^\w+$/',
            'captcha_code' => 'required|string|max:10|regex:/^\w+$/',
        ]);

        $username = $request->username;
        $password = $request->password;
        $captcha_key = $request->captcha_key;
        $captcha_code = $request->captcha_code;

        $decryptedData = $this->decryptPassword($password);

        $redis_captcha_key = 'login:captcha_key:' . $captcha_key;
        //从redis中取数据
        $captcha_info_JSON = Redis::get($redis_captcha_key);
        if (is_null($captcha_info_JSON)) {
            Log::error('captcha_key not found', ["captcha_key" => $captcha_key]);
            return $this->jsonError('验证码已过期');
        }

        $captcha_info = json_decode($captcha_info_JSON, true);
        if (empty($captcha_info)) {
            Log::error('captcha_info not found', ['captcha_info_JSON' => $captcha_info_JSON]);
            return $this->jsonError('验证码无效');
        }
        $server_captcha_code = $captcha_info['captcha_code'];
        if ($server_captcha_code != $captcha_code) {
            return $this->jsonError('验证码错误');
        }

        $userInfo = DB::table('sys_users')
            ->select('id', 'username', 'password')
            ->where('username', $username)
            ->first();
        if (is_null($userInfo)) {
            return $this->jsonError('用户名或密码错误');
        }

        if (!password_verify($decryptedData, $userInfo->password)) {
            return $this->jsonError('用户名或密码错误');
        }

        $userId = $userInfo->id;
        $token = $this->createToken($userId, $userInfo->username);
        $data = [
            "token" => $token,
            // "user" => [
            //     "userId" => 40,
            //     "username" => "admin",
            //     "password" => "",
            //     "nickname" => "管理员",
            //     "avatar" => "https://cdn.eleadmin.com/20200610/avatar.jpg",
            //     "sex" => "2",
            //     "phone" => "12345678901",
            //     "email" => "eleadmin@eclouds.com",
            //     "emailVerified" => 0,
            //     "realName" => null,
            //     "idCard" => null,
            //     "birthday" => "2021-05-21",
            //     "introduction" => "遗其欲，则心静！",
            //     "organizationId" => 31,
            //     "status" => 0,
            //     "deleted" => 0,
            //     "tenantId" => 4,
            //     "createTime" => "2020-01-13 14:43:52",
            //     "updateTime" => "2023-04-10 15:08:51",
            //     "organizationName" => "XXX公司",
            //     "sexName" => "女",
            //     "roles" => [
            //         "roleId" => 10,
            //         "roleCode" => "admin",
            //         "roleName" => "管理员",
            //         "comments" => "管理员",
            //         "deleted" => 0,
            //         "tenantId" => 4,
            //         "createTime" => "2020-02-26 15:18:37",
            //         "updateTime" => "2020-03-21 15:15:54",
            //         "userId" => null
            //     ],
            //     "authorities" => [
            //         [
            //             "menuId" => 336,
            //             "parentId" => 0,
            //             "title" => "Dashboard",
            //             "path" => "/dashboard",
            //             "component" => null,
            //             "menuType" => 0,
            //             "sortNumber" => 0,
            //             "authority" => null,
            //             "icon" => "IconProHomeOutlined",
            //             "hide" => 0,
            //             "meta" => null,
            //             "deleted" => 0,
            //             "tenantId" => 4,
            //             "createTime" => "2021-02-02 20:00:34",
            //             "updateTime" => "2025-01-10 13:35:07",
            //             "children" => null,
            //             "checked" => null
            //         ],
            //         [

            //             "menuId" => 301,
            //             "parentId" => 0,
            //             "title" => "系统管理",
            //             "path" => "/system",
            //             "component" => null,
            //             "menuType" => 0,
            //             "sortNumber" => 1,
            //             "authority" => null,
            //             "icon" => "IconProSettingOutlined",
            //             "hide" => 0,
            //             "meta" => "{\"lang\": {\"zh_TW\": \"系統管理\", \"en\": \"System\"}}",
            //             "deleted" => 0,
            //             "tenantId" => 4,
            //             "createTime" => "2020-02-26 12:51:23",
            //             "updateTime" => "2025-01-10 13:35:27",
            //             "children" => null,
            //             "checked" => null

            //         ]
            //     ],
            // ]
        ];
        return $this->jsonOk($data);

        // return $this->jsonOk([
        //     "token" => $token,
        //     "currentUser" => [
        //         "userInfo" => [
        //             "id" => $userId,
        //             "username" => $userInfo->username,
        //         ],
        //         "roleKey" => "admin",
        //         "permissions" => [
        //             "*:*:*"
        //         ]
        //     ],
        // ]);
    }

    private function decryptPassword($originalPassword)
    {
        $privateKeyPath = base_path() . '/storage/rsa.key';
        $rsaPrivateKey = file_get_contents($privateKeyPath);
        $decryptBytes = openssl_private_decrypt(
            base64_decode($originalPassword),
            $decryptedData,
            $rsaPrivateKey,
            OPENSSL_PKCS1_PADDING
        );

        if (!$decryptBytes) {
            // 处理解密失败的情况
            return "Decryption failed";
        }

        return $decryptedData;
    }

    private function createToken($userId, $username): string
    {
        //考虑重复登录的情况，删除相同用户已经存在的token
        //        $oldToken = Redis::get("admin:user:token:$userId");
        //        if(! is_null($oldToken)) {
        //            Redis::del("token:$oldToken");
        //        }

        $token = md5(uniqid(mt_rand(), true));
        $userinfo = [
            'id' => $userId,
            'username' => $username,
        ];
        $userinfoJSON = $this->toJson($userinfo);
        $ttl = 60 * 60 * 24 * 30; //30天
        Redis::setex("admin:token:$token", $ttl, $userinfoJSON);
        Redis::setex("admin:user:token:$userId", $ttl, $token);

        return $token;
    }
}
