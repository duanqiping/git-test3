<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/7
 * Time: 10:29
 */
class ShopcarModel extends BaseModel
{
    //获取商品的收藏状态
    protected $fields = array('shop_car_id','goods_id','amount','buyers_id','suppliers_id','car_type','version_id','area_id','_pk'=>'shop_car_id','_autoinc'=>true);

    public function goodsType($goods_id)
    {
        $condition['goods_id'] = $goods_id;
        $condition['car_type'] = 1;
        $condition['buyers_id'] = $_SESSION['temp_buyers_id'];

        $res = $this -> where($condition)->field('goods_id') -> select(); //能得到东西，表示已经收藏

        if(!$res)
        {
            return 0;//未收藏
        }
        return 1;//已经收藏
    }

    //判断商品是否存在  存在就改变数量  否则就添加一条新纪录到购物车表
    public function goodsIfExist($arr)
    {
        $condition['goods_id'] = $arr['goods_id'];
        $condition['buyers_id'] = $arr['buyers_id'];
        $condition['area_id'] = $arr['area_id'];
        $condition['car_type'] = 0;

        $res = $this -> where($condition) -> field('shop_car_id')->select();//查询购物车中是否存在************

        $sql = "update ecs_shopcar set amount=amount+"."{$arr['amount']}"." where shop_car_id=".$res[0]['shop_car_id'];

        if($res)
        {
            //商品已存在， 添加数量即可
            if( $this ->execute($sql)  )//where("shop_car_id=$res[0]['shop_car_id']") -> setInc('amount',$arr['amount'])这种方法为什么会失败呢？？？？
            {
                return true;
            }
            else
            {
                $response = array('success' => 'false', 'error' => array('msg' => '添加失败1！', 'code' => 4902));
                $response = ch_json_encode($response);
                exit($response);
            }
        }
        else
        {
            //加入购物车
            if ($this -> addone($arr, $car_type=0))//car_type=0是加入购物车
            {
                return true;
            }
            else
            {
                $response = array('success' => 'false', 'error' => array('msg' => '添加失败2！', 'code' => 4902));
                $response = ch_json_encode($response);
                exit($response);
            }
        }
    }

    //删除购物车
    public function goodsDelById($arr)
    {
        $condition['goods_id']=$arr['goods_id'];
        $condition['buyers_id']=$arr['buyers_id'];
        $condition['car_type']=0;
        $condition['area_id'] = $arr['area_id'];

        $res = $this -> where($condition) -> delete();

        if(!$res)
        {
            $response = array("success"=>"false","error"=>array("msg"=>'删除购物车商品失败','code'=>4902));
            $response = ch_json_encode($response);
            exit($response);
        }
        return true;
    }

    //修改购物车的数量
    public function amountUpdate($arr)
    {
        $sql = "update ecs_shopcar set amount= {$arr['amount']} where goods_id={$arr['goods_id']} and buyers_id={$arr['buyers_id']} and car_type=0 and area_id={$arr['area_id']}";
//        echo $sql;
//        exit();
        if ($this->execute($sql))
        {
            return true;
        }
        else
        {
            $response = array('success' => 'false', 'error' => array('msg' => '修改购物车失败！', 'code' => 4902));
            $response = ch_json_encode($response);
            exit($response);
        }
    }

    //插入一条记录
    public function addone($data,$car_type)//car_type=0加入购物车  car_type=1加入收藏>>>>>>>>>>>>
    {
        //判断收藏夹中是否已经收藏该商品
        $condition['buyers_id'] = $data['buyers_id'];
        $condition['goods_id'] = $data['goods_id'];
        $condition['area_id'] = $data['area_id'];
        $condition['car_type'] = $car_type;
        $result = $this->where($condition)->field('goods_id')->select();
        if ($result)
        {
            $response = array('success' => 'true', 'data' => array('msg' => '已经加入！'));
            $response = ch_json_encode($response);
            exit($response);
        }

        $res = $this -> data($data) -> add();

        if (!$res)
        {
            $response = array('success' => 'false', 'error' => array('msg' => '添加失败3！', 'code' => 4902));
            $response = ch_json_encode($response);
            exit($response);
        }
        return true;
    }

    //删除一条记录 取消收藏
    public function delone($arr)
    {
        $condition['goods_id'] = $arr['goods_id'];
        $condition['area_id'] = $arr['area_id'];
        $condition['buyers_id'] = $_SESSION['temp_buyers_id'];
        $condition['car_type'] =1;
        $b = $this -> where($condition) -> delete();
        if(!$b)
        {
            $response = array("success"=>"false","error"=>array("msg"=>'取消收藏失败！','code'=>4903));
            $response = ch_json_encode($response);
            exit($response);
        }
        return $b;
    }

    //获取购物车中的数据
    public function getByBuyersId($buyers_id,$arr)
    {

        $condition['buyers_id']=$buyers_id;

        $condition['car_type']=0;
        $condition['area_id']=$arr['area_id'];

        $res = $this -> where($condition) -> field('goods_id,amount') -> select();

        if(count($res)<1)
        {
            $response = array('success' => 'true', 'data'=>array());
            $response = ch_json_encode($response);
            exit($response);
        }

        $res2=array();
        $num = 0;
        for($i=0,$count = count($res); $i<$count; $i++)
        {
            $goods = new GoodsModel($arr['goods_table']);

            $g = $goods -> getBrandNameById($res[$i]['goods_id']);
            if($g){
                $res2[$num]=$g[0];//获取 goods_id,cat_id,brand_name等等
                $res2[$num]['amount'] = $res[$i]['amount'];//把数量 放入到数组$res2中
                $num++;
            }

        }

        $shopcar = new ShopcarModel();
        for($j=0,$count  = count($res2); $j<$count; ++$j)
        {

            $type = $shopcar->goodsType($res2[$j]['goods_id']);//type=0未收藏  type=1已收藏
            $res2[$j]['is_collection'] = $type;

            $res2[$j]['brand']['brand_name'] = $res2[$j]['brand_name'];
            $res2[$j]['brand']['brand_id'] = $res2[$j]['brand_id'];
            unset($res2[$j]['brand_id']);
            unset($res2[$j]['brand_name']);

            $res2[$j]['version']['version_name'] = $res2[$j]['goods_version'];
            $res2[$j]['version']['version_id'] = $res2[$j]['version_id'];
            unset($res2[$j]['version_id']);
            unset($res2[$j]['goods_version']);

            $res2[$j]['color']['color_name'] = $res2[$j]['goods_color'];
            $res2[$j]['color']['color_id'] = $res2[$j]['color_id'];
            unset($res2[$j]['goods_color']);
            unset($res2[$j]['color_id']);
        }


        return $res2;
    }

    //获取收藏列表的数据
    public function getCollectByBuyersId($buyers_id,$area_id, $limit=10, $page=1,$goods_table)
    {

        $condition['buyers_id']=$buyers_id;
        $condition['area_id'] = $area_id;
        $condition['car_type']=1;

        //偏移量
        $offset = ($page-1)*$limit;

        $res = $this -> where($condition)->field('goods_id')->limit($offset,$limit)->select();
        //$sql = "SELECT goods_id FROM ecs_shopcar WHERE ( buyers_id = "."{$buyers_id}"." ) AND ( car_type = 1 )  AND (area_id="."{$area_id}".") limit ".$offset.",".$limit;
        //$res = $this->query($sql);

        if(!$res)
        {
            $response = array('success' => 'true', 'data'=>array());
            $response = ch_json_encode($response);
            exit($response);
        }

        $goods = new GoodsModel($goods_table);

        return $goods -> getById($res);

    }

    //清空收藏夹
    public function cleanByUserId($area_id)
    {
        $condition['buyers_id']=$_SESSION['temp_buyers_id'];
        $condition['car_type']=1;//0表示加入购物车   1表示收藏
        $condition['area_id'] = $area_id;
        //$res = $this -> where($condition) -> delete();

        $sql = "DELETE FROM ecs_shopcar WHERE ( buyers_id = "."{$_SESSION['temp_buyers_id']}"." ) AND ( car_type = 1 ) AND (area_id = "."{$area_id}".")";

        $res=$this->execute($sql);

        if(!$res)
        {
            $response = array("success"=>"false","error"=>array("msg"=>'你的收藏夹为空！','code'=>4903));
            $response = ch_json_encode($response);
            exit($response);
        }
        return true;
    }

    //确认订单 后 把对应的商品 从购物车中移除..
    public function deleteById($goods)
    {
        $condition['buyers_id'] = $_SESSION['temp_buyers_id'];
        $condition['car_type'] = 0;
        $condition['area_id'] = $_POST['area_id'];

        for($i=0; $i<count($goods); $i++)
        {
            $condition['goods_id'] = $goods[$i]['goods_id'];
            $res = $this -> where($condition)->delete();
            if(!$res)
            {
                $response = array("success"=>"false","error"=>array("msg"=>'商品从购物车移除失败！','code'=>4903));
                $response = ch_json_encode($response);
                exit($response);
            }
        }
        return true;
    }

}
