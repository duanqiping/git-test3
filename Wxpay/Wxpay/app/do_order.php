<?php
/*APP统一下单接口*/
define('ACC',true);
require('../../include/init.php');
ini_set('date.timezone','Asia/Shanghai');
require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';
//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
class AppNotifyCallBack extends WxPayNotify
{
    public function 
    public function unifiedorder()
    {
        //统一下单

        $input = new WxPayUnifiedOrder($out_trade_no, $body,$total_fee);

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
        return $result;
    }
    
    public function NotifyProcess($data, &$msg)
    {
        //echo "处理回调";
        Log::DEBUG("call back:" . json_encode($data));
        
        if(!array_key_exists("order_id", $data) )
        {
            $msg = "回调数据异常";
            return false;
        }
         
        $order_id = isset($data['order_id'])?$data['order_id']+0:0;
        $purchase = new PurchaseModel();
        $orderinfo = $purchase->find($order_id);
        if(!$orderinfo){
               $msg = '无此订单信息';
               return false;
        }
        $out_trade_no = $orderinfo['temp_purchase_sn'];
        $body = $orderinfo['description'];
        $total_fee = $orderinfo['money'];
        
        //统一下单
        $result = $this->unifiedorder($out_trade_no, $body,$total_fee);
        if(!array_key_exists("trade_type", $result) ||
             !array_key_exists("code_url", $result) ||
             !array_key_exists("prepay_id", $result))
        {
            $msg = "统一下单失败";
            return false;
         }
        
        $this->SetData("trade_type", $result["trade_type"]);
        $this->SetData("code_url", $result["code_url"]);
        $this->SetData("prepay_id", $result["prepay_id"]);
        $this->SetData("result_code", "SUCCESS");
        $this->SetData("err_code_des", "OK");
        return true;
    }
}

Log::DEBUG("begin notify!");
$order_id = isset($_POST['order_id'])?$_POST['order_id']+0:0;
$notify = new AppNotifyCallBack();
$notify->Handle(true);

?>