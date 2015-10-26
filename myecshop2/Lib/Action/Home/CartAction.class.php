<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/5
 * Time: 13:54
 */
class CartAction extends Action
{
    //加入购物车..
    public function add()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();

        $arr = array();
        $arr['amount'] = $_POST['amount'];//商品数量
        $arr['goods_id'] = $_POST['goods_id'];//商品ID号

        $arr['goods_table'] = $_POST['goods_table'];
        $arr['area_id']=$_POST['area_id'];

        $goods_table = preg_replace('/ecs_/','',$_POST['goods_table']);//去掉表前缀 城市对应的category表

        unset($arr['goods_table']);

        is_empty($arr);//检查接收的变量是否为空

        $goods = new GoodsModel($goods_table);
        $res = $goods->getSuppliersIdByGoodsId($arr['goods_id']);
        $arr['suppliers_id'] = $res[0]['suppliers_id'];//供应商的id号

        $arr['buyers_id'] = $_SESSION['temp_buyers_id'];

        $shopcar -> goodsIfExist($arr);//判断是否已加入购物车，已加入则直接改变数量，否则把商品加入购物车

        $response = array('success' => 'true', 'message' => '加入购物车成功');
        $response = ch_json_encode($response);
        exit($response);
    }

    //删除购物车
    public function delete()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();

        $arr=array();
        $arr['area_id']=$_POST['area_id'];
        $arr['goods_id'] = $_POST['goods_id'];
        is_empty($arr);

        $arr['buyers_id'] = $_SESSION['temp_buyers_id'];

        $shopcar -> goodsDelById($arr);

        $response = array('success' => 'true', 'message' => '删除购物车商品成功');
        $response = ch_json_encode($response);
        exit($response);

    }

    //修改购物车
    public function update()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();

        $arr = array();
        $arr['amount'] = $_POST['amount'];//商品数量
        $arr['goods_id'] = $_POST['goods_id'];//商品ID号
        $arr['area_id']=$_POST['area_id'];

        is_empty($arr);

        $arr['buyers_id'] = $_SESSION['temp_buyers_id'];

        $shopcar -> amountUpdate($arr);

        $response = array('success' => 'true', 'message' => '修改购物车成功');
        $response = ch_json_encode($response);
        exit($response);
    }

    //收藏
    public function collect()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();

        $arr=array();
        $arr['act']=$_POST['act'];
        $arr['goods_id']=$_POST['goods_id'];
        $arr['goods_table'] = $_POST['goods_table'];
        $arr['area_id'] = $_POST['area_id'];

        $goods_table = preg_replace('/ecs_/','',$_POST['goods_table']);//去掉表前缀 城市对应的category表

        is_empty($arr);//判断接收的参数是否为空

        if($arr['act']=='del')//取消收藏
        {
            $shopcar = new ShopcarModel();
            $shopcar -> delone($arr);

            $response = array('success' => 'true', 'message' => '取消收藏成功！');
            $response = ch_json_encode($response);
            exit($response);

        }
        elseif($arr['act']=='add')//添加收藏
        {

            $goods = new GoodsModel($goods_table);
            $res = $goods->getSuppliersIdByGoodsId($arr['goods_id']);

            $arr2=array();
            $arr2['suppliers_id'] = $res[0]['suppliers_id'];//供应商的id号
            $arr2['goods_id']=$arr['goods_id'];
            $arr2['buyers_id'] = $_SESSION['temp_buyers_id'];
            $arr2['area_id'] = $arr['area_id'];//所在城市区域id号

            $arr2['car_type'] = 1;//收藏时car_type=1  加入购物车是car_type=0

            //判断是否已经加入收藏
            $shopcar = new ShopcarModel();

            $shopcar -> addone($arr2,$car_type=1);

            $response = array('success' => 'true', 'message' => '收藏成功！');
            $response = ch_json_encode($response);
            exit($response);
        }
        else
        {
            $response = array("success"=>"false","error"=>array("msg"=>'提交动作act的值只能为del或者add','code'=>4120));
            $response = ch_json_encode($response);
            exit($response);
        }
    }

    //获取收藏夹列表
    public function show()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();

        $limit = isset($_POST['pageSize']) ? intval($_POST['pageSize']) : 10;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

        $goods_table = $_POST['goods_table'];
        $area_id = $_POST['area_id'];

        $goods_table = preg_replace('/ecs_/','',$goods_table);//去掉表前缀 城市对应的category表


        $buyers_id = $_SESSION['temp_buyers_id'];
        $data = $shopcar -> getCollectByBuyersId($buyers_id,$area_id,$limit,$page,$goods_table);

        $response = array('success' => 'true', 'data' => $data);
        $response = ch_json_encode($response);
        exit($response);

    }

    //清空收藏夹
    public function clean()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();

        $goods_table = $_POST['goods_table'];
        $goodsarea = new GoodsAreaModel();
        $res = $goodsarea->getIdByGoodsTable($goods_table);
        $area_id=$res[0]['goods_area_id'];

        $shopcar -> cleanByUserId($area_id);

        $response = array('success' => 'true', 'message' => '成功清空收藏夹！');
        $response = ch_json_encode($response);
        exit($response);

    }

    //获取购物车列表
    public function cart()
    {
        $shopcar = new ShopcarModel();
        $shopcar->is_login();


        $arr = array();
        $arr['goods_table'] = $_POST['goods_table'];//接收返回的城市对应的商品表
        $arr['area_id'] = $_POST['area_id'];
        is_empty($arr);

        $arr['goods_table'] = preg_replace('/ecs_/','',$arr['goods_table']);//去掉表前缀  有的又不需要

        $buyers_id = $_SESSION['temp_buyers_id'];
        $data = $shopcar->getByBuyersId($buyers_id,$arr);

        $response = array('success' => 'true', 'data' => $data);
        $response = ch_json_encode($response);
        exit($response);

    }
}