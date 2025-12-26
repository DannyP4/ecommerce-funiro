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
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}
