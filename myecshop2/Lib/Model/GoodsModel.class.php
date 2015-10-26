<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/24
 * Time: 18:23
 */
class GoodsModel extends BaseModel
{
    protected $tableName = 'goods';

    protected $fields = array('goods_id','cat_id','goods_sn','goods_version','goods_name','goods_unit','brand_name','suppliers_name','shop_price','public_price','private_price','goods_desc','goods_thumb','goods_img','original_img','add_time','sort_order','is_delete','last_update','suppliers_id','is_pass','reason','admin_id','admin_time','mobile_sort_order','goods_cat_id','color_id','version_id','brand_id','goods_color','goods_name_id','_pk'=>'goods_id','_autoinc'=>true);

    //命名规范  商品的默认分组和排序..
    protected $_scope = array(
        'default'=>array(

            'order'=>'mobile_sort_order,CONVERT(goods_name USING gb2312),goods_id',

           // 'field'=>'goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,color_id,brand_id,goods_color',
            'group'=>'goods_name_id,version_id',
        ),
    );

    public function __construct($table_name)
    {
        parent::__construct();
        $this -> tableName = $table_name;
    }

    //模糊查询(通过型号 或者 名字 模糊查询)

    public function getByNameLike($name,$page,$pageSize)
    {
        $offset = ($page-1)*$pageSize;//

        $map['goods_name|goods_version|brand_name'] = array('like','%'.$name.'%');
        $map['is_pass'] = 1;

        $map['is_delete'] = 0;



        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        //$res = $this -> where($map)->group('goods_name_id,version_id') -> order('mobile_sort_order,goods_id')->field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,color_id,brand_id,goods_color')->limit($offset,$pageSize) -> select();
        if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
        {
            $res = $this -> where($map)->scope('default')->field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,public_price shop_price,version_id,color_id,brand_id,goods_color')->limit($offset,$pageSize) -> select();
        }else{
            $res = $this -> where($map)->scope('default')->field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,color_id,brand_id,goods_color')->limit($offset,$pageSize) -> select();
        }

        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }
        return $res = arr_reform($res);//该函数是对数组进行重组
    }


    //确认订单时 获取第一个商品的名字
    public function getFirstGoodsName($goods_id)
    {
        $condition['goods_id'] =  $goods_id;
        return $res = $this -> where($condition) -> field('goods_name') -> select();
    }

    //通过goods_id查询商品的vesion_id
    public function getByVersionId($id)
    {
        $res = $this -> where("goods_id=$id")->field('version_id,brand_id,goods_name_id')->select();//获取version_id

        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }


        //通过version_id 和 brand_id 获取商品信息
        $condition["version_id"] = $res[0]["version_id"];
        $condition["brand_id"] = $res[0]["brand_id"];
        $condition["goods_name_id"] = $res[0]["goods_name_id"];
        $condition['is_pass'] = 1;

        $condition['is_delete'] = 0;


        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
        {
            $res1 = $this -> where($condition) -> field('goods_name_id, goods_id, goods_cat_id, goods_version, goods_name, goods_unit, brand_name, public_price shop_price, version_id, brand_id, goods_color, color_id')->select();
        }else
        {
            $res1 = $this -> where($condition) -> field('goods_name_id, goods_id, goods_cat_id, goods_version, goods_name, goods_unit, brand_name, shop_price, version_id, brand_id, goods_color, color_id')->select();
        }

