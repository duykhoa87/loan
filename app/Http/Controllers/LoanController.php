<?php

namespace App\Http\Controllers;


use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'amount' => 'required|numeric|gt:0',
            'loan_term' => 'required|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        //loan term is number of month need to finish payment. Assume interest rate is 0%
        $loan = Loan::create([
            'amount' => $request->amount,
            'remain' => $request->amount,
            'loan_term' => $request->loan_term,
            'weekly_payment' => $request->amount / ($request->loan_term*4),
            'created_by' => auth()->user()->id
        ]);
        
        return response()->json(['data' => $loan]);
    }

    public function approve(Request $request): JsonResponse
    {
        $loan = Loan::find($request->id);
        if (! $loan) {
            return response()->json(['message' => 'Loan is not found'], 404);
        } elseif ($loan->status && $loan->approver) {
            return response()->json(['message' => 'Your loan has been approved already']);
        }
        $loan->status = 1;
        $loan->approver = auth()->user()->id;
        $loan->update();
    
        return response()->json(['data' => $loan]);
    }
    
    public function pay(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'loan_id' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $loan = Loan::find($request->loan_id);
        if (! $loan) {
            return response()->json(['message' => 'Loan is not found'], 404);
        }
        if ($request->amount > $loan->remain) {
            return response()->json(['message' => 'Amount is invalid']);
        }
        //update remain of amount of loan
        $payment = new Payment();
        $payment->loan_id = $request->loan_id;
        $payment->amount = $request->amount;
        $payment->created_by = auth()->user()->id;
        $payment->save();
        
        //update remain of amount of loan
        $countPayment = Payment::where('loan_id', $loan->id)->count();
        $loanTerm = ($loan->loan_term*4) - ($countPayment ?? 1);
        $loan->remain -= $payment->amount;
        $loan->weekly_payment = $loan->remain / $loanTerm;
        $loan->update();
        
        return response()->json(['data' => $payment]);
    }
}
