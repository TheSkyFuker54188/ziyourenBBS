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

$souhu_pv_ip  = isset($_GET['souhu_pv_ip'])? addslashes($_GET['souhu_pv_ip']):'';

$clientip = $_G['clientip'];
if(!empty($souhu_pv_ip) && $payConfig['open_js_getip'] == 1){
    $clientip = $souhu_pv_ip;
}

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/wxpay/lib/WxPay.Api.php';

$outArr = array(
    'status'=> 1,
);

$orderInfo['goods_name'] = cutstr($orderInfo['goods_name'],10,"..");
$goods_name = diconv($orderInfo['goods_name'],CHARSET,'utf-8');
$notifyUrl = $_G['siteurl']."source/plugin/tom_pay/wxpayNotify.php";
if($payConfig['must_http'] == 1){
    $notifyUrl = str_replace("https:", "http:", $notifyUrl);
}
$pay_price = $orderInfo['pay_price']*100;
$scene_info = '{"h5_info": {"type":"Wap","wap_url": "'.$orderInfo['goods_url'].'","wap_name": "'.$goods_name.'"}}';

$mweb_url = '';

if(!empty($orderInfo['order_time']) && !empty($orderInfo['mweb_url']) && ($orderInfo['order_time']+0) > TIMESTAMP){
    
    $mweb_url = $orderInfo['mweb_url'];

}else{

    $orderInput = new WxPayUnifiedOrder();
    $orderInput->SetBody($goods_name);		
    $orderInput->SetAttach("tom_pay");		
    $orderInput->SetOut_trade_no($order_no);	
    $orderInput->SetTotal_fee($pay_price);
    $orderInput->SetSpbill_create_ip($clientip);
    $orderInput->SetNotify_url($notifyUrl);	
    $orderInput->SetTrade_type("MWEB");
    $orderInput->SetScene_info($scene_info);
    $returnInfo = WxPayApi::unifiedOrder($orderInput,300);
    
    if(is_array($returnInfo) && $returnInfo['result_code']=='SUCCESS' && $returnInfo['return_code']=='SUCCESS'){

        $mweb_url = $returnInfo['mweb_url'];

        $updateData = array();
        $updateData['payment']          = 'wxpay_h5';
        $updateData['mweb_url']         = $returnInfo['mweb_url'];
        $updateData['order_time']       = TIMESTAMP;
        C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);

    }else{

        Log::DEBUG("[h5]status(500):" . json_encode($returnInfo));

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
    

if(!empty($mweb_url)){

    $outArr = array(
        'status'=> 200,
        'mweburl' => $mweb_url,
    );
    echo json_encode($outArr); exit;
}else{
    $outArr = array(
        'status'=> 301,
    );
    echo json_encode($outArr); exit;
}