<?php

namespace App\Http\Controllers;

use App\Traits\ResponseJsonTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    use ResponseJsonTrait;

    protected function getAdminId(): int
    {
        $userId = intval(Auth::id());
        return $userId;
    }

    protected function getAdminName(): string
    {
        return Auth::user()['username'];
    }

    protected function toJson($value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
