<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\PaymentGatewaySetting;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['bKash Merchant', 'bkash', 1, false, 'sandboxTokenizedUser02', 'sandboxTokenizedUser02@12345', null, '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/create', 'bKash tokenized sandbox is configured with the provided app key, app secret, username and password.'],
            ['aamarPay', 'amarpay', 2, false, null, null, 'aamarpaytest', 'dbb74894e82415a2f7ff0ec3a97e4183', 'https://sandbox.aamarpay.com', 'https://sandbox.aamarpay.com/index.php', 'aamarPay sandbox is configured with the provided test Store ID and Signature Key.'],
            ['ShurjoPay', 'shurjopay', 3, false, 'sp_sandbox', 'pyyk97hu&6u6', null, null, 'https://sandbox.shurjopayment.com', 'https://sandbox.shurjopayment.com/api/secret-pay', 'ShurjoPay sandbox is configured with username sp_sandbox and transaction prefix NOK.'],
        ] as [$name, $code, $sort, $simulator, $username, $password, $storeId, $signatureKey, $baseUrl, $checkoutUrl, $notes]) {
            PaymentGatewaySetting::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'is_active' => true,
                    'is_sandbox' => true,
                    'use_sandbox_simulator' => $simulator,
                    'currency' => 'BDT',
                    'username' => $username,
                    'password' => $password,
                    'store_id' => $storeId,
                    'api_key' => $code === 'bkash' ? '4f6o0cjiki2rfm34kfdadl1eqq' : null,
                    'api_secret' => $signatureKey,
                    'base_url' => $baseUrl,
                    'checkout_url' => $checkoutUrl,
                    'logo_url' => $code === 'amarpay' ? 'https://www.aamarpay.com/images/logo/aamarpay_logo.png' : null,
                    'extra_config' => match ($code) {
                        'bkash' => [
                            'grant_token_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant',
                            'create_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/create',
                            'execute_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/execute',
                            'status_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/payment/status',
                            'intent' => 'sale',
                            'mode' => '0011',
                            'success_wallets' => '01770618575,01929918378,01770618576,01877722345,01619777282,01619777283',
                            'failed_wallets' => '01823074817,01823074818',
                            'sandbox_pin' => '12121',
                            'sandbox_otp' => '123456',
                        ],
                        'shurjopay' => [
                            'prefix' => 'NOK',
                            'auth_url' => 'https://sandbox.shurjopayment.com/api/get_token',
                            'verify_url' => 'https://sandbox.shurjopayment.com/api/verification',
                            'ssl_verifypeer' => false,
                        ],
                        default => null,
                    },
                    'notes' => $notes,
                    'sort_order' => $sort,
                ],
            );
        }

        Course::query()
            ->whereNull('fee_amount')
            ->get()
            ->each(function (Course $course): void {
                $amount = (float) preg_replace('/[^0-9.]/', '', (string) $course->fee);

                if ($amount > 0) {
                    $course->forceFill(['fee_amount' => $amount])->save();
                }
            });
    }
}
