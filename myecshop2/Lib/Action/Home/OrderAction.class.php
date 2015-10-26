<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/14
 * Time: 14:35
 */
class OrderAction extends Action
{

    //确认下单
    public function confirm()
    {
        $temppurchase = new TempPurchaseModel();
        $temppurchase -> is_login();//判断是否登录

        $goods = $_POST['goods'];
        $goods_table = $_POST['goods_table'];
        $goods_table = preg_replace('/ecs_/','',$goods_table);//去掉表前缀 城市对应的category表

        for ($i=0; $i<count($goods); $i++)
        {
            is_empty($goods[$i]); //判断传入的goods信息 是否有为空
        }

        $arr = array();

        $arr['name'] = trim($_POST['name']); //收货人姓名
        $arr['mobile'] = trim($_POST['mobile']); //手机号码...
        $arr['address'] = $_POST['address']; //详细地址
        $arr['receive_time'] = $_POST['receive_time'];//送货时间
        //$arr['method'] =$_POST['method']; //付款方式

        is_empty($arr); //判断是否有空

        $arr['description'] = $_POST['description']; //备注, 这个应该可以为空
        $arr['transportation'] = $_POST['transportation'];//物流费用

        _filter_temp_purchase($arr);//判断接收的数据是否规范

        $arr['state']=1;//状态 待付款
        if($_POST['area_id'] == 1)
		{
			$arr['suppliers_id'] = 1024;//对应上海
		}
		else
		{
			$arr['suppliers_id'] = 1152;//对应南京
		}

        $good = new GoodsModel($goods_table);
        $arr['money'] = $good -> getTotalPrice($goods); //获取总价

        $arr['money'] = $arr['money']+$arr['transportation'];//总价加上物流真的总价


        //如果都通过，则把数据插入到相应的数据库
        $sn = $temppurchase -> orderSn();//获取订单号

        $arr['temp_purchase_sn']=$sn;
        $arr['time'] = time();
        $arr['buyers_id'] = $_SESSION['temp_buyers_id'];
        $arr['quotation_id'] = -1;//通过购物车购买 写死为-1
        $arr['suppliers_name']='找材猫';//写死 供应商名

        //获取第一商品的名字，把它插入到purchase_title这个字段中
        $good = new GoodsModel($goods_table);
        $name = $good -> getFirstGoodsName($goods[0]['goods_id']);
        $arr['purchase_title'] = $name[0]['goods_name'];


        $temp_purchase_id = $temppurchase -> add22($arr);//先对temp_purchase表进行插入，然后把生成的temp_purchase_id号用session保存

        $data1 = $temppurchase -> getByTempId($temp_purchase_id);//通过订单temp_purchase_id获取订单详情.....

        $res = $good -> getByGoodsId($goods,$temp_purchase_id);// 获取商品信息

        $temppurchasegoods = new TempPurchaseGoodsModel();

        $temppurchasegoods -> add11($res);//然后对temp_purchase_goods表进行插入
        $data2 = $temppurchasegoods -> getByTempGoodsId($temp_purchase_id);
        $data1['goods'] = $data2;

        $shopcar = new ShopcarModel();
        $shopcar -> deleteById($goods);//移除购物车对应的商品

        //返回总金额和订单号
        $response = array('success' => 'true', 'data' => $data1);
        $response = ch_json_encode($response);
        exit($response);
    }

    //订单详情


    public function detail()
    {

        $temppurchase = new TempPurchaseModel();
        $temppurchase -> is_login();//判断是否登录

        $temp_purchase_id = $_POST['temp_purchase_id'];

        $data1 = $temppurchase -> getById($temp_purchase_id);//通过订单id 获取订单详情

        $temppurchasegoods = new TempPurchaseGoodsModel();
        $data2 = $temppurchasegoods -> getByTempGoodsId($temp_purchase_id);//通过id 获取订单商品信息

        $data1['goods'] = $data2;//把两个数组合并成一个数组

        $response = array('success' => 'true', 'data' => $data1);
        $response = ch_json_encode($response);
        exit($response);

    }


    public function pay()
    {
        $temppurchase = new TempPurchaseModel();
        $temppurchase -> is_login();//判断是否登录

        //接收订单id号
        $temp_purchase_id = $_POST['temp_purchase_id'];//
        $temppurchase -> updateByID($temp_purchase_id);

        $response = array('success' => 'true', 'msg' => '订单状态更新成功！');
        $response = ch_json_encode($response);
        exit($response);

    }


    //接收结算金额， 返回最低金额和 物流费用

    public function judge()
    {
        $temppurchase = new TempPurchaseModel();
        $temppurchase -> is_login();//判断是否登录

        if (isset($_POST['price']))
        {
            $arr = array();
			if($_POST['area_id']==1)
			{

				$arr['price'] = 288;//最低金额  对应上海

                if($_POST['price'] >= 288)//................
                {
                    $arr['transportation'] = 0;//物流费用
                }else
                {
                    $arr['transportation'] = 50;//物流费用
                }


                $arr['explain'] = '订单满288元，免运费搬楼费';

                $arr['explain'] = '促销期间，免物流费用，免搬楼费';

			}
			else
			{
				$arr['price'] = 200;//最低金额  对应南京
                $arr['transportation'] = 0;//物流费用
                $arr['explain'] = '促销期间，免物流费用';
			}

            $arr['receive_time'] = "当天下单第二天到货";

            $response = array('success' => 'true', 'data' => $arr);
            $response = ch_json_encode($response);
            exit($response);

        }
        else
        {
            $response = array("success"=>"false","error"=>array("msg"=>'金额不能为空','code'=>4120));
            $response = ch_json_encode($response);
            exit($response);
        }
    }

    //获取支付方式列表
    public function paylist()
    {
        $arr = array();

        $arr[0]['icon']=NROOT.'/myecshop2/Public/img/payment_icon_alipay@3x.png';
        $arr[0]['name']='支付宝';
        $arr[0]['type']='0';
        $arr[0]['pay_default']='1';//default=1表示默认支付方式

//        $arr[1]['icon']=NROOT.'/myecshop2/Lib/Widget/img/payment_icon_bank@3x.png';
//        $arr[1]['name']='银行卡支付';
//        $arr[1]['type']='4';
//        $arr[1]['pay_default']='0';

        $arr[1]['icon']=NROOT.'/myecshop2/Public/img/huodaofukuan.png';
        $arr[1]['name']='货到付款';
        $arr[1]['type']='1';
        $arr[1]['pay_default']='0';

        $response = array('success' => 'true', 'data' => $arr);
        $response = ch_json_encode($response);
        exit($response);
    }
}
