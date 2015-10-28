<?php
/*APP统一下单接口*/
define('ACC',true);
require('../../include/init.php');
ini_set('date.timezone','Asia/Shanghai');
require_once "../lib/WxPay.Api.php";
require_once 'log.php';
//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
//接收订单ID

$order_id = isset($_POST['order_id'])?$_POST['order_id']+0:0;
if(!$order_id){
        $msg = '订单号必须存在';
        $response = array("success"=>"false","error"=>array("msg"=>$msg,'code'=>4800));
        $response = ch_json_encode($response);
        exit($response);
}
$purchase = new PurchaseModel();
$orderinfo = $purchase->find($order_id);
if(!$orderinfo){
	   $msg = '无此订单信息';
        $response = array("success"=>"false","error"=>array("msg"=>$msg,'code'=>4800));
        $response = ch_json_encode($response);
        exit($response);
}
$out_trade_no = $orderinfo['temp_purchase_sn'];
$body = $orderinfo['description'];
$total_fee = $orderinfo['money'];

$input = new WxPayUnifiedOrder();

//设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
$input->SetOut_trade_no($out_trade_no);
//设置商品或支付单简要描述
$input->SetBody($body);
//设置订单总金额，只能为整数，详见支付金额
$input->SetTotal_fee($total_fee);
//设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
$input->SetTrade_type("APP");
//设置接收微信支付异步通知回调地址
$input->SetNotify_url("http://115.182.53.111/ecshop2/AskPriceApi/Wxpay/app/notify.php");

$result = WxPayApi::unifiedOrder($input);
Log::DEBUG("unifiedorder:" . json_encode($result));
if($result){
//返回数据给APP:trade_type,prepay_id

	$response = array('success'=>'true','data'=>$result);
    $response = ch_json_encode($response);
    exit($response);
}
?>