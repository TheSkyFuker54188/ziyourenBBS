<?php

/* *
 * This is NOT a freeware, use is subject to license terms
 * 版权所有：点微科技 www.tomwx.cn
 * 功能：支付宝服务器异步通知页面
 */

define('IN_API', true);
define('CURSCRIPT', 'api');
define('DISABLEXSSCHECK', true); 
define('IN_TOM_PAY', true); 

require '../../../source/class/class_core.php';

$discuz = C::app();
$cachelist = array('plugin', 'diytemplatename');
$discuz->cachelist = $cachelist;
$discuz->init();

$_G['siteurl'] = substr($_G['siteurl'], 0, -22);
$_G['siteroot'] = substr( $_G['siteroot'], 0, - 22);

$payConfig = $_G['cache']['plugin']['tom_pay'];

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
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/log.class.php';
include DISCUZ_ROOT.'./source/plugin/tom_pay/class/function.core.php';

$logDir = DISCUZ_ROOT."./source/plugin/tom_pay/logs/";
if(!is_dir($logDir)){
    mkdir($logDir, 0777,true);
}else{
    chmod($logDir, 0777); 
}
$logHandler= new CLogFileHandler(DISCUZ_ROOT."./source/plugin/tom_pay/logs/[".date("Y-m-d")."]alipay.log");
$log = Log::Init($logHandler, 15);

$arr = $_POST;
$alipaySevice = new AlipayTradeService($config); 
$result = $alipaySevice->check($arr);

Log::DEBUG("[notify]start:" . json_encode(iconv_to_utf8($_GET['out_trade_no'])));

if($result) {

	$out_trade_no   = $_GET['out_trade_no'];
	$trade_no       = $_GET['trade_no'];
	$trade_status   = $_GET['trade_status'];

    if($_GET['trade_status'] == 'TRADE_FINISHED') {
        
        Log::DEBUG("[notify]trade_status:TRADE_FINISHED");
        
    }else if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
        
        Log::DEBUG("[notify]trade_status:TRADE_SUCCESS");
        
        $payOrderInfo = C::t('#tom_pay#tom_pay_order')->fetch_by_order_no($out_trade_no);
        
        if($payOrderInfo){
            if($_GET['total_amount'] != $payOrderInfo['pay_price']){
                Log::DEBUG("[notify]total_amount error:".$_GET['total_amount']." no equal ".$payOrderInfo['pay_price']);
            }else{
                
                
                if($payOrderInfo['order_status'] == 1){
                    $updateData = array();
                    $updateData['order_status'] = 2;
                    $updateData['pay_time'] = TIMESTAMP;
                    C::t('#tom_pay#tom_pay_order')->update($payOrderInfo['id'],$updateData);

                    Log::DEBUG("[notify]update order flow:" . json_encode(iconv_to_utf8($payOrderInfo['order_no'])));
                }
                
                # plugin api start
                $order_no = $payOrderInfo['order_no'];
                if(checkDirNameChar($payOrderInfo['plugin_id'])){ // 正则验证文件名合法性  preg_match("#^[a-zA-Z0-9_]+$#", $str)
                    $payNotufyFile = DISCUZ_ROOT.'./source/plugin/'.$payOrderInfo['plugin_id'].'/paynotify.php';
                    if(file_exists($payNotufyFile)){
                        include $payNotufyFile;
                    }else{
                        Log::DEBUG("[error]no find:".$payNotufyFile);
                    }
                }else{
                    Log::DEBUG("[error]checkDirNameChar:".$payOrderInfo['plugin_id']);
                }
                # plugin api end
                
            }
        }else{
            Log::DEBUG("[notify]no find order flow:".$out_trade_no);
        }
        
    }else{
        Log::DEBUG("[notify]trade_status:".$_GET['trade_status']);
    }
        
	echo "success";
    
    Log::DEBUG("[notify]success:" . json_encode(iconv_to_utf8($_GET['out_trade_no'])));
    
}else {
    
    echo "fail";

    Log::DEBUG("[notify]fail:" . json_encode(iconv_to_utf8($_GET)));
}