<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/8/3
 * Time: 9:45
 */
class CatModel extends Model
{
    protected $tableName = 'cat';
   // protected $fields = array();

    //可以避免IO加载的效率开销
    protected $fields = array('cat_id','cat_name','_pk'=>'cat_id', '_autoinc' => true );

    //获取所有辅材的id和名字
    public function getName()
    {
        return $res = $this ->field('cat_id,cat_name') -> select();
    }

    //通过cat_id 获取辅材的名字
    public function getNameById($cat_id)
    {
        $res = $this -> where("cat_id='$cat_id'") -> field('cat_name') -> select();
        if(!$res)
        {
            $response = array('success' => 'false', 'error' => array('msg' => '查询结果为空2！', 'code' => 4902));
            $response = ch_json_encode($response);
            exit($response);
        }
        return $res;
    }
}