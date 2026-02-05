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

$order_no  = isset($_GET['order_no'])? addslashes($_GET['order_no']):'';

$orderInfo = C::t('#tom_pay#tom_pay_order')->fetch_by_order_no($order_no);

if($_isWeiXin == 1){
    $payConfig['open_alipay'] = 0;
}

$show_wxpay_jsapi = $show_wxpay_native = $show_wxpay_h5 = 0;
if($__Ios == 1 || $__Android == 1){
    if($_isWeiXin == 1){
        $show_wxpay_jsapi = 1;
    }else{
        if($payConfig['open_h5'] == 1){
            $show_wxpay_h5 = 1;
        }
    }
}else{
    $show_wxpay_native = 1;
}

if($__UserInfo && $__UserInfo['id'] > 0 && $orderInfo['user_id'] == 0){
    $updateData = array();
    $updateData['user_id'] = $__UserInfo['id'];
    C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);
}

$payUrl = "plugin.php?id=tom_pay:pay";

$syTime = 300*1000;

$isGbk = false;
if (CHARSET == 'gbk') $isGbk = true;
include template("tom_pay:wap");