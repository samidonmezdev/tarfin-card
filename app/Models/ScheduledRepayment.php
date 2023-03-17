<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledRepayment extends Model
{
    use HasFactory;
    protected $fillable =[
        'loan_id','amount','outstanding_amount','currency_code','due_date','status'
    ];
    protected $casts = [
        'loan_id'         => 'integer',
        'due_date' => 'datetime',
    ];
}
