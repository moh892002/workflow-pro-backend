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

        return $this->success(SalaryRecordResource::collection($records));
    }

    public function store(StoreRecordRequest $request)
    {
        return $this->created($this->recordService->create($request->validated()));
    }

    public function show($id)
    {
        $record = $this->recordService->find($id);
        if (! $record) {
            return $this->error('Record not found', 404);
        }

        return $this->success($record);
    }

    public function update(UpdateRecordRequest $request, $id)
    {
        $record = $this->recordService->find($id);

        if (! $record) {
            return $this->error('Record not found', 404);
        }

        $this->recordService->update($record, $request->validated());

        return $this->success($record);
    }

    public function destroy($id)
    {
        $record = $this->recordService->find($id);
        if (! $record) {
            return $this->error('Record not found', 404);
        }

        $this->recordService->delete($record);

        return $this->message('Record deleted successfully');
    }
}
