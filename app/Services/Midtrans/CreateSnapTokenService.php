<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;

    public function __construct($order)
    {
        parent::__construct();

        $this->order = $order;
    }

    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->code,
                'gross_amount' => $this->order->total,
            ],
            'item_details' => [
                [
                    'id' => $this->order->id,
                    'price' => $this->order->schedule->price,
                    'quantity' => $this->order->orderDetails ? $this->order->orderDetails->count() : 1,
                    'name' => 'Tiket' . $this->order->schedule->route,
                ]
            ],
            'customer_details' => [
                'first_name' => $this->order->user->name,
                'email' => $this->order->user->email,
                'phone' => $this->order->user->phone,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }
}
