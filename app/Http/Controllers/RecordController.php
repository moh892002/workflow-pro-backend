<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index()
    {
        $records = SalaryRecord::with('user')->get();
        return response()->json([
            'status' => true,
            'data' => $records
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
            'status' => true,
            'data' => $record
        ], 201);
    }

    public function show($id){
        $record = SalaryRecord::with('user')->find($id);
        if(!$record){
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $record
        ], 200);
    }

    public function update(Request $request, $id){
        $record = SalaryRecord::find($id);
        if(!$record){
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        $validated = $request->validate([
            "user_id" => "required|exists:users,id",
            "transaction_type" => "required|in:salary,bonus,deduction,advance,overtime",
            "amount" => "required|numeric",
            "transaction_date" => "required|date",
            "notes" => "nullable|string",
        ]);

        $record->update($validated);

        return response()->json([
            'status' => true,
            'data' => $record
        ], 200);
    }

        public function destroy($id){
            $record = SalaryRecord::find($id);
            if(!$record){
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found'
                ], 404);
            } 
            $record->delete();
            return response()->json([
                'status' => true,
                'message' => 'Record deleted successfully'
            ], 200);
        }

}