<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/3
 * Time: 11:13
 */
class GoodsCategoryModel extends Model
{
    protected $tableName = "goods_category";

    protected $fields = array('goods_category_id','goods_category_name','cat_id','parent_id','sort_order','root_id','style','grade','img_url','is_show','_pk'=>'goods_category_id','_autoinc'=>true);

    public function __construct($table_name)
    {
        parent::__construct();
        $this -> tableName = $table_name;
    }

    //获取一级分类  id和名字.....
    public function getFirstName()
    {
        $condition['grade'] = 2;
        $condition['parent_id'] = 117;
        $res = $this -> where($condition) -> field("goods_category_id cat_id,goods_category_name cat_name") ->order('sort_order') -> select();
        if(!$res)
        {
            $response = array('success' => 'true', 'data'=>array());
            $response = ch_json_encode($response);
            exit($response);
        }
        return $res;
    }

    public function getSecondName($parent_id,$brand_table)//parent_id 也就是一级分类goods_category_id
    {
        $condition['parent_id'] = $parent_id;
        $condition['is_show'] = 1;

        //$res = $this -> where($condition) -> field('goods_category_id,goods_category_name,img_url') ->order('sort_order') -> select();
        $res = $this -> where("parent_id=$parent_id and is_show=1") -> field('goods_category_id cat_id,goods_category_name cat_name,img_url') ->order('sort_order') -> select();

		//$sql="select goods_category_id,goods_category_name from ecs_goods_category where is_show=1 and parent_id=".$parent_id." order by sort_order";
		//$res=$this -> query($sql);

		if(!$res)
        {
            return array(); //这里要注意，有可能有的一级栏目下没有二级栏目
        }

        $len = count($res);
        for($j=0; $j<$len; $j++){
            $res[$j]['cat_children'] = array();

            $tempbrand = new TempBrandModel($brand_table);
            $res2 = $tempbrand ->getBrandName2($res[$j]['cat_id']);//获取品牌信息

            $len2=count($res2);
            for($k=0; $k<$len2; $k++)
            {
                $res[$j]['cat_children'][$k]['cat_id'] = $res2[$k]['cat_id'];
                $res[$j]['cat_children'][$k]['cat_name'] = $res2[$k]['cat_name'];
            }
        }
        return $res;
    }

    //获取 name..
    public function getNameById($id)
    {
        $condition['goods_category_id'] = $id;

        $res = $this -> where($condition) -> field('goods_category_name cat_name') -> select();
        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }
        return $res;
    }


}