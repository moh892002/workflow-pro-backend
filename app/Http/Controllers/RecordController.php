<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalaryRecordResource;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RecordController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $records = SalaryRecord::with('user')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => SalaryRecordResource::collection($records)
        ], 200);
    }

    public function store(Request $request){
        $validated = $request->validate([
            "user_id" => "required|exists:users,id",
            "transaction_type" => "required|in:salary,bonus,deduction,advance,overtime",
            "amount" => "required|numeric",
            "transaction_date" => "required|date",
            "notes" => "nullable|string",
        ]);

        $record = SalaryRecord::create($validated);
        return response()->json([
            'success' => true,
            'data' => $record
        ], 201);
    }

    public function show($id){
        $record = SalaryRecord::with('user')->find($id);
        if(!$record){
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $record
        ], 200);
    }

    public function update(Request $request, $id){
        $record = SalaryRecord::find($id);

        $validated = $request->validate([
            "user_id" => "required|exists:users,id",
            "transaction_type" => "required|in:salary,bonus,deduction,advance,overtime",
            "amount" => "required|numeric",
            "transaction_date" => "required|date",
            "notes" => "nullable|string",
        ]);

        $record->update($validated);

        if(!$record){
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $record
        ], 200);
    }

        public function destroy($id){
            $record = SalaryRecord::find($id);
            if(!$record){
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            } 
            $record->delete();
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ], 200);
        }

}