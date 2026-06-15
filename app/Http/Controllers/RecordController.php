<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreRecordRequest;
use App\Http\Requests\Api\UpdateRecordRequest;
use App\Http\Resources\SalaryRecordResource;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $records = SalaryRecord::with('user')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => SalaryRecordResource::collection($records),
        ], 200);
    }

    public function store(StoreRecordRequest $request)
    {
        $record = SalaryRecord::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $record,
        ], 201);
    }

    public function show($id)
    {
        $record = SalaryRecord::with('user')->find($id);
        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $record,
        ], 200);
    }

    public function update(UpdateRecordRequest $request, $id)
    {
        $record = SalaryRecord::find($id);

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

        $record->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $record,
        ], 200);
    }

    public function destroy($id)
    {
        $record = SalaryRecord::find($id);
        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully',
        ], 200);
    }
}
