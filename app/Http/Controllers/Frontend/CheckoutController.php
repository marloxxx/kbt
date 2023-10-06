<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Schedule;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function checkout()
    {
        return view('pages.frontend.checkout.index');
    }

    public function confirm(Request $request)
    {
        $schedule = Schedule::find($request->schedule_id);
        $seats = $request->seats;
        $seats = explode(',', $seats[0]);
        $seats_count = count($seats);
        return view('pages.frontend.checkout.confirm', compact('schedule', 'seats', 'seats_count'));
    }

    public function check_coupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->coupon)->first();
        $schedule = Schedule::find($request->schedule_id);
        $seats = $request->seats;
        $seats = explode(',', $seats);
        if ($coupon) {
            if ($coupon->used == 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kode kupon sudah melebihi batas penggunaan.'
                ]);
            } else {
                $price = $schedule->price * count($seats);
                $discount = $price * ($coupon->discount / 100);
                $total = $price - $discount;
                $coupon->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully',
                    'coupon' => $coupon->id,
                    'total' => $total,
                    'price' => $price,
                    'schedule' => $schedule,
                    'discount' => $coupon->discount,
                    'total_discount' => $discount,
                ]);
            }
        } else {
            return response()->json([
                'alert' => 'error',
                'message' => 'Kode kupon tidak ditemukan.'
            ]);
        }
    }

    public function payment(Request $request)
    {
        $validators = Validator::make($request->all(), []);

        if ($validators->fails()) {
            return response()->json([
                'alert' => 'error',
                'message' => $validators->errors()->first(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully',
        ]);
    }

    public function do_checkout(Request $request)
    {
        $schedule = Schedule::find($request->schedule_id);
        $route = $schedule->route;
        $seats = $request->seats;
        $seats = explode(',', $seats);
        $seats_count = count($seats);
        $discount = 0;
        if ($request->coupon_id) {
            $coupon = Coupon::find($request->coupon_id);
            $discount = $schedule->price * $coupon->discount / 100;
        }
        $price = $schedule->price - $discount;
        $total = $price * $seats_count;

        $order = new Order;
        $order->code = Helper::generate_order_code();
        $order->user_id = auth()->user()->id;
        $order->schedule_id = $request->schedule_id;
        $order->coupon_id = $request->coupon_id;
        $order->total = $total;
        $order->save();
        foreach ($seats as $seat) {
            $orderDetail = new OrderDetail;
            $orderDetail->order_id = $order->id;
            $orderDetail->seat_id = $seat;
            $orderDetail->save();
        }

        if ($schedule->route == 'ML') {
            $from = 'Medan';
            $to = 'Laguboti';
        } elseif ($schedule->route == 'LB') {
            $from = 'Laguboti';
            $to = 'Medan';
        } elseif ($schedule->route == 'SL') {
            $from = 'Sibolga';
            $to = 'Laguboti';
        } else {
            $from = 'Laguboti';
            $to = 'Sibolga';
        }

        $admin = User::where('id', 1)->first();
        $admin->notify(new \App\Notifications\NewOrderNotification($order, $from, $to, $schedule));

        $user = User::where('id', auth()->user()->id)->first();
        $user->notify(new \App\Notifications\OrderCreatedNotification($order));

        return view('pages.backend.checkout.detail', compact('order'));
    }
}
