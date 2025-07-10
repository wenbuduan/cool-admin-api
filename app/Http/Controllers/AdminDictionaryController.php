<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminDictionaryController extends Controller
{
    public function getDictionaryByFilter(Request $request)
    {
        $request->validate([
            'dict_code' => 'required|string|max:255',
        ]);
        $dictCode = $request->input('dict_code');


        $dictionary = DB::table('sys_dictionary')->where('dict_code', $dictCode)->get();

        return $this->toJson($dictionary);
    }

    public function create(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'dictCode' => 'required|string|max:255',
            'dictName' => 'required|string|max:255',
            'sortNumber' => 'required|integer',
            'comments' => 'nullable|string|max:255',
        ]);

        $dict = $request->json()->all();
        $dict_info = [
            'dict_code' => $dict['dictCode'],
            'dict_name' => $dict['dictName'],
            'sort_number' => $dict['sortNumber'],
            'comments' => $dict['comments']
        ];
        DB::beginTransaction();
        try {
            $dict_id = DB::table('sys_dictionary')->insertGetId($dict_info);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('create dictionary error:' . $e->getMessage());
            return $this->jsonError('create dictionary error');
        }

        return $this->jsonOk(['dictId' => $dict_id]);
    }

    /**
     * 字典列表
     * @return json
     **/
    public function getDictionaryList(Request $request)
    {
        $dictionary = DB::table('sys_dictionary')
            ->select([
                'dict_id as dictId',
                'dict_code as dictCode',
                'dict_name as dictName',
                'sort_number as sortNumber',
                'comments',
                'create_time as createTime',
            ])
            ->where('deleted', 0)
            ->get();

        return $this->jsonOk($dictionary);
    }

    // public function getDictionaryData(Request $request)
    // {
    //     $request->validate([
    //         'dictId' => 'required|integer',
    //     ]);
    //     $page_index = intval($request->page);
    //     $page_size = intval($request->limit);
    //     $dict_id = intval($request->dictId);

    //     // \dd($request->all());
    //     $query = DB::table('sys_dictionary_data')->select([
    //         'dict_data_id as dictDataId',
    //         'dict_id as dictId',
    //         'dict_data_code as dictDataCode',
    //         'dict_data_name as dictDataName',
    //         'sort_number as sortNumber',
    //         'comments',
    //         'create_time'
    //     ])
    //         ->when($request->dictDataName, function ($query) use ($request) {
    //             $query->where('dict_data_name', 'like', '%' . $request->dictDataName . '%');
    //         })
    //         ->when($request->dictDataCode, function ($query) use ($request) {
    //             $query->where('dict_data_code', 'like', '%' . $request->dictDataCode . '%');
    //         })
    //         ->where('dict_id', $dict_id)
    //         ->where('deleted', 0);


    //     $pages = $query
    //         ->orderBy($request->sort ?? 'create_time', $request->order ?? 'desc')
    //         ->paginate($page_size, ['*'], 'page', $page_index);
    //     return $this->jsonOk([
    //         'count' => $pages->count(),
    //         'list' => $pages->items(),
    //     ]);
    // }

    public function update(Request $request)
    {
        // \dd($request->all());
        $this->validate($request, [
            'dictId' => 'required|integer',
            'dictCode' => 'required|string|max:255',
            'dictName' => 'required|string|max:255',
            'sortNumber' => 'required|integer',
            'comments' => 'nullable|string|max:255',
        ]);

        $dict = $request->json()->all();
        $dict_info = [
            'dict_code' => $dict['dictCode'],
            'dict_name' => $dict['dictName'],
            'sort_number' => $dict['sortNumber'],
            'comments' => $dict['comments']
        ];
        DB::beginTransaction();
        try {
            DB::table('sys_dictionary')->where('dict_id', $dict['dictId'])->update($dict_info);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('update dictionary error:' . $e->getMessage());
            return $this->jsonError('update dictionary error');
        }

        return $this->jsonOk(['dictId' => $dict['dictId']]);
    }
    public function delete($dict_id)
    {
        if (empty($dict_id)) {
            return $this->jsonError('data not found');
        }

        DB::beginTransaction();
        try {
            DB::table('sys_dictionary')->where('dict_id', $dict_id)->update(['deleted' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('delete dictionary error:' . $e->getMessage());
            return $this->jsonError('delete dictionary error');
        }

        return $this->jsonOk(['dictId' => $dict_id]);
    }
}
