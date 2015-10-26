<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/7/29
 * Time: 15:31
 */
function _filter_temp_purchase($data)
{
    //验证送货时间
    if(strlen($data['receive_time'])>600)
    {
        $response = array('success' => 'false', 'error' => array('msg' => '送货时间描述过长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }

    //验证添加地址
    if(strlen($data['address'])>200)
    {
        $response = array('success' => 'false', 'error' => array('msg' => '地址太长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
//    //验证支付方式
//    if(!(($data['method'])=='1' || ($data['method'])=='4' || ($data['method'])=='2' || ($data['method'])=='5' || ($data['method'])=='0'))
//    {
//        $response = array('success' => 'false', 'error' => array('msg' => '不支持这种支付方式！', 'code' => 4108));
//        $response = ch_json_encode($response);
//        exit($response);
//    }
    //验证手机号码
    if(!$res=preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$$|14[0-9]{1}[0-9]{8}$/i",$data['mobile'],$res))
    {
        $response = array('success' => 'false', 'error' => array('msg' => '手机号码不规范', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    if(!strlen($data['description'])>2000)
    {
        $response = array('success' => 'false', 'error' => array('msg' => '备注太长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    if(!strlen($data['name'])>200)
    {
        $response = array('success' => 'false', 'error' => array('msg' => '输入的名字过长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }

}

function _filter_temp_purchase_goods($data)
{
    if (strlen($data['name']) > 200) {
        $response = array('success' => 'false', 'error' => array('msg' => '商品名称太长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }

    //验证品牌
    if (strlen($data['brand_name']) > 50) {
        $response = array('success' => 'false', 'error' => array('msg' => '品牌名字不能过长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    if (strlen($data['version']) > 300) {
        $response = array('success' => 'false', 'error' => array('msg' => '型号不能过长', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    //精确到小数点后两位
    $temp = explode('.', $data['amount']);
    if (strlen($temp[1]) > 2) {
        $response = array('success' => 'false', 'error' => array('msg' => '数量只能精确到小数点后两位', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    //数字的长度判定
    if (strlen(($data['amount'])) > 12) {
        $response = array('success' => 'false', 'error' => array('msg' => '数量过大', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    //数量的单位
    if (!($data['unit'] == '个' || $data['unit'] == '箱' || $data['unit'] == '桶' || $data['unit'] == '只' || $data['unit'] == '根' || $data['unit'] == '卷' || $data['unit'] == '台' || $data['unit'] == '件' || $data['unit'] == '米' || $data['unit'] == '套' || $data['unit'] == '片' || $data['unit'] == '㎡')) {
        $response = array('success' => 'false', 'error' => array('msg' => '数量单位不合规范', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    //price精确到小数点后两位
    $temp1 = explode('.', $data['price']);
    if (strlen($temp1[1]) > 2) {
        $response = array('success' => 'false', 'error' => array('msg' => '价格只能精确到小数点后两位', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
    //price数字的长度判定
    if (strlen(($data['price'])) > 17) {
        $response = array('success' => 'false', 'error' => array('msg' => '价格数值超过限制', 'code' => 4108));
        $response = ch_json_encode($response);
        exit($response);
    }
}

function is_empty($arr)  //一维数组
{
    foreach($arr as $k=>$v)
    {
        if($v=='')
        {
            $response = array("success"=>"false","error"=>array("msg"=>$k.'不能为空','code'=>4122));
            $response = ch_json_encode($response);
            exit($response);
        }
    }
}
