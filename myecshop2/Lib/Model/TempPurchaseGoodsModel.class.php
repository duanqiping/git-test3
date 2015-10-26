<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/23
 * Time: 18:29
 */
class TempPurchaseGoodsModel extends BaseModel
{
    protected $fields = array('temp_purchase_goods_id','temp_purchase_id','version','amount','unit','price','description','goods_id','name','goods_cat_id','brand_name','goods_color','area_id','goods_sn','_pk'=>'temp_purchase_goods_id','autoinc'=>true);

    public function getByTempGoodsId($temp_purchase_id)
    {
        $condition['temp_purchase_id']=$temp_purchase_id;
        $res = $this -> where($condition) -> field('version,amount,unit goods_unit,price shop_price,description,name goods_name,goods_cat_id,goods_id,brand_name') -> select();

        for($i=0; $i<count($res); $i++)
        {
            $res[$i]['version_name'] = $res[$i]['version'];//这一步搞了我很久....
            unset($res[$i]['version']);

            $res[$i]['brand']['brand_name'] = $res[$i]['brand_name'];
            $res[$i]['version']['version_name'] = $res[$i]['version_name'];
            $res[$i]['cat']['cat_id'] = $res[$i]['goods_cat_id'];

            unset($res[$i]['brand_name']);
            unset($res[$i]['version_name']);
            unset($res[$i]['goods_cat_id']);

            $res[$i]['amount'] = floor($res[$i]['amount']);//数量只能为整数
        }
        return $res;
    }


    public function add11($data)
    {

        if($this -> addAll($data))
        {
            $this -> commit();//成功 事物提交

            return true;//批量插入成功..
        }
        else
        {
            $this -> rollback();//失败 回滚

            $response = array('success' => 'false', 'error' => array('msg' => '订单商品入库失败！', 'code' => 4904));
            $response = ch_json_encode($response);
            exit($response);

        }
    }
    //商品名获取 已售数量
    public function  getGoodsMount($name)
    {
        //$name="卫生间吊顶";//测试数据
        $sql = "select amount from ecs_temp_purchase_goods where name='{$name}'";
        $res = $this->query($sql);

        $totalNum = 0;
        foreach($res as $k=>$v)
        {
            $totalNum += $res[$k]['amount'];
        }
        return $totalNum;
    }
}