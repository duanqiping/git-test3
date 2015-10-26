<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/3
 * Time: 9:37
 */
class MaterialAction extends Action
{
    //辅材(type=1)  和 二级栏目....
    public function sub()
    {
        $city['goods_table'] = $_POST['goods_table'];//接收返回的城市对应的商品表
        is_empty($city);
        $goodsarea = new GoodsAreaModel();
        $res = $goodsarea->getGoodsCategoryTable($city['goods_table']);

        $brand_table = $res['brand_table'];
        $category_name_table = $res['goods_category_table'];

        $category_name_table = preg_replace('/ecs_/','',$category_name_table);//去掉表前缀 城市对应的category表
        $brand_table = preg_replace('/ecs_/','',$brand_table);//去掉表前缀  城市对应的brand表

        if (isset($_POST['type'])) {
            if ( $_POST['type'] == 1) {

                $goodscategory = new GoodsCategoryModel($category_name_table);
                $data = $goodscategory -> getFirstName();  //一级栏目..

                $data1 = array();
                $len = count($data);
                for($i=0; $i<$len; $i++)
                {
                    $data[$i]['cat_children'] = array();

                    $id = $data[$i]['cat_id'];  //goods_category_id
                    $data1[$i] = $goodscategory -> getSecondName($id,$brand_table);  //二级栏目

                    $data[$i]['cat_children'] = $data1[$i];
                }

                $response = array('success' => 'true', 'data' => $data);
                $response = ch_json_encode($response);
                exit($response);

            } else if($_POST['type'] == 2){
                echo '主材，待定……';
                exit();
            } else
            {
                $response = array('success' => 'false', 'error' => array('msg' => 'type的值只能为1或2 !', 'code' => 4800));
                $response = ch_json_encode($response);
                exit($response);
            }
        } else {
            $response = array('success' => 'false', 'error' => array('msg' => 'type不能为空!', 'code' => 4801));
            $response = ch_json_encode($response);
            exit($response);
        }
    }

    //商品列表..
    public function brandlist2()
    {
        $limit = isset($_POST['pageSize']) ? intval($_POST['pageSize']) : 10;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        //接收goods_category_id  或者  接收brand_id


        $city['goods_table'] = $_POST['goods_table'];//接收返回的城市对应的商品表
        is_empty($city);

        $goodsarea = new GoodsAreaModel();

        $res = $goodsarea->getBrandName($city['goods_table']);

        $brand_table['brand_table'] = $res['brand_table'];
        $category_table['goods_category'] = $res['goods_category_table'];

        $city['goods_table'] = preg_replace('/ecs_/','',$city['goods_table']);//去掉表前缀  有的又不需要
        $city['brand_table'] = preg_replace('/ecs_/','',$brand_table['brand_table']);//去掉表前缀  有的又不需要
        $city['goods_category_table'] = preg_replace('/ecs_/','',$category_table['goods_category']);//去掉表前缀  有的又不需要

        if(isset($_POST['goods_category_id']))
        {
            $goods_category_id = $_POST['goods_category_id'];

            $tempbrand = new TempBrandModel($city['brand_table']);
            $data = $tempbrand -> getBrandByCatId($goods_category_id, $limit, $page, $city['goods_table'],$city['goods_category_table'] );

            $response = array('success' => 'true', 'data' => $data);
            $response = ch_json_encode($response);
            exit($response);
        }
        else if(isset($_POST['brand_id']))
        {
            $goods = new GoodsModel("{$city['goods_table']}");
            $brand_id = $_POST['brand_id'];

            if($brand_id == 135)
            {
                $offset = ($page-1)*$limit;//

                $invitation_person = 0;
                if($_SESSION['temp_buyers_id']>0)
                {
                    $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];

                    $res = $goods->query($sql);
                    $invitation_person = $res[0]['invitation_person'];
                }

                if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
                {
                    $sql = "SELECT goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,public_price shop_price,version_id,color_id,brand_id,goods_color FROM "."{$_POST['goods_table']}"." WHERE is_pass=1 and brand_id <>135 order by goods_id desc limit ".$offset.','.$limit;
                }else
                {
                    $sql = "SELECT goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,color_id,brand_id,goods_color FROM "."{$_POST['goods_table']}"." WHERE is_pass=1 and brand_id <>135 order by goods_id desc limit ".$offset.','.$limit;
                }

                $res = $goods->query($sql);

                $data = arr_reform($res);//对数组进行重组
                $response = array('success' => 'true', 'data' => $data);
                $response = ch_json_encode($response);
                exit($response);
            }

            $data = $goods -> getByBrandId($brand_id,$limit,$page,$city['goods_category_table']);

            $response = array('success' => 'true', 'data' => $data);
            $response = ch_json_encode($response);
            exit($response);
        }
        else
        {
            $response = array('success' => 'false', 'error' => array('msg' => 'id不能为空!', 'code' => 4106));
            $response = ch_json_encode($response);
            exit($response);
        }
    }

}