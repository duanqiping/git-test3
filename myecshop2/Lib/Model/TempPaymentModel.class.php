<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/27
 * Time: 10:43
 */
class TempPaymentModel extends Model
{
    protected $fields = array('temp_payment_id','temp_purchase_sn','time','from_user','to_user','from_account','to_account','method','type','admin_id','user_id','money','pay_from','client_from','edit_time','_pk'=>'temp_payment_id','autoinc'=>true);
    //货到付款时插入一条记录
    public function payToInsert($data)
    {
        if(!$res = $this -> data($data) -> add())
        {
            $response = array('success' => 'false', 'error' => array('msg' => '货到付款入库失败！', 'code' => 4904));
            $response = ch_json_encode($response);
            exit($response);
        }
        return true;
    }
}