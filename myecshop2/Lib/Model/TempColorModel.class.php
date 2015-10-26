<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/6
 * Time: 18:05
 */
class TempColorModel extends Model
{
    protected $fields = array('color_id','color','_pk'=>'color_id','autoinc'=>true);

    public function getNameById($id)
    {
        $res = $this -> where("color_id=$id")->field('color')->select();
        if(!$res)
        {
            $response = array('success' => 'true', 'data' => array());
            $response = ch_json_encode($response);
            exit($response);
        }
        return $res;
    }

}