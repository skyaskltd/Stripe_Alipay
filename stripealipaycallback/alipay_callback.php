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

$endpoint_secret = $gatewayParams['StripeWebhookKey'];
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;
if ($endpoint_secret) {
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        echo json_encode(array("error" => "无效的负载"));
        exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        http_response_code(400);
        echo json_encode(array("error" => "无效的签名"));
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(array("error" => "未配置 endpoint_secret"));
    exit();
    }
    
    // 处理checkout session事件
    switch ($event->type) {
        case 'checkout.session.async_payment_failed':
            $session = $event->data->object;
            // 更新订单状态为支付失败
            $invoiceId = substr($session->success_url, -4); // 从 success_url 中获取 invoice_id
            logTransaction($gatewayModuleName, $session, "支付失败");
            http_response_code(400);
            echo json_encode(array("error" => "支付失败"));
            break;
    
        case 'checkout.session.async_payment_succeeded':
            $session = $event->data->object;
            // 更新订单状态为已支付
            $invoiceId = substr($session->success_url, -4); // 从 success_url 中获取 invoice_id
            addInvoicePayment($invoiceId, $session->payment_intent, $session->amount_total / 100, 0, $gatewayModuleName);
            logTransaction($gatewayModuleName, $session, "支付成功");
            http_response_code(200);
            break;
    
        case 'checkout.session.completed':
            $session = $event->data->object;
            // 检查支付状态并更新订单状态
            if ($session->payment_status == 'paid') {
                $invoiceId = substr($session->success_url, -4); // 从 success_url 中获取 invoice_id
                addInvoicePayment($invoiceId, $session->payment_intent, $session->amount_total / 100, 0, $gatewayModuleName);
                logTransaction($gatewayModuleName, $session, "支付成功");
                http_response_code(200);
            } else {
                $invoiceId = substr($session->success_url, -4); // 从 success_url 中获取 invoice_id
                logTransaction($gatewayModuleName, $session, "支付未完成");
                http_response_code(400);
                echo json_encode(array("error" => "支付未完成"));
            }
            break;
    
        // 处理其他事件类型
        default:
            http_response_code(400);
            echo json_encode(array("error" => "未处理的事件类型: " . $event->type));
            exit();
    }
?>