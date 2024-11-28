<?php

namespace App\Providers;

use App\Models\RedisTokenUser;
use Illuminate\Contracts\Auth\UserProvider as IlluminateUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Redis;

class RedisTokenUserProvider implements IlluminateUserProvider
{
    /**
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // Get and return a user by their unique identifier
    }

    /**
     * @param mixed $identifier
     * @param string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // Get and return a user by their unique identifier and "remember me" token
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Save the given "remember me" token for the given user
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        //根据通行证去返回用户信息
        $user = null;
        if (isset($credentials['api_token'])) {

            $token = $credentials['api_token'];
            if (!is_null($token) && strlen($token) == 32) {

                $userinfoJSON = Redis::get("admin:token:$token");
                if (!is_null($userinfoJSON)) {
                    $userinfo = json_decode($userinfoJSON, true);
                    if (!empty($userinfo)) {
                        $user = new RedisTokenUser();
                        $user->setAttributes($userinfo);
                    }
                }
            }
        }
        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Check that given credentials belong to the given user
        return false;
    }

}
