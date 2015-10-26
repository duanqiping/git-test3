<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/22
 * Time: 15:12
 */

class IndexAction extends Action
{
    //默认首页
    public function index()
    {
        echo '首页入口，待定……';
        exit();
    }

    //商品详情
    public function goodsdetail()
    {

        $goods_table = $_POST['goods_table'];//接收返回的城市对应的商品表
        $goods_table = preg_replace('/ecs_/','',$goods_table);//去掉表前缀 城市对应的category表

        $goods = new GoodsModel($goods_table);
        $goods ->is_login();

        if(isset($_POST['goods_id']))
        {
            //获取商品信息
            $data = $goods -> getByVersionId($_POST['goods_id']);

            $response = array('success' => 'true', 'data' => $data);
            $response = ch_json_encode($response);
            exit($response);

        }
        else
        {
            $response = array('success' => 'false', 'error' => array('msg' => '你没输入goods_id!', 'code' => 4801));
            $response = ch_json_encode($response);
            exit($response);
        }
    }

}