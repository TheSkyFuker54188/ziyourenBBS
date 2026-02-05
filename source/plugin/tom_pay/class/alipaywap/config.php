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


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$config = array (	
		//应用ID,您的APPID。
		'app_id' => TOM_ALIPAY_APPID,

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => TOM_ALIPAY_PRIVATE_KEY,
		
		//异步通知地址
		'notify_url' => TOM_ALIPAY_NOTIFY_URL,
		
		//同步跳转
		'return_url' => TOM_ALIPAY_RETURN_URL,

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => TOM_ALIPAY_PUBLIC_KEY,

        //日志路径
        'log_path' => TOM_ALIPAY_LOG_PATH,
);