<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/8
 * Time: 17:04
 */
function arr_reform($res)//该函数仅对 goods 表有用
{
    for($i=0; $i<count($res); $i++)
    {
        $res[$i]['color']=array();//在res1添加一个 color数组字段
        $res[$i]['brand']=array();//在res1添加一个 brand数组字段
        $res[$i]['version']=array();//在res1添加一个version数组字段

        $res[$i]['color']['color_name'] = $res[$i]['goods_color'];//color数组中添加color元素
        $res[$i]['color']['color_id'] = $res[$i]['color_id'];
        unset($res[$i]['color_id']);//删除$res数组中color_id元素

        $res[$i]['brand']['brand_name'] = $res[$i]['brand_name'];
        $res[$i]['brand']['brand_id'] = $res[$i]['brand_id'];
        unset($res[$i]['brand_id']);
        unset($res[$i]['brand_name']);

        $res[$i]['version']['version_name'] = $res[$i]['goods_version'];
        $res[$i]['version']['version_id'] = $res[$i]['version_id'];
        unset($res[$i]['version_id']);
        unset($res[$i]['goods_version']);

        $res[$i]['goods_img']=NROOT.'/Guest/'.$res[$i]['goods_img'];//完善图片路径

    }
    return $res;
}



