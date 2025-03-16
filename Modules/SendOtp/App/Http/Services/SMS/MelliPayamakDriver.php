<?php
namespace Modules\SendOtp\App\Http\Services\SMS;

use Modules\SendOtp\App\Http\Interfaces\SmsDriverInterface;

class MelliPayamakDriver implements SmsDriverInterface
{
    public function send($otpId, $parameters, $senderNumber, $recipientNumbers)
    {
        $url = "https://api.melipayamak.com/api/send/simple";
        $message = $parameters['message'] ?? 'کد تأیید: ' . ($parameters['code'] ?? '');
        $data = [
            'username' => env('MELLIPAYAMAK_USERNAME', ''),
            'password' => env('MELLIPAYAMAK_PASSWORD', ''),
            'from' => $senderNumber,
            'to' => implode(',', $recipientNumbers),
            'text' => $message,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function sendMessage($message, $senderNumber, $recipientNumbers)
    {
        $url = "https://api.melipayamak.com/api/send/simple";
        $data = [
            'username' => env('MELLIPAYAMAK_USERNAME', ''),
            'password' => env('MELLIPAYAMAK_PASSWORD', ''),
            'from' => $senderNumber,
            'to' => implode(',', $recipientNumbers),
            'text' => $message,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}