//        $sql = "SELECT goods_name_id, goods_id, goods_cat_id, goods_version, goods_name, goods_unit, brand_name, shop_price, version_id, brand_id, goods_color, color_id FROM ".$this->tableName." WHERE is_pass=1 AND version_id=".$res[0]["version_id"]." and brand_id=".$res[0]["brand_id"]." and goods_name_id=".$res[0]["goods_name_id"];
//        $res1 = $this -> query($sql);

        $shopcar = new ShopcarModel();
        for($i=0; $i<count($res1); $i++)
        {
            $type = $shopcar->goodsType($res1[$i]['goods_id']);
            $res1[$i]['is_collection'] = $type;
        }
        $res1 = arr_reform($res1);//组装数组
        return $res1;
    }
    //通过brand_id查询商品信息
    public function getByBrandId($brand_id,$limit=10,$page=1,$categoryTable)
    {
        $condition['brand_id'] = $brand_id;
        $condition['is_pass'] = 1;

        $condition['is_delete'] = 0;


        //偏移量
        $offset = ($page-1)*$limit;//

        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        //$res = $this -> where($condition)->group('goods_name_id,version_id') ->order('mobile_sort_order,goods_id') -> field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,brand_id') ->limit($offset,$limit) -> select();
        if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
        {
            $res = $this -> where($condition)->scope('default') -> field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,public_price shop_price,version_id,brand_id') ->limit($offset,$limit) -> select();
        }else
        {
            $res = $this -> where($condition)->scope('default') -> field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,brand_id') ->limit($offset,$limit) -> select();
        }

        //$sql = "SELECT goods_name_id, goods_id, goods_cat_id, goods_version, goods_name, goods_unit, brand_name, shop_price, version_id, brand_id FROM ".$this->tableName." WHERE is_pass=1 AND brand_id =".$brand_id." GROUP BY goods_name_id,version_id order by mobile_sort_order,goods_id limit ".$offset.','.$limit;
        //$res = $this -> query($sql);

        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }

        $category = new GoodsCategoryModel($categoryTable);
        $cat_name=array();
        for($i=0; $i<count($res); $i++)
        {
            $cat_name[$i] = $category -> getNameById($res[$i]['goods_cat_id']);//获取goods_category_name

            $res[$i]['cat']['cat_name']=$cat_name[$i][0]['goods_category_name'];
            $res[$i]['cat']['cat_id']=$res[$i]['goods_cat_id'];

            $res[$i]['version']['version_name']=$res[$i]['goods_version'];
            $res[$i]['version']['version_id']=$res[$i]['version_id'];
            unset($res[$i]['goods_version']);
            unset($res[$i]['version_id']);

            $res[$i]['brand']['brand_name']=$res[$i]['brand_name'];
            $res[$i]['brand']['brand_id']=$res[$i]['brand_id'];
            unset($res[$i]['brand_name']);
            unset($res[$i]['brand_id']);
        }
        return $res;
    }

    //通过brand_id(为一个数组) 查询商品信息
    public function getByBrandId2($arr, $limit, $page,$goods_category_table)
    {
        $category = new GoodsCategoryModel($goods_category_table);

        $res1=array();
        $cat_name=array();

        for($i=0; $i<count($arr); $i++)
        {
            $condition['brand_id']=$arr[$i]['brand_id'];
            $condition['is_pass'] = 1;

            $condition['is_delete'] = 0;


            //偏移量
            $offset = ($page-1)*$limit;

            $invitation_person = 0;
            if($_SESSION['temp_buyers_id']>0)
            {
                $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
                $res = $this->query($sql);
                $invitation_person = $res[0]['invitation_person'];
            }

            //经过测试 下面的tp 这种写法 和下面的原生 sql 等效
            //$res1[$i] = $this -> where($condition) ->group('goods_name_id,version_id') -> order('mobile_sort_order,goods_id') -> field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,brand_id') -> limit($offset,$limit) -> select();

            if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
            {
                $res1[$i] = $this -> where($condition) -> scope('default')->field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,public_price shop_price,version_id,brand_id') -> limit($offset,$limit) -> select();
            }
            //其他情况使用shop_price
            else{
                $res1[$i] = $this -> where($condition) -> scope('default')->field('goods_name_id,goods_id,goods_cat_id,goods_version,goods_name,goods_unit,brand_name,shop_price,version_id,brand_id') -> limit($offset,$limit) -> select();
            }

            $cat_name = $category -> getNameById($res1[$i][0]['goods_cat_id']);//获取goods_category_name

        }

        $res3=array();
        for($i=0; $i<count($res1); $i++)
        {
            for($j=0; $j<count($res1[$i]); $j++)
            {
                $res1[$i][$j]['cat']['cat_name']=$cat_name[0]['cat_name'];
                $res1[$i][$j]['cat']['cat_id'] = $res1[$i][$j]['goods_cat_id'];
                unset($res1[$i][$j]['cat_id']);

                $res1[$i][$j]['version']['version_name']=$res1[$i][$j]['goods_version'];
                $res1[$i][$j]['version']['version_id']=$res1[$i][$j]['version_id'];
                unset($res1[$i][$j]['goods_version']);
                unset($res1[$i][$j]['version_id']);

                $res1[$i][$j]['brand']['brand_name']=$res1[$i][$j]['brand_name'];
                $res1[$i][$j]['brand']['brand_id']=$res1[$i][$j]['brand_id'];
                unset($res1[$i][$j]['brand_name']);
                unset($res1[$i][$j]['brand_id']);

                $res3[]=$res1[$i][$j];

            }

        }
        return $res3;
    }

    //通过goods_id获取suppliers_id
    public function getSuppliersIdByGoodsId($goods_id)
    {
        return $this -> where("goods_id=$goods_id")->field('suppliers_id')->select();
    }

    //通过goods_id 获取brand_name
    public function getBrandNameById($goods_id)
    {
        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
        {
            $res = $this -> where("goods_id='$goods_id'") -> field('goods_id,goods_version,goods_name,goods_unit,brand_name,public_price shop_price,color_id,brand_id,version_id,goods_color') -> select();
        }else{
            $res = $this -> where("goods_id='$goods_id'") -> field('goods_id,goods_version,goods_name,goods_unit,brand_name,shop_price,color_id,brand_id,version_id,goods_color') -> select();
        }
          return $res;
    }

    //通过goods_id 获取商品详情
    public function getById($arr)
    {
        $res=array();

        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        $len = count($arr);
        for($i=0; $i<$len; $i++)
        {
            $condition['goods_id']=$arr[$i]['goods_id'];
            if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
            {
                $res[$i] = $this -> where($condition) -> order('version_id') -> field('goods_id,goods_version,goods_name,goods_unit,brand_name,public_price shop_price,color_id,version_id,brand_id,goods_color') -> select();
            }else
            {
                $res[$i] = $this -> where($condition) -> order('version_id') -> field('goods_id,goods_version,goods_name,goods_unit,brand_name,shop_price,color_id,version_id,brand_id,goods_color') -> select();
            }
      }
        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }

        $len2 = count($res);
        for($i=0;$i<$len2;$i++)
        {
            $res[$i] = $res[$i][0];
            unset($res[$i][0]);

            $res[$i]['color']['color_id'] = $res[$i]['color_id'];
            $res[$i]['color']['color_name']=$res[$i]['goods_color'];
            unset($res[$i]['color_id']);
            unset($res[$i]['goods_color']);

            $res[$i]['version']['version_id'] = $res[$i]['version_id'];
            $res[$i]['version']['version_name']=$res[$i]['goods_version'];
            unset($res[$i]['goods_version']);
            unset($res[$i]['version_id']);

            $res[$i]['brand']['brand_id']=$res[$i]['brand_id'];
            $res[$i]['brand']['brand_name'] = $res[$i]['brand_name'];
            unset($res[$i]['brand_id']);
            unset($res[$i]['brand_name']);
        }

        return $res;
    }

    //获取商品总价
    public function getTotalPrice($arr)
    {

        $total = 0;
        $res=array();

        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        for($i=0; $i<count($arr); $i++)
        {
            $condition['goods_id'] = $arr[$i]['goods_id'];
            if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
            {
                $res[$i] = $this -> where($condition) -> field('public_price shop_price')->select();
                $total+=($res[$i][0]['shop_price']*$arr[$i]['amount']);//计算商品的总价
            }else{
                $res[$i] = $this -> where($condition) -> field('shop_price')->select();
                $total+=($res[$i][0]['shop_price']*$arr[$i]['amount']);//计算商品的总价
            }
        }
        return $total;
    }

    //通过goods（数组）获取商品信息......
    public function getByGoodsId($goods,$temp_purchase_id)
    {

        $res = array();
        $res2 = array();

        $invitation_person = 0;
        if($_SESSION['temp_buyers_id']>0)
        {
            $sql = "select invitation_person from ecs_temp_buyers where temp_buyers_id=".$_SESSION['temp_buyers_id'];
            $res = $this->query($sql);
            $invitation_person = $res[0]['invitation_person'];
        }

        for($i=0; $i<count($goods); $i++)
        {
            $condition['goods_id']=$goods[$i]['goods_id'];
            if($invitation_person < 1)//当没有登陆或者没有邀请码的情况下使用public_price
            {
                $res[$i] = $this -> where($condition) -> field('goods_version version,goods_unit unit,public_price price,goods_desc description,goods_name name,cat_id goods_cat_id,brand_name,goods_id,goods_color,goods_sn')->select();
            }else{
                $res[$i] = $this -> where($condition) -> field('goods_version version,goods_unit unit,shop_price price,goods_desc description,goods_name name,cat_id goods_cat_id,brand_name,goods_id,goods_color,goods_sn')->select();
            }

            $res[$i][0]['amount']=$goods[$i]['amount'];
            $res[$i][0]['temp_purchase_id']=$temp_purchase_id;//这里把$temp_purchase_id 拼到数组中

            $res[$i][0]['area_id'] = $_POST['area_id'];//顾客对应的城市 id

            $res2[] = $res[$i][0];
        }
        return $res2;
    }

}
