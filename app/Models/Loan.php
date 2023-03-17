<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id','amount','outstanding_amount','currency_code','status','processed_at','terms'
    ];
    protected $casts = [
        'user_id'      => 'integer',
        'processed_at' => "datetime"
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function scheduledRepayments(){
        return $this->hasMany(ScheduledRepayment::class);
    }
}
