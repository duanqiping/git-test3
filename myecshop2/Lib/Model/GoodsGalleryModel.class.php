<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/7/31
 * Time: 16:45
 */
class GoodsGalleryModel extends Model
{
    protected $tableName = 'goods_gallery';
    protected $fields = array('img_id','goods_id','img_url','thumb_url','img_original','_pk'=>'img_id','_autoinc'=>true);
    //获取展示图片
    public function getImgs($goods_id)
    {
        return $this -> where("goods_id=$goods_id")-> field('img_url') -> select();
    }
}