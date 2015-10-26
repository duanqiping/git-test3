<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/14
 * Time: 15:26
 */
class TempAccountModel extends BaseModel
{
    //创建一个用户账号
    public function addOne($data)
    {
        $id = $this->data($data)->add();

        if(!$id)
        {
            $response = array("success"=>"false","error"=>array("msg"=>'创建账号失败','code'=>4907));
            $response = ch_json_encode($response);
            exit($response);
        }
        return $id;
    }
}