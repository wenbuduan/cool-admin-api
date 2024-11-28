<?php

namespace App\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisTokenGuard extends TokenGuard
{
    /**
     * Create a new authentication guard.
     *
     * @param \Illuminate\Contracts\Auth\UserProvider $provider
     * @param \Illuminate\Http\Request $request
     * @param string $inputKey
     * @param string $storageKey
     * @param bool $hash
     * @return void
     */
    public function __construct(
        UserProvider $provider,
        Request      $request,
                     $inputKey = 'api_token',
                     $storageKey = 'api_token',
                     $hash = false)
    {
        parent::__construct($provider, $request, $inputKey, $storageKey, $hash);
    }

    public function getTokenForRequest()
    {
        //与父类不同的是，这里只取了bearerToken
        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        return $token;
    }
}
