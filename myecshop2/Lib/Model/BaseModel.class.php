<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/24
 * Time: 10:00
 */
class BaseModel extends Model
{

    //判断登录状态
    public function is_login(){

        //unset($_SESSION['temp_buyers_id']);
        if(!(isset($_SESSION['temp_buyers_id'])&&$_SESSION['temp_buyers_id']>0)){
            $response = array("success"=>"false","error"=>array("msg"=>'你还没有登录','code'=>4120));
            $response = ch_json_encode($response);
            exit($response);

        }
    }

}