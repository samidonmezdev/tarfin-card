<?php

namespace App\Facades;
use App\Enums\PaymentStatus;
use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\ScheduledRepayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;
class LoanFacade extends Facade
{
   public static function createLoan($customer, $amount, $currencyCode, $terms, $processedAt){
       $status = PaymentStatus::DUE;

       $loan = new Loan();
        $loan->user_id = $customer->id;
        $loan->amount = $amount;
        $loan->terms = $terms;
        $loan->outstanding_amount = $amount;
        $loan->currency_code = $currencyCode;
        $loan->processed_at = $processedAt;
        $loan->status = $status;
        if ($loan->save()) {
            $division = floor($amount / $terms);
            $mode = $amount % $terms;

            for ($i = 0 ;$i<$terms;$i++){
                if ($i< $terms - 1) {
                    $subAmount =$division;
                }else{
                    $subAmount =$division +$mode;
                }
                $processedAt = Carbon::parse($processedAt)->addMonth(1)->toString();
                self::sepayCreate($loan,$subAmount,$currencyCode,$processedAt);
            }
        };
        return $loan;
   }

    public static function repayLoan($loan, $receivedRepayment, $currencyCode, $receivedAt){
        $totalReceivedRepayment = $receivedRepayment;
        $repay = new ReceivedRepayment();
        $repay->loan_id = $loan->id;
        $repay->amount = $receivedRepayment;
        $repay->currency_code = $currencyCode;
        $repay->received_at = $receivedAt;
        if ($repay->save()) {
            $pays = $loan->scheduledRepayments()->whereIn('status',[PaymentStatus::DUE,PaymentStatus::PARTIAL,])->get();
            foreach ($pays as $pay){
                var_dump($pay->outstanding_amount);
                if ($pay->outstanding_amount > 0) {
                    if ($pay->outstanding_amount == $receivedRepayment) {
                        $subStatus = PaymentStatus::REPAID;
                        $sub = 0;
                        $receivedRepayment =0;
                    }elseif ($pay->outstanding_amount < $receivedRepayment) {
                        $subStatus = PaymentStatus::REPAID;
                        $sub = 0;
                        $receivedRepayment = $receivedRepayment - $pay->outstanding_amount;
                    }else {
                        $subStatus = PaymentStatus::PARTIAL;
                        $sub = $pay->outstanding_amount - $receivedRepayment ;
                        $receivedRepayment = 0;
                    }

                    $pay->status = $subStatus;
                    $pay->outstanding_amount = $sub ;
                    $pay->save();
                }else {
                    $pay->status = PaymentStatus::REPAID;
                    $pay->save();
                }
                if ($receivedRepayment == 0) {
                    break;
                }
            }
            $subTotal = $loan->outstanding_amount -  $totalReceivedRepayment;
            if ($subTotal!=0) {
                $status = PaymentStatus::DUE;
            }else{
                $status = PaymentStatus::REPAID;
            }
            $loan->status = $status;
            $loan->outstanding_amount = $subTotal;
            $loan->save();
        }
        return $repay;
    }
    public static function sepayCreate($loan, $receivedRepayment, $currencyCode, $receivedAt){
        $today = Carbon::today();
        $status = PaymentStatus::DUE;

       $repay = new ScheduledRepayment();
        $repay->loan_id = $loan->id;
        $repay->amount = $receivedRepayment;
        $repay->outstanding_amount = $receivedRepayment;
        $repay->currency_code = $currencyCode;
        $repay->status = $status;
        $repay->due_date = $receivedAt;
        $repay->save();
        return $repay;
   }
}
