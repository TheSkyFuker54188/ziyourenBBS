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

if(!defined('IN_DISCUZ')){
	exit('Access Denied');
}

$wxpay_appid        = trim($payConfig['wxopen_appid']);
$wxpay_mchid        = trim($payConfig['wxopen_mchid']);
$wxpay_key          = trim($payConfig['wxopen_key']);
$wxpay_appsecret    = trim($payConfig['wxopen_appsecret']);

define("TOM_WXPAY_APPID", $wxpay_appid);
define("TOM_WXPAY_MCHID", $wxpay_mchid);
define("TOM_WXPAY_KEY", $wxpay_key);
define("TOM_WXPAY_APPSECRET", $wxpay_appsecret);

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/wxpay/lib/WxPay.Api.php';

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/xiaoyun.core.php';

$outArr = array(
    'status'=> 1,
);

$orderInfo['goods_name'] = cutstr($orderInfo['goods_name'],10,"..");
$goods_name = diconv($orderInfo['goods_name'],CHARSET,'utf-8');
$notifyUrl = $_G['siteurl']."source/plugin/tom_pay/appbymeNotify.php";
if($payConfig['must_http'] == 1){
    $notifyUrl = str_replace("https:", "http:", $notifyUrl);
}
$pay_price = $orderInfo['pay_price']*100;

$prepay_id = '';

if(!empty($orderInfo['order_time']) && !empty($orderInfo['prepay_id']) && ($orderInfo['order_time']+5400) > TIMESTAMP ){

    $prepay_id = $orderInfo['prepay_id'];

}else{

    $orderInput = new WxPayUnifiedOrder();
    $orderInput->SetBody($goods_name);		
    $orderInput->SetAttach("tom_pay");		
    $orderInput->SetOut_trade_no($order_no);
    $orderInput->SetTotal_fee($pay_price);	
    $orderInput->SetGoods_tag("null");	
    $orderInput->SetNotify_url($notifyUrl);	
    $orderInput->SetTrade_type("APP");
    $returnInfo = WxPayApi::unifiedOrder($orderInput,300);

    if(is_array($returnInfo) && $returnInfo['result_code']=='SUCCESS' && $returnInfo['return_code']=='SUCCESS'){

        $prepay_id = $returnInfo['prepay_id'];

        $updateData = array();
        $updateData['payment']          = 'appbyme_pay';
        $updateData['prepay_id']        = $returnInfo['prepay_id'];
        $updateData['order_time']       = TIMESTAMP;
        C::t('#tom_pay#tom_pay_order')->update($orderInfo['id'],$updateData);

    }else{

        Log::DEBUG("[appbyme]status(500):" . json_encode($returnInfo));

        $outArr = array(
            'status'=> 500,
        );
        echo json_encode($outArr); exit;
    }
}
    

if(!empty($prepay_id)){
    
    $parameters = array();
    $parameters['appid']        = $wxpay_appid;
    $parameters['partnerid']    = $wxpay_mchid;
    $parameters['prepayid']     = $prepay_id;
    $parameters['noncestr']     = xy_getNonceStr(); 
    $timeStamp = time();
    $parameters['timestamp']    = $timeStamp;
    $parameters['package']      = "Sign=WxPay";
    $parameters['sign']         = xy_MakeSign($parameters);

    $outArr = array(
        'status'=> 200,
        'parameters' => $parameters,
    );
    echo json_encode($outArr); exit;
}else{
    $outArr = array(
        'status'=> 301,
    );
    echo json_encode($outArr); exit;
}