<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/5
 * Time: 10:23
 */
class TempBrandModel extends Model
{
    protected $fields = array('brand_id','brand_name','goods_category_id','sort_order','_pk'=>'brand_id','autoinc'=>true);

    public function __construct($table_name)
    {
        parent::__construct();
        $this -> tableName = $table_name;
    }

    public function getBrandName($goods_category_id)
    {
        $condition['goods_category_id'] = $goods_category_id;
        $res = $this -> where($condition) -> field('brand_id,brand_name') -> select();
        if(!$res)
        {
            $response = array('success' => 'false', 'error' => array('msg' => '查询结果为空！', 'code' => 4902));
            $response = ch_json_encode($response);
            exit($response);
        }
        return $res;
    }

    //获取品牌名 和 品牌id号
    public function getBrandName2($goods_category_id)
    {
        $condition['goods_category_id'] = $goods_category_id;
        $res = $this -> where($condition) -> field('brand_id cat_id,brand_name cat_name')->order('sort_order') -> select();
        if(!$res)
        {
            return array();
        }
        return $res;
    }
    //通过goods_category_id 获取所有的brand_id..
    public function getBrandByCatId($goods_category_id, $limit=10, $page=1,$goods_table,$goods_category_table )
    {
        $condition['goods_category_id'] = $goods_category_id;

        $res = $this -> where($condition) -> field('brand_id')->order('sort_order') -> select();


        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }

        $goods = new GoodsModel($goods_table);

        $data = $goods -> getByBrandId2($res, $limit, $page,$goods_category_table);

        return $data;
    }
}