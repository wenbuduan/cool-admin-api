<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminDictionaryDataController extends Controller
{

    public function getDictionaryData(Request $request)
    {
        $page_index = intval($request->page);
        $page_size = intval($request->limit);
        $dict_id = intval($request->dictId);

        $query = DB::table('sys_dictionary_data')
            ->select([
                'dict_data_id as dictDataId',
                'dict_data_name as dictDataName',
                'dict_data_code as dictDataCode',
                'sort_number as sortNumber',
                'comments',
                'create_time'
            ])
            ->where('dict_id', $dict_id)
            ->when($request->dictDataName, function ($query) use ($request) {
                $query->where('dict_data_name', 'like', '%' . $request->dictDataName . '%');
            })
            ->when($request->dictDataCode, function ($query) use ($request) {
                $query->where('dict_data_code', 'like', '%' . $request->dictDataCode . '%');
            })
            ->where('deleted', 0);

        $pages = $query
            ->orderBy($request->sort ?? 'create_time', $request->order ?? 'desc')
            ->paginate($page_size, ['*'], 'page', $page_index);

        return $this->jsonOk([
            'count' => $pages->total(),
            'list' => $pages->items()
        ]);
    }

    public function create(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'dictDataName' => 'required|string|max:255',
            'dictDataCode' => 'required|string|max:255',
            'sortNumber' => 'required|integer',
            'comments' => 'nullable|string|max:255',
            'dictId' => 'required|integer',
        ]);
        $body_entity = $request->json()->all();
        $data = [
            'dict_data_name' => $body_entity['dictDataName'],
            'dict_data_code' => $body_entity['dictDataCode'],
            'sort_number' => $body_entity['sortNumber'],
            'sort_number' => $body_entity['sortNumber'],
            'comments' => $body_entity['comments'],
            'dict_id' => $body_entity['dictId']
        ];
        DB::beginTransaction();
        try {
            $id = DB::table('sys_dictionary_data')->insertGetId($data);
            DB::commit();
        } catch (\Exception $e) {
            Log::error('AdminDictionaryDataController@create: ' . $e->getMessage());
            DB::rollBack();
            return $this->jsonError($e->getMessage());
        }

        return $this->jsonOk(['id' => $id]);
    }

    public function update(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'dictDataName' => 'required|string|max:255',
            'dictDataCode' => 'required|string|max:255',
            'sortNumber' => 'required|integer',
            'comments' => 'nullable|string|max:255',
            'dictId' => 'required|integer',
            'dictDataId' => 'required|integer',
        ]);
        $body_entity = $request->json()->all();
        $data = [
            'dict_data_name' => $body_entity['dictDataName'],
            'dict_data_code' => $body_entity['dictDataCode'],
            'sort_number' => $body_entity['sortNumber'],
            'comments' => $body_entity['comments'],
            'dict_id' => $body_entity['dictId']
        ];
        // \dd($data);
        DB::beginTransaction();
        try {
            $id = DB::table('sys_dictionary_data')->where('dict_data_id', $body_entity['dictDataId'])->update($data);
            DB::commit();
        } catch (\Exception $e) {
            Log::error('AdminDictionaryDataController@update: ' . $e->getMessage());
            DB::rollBack();
            return $this->jsonError($e->getMessage());
        }

        return $this->jsonOk(['id' => $id]);
    }

    public function delete(Request $request)
    {
        if (empty($request->all())) {
            return $this->jsonError('请选择要删除的记录');
        }

        $dictDataIds = $request->json()->all();
        DB::beginTransaction();
        try {
            $id = DB::table('sys_dictionary_data')->whereIn('dict_data_id', $dictDataIds)->delete();
            DB::commit();
        } catch (\Exception $e) {
            Log::error('AdminDictionaryDataController@delete: ' . $e->getMessage());
            DB::rollBack();
            return $this->jsonError($e->getMessage());
        }

        return $this->jsonOk(['id' => $id]);
    }
}
