<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/18
 * Time: 13:04
 */
class TempCashModel extends BaseModel
{
    protected static $total_money=0;
    protected static $num=0;
    //获取红包列表
    public function getUserId($user_id,$limit,$page)
    {
        $offset = ($page-1)*$limit;

        $condition['user_id'] = $user_id;
        $condition['state'] = 0;

        $res = $this->where($condition)->field('cash_id,cash_bonus_id,cash_sn,cash_time,cash_money')->limit($offset,$limit)->order('cash_time desc')->select();
        $this->total_money =   $this->where($condition)->sum('cash_money');//计算总价
        $this->num = $this->where($condition)->count();


        if(!$res)
        {
            $arr['num'] = 0;
            $arr['total_money'] =0;
            $arr['list'] = array();
            $response = array("success"=>"true","data"=>$arr);
            $response = ch_json_encode($response);
            exit($response);
        }
        $res2 = array();
        for($i=0; $i<count($res); $i++)
        {
            $res2[$i]['id'] = $res[$i]['cash_id'];
            $res2[$i]['sn'] = $res[$i]['cash_sn'];
            $res2[$i]['time'] = $res[$i]['cash_time'];
            $bonus = M('TempBonus');
            $bonus_name[$i] = $bonus->where("bonus_id={$res[$i]['cash_bonus_id']}")->getField('bonus_name');//获取红包名字
            unset($res[$i]['cash_bonus_id']);

            $res2[$i]['name'] = $bonus_name[$i];
            $res2[$i]['money'] = $res[$i]['cash_money'];
        }

        return $res2;
    }
}