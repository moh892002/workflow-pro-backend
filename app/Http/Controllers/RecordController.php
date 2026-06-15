<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreRecordRequest;
use App\Http\Requests\Api\UpdateRecordRequest;
use App\Http\Resources\SalaryRecordResource;
use App\Services\SalaryRecordService;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function __construct(
        private readonly SalaryRecordService $recordService,
    ) {}

    public function index(Request $request)
    {
        $records = $this->recordService->list($request->only(['per_page', 'page']));

        return response()->json([
            'success' => true,
            'data' => SalaryRecordResource::collection($records),
        ], 200);
    }

    public function store(StoreRecordRequest $request)
    {
        $record = $this->recordService->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $record,
        ], 201);
    }

    public function show($id)
    {
        $record = $this->recordService->find($id);
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
        $record = $this->recordService->find($id);

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

        $this->recordService->update($record, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $record,
        ], 200);
    }

    public function destroy($id)
    {
        $record = $this->recordService->find($id);
        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

        $this->recordService->delete($record);

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully',
        ], 200);
    }
}
