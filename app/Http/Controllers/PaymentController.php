<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['student', 'fee'])->get();
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::all();
        $fees = Fee::where('status', 'active')->get();
        return view('payments.create', compact('students', 'fees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create($request->all());

            // Update fee status if needed
            $fee = Fee::find($request->fee_id);
            if ($fee) {
                $totalPaid = Payment::where('fee_id', $fee->id)
                    ->where('student_id', $request->student_id)
                    ->sum('amount');
                
                if ($totalPaid >= $fee->amount) {
                    $fee->update(['status' => 'paid']);
                }
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while processing the payment.')
                ->withInput();
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'fee']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $students = Student::all();
        $fees = Fee::where('status', 'active')->get();
        return view('payments.edit', compact('payment', 'students', 'fees'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $payment->update($request->all());

            // Update fee status if needed
            $fee = Fee::find($request->fee_id);
            if ($fee) {
                $totalPaid = Payment::where('fee_id', $fee->id)
                    ->where('student_id', $request->student_id)
                    ->sum('amount');
                
                if ($totalPaid >= $fee->amount) {
                    $fee->update(['status' => 'paid']);
                } else {
                    $fee->update(['status' => 'pending']);
                }
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the payment.')
                ->withInput();
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            DB::beginTransaction();

            $fee = $payment->fee;
            $studentId = $payment->student_id;

            $payment->delete();

            // Update fee status after deletion
            if ($fee) {
                $totalPaid = Payment::where('fee_id', $fee->id)
                    ->where('student_id', $studentId)
                    ->sum('amount');
                
                if ($totalPaid >= $fee->amount) {
                    $fee->update(['status' => 'paid']);
                } else {
                    $fee->update(['status' => 'pending']);
                }
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the payment.');
        }
    }

    public function generateReceipt(Payment $payment)
    {
        $payment->load(['student', 'fee']);
        return view('payments.receipt', compact('payment'));
    }
} 