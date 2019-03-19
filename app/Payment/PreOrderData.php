<?php

declare(strict_types=1);


namespace App\Payment;


use App\Models\Recharge;
use App\Types\Channel;

class PreOrderData
{
    static public function build(Recharge $recharge)
    {
        if ($recharge->isAlipay()) {
             $data = [
                'subject'      => $recharge->{Recharge::SUBJECT},
                'body'      => $recharge->{Recharge::BODY},
                'out_trade_no' => TradeNo::encode($recharge->{Recharge::APP_ID}, $recharge->{Recharge::ORDER_NO}, $recharge->{Recharge::ID}),
                'total_amount' => $recharge->getCentAmount(),
                'product_code' => static::getAlipayProductCode($recharge->{Recharge::CHANNEL}),
            ];

             return $data;
        }

        if ($recharge->isWx()) {
            return $data = [
                'body'      => $recharge->{Recharge::BODY},
                'out_trade_no' => $recharge->{Recharge::ID},
                'total_fee'         => $recharge->{Recharge::AMOUNT},
                'spbill_create_ip'  => $recharge->{Recharge::CLIENT_IP},
                'fee_type'          => $recharge->{Recharge::CURRENCY},
            ];
        }

        if ($recharge->isQpay()) {
            return $data = [
                'attach'      => $recharge->{Recharge::SUBJECT},
                'body'      => $recharge->{Recharge::BODY},
                'out_trade_no' => $recharge->{Recharge::ID},
                'total_fee'         => $recharge->{Recharge::AMOUNT},
                'spbill_create_ip'  => $recharge->{Recharge::CLIENT_IP},
                'fee_type'          => $recharge->{Recharge::CURRENCY},
                'notify_url' => route('notify_url')
            ];
        }
    }

    static function getAlipayProductCode(string $channel): string
    {
        switch ($channel) {
            case Channel::ALIPAY_PC:
                return 'FAST_INSTANT_TRADE_PAY';
            case Channel::ALIPAY_WAP:
                return 'QUICK_WAP_PAY';
            case Channel::ALIPAY:
                return 'QUICK_MSECURITY_PAY';
            case Channel::ALIPAY_JS:
                return '';
            case Channel::ALIPAY_F2F:
                return '';
        }
    }
}
