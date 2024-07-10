<<<<<<< HEAD
<?php
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
require_once(__DIR__ . '/stripeaplipaycallback/vendor/autoload.php');
function stripealipay_config() {
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Stripe Alipay',
        ),
        'WebsiteDomin' => array(
            'FriendlyName' => 'WebsiteDomin',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '填写您的网站域名，例如：https://你的whmcs站点域名/',
        ),
        'Fixedfee' => array(
            'FriendlyName' => 'Fixedfee',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '每单收取的固定费用，例如：2.5',
        ),
        'Percentagefee' => array(
            'FriendlyName' => 'Percentagefee',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '每单收取的百分比费用，例如：2.5',
        ),
        'StripeSkLive' => array(
            'FriendlyName' => 'SK_LIVE',
            'Type' => 'password',
            'Size' => 30,
            'Description' => '填写从Stripe获取到的秘密密钥（SK_LIVE）',
        ),
        'StripeWebhookKey' => array(
            'FriendlyName' => 'Stripe Webhook密钥',
            'Type' => 'password',
            'Size' => 30,
            'Description' => '填写从Stripe获取到的Webhook密钥签名',
        ),
        'StripeCurrency' => array(
            'FriendlyName' => '发起交易货币',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '默认获取WHMCS的货币，与您设置的发起交易货币进行汇率转换，再使用转换后的价格和货币向Stripe请求',
        )
    );
}
function create_stripe_price_object($params){
    Stripe::setApiKey($params['StripeSkLive']);

    try {
        // 创建Stripe产品对象
        $product = Product::create([
            'name' => 'Invoice #' . $params['invoiceid'],
        ]);

        // 创建Stripe价格对象
        $price = Price::create([
            'unit_amount' => $params['amount'] * 100 + $params['Fixedfee'] * 100 +  ceil($params['amount'] * $params['Percentagefee']), // 转换为分
            'currency' => $params['currency'],
            'product' => $product->id,
        ]);

        // 返回价格对象的ID
        return $price->id;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // 处理API错误
        error_log("Stripe API error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    } catch (Exception $e) {
        // 处理其他错误
        error_log("General error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    }
}

function stripealipay_link($params){
    $stripeSKlive = $params['StripeSkLive'];
    Stripe::setApiKey($stripeSKlive);
    try {
        $price_id = create_stripe_price_object($params);
        if (!$price_id) {
            throw new Exception("Failed to create Stripe price object.");
        }
        $YOUR_DOMAIN = $params['WebsiteDomin'];
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['alipay'],
            'line_items' => [[
                'price' => $price_id,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN .'/modules/gateways/stripealipaycallpay/alipay_callback.php?session_id= {CHECKOUT_SESSION_ID} '', 
            'cancel_url' => $YOUR_DOMAIN .'/viewinvoice.php?id='. $params['invoiceid'] .'',
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);
        return '<form action="'.$checkout_session['url'].'" method="get"><input type="submit" class="btn btn-primary" value="'.$params['langpaynow'].'" /></form>';//返回一个按钮，点击后跳转到Stripe支付页面
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // 处理Stripe API错误
        error_log("Stripe API error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    } catch (Exception $e) {
        // 处理其他错误
        error_log("General error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    }
}


=======
<?php
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
require_once(__DIR__ . '/stripeaplipaycallback/vendor/autoload.php');
function stripealipay_config() {
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Stripe Alipay',
        ),
        'WebsiteDomin' => array(
            'FriendlyName' => 'WebsiteDomin',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '填写您的网站域名，例如：https://你的whmcs站点域名/',
        ),
        'Fixedfee' => array(
            'FriendlyName' => 'Fixedfee',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '每单收取的固定费用，例如：2.5',
        ),
        'Percentagefee' => array(
            'FriendlyName' => 'Percentagefee',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '每单收取的百分比费用，例如：2.5',
        ),
        'StripeSkLive' => array(
            'FriendlyName' => 'SK_LIVE',
            'Type' => 'password',
            'Size' => 30,
            'Description' => '填写从Stripe获取到的秘密密钥（SK_LIVE）',
        ),
        'StripeWebhookKey' => array(
            'FriendlyName' => 'Stripe Webhook密钥',
            'Type' => 'password',
            'Size' => 30,
            'Description' => '填写从Stripe获取到的Webhook密钥签名',
        ),
        'StripeCurrency' => array(
            'FriendlyName' => '发起交易货币',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '默认获取WHMCS的货币，与您设置的发起交易货币进行汇率转换，再使用转换后的价格和货币向Stripe请求',
        )
    );
}
function create_stripe_price_object($params){
    Stripe::setApiKey($params['StripeSkLive']);

    try {
        // 创建Stripe产品对象
        $product = Product::create([
            'name' => 'Invoice #' . $params['invoiceid'],
        ]);

        // 创建Stripe价格对象
        $price = Price::create([
            'unit_amount' => $params['amount'] * 100 + $params['Fixedfee'] * 100 +  ceil($params['amount'] * $params['Percentagefee']), // 转换为分
            'currency' => $params['currency'],
            'product' => $product->id,
        ]);

        // 返回价格对象的ID
        return $price->id;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // 处理API错误
        error_log("Stripe API error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    } catch (Exception $e) {
        // 处理其他错误
        error_log("General error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    }
}

function stripealipay_link($params){
    $stripeSKlive = $params['StripeSkLive'];
    Stripe::setApiKey($stripeSKlive);
    try {
        $price_id = create_stripe_price_object($params);
        if (!$price_id) {
            throw new Exception("Failed to create Stripe price object.");
        }
        $YOUR_DOMAIN = $params['WebsiteDomin'];
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['alipay'],
            'line_items' => [[
                'price' => $price_id,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN .'/modules/gateways/stripealipaycallpay/alipay_callback.php?session_id= {CHECKOUT_SESSION_ID} '', 
            'cancel_url' => $YOUR_DOMAIN .'/viewinvoice.php?id='. $params['invoiceid'] .'',
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);
        return '<form action="'.$checkout_session['url'].'" method="get"><input type="submit" class="btn btn-primary" value="'.$params['langpaynow'].'" /></form>';//返回一个按钮，点击后跳转到Stripe支付页面
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // 处理Stripe API错误
        error_log("Stripe API error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    } catch (Exception $e) {
        // 处理其他错误
        error_log("General error: " . $e->getMessage());
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理 '.$e.'</div>';
    }
}


>>>>>>> 7e0e488 (chore: Add Alipay callback script for Stripe integration)
