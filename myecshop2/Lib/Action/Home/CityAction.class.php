<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/8
 * Time: 11:33
 */
import('ORG.Net.IpLocation');

class CityAction extends Action
{
    //获取城市列表
    public function show()
    {
        $goodsarea = new  GoodsAreaModel();
        $data = $goodsarea->where('app_is_show=1')->field('goods_area_id,goods_area,goods_table')->select();

        $response = array('success' => 'true', 'data' => $data);
        $response = ch_json_encode($response);
        exit($response);

    }

    //获取定位城市
    public function location()
    {
        $goodsarea = new  GoodsAreaModel();
        $ip = get_client_ip();//获取用户的IP

        //$Ip = new IpLocation(); // 实例化类
        $Ip = new IpLocation('UTFWry.dat'); // 传入IP地址库文件名

        $location = $Ip->getlocation($ip); // 获取某个IP地址所在的位置  218.64.55.216  -》 江西省南昌市  218.79.93.194=>上海市

        $info = $location['country'];//1. 上海市 、北京市  2.江西省南昌市、福建省泉州市  有这两种情况  后面还发现有ip直接对应到 中国 无语咯
        // $city = strstr($info, '市',true);
        if (preg_match('/省/i', $info, $res))//江西省南昌市
        {
            $city = explode('省', $info);
            $city = explode('市', $city[1]);
        } else {
            $city = explode('市', $info);//上海市
        }

        $_SESSION['city'] = $city[0];//保存定位城市，返回用户信息能用的着

        $res = $goodsarea->judge($city[0]);//通过查询城市名 判断该城市是否入库

        $_SESSION['area_id'] = $res['goods_area_id'];//保存定位城市id号

        $response = array('success' => 'true', 'data' => $res);
        $response = ch_json_encode($response);
        exit($response);

    }

    //搜索接口
    public function search()
    {
        $name = isset($_POST['name']) ? $_POST['name'] : 0;

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $pageSize = isset($_POST['pageSize']) ? $_POST['pageSize'] : 10;

        if ($name == '0') {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }
        $city['goods_table'] = $_POST['goods_table'];//接收返回的城市对应的商品表
        is_empty($city);

        $city['goods_table'] = preg_replace('/ecs_/','',$city['goods_table']);//去掉表前缀
        $goods = new GoodsModel("{$city['goods_table']}");
        //$var = $goods -> getTableName();

        $data = $goods->getByNameLike($name,$page,$pageSize);

        $response = array('success' => 'true', 'data' => $data);
        $response = ch_json_encode($response);
        exit($response);

    }

    //城市选择接口
    public function select()
    {

        if (empty($_SESSION['temp_buyers_id']))//未登录的情况下...............
        {

            $arr['goods_area_id'] = isset($_POST['area_id']) ? $_POST['area_id'] : 1;//接收传过来的城市名
            if ($arr['goods_area_id'] == 1) {  //取默认城市上海
                $data['goods_area_id'] = 1;
                $data['city'] = "上海";
                $data['goods_table'] = 'ecs_goods';

                $response = array('success' => 'true', 'data' => $data);
                $response = ch_json_encode($response);
                exit($response);
            } else { //取本地城市
                $goodsarea = M('GoodsArea');
                $res = $goodsarea->where("goods_area_id={$arr['goods_area_id']}")->field('goods_area_id,goods_area,goods_table')->find();

                $data['goods_area_id'] = $res['goods_area_id'];
                $data['city'] = $res['goods_area'];
                $data['goods_table'] = $res['goods_table'];

                $response = array('success' => 'true', 'data' => $data);
                $response = ch_json_encode($response);
                exit($response);
            }
        } //已经登录的情况下
        else {

            //$goods_area_id = $_SESSION['area_id'];
            $goods_area_id = $_POST['area_id'];

            $goodsarea = M('GoodsArea');

            $res = $goodsarea->where("goods_area_id='$goods_area_id'")->field('goods_area_id,goods_area,goods_table')->find();

            $data['goods_area_id'] = $res['goods_area_id'];
            $data['city'] = $res['goods_area'];
            $data['goods_table'] = $res['goods_table'];

            $response = array('success' => 'true', 'data' => $data);
            $response = ch_json_encode($response);
            exit($response);

        }

    }
}