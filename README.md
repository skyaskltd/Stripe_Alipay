## quick-view
whmcs的`stripe alipay`接入解决方案，使用了`stripe checkout`控件

## 如何使用

1. 下载release文件
2. 获取stripe密钥和webhook密钥
3. 上传文件到 `/whmcs目录/modules/gateways`
4. 配置所有配置项
5. enjoy！

## stripe webhook 配置

在Stripe的Webhook设置中，添加以下URL作为Endpoint：
```
https://your-whmcs-domain.com/stripealipaycallback/alipay_callback.php
```

确保选择以下事件类型：
- `checkout.session.async_payment_failed`
- `checkout.session.async_payment_succeeded`
- `checkout.session.completed`
- `checkout.session.expired`

详见：
![1.png](/img/1.png)

完成以上步骤后，Stripe的支付回调功能将与Alipay集成成功。

## 贡献

如果你发现了任何问题或者有改进的建议，请随时提交issue或者pull request。

感谢你的贡献！

build with love ❤
