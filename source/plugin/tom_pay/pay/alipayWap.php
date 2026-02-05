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

$alipay_pid    = trim($payConfig['alipay_pid']);
$alipay_key    = trim($payConfig['alipay_key']);

define("TOM_ALIPAY_PID", $alipay_pid);
define("TOM_ALIPAY_KEY", $alipay_key);

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipay/wap/alipay.config.php';
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/alipay/wap/lib/alipay_submit.class.php';


$out_trade_no = $orderInfo['order_no']."_alipay";
$subject = diconv($orderInfo['goods_name'],CHARSET,'utf-8');
$showUrl = $orderInfo['goods_url'];
$notifyUrl = $_G['siteurl']."source/plugin/tom_pay/alipayNotify.php";
$returnUrl = $_G['siteurl']."source/plugin/tom_pay/alipayReturn.php";
$pay_price = $orderInfo['pay_price'];

$updateData = array();
$updateData['payment']          = 'alipay_wap';
$updateData['order_time']       = TIMESTAMP;
C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);

$parameter = array(
    "service"           => $alipay_config['service'],
    "partner"           => $alipay_config['partner'],
    "seller_id"         => $alipay_config['seller_id'],
    "payment_type"      => $alipay_config['payment_type'],
    "notify_url"        => $notifyUrl,
    "return_url"        => $returnUrl,
    "_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
    "out_trade_no"      => $order_no,
    "subject"           => $subject,
    "total_fee"         => $pay_price,
    "show_url"          => $showUrl,
    "app_pay"           => "Y",
    "body"              => $subject,
);

$alipaySubmit = new AlipaySubmit($alipay_config);
$urlStr = $alipaySubmit->buildRequestParaToString($parameter);

$payurl = 'https://mapi.alipay.com/gateway.do?'.$urlStr;

$outArr = array(
    'status'=> 200,
    'payurl' => $payurl,
);
echo json_encode($outArr); exit;