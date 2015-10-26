<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/10
 * Time: 10:01
 */
class BonusAction extends Action
{
    //余额接口.......................
    public function recharge()
    {
        $tempaccount = new TempAccountModel();
        $tempaccount->is_login();
        $res = $tempaccount->where("temp_buyers_id={$_SESSION['temp_buyers_id']}")->field('temp_account_id,temp_buyers_id,total')->select();
        if(!$res)
        {
            //创建一个账户
            $data['temp_buyers_id'] = $_SESSION['temp_buyers_id'];

            $id = $tempaccount->addOne($data);
            $res2 = $tempaccount->where("temp_account_id=$id")->field('temp_account_id,temp_buyers_id,total')->select();
            $response = array("success"=>"true","data"=>$res2[0]);
            $response = ch_json_encode($response);
            exit($response);
        }
        //返回余额
        $data=$res[0];
        $data['switch']='true';
        $response = array("success"=>"true","data"=>$data);
        $response = ch_json_encode($response);
        exit($response);
    }

    //红包列表接口
    public function show()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;

        $tempcashorder = new TempCashModel();
        $tempcashorder->is_login();

        $data = $tempcashorder->getUserId($_SESSION['temp_buyers_id'],$limit,$page);


        $data2['num'] = $tempcashorder->num;
        $data2['total_money'] =$tempcashorder->total_money;
        $data2['list'] = $data;

        $response = array("success"=>"true","data"=>$data2);
        $response = ch_json_encode($response);
        exit($response);
    }

}