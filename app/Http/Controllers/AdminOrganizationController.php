<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Imagick;
use Illuminate\Support\Facades\Validator;

class AdminOrganizationController extends Controller
{
    public function getOrganizationList(Request $request)
    {
        $organizationList = DB::table('sys_organization')
            ->select([
                'organization_id as organizationId',
                'organization_name as organizationName',
                'organization_full_name as organizationFullName',
                'organization_code as organizationCode',
                'organization_type as organizationType',
                'sort_number as sortNumber',
                'create_time as createTime',
            ])
            ->where('deleted', 0)
            ->get();
        return $this->jsonOk($organizationList);
    }

    public function create(Request $request)
    {
        \dd($request->all());
        //           "parentId" => 1
        //   "organizationName" => "测试组"
        //   "organizationFullName" => "测试组"
        //   "organizationCode" => null
        //   "organizationType" => "tesst1"
        //   "sortNumber" => 1
        //   "comments" => null
        $this->validate($request, [
            'parentId' => 'required|integer',
            'organizationName' => 'required|string|max:255',
            'organizationFullName' => 'required|string|max:255',
            'organizationType' => 'required|string|max:255',
            'sortNumber' => 'required|integer',
        ]);
    }
}
