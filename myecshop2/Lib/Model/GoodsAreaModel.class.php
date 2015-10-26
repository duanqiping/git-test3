<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/9
 * Time: 10:02
 */
class GoodsAreaModel extends Model
{
    protected $fileds = array('goods_area_id','goods_area','goods_table','gallery_table','brand_table','version_table','goods_name_table','goods_category_table','ali_area_name','_pk'=>'goods_area_id','_autoinc'=>true);
    //判断定位的城市是否已经入入库中
    public function judge($city_name)
    {
        $condition['goods_area'] = $city_name;
        $res = $this -> where($condition) -> field('goods_area_id,goods_area,goods_table')->select();

        if(!$res)
        {
            $res['goods_area_id'] = 0;
            $res['goods_area'] = $city_name;
            $res['goods_table'] = '';
            return $res;
        }
        return $res[0];
    }

    //获取城市对应的品牌表
    public function getBrandName($goods_table)
    {
        $data['goods_table'] = $goods_table;
        $res = $this->where($data)->field('brand_table,goods_category_table')->select();

        return $res[0];
    }

    //获取城市对应的二级分类表
    public function getGoodsCategoryTable($goods_table)
    {
        $data['goods_table'] = $goods_table;
        $res = $this->where($data)->field('brand_table,goods_category_table')->select();

        return $res[0];
    }

    public function getIdByGoodsTable($goods_table)
    {
        return $res = $this->where("goods_table='$goods_table'")->field('goods_area_id')->select();
    }

}