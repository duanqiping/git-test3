<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/22
 * Time: 15:29
 */
class TempBuyersModel extends BaseModel
{
    protected $fields = array('temp_buyers_id','is_check','temp_buyers_mobile','temp_buyers_password','add_time','nick','is_regist','photo','info','client','role','lastlogin','vip','_pk'=>'temp_buyers_id','_autoinc'=>'true');

    public function getById()
    {
        $condition['temp_buyers_id']=$_SESSION['temp_buyers_id'];
        $res = $this -> where($condition) -> field('temp_buyers_id,temp_buyers_mobile,nick') -> select();
        $res=$res[0];
//        print_r($res);
//        exit();
        return $res;
    }

    public function getId($moblie)
    {
        $res = $this->where("temp_buyers_mobile=$moblie") -> field('temp_buyers_id') -> select();
        print_r($res);
    }
}