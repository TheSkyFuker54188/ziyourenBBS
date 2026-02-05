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

$qf_order_id  = isset($_GET['qf_order_id'])? addslashes($_GET['qf_order_id']):'';

$updateData = array();
$updateData['payment']          = 'qianfan_pay';
$updateData['qf_order_id']      = $qf_order_id;
$updateData['order_time']       = TIMESTAMP;
C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);


$outArr = array(
    'status'=> 200,
);
echo json_encode($outArr); exit;