<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan, App\RepaymentSchedule;
use DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loan::orderBy('created_at', 'DESC')->paginate(15);

        return view('loan.index', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('loan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'loan_amount' => 'required|numeric|min:1000|max:100000000',
            'loan_term' => 'required|numeric|min:1|max:50',
            'interest_rate' => 'required|numeric|min:1|max:36',
            'start_date' => 'required',
        ]);

        $month_amount = ($request->loan_term * 12) - 1;

        $success;

        DB::beginTransaction();

        try {
            $loan = new Loan;

            $loan->loan_amount = $request->loan_amount;
            $loan->loan_term = $request->loan_term;
            $loan->interest_rate = $request->interest_rate;

            $loan->save();

            foreach($this->calRepaymentSchedule($request->loan_amount, $request->interest_rate/100, $request->loan_term, $month_amount, $request->start_date) as $repayment_schedule_data) {

                $repayment_schedule = new RepaymentSchedule;

                $repayment_schedule->loan_id = $loan->id;
                $repayment_schedule->payment_no = $repayment_schedule_data['payment_no'];
                $repayment_schedule->date = $repayment_schedule_data['date'];
                $repayment_schedule->payment_amount = $repayment_schedule_data['PMT'];
                $repayment_schedule->principal = $repayment_schedule_data['principal'];
                $repayment_schedule->interest = $repayment_schedule_data['interest'];
                $repayment_schedule->balance = $repayment_schedule_data['balance'];

                $repayment_schedule->save();
            }

            DB::commit();
            $success = true;

        }catch (\Exception $e) {
            DB::rollback();
            $success = false;
        }

        if($success) {
            return redirect()->route('loan.show', $loan->id)->with('alert-info', 'The loan has been created successfully.');
        }else {
            return redirect()->route('loan.index')->with('alert-danger', 'Cannot create loan, something went wrong :(');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = Loan::find($id);

        return view('loan.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loan = Loan::find($id);

        return view('loan.edit', compact('loan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'loan_amount' => 'required|numeric|min:1000|max:100000000',
            'loan_term' => 'required|numeric|min:1|max:50',
            'interest_rate' => 'required|numeric|min:1|max:36',
            'start_date' => 'required',
        ]);

        $month_amount = ($request->loan_term * 12) - 1;

        $success;

        DB::beginTransaction();

        try {
            $loan = Loan::find($id);

            $loan->loan_amount = $request->loan_amount;
            $loan->loan_term = $request->loan_term;
            $loan->interest_rate = $request->interest_rate;

            $loan->save();

            foreach($loan->repayment_schedule as $repayment_schedule) {
                $repayment_schedule->delete();
            }

            foreach($this->calRepaymentSchedule($request->loan_amount, $request->interest_rate/100, $request->loan_term, $month_amount, $request->start_date) as $repayment_schedule_data) {

                $repayment_schedule = new RepaymentSchedule;

                $repayment_schedule->loan_id = $loan->id;
                $repayment_schedule->payment_no = $repayment_schedule_data['payment_no'];
                $repayment_schedule->date = $repayment_schedule_data['date'];
                $repayment_schedule->payment_amount = $repayment_schedule_data['PMT'];
                $repayment_schedule->principal = $repayment_schedule_data['principal'];
                $repayment_schedule->interest = $repayment_schedule_data['interest'];
                $repayment_schedule->balance = $repayment_schedule_data['balance'];

                $repayment_schedule->save();
            }
            
            DB::commit();
            $success = true;

        }catch (\Exception $e) {
            DB::rollback();
            $success = false;
        }

        if($success) {
            return redirect()->route('loan.show', $loan->id)->with('alert-info', 'The loan has been updated successfully.');
        }else {
            return redirect()->route('loan.index')->with('alert-danger', 'Cannot update loan, something went wrong :(');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $success;

        DB::beginTransaction();
        try {
            $loan = Loan::find($id);

            foreach($loan->repayment_schedule as $repayment_schedule) {
                $repayment_schedule->delete();
            }

            $loan->delete();

            DB::commit();
            $success = true;

        }catch (\Exception $e) {
            DB::rollback();
            $success = false;
        }

        if($success) {
            return redirect()->back()->with('alert-info', 'The loan has been deleted successfully.');
        }
    }

    private function calRepaymentSchedule($loan_amount, $interest, $year, $month_amount, $start_date) {
        $data = [];

        for($i = 0; $i <= $month_amount; $i++) {
             if($i == 0) {
                $data[$i]['payment_no'] = $i + 1;
                $data[$i]['date'] = date('Y-m-d', strtotime('+'.$i.' month', strtotime($start_date)));
                $data[$i]['PMT'] = ($loan_amount * ($interest / 12)) / (1 - pow((1 + ($interest / 12)), (-12 * $year)));
                $data[$i]['interest'] = ($interest/12) * $loan_amount;
                $data[$i]['principal'] = $data[$i]['PMT'] - $data[$i]['interest'];
                $data[$i]['balance'] = $loan_amount - $data[$i]['principal'];
             }else {
                $data[$i]['payment_no'] = $i + 1;
                $data[$i]['date'] = date('Y-m-d', strtotime('+'.$i.' month', strtotime($start_date)));
                $data[$i]['PMT'] = $data[$i-1]['PMT'];
                $data[$i]['interest'] = ($interest/12) * $data[$i-1]['balance'];
                $data[$i]['principal'] = $data[$i]['PMT'] - $data[$i]['interest'];
                $data[$i]['balance'] = $data[$i-1]['balance'] - $data[$i]['principal'];
             }
        }

        return $data;
    }

    public function advancedSeach(Request $request) {
        $loans = Loan::whereBetween('loan_amount', [$request->loan_amount_min, $request->loan_amount_max])
                        ->orWhereBetween('loan_term', [$request->loan_term_min, $request->loan_term_max])
                        ->orWhereBetween('interest_rate', [$request->interest_rate_min, $request->interest_rate_max])
                        ->paginate(15);

        return view('loan.search', compact('loans'));
    }
}
