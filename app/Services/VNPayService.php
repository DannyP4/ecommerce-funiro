<?php

namespace App\Services;

class VNPayService
{
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;

    public function __construct()
    {
        $this->tmnCode = config('vnpay.tmn_code');
        $this->hashSecret = config('vnpay.hash_secret');
        $this->url = config('vnpay.url');
        $this->returnUrl = config('vnpay.return_url');
    }

    /**
     * Create VNPay payment URL
     */
    public function createPaymentUrl($orderId, $amount, $orderInfo, $ipAddress)
    {
        $vnpTxnRef = $orderId . '_' . time();
        $vnpAmount = $amount * 100; // VNPay requires amount in VND cents

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $vnpAmount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $ipAddress,
            "vnp_Locale" => app()->getLocale() === 'vi' ? 'vn' : 'en',
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $vnpTxnRef,
        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpUrl = $this->url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;

        return [
            'url' => $vnpUrl,
            'txn_ref' => $vnpTxnRef
        ];
    }

    /**
     * Verify return URL from VNPay
     */
    public function verifyReturnUrl($inputData)
    {
        $vnpSecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashdata = "";
        $i = 0;
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);

        return $secureHash === $vnpSecureHash;
    }

    /**
     * Get transaction info from VNPay response
     */
    public function getTransactionInfo($inputData)
    {
        return [
            'order_id' => $this->getOrderIdFromTxnRef($inputData['vnp_TxnRef'] ?? ''),
            'amount' => ($inputData['vnp_Amount'] ?? 0) / 100,
            'bank_code' => $inputData['vnp_BankCode'] ?? '',
            'bank_tran_no' => $inputData['vnp_BankTranNo'] ?? '',
            'card_type' => $inputData['vnp_CardType'] ?? '',
            'pay_date' => $inputData['vnp_PayDate'] ?? '',
            'response_code' => $inputData['vnp_ResponseCode'] ?? '',
            'transaction_no' => $inputData['vnp_TransactionNo'] ?? '',
            'transaction_status' => $inputData['vnp_TransactionStatus'] ?? '',
            'txn_ref' => $inputData['vnp_TxnRef'] ?? '',
        ];
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful($responseCode)
    {
        return $responseCode === '00';
    }

    /**
     * Get order ID from transaction reference
     */
    private function getOrderIdFromTxnRef($txnRef)
    {
        $parts = explode('_', $txnRef);
        return $parts[0] ?? null;
    }

    /**
     * Get VNPay response message
     */
    public function getResponseMessage($responseCode)
    {
        $messages = [
            '00' => __('Transaction successful'),
            '07' => __('Transaction suspected fraud'),
            '09' => __('Card not registered for InternetBanking'),
            '10' => __('Card authentication failed'),
            '11' => __('Payment timeout'),
            '12' => __('Card locked'),
            '13' => __('Invalid OTP'),
            '24' => __('Transaction cancelled by customer'),
            '51' => __('Insufficient balance'),
            '65' => __('Daily transaction limit exceeded'),
            '75' => __('Bank under maintenance'),
            '79' => __('Too many wrong password attempts'),
            '99' => __('Other error'),
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}
