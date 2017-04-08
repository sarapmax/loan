<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public function repayment_schedule() {
    	return $this->hasMany('App\RepaymentSchedule');
    }
}
