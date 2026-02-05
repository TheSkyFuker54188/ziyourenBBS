<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: [点微]支付中心 6.3
 *      下载地址: https://addon.dismall.com/plugins/tom_pay.html
 *      应用开发者: 点微科技
 *      开发者QQ: 800046233
 *      更新日期: 202602051144
 *      授权域名: 43.143.218.163
 *      授权码: 2026020503VZ4u4wh6Fb
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */


/*
   This is NOT a freeware, use is subject to license terms
   版权所有：点微科技 www.tomwx.cn
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$payConfig['alipay_private_key'] = str_replace("\r\n","",$payConfig['alipay_private_key']);
$payConfig['alipay_private_key'] = str_replace("\n","",$payConfig['alipay_private_key']);
$payConfig['alipay_private_key'] = str_replace("\r","",$payConfig['alipay_private_key']);

$payConfig['alipay_public_key'] = str_replace("\r\n","",$payConfig['alipay_public_key']);
$payConfig['alipay_public_key'] = str_replace("\n","",$payConfig['alipay_public_key']);
$payConfig['alipay_public_key'] = str_replace("\r","",$payConfig['alipay_public_key']);

$alipay_app_id          = trim($payConfig['alipay_app_id']);
$alipay_private_key     = trim($payConfig['alipay_private_key']);
$alipay_public_key      = trim($payConfig['alipay_public_key']);

define("TOM_ALIPAY_APPID", $alipay_app_id);
define("TOM_ALIPAY_PRIVATE_KEY", $alipay_private_key);
define("TOM_ALIPAY_PUBLIC_KEY", $alipay_public_key);
define("TOM_ALIPAY_NOTIFY_URL", $_G['siteurl']."source/plugin/tom_pay/newalipayNotify.php");
define("TOM_ALIPAY_RETURN_URL", $_G['siteurl']."source/plugin/tom_pay/newalipayReturn.php");
define("TOM_ALIPAY_LOG_PATH", '');

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipaywap/config.php';
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipaywap/wappay/service/AlipayTradeService.php';
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipaywap/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipaywap/aop/AopClient.php';
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipaywap/aop/request/AlipayTradeWapPayRequest.php';

$subject = diconv($orderInfo['goods_name'],CHARSET,'utf-8');
$showUrl = $orderInfo['goods_url'];
$pay_price = $orderInfo['pay_price'];

$updateData = array();
$updateData['payment']          = 'alipay_wap';
$updateData['order_time']       = TIMESTAMP;
C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);

header("Content-type: text/html; charset=utf-8");

$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
$payRequestBuilder->setBody($subject);
$payRequestBuilder->setSubject($subject);
$payRequestBuilder->setOutTradeNo($order_no);
$payRequestBuilder->setTotalAmount($pay_price);
$payRequestBuilder->setTimeExpress("1m");

$payResponse = new AlipayTradeService($config);
$result = $payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);

exit;