<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreRecordRequest;
use App\Http\Requests\Api\UpdateRecordRequest;
use App\Http\Resources\SalaryRecordResource;
use App\Models\SalaryRecord;
use App\Services\SalaryRecordService;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function __construct(
        private readonly SalaryRecordService $recordService,
    ) {}

    public function index(Request $request)
    {
        $records = $this->recordService->list($request->user(), $request->only(['per_page', 'page']));

        return $this->success(SalaryRecordResource::collection($records));
    }

    public function store(StoreRecordRequest $request)
    {
        $this->authorize('create', SalaryRecord::class);

        return $this->created($this->recordService->create($request->validated()));
    }

    public function show(SalaryRecord $record)
    {
        $this->authorize('view', $record);

        return $this->success($record->load('user'));
    }

    public function update(UpdateRecordRequest $request, SalaryRecord $record)
    {
        $this->authorize('update', $record);

        $this->recordService->update($record, $request->validated());

        return $this->success($record);
    }

    public function destroy(SalaryRecord $record)
    {
        $this->authorize('delete', $record);

        $this->recordService->delete($record);

        return $this->message('Record deleted successfully');
    }
}
