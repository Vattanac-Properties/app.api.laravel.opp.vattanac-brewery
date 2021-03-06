<?php

namespace App\Http\Controllers\API;

use App\Core\EncryptLib;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreatePaymentRequest;
use App\Http\Resources\Payment\AgentPaymentAccount;
use App\Http\Traits\TelegramLogTrait;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use TelegramLogTrait;


    public function hasPendingTransaction()
    {
        try {
            $lastTransaction = PaymentTransaction::getLastTransaction();

            $data = array();
            $status = "";
            if (filled($lastTransaction)) {
                $status = $lastTransaction->status;
                $created_at = Carbon::parse($lastTransaction->created_at);
                $second = now()->diffInSeconds($created_at);
                info("====== created_at : " . $created_at);
                info("====== now : " . now());
                info("====== second : " . $second);

                if($status == "PENDING" && $second > 90) {
                    $status = "";
                    $lastTransaction->update([
                        'status' => 'FAIL',
                    ]);
                }
            }

            if ($status == "PENDING") {
                $data["is_pending"] = true;
                $data["external_id"] = $lastTransaction->encrypt_cart_id;
                $data["orderId"] = $lastTransaction->vb_order_id;
                $data["amount"] = $lastTransaction->amount;
            } else {
                $data["is_pending"] = false;
                $data["external_id"] = null;
                $data["orderId"] = null;
                $data["amount"] = null;
            }
            return $this->ok($data);
        } catch (Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function getPaymentAccount()
    {
        try {
            $payment = auth()->user()->payment_account;
            if (!$payment || !filled($payment)) {
                return $this->fail('No payment account');
            }

            return $this->ok(new AgentPaymentAccount($payment));
        } catch (Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function store(CreatePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $decypt = EncryptLib::decryptString($request->encrypt_cart_id, config('services.vb_cipher.password'), config('services.vb_cipher.iv'));
            info("decypt", [
                'value' => $decypt,
            ]);
            $cart_id_arr = explode('-', $decypt);
            $cart_id = $cart_id_arr[0] ?? '';
            $cart = Cart::where('outlet_id', auth()->user()->id)
                ->first();

            if (!$cart_id && $cart_id != $cart->id) {
                return $this->fail(__('validation.not_found', ['attribute' => __('validation.attributes.cart')]));
            }

            if ($request->amount != $cart->total) {
                return $this->fail(__('validation.incorrect', ['attribute' => 'amount']));
            }

            $payment = PaymentTransaction::create([
                'cart_id' => $cart_id,
                'outlet_id' => auth()->user()->id,
                'encrypt_cart_id' => $request->encrypt_cart_id,
                'vb_order_id' => $request->vb_order_id,
                'amount' => $request->amount,
                'status' => $request->status,
            ]);

            DB::commit();
            return $this->ok(true);
        } catch (Exception $e) {
            DB::rollback();
            $this->logDataTelegram($e->getMessage());
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function update(CreatePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $decypt = EncryptLib::decryptString($request->encrypt_cart_id, config('services.vb_cipher.password'), config('services.vb_cipher.iv'));
            $cart_id_arr = explode('-', $decypt);
            $cart_id = $cart_id_arr[0] ?? '';
            $cart = Cart::where('outlet_id', auth()->user()->id)
                ->first();
            if (!$cart_id && $cart_id != $cart->id) {
                return $this->fail(__('validation.not_found', ['attribute' => __('validation.attributes.cart')]));
            }

            if ($request->amount != $cart->total) {
                return $this->fail(__('validation.incorrect', ['attribute' => 'amount']));
            }

            $payment = PaymentTransaction::where([
                'vb_order_id' => $request->vb_order_id,
                'order_id' => null,
                'encrypt_cart_id' => $request->encrypt_cart_id,
                'outlet_id' => auth()->user()->id,
            ]);

            if ($request->status === 'SUCCESS') {
                $status = ['SCANNED', 'PENDING'];
                $payment = $payment->whereIn('status', $status);
            }

            if ($request->status === 'FAIL') {
                $payment = $payment->where('status', '!=', 'SUCCESS');
            }

            if ($request->status === 'SCANNED') {
                $payment = $payment->where('status', 'PENDING');
            }

            $payment = $payment->first();
            if (!filled($payment)) {
                return $this->fail(__('validation.not_found', ['attribute' => __('validation.attributes.payment')]));
            }

            if ($request->status === 'SUCCESS') {
                $order = Order::placeOrder();
                $payment->order_id = $order->id;
                $payment->transaction_id = $request->transaction_id;
            }

            $payment->status = $request->status;
            $payment->save();

            DB::commit();
            return $this->ok([
                'order_id' => $payment->order_id,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            $this->logDataTelegram($e->getMessage());
            return $this->fail($e->getMessage(), 500);
        }
    }

    public function logData()
    {
        $this->logRequestTelegram();
    }
}
