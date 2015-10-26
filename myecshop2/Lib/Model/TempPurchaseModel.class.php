<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/23
 * Time: 18:27
 */
class TempPurchaseModel extends BaseModel
{
    protected $fields = array('temp_purchase_id','temp_purchase_sn','temp_inquiry_id','buyers_id','suppliers_id','suppliers_name','suppliers_alipay','time','money','name','mobile','address','state','description','receive_time','finish_time','method','transportation','temp_buyers_address_id','bank_id','bank_name','purchase_title','quotation_id','is_read','send_time','picture','request_id','delivery_time','suppliers_remarks','actually_money','_pk'=>'temp_purchase_id','autoinc'=>true);

    //通过buyers_id 获取全部订单信息  或者 state 成功的订单....
    public function getByBuyersIdAndState($state="")
    {
        $condition['buyers_id'] = $_SESSION['temp_buyers_id'];
        if(!empty($state))
        {
            $condition['state'] = $state;//实际上 state=4
        }
        $res = $this -> where($condition) -> field('temp_purchase_id,temp_purchase_sn,buyers_id,time,money,name,address,state,description,receive_time,finish_time,method') -> select();
        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }

        $res1 = array();
        for ($i=0; $i<count($res); $i++)
        {
            $temppurchasegoods = new TempPurchaseGoodsModel();
            $res1[$i] = $temppurchasegoods -> getByTempGoodsId($res[$i]['temp_purchase_id']);

            $res[$i]['goods'] = $res1[$i];
        }
//        print_r($res);
//        exit();
        return $res;
    }

    //通过订单ID更改method和state
    public function updateByID($temp_purchase_id)
    {
        $data['method'] = 1;
        $data['state'] =2;
        $condition['temp_purchase_id'] = $temp_purchase_id;

        $res = $this -> where($condition) ->data($data) -> save();
        if(!$res)
        {
            $response = array('success' => 'false', 'error' => array('msg' => '订单状态更新失败！', 'code' => 4904));
            $response = ch_json_encode($response);
            exit($response);
        }
        //往temp_payment表和temp_account表中插入数据
        $arr = array();
        $res1 = $this -> where($condition) -> field('temp_purchase_sn,buyers_id,time,money,name,mobile') -> select();

        $arr['temp_purchase_sn'] = $res1[0]['temp_purchase_sn'];
        $arr['user_id'] = $res1[0]['buyers_id'];
        $arr['time'] = $res1[0]['time'];
        $arr['money'] = $res1[0]['money'];
        $arr['from_user'] = $res1[0]['mobile'];

        $arr['to_user'] = '品材网支付';
        $arr['to_account'] = 'hbz@pcw268.com';
        $arr['method'] = -1;
        $arr['type'] = 5;
        $arr['client_from'] = 1;

        $temppayment = new TempPaymentModel();
        $temppayment -> payToInsert($arr);//往temp_payment表中插入一条记录


        return true;
    }

    //通过订单号获取订单详情
    public function getById($temp_purchase_id)
    {
        $condition['temp_purchase_id'] = $temp_purchase_id;
        $res = $this -> where($condition) -> field('temp_purchase_id,temp_purchase_sn,buyers_id,time,money,name,address,mobile,state,method,description,receive_time,finish_time,method,transportation,account_money') -> select();

        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }
        $res1=$res[0];

        $tempbuyers = new TempBuyersModel();
        $data1 = $tempbuyers -> getById();

        $res1['buyersinfo']['temp_buyers_id'] = $res1['buyers_id'];
        unset($res1['buyers_id']);
        $res1['buyersinfo']['nick'] = $data1['nick'];

        $res1['addressinfo']['name']=$res1['name'];
        $res1['addressinfo']['address']=$res1['address'];
        $res1['addressinfo']['mobile']=$res1['mobile'];
        unset($res1['name']);
        unset($res1['address']);

        return $res1;
    }

    public function pay($id)
    {
        $arr = array('temp_purchase_sn','suppliers_name','money','mobile','address','method');
        $data = $this->where("temp_purchase_id=$id")->field($arr)->find();
        return $data;
    }

    //通过订单ID获取订单详情........
    public function getByTempId($temp_purchase_id)
    {
        $condition['temp_purchase_id'] = $temp_purchase_id;

        $res = $this -> where($condition) -> field('temp_purchase_id,temp_purchase_sn,name,time,money,mobile,transportation,method,address,state,receive_time,description,account_money') -> select();
        $res1 = $res[0];
        $res1['addressinfo']['name']=$res1['name'];
        $res1['addressinfo']['address']=$res1['address'];
        $res1['addressinfo']['mobile']=$res1['mobile'];
        unset($res1['name']);
        unset($res1['address']);
        unset($res1['mobile']);

        return $res1;
    }


    public function add22($data)
    {
        //启动事务
        $this -> startTrans();

        $res = $this -> data($data) -> add();//插入成功时返回的就是 自增的id号

        if(!$res)
        {
            $response = array('success' => 'false', 'error' => array('msg' => '订单入库失败！', 'code' => 4904));
            $response = ch_json_encode($response);
            exit($response);
        }
        else
        {
//            $sql="select last_insert_id() as temp_purchase_id";//获取刚插入数据的自增ID号
//            $res = $this->query($sql);//$res是个二维数组..
//            $_SESSION['temp_purchase_id'] = $res[0]['temp_purchase_id'];// 插入ecs_temp_purchase_goods要用
//            return true;
            return $res;
        }

    }

    //生成订单号
    public function orderSn() {

        $sn = date('ymdHis').str_pad($_SESSION['temp_buyers_id'],6,"0",STR_PAD_LEFT).substr(microtime(),2,4);
        return $sn;
    }
    public function getTotalMonery()
    {
        $id = $_SESSION['temp_purchase_id'];
        $res = $this ->where("id=$id")->setField('money');
        echo $res;
        exit();
    }
}