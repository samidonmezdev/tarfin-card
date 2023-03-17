<?php

namespace App\Facades;
use App\Enums\PaymentStatus;
use App\Models\Loan;
use App\Models\ReceivedRepayment;
use Illuminate\Support\Facades\Facade;
class LoanFacade extends Facade
{
   public static function createLoan($customer, $amount, $currencyCode, $terms, $processedAt){
        $loan = new Loan();
        $loan->user_id = $customer->id;
        $loan->amount = $amount;
        $loan->terms = $terms;
        $loan->outstanding_amount = $amount;
        $loan->currency_code = $currencyCode;
        $loan->processed_at = $processedAt;
        $loan->status = PaymentStatus::DUE;
        $loan->save();
        return $loan;
   }

    public static function repayLoan($loan, $receivedRepayment, $currencyCode, $receivedAt){
        $repay = new ReceivedRepayment();
        $repay->loan_id = $loan->id;
        $repay->amount = $receivedRepayment;
        $repay->currency_code = $currencyCode;
        $repay->received_at = $receivedAt;
        $repay->save();
        return $repay;



    }
}
