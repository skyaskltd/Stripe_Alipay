<?php
require_once realpath(dirname(__FILE__)) . '/../../../init.php';
require_once ROOTDIR . '/includes/gatewayfunctions.php';
require_once ROOTDIR . '/includes/invoicefunctions.php';
require 'vendor/autoload.php';

// 网关模块名称
$gatewayModuleName = 'stripealipay';
// 获取网关配置参数
$gatewayParams = getGatewayVariables($gatewayModuleName);
if (!$gatewayParams['type']) {
    die("模块未激活");
}

$stripe = new \Stripe\StripeClient($gatewayParams['StripeSkLive']); // 使用Stripe秘钥初始化Stripe客户端

try {
    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']); // 使用Stripe客户端检索会话

    if ($session->payment_status === 'paid') { // 如果支付状态为已支付
        $invoiceId = $session->metadata->invoice_id;
        $transactionId = $session->payment_intent;
        $paymentAmount = $session->amount_total / 100;
        $paymentFee = $session->total_details->fee_amount / 100; // 修正了支付费用的计算方式

        addInvoicePayment( // 添加付款
            $invoiceId,
            $transactionId,
            $paymentAmount,
            $paymentFee,
            $gatewayModuleName
        );

        logTransaction( // 记录交易日志
            $gatewayParams['name'],
            $_GET,
            'Successful'
        );

        // 重定向客户到成功页面（此处需要添加重定向逻辑）
        header("Location: " . $gatewayParams['returnUrl']);
        exit;
    } else {
        // 重定向客户到失败页面（此处需要添加重定向逻辑）
        header("Location: " . $gatewayParams['cancelUrl']);
        exit;
    }
} catch (\Exception $e) {
    // 处理异常，例如记录日志或重定向到错误页面
    logTransaction($gatewayParams['name'], ['error' => $e->getMessage()], 'Error');
    header("Location: " . $gatewayParams['cancelUrl']);
    exit;
}
