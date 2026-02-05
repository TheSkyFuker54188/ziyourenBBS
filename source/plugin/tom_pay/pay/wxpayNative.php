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

$wxpay_appid        = trim($payConfig['wxpay_appid']);
$wxpay_mchid        = trim($payConfig['wxpay_mchid']);
$wxpay_key          = trim($payConfig['wxpay_key']);
$wxpay_appsecret    = trim($payConfig['wxpay_appsecret']);

define("TOM_WXPAY_APPID", $wxpay_appid);
define("TOM_WXPAY_MCHID", $wxpay_mchid);
define("TOM_WXPAY_KEY", $wxpay_key);
define("TOM_WXPAY_APPSECRET", $wxpay_appsecret);

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/wxpay/lib/WxPay.Api.php';

$outArr = array(
    'status'=> 1,
);

$orderInfo['goods_name'] = cutstr($orderInfo['goods_name'],10,"..");
$goods_name = diconv($orderInfo['goods_name'],CHARSET,'utf-8');
$notifyUrl = $_G['siteurl']."source/plugin/tom_pay/wxpayNotify.php";
$pay_price = $orderInfo['pay_price']*100;

$code_url = '';

if(!empty($orderInfo['order_time']) && !empty($orderInfo['code_url']) && ($orderInfo['order_time']+5400) > TIMESTAMP){

    $code_url = $orderInfo['code_url'];

}else{

    $orderInput = new WxPayUnifiedOrder();
    $orderInput->SetBody($goods_name);		
    $orderInput->SetAttach("tom_pay");		
    $orderInput->SetOut_trade_no($order_no);	
    $orderInput->SetTotal_fee($pay_price);	
    $orderInput->SetGoods_tag("null");	
    $orderInput->SetNotify_url($notifyUrl);	
    $orderInput->SetTrade_type("NATIVE");
    $orderInput->SetProduct_id($orderInfo['goods_id']);
    $returnInfo = WxPayApi::unifiedOrder($orderInput,300);

    if(is_array($returnInfo) && $returnInfo['result_code']=='SUCCESS' && $returnInfo['return_code']=='SUCCESS'){

        $code_url = $returnInfo['code_url'];

        $updateData = array();
        $updateData['payment']          = 'wxpay_native';
        $updateData['code_url']         = $returnInfo['code_url'];
        $updateData['order_time']       = TIMESTAMP;
        C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);

    }else{

        Log::DEBUG("[native]status(500):" . json_encode($returnInfo));

        if($returnInfo['return_code']=='FAIL'){
            $outArr = array(
                'status'=> 505,
                'error_msg' => '[505] '.$returnInfo['return_msg']
            );
            echo json_encode($outArr); exit;
        }else if($returnInfo['result_code']=='FAIL'){
            $outArr = array(
                'status'=> 505,
                'error_msg' => '[505] '.'['.$returnInfo['err_code'].'] '.$returnInfo['err_code_des']
            );
            echo json_encode($outArr); exit;
        }else{
            $outArr = array(
                'status'=> 500,
            );
            echo json_encode($outArr); exit;
        }
        
    }
}
    

if(!empty($code_url)){
    
    $qrcodeImg = $_G['siteurl']."plugin.php?id=tom_qrcode&data=".urlencode($code_url);

    $outArr = array(
        'status'=> 200,
        'src' => $qrcodeImg,
    );
    echo json_encode($outArr); exit;
}else{
    $outArr = array(
        'status'=> 301,
    );
    echo json_encode($outArr); exit;
}