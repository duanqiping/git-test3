<?php
/**
 * Created by PhpStorm.
 * User: qiping
 * Date: 2015/9/24
 * Time: 10:30
 */
class OtherAction extends Action
{
    //版本型号接口
    public function version()
    {
        $device = 0;

        if (isset($_POST['device'])) {
            $device = $_POST['device'];
        }
        $data = array();
        if ($device == 0) {//android
            $data['version'] = '1.3.1';
            $data['versionCode'] = 103;
            $data['desc'] = '有新版本更新';
            $data['url'] = 'http://www.aec188.com/askprice/download/PcwStore.apk';
        }else { //ios
            $data['version'] = '1.2.1';
            $data['desc'] = '有新版本更新';
            $data['url'] = 'https://geo.itunes.apple.com/cn/app/zhao-cai-mao-zhuang-xiu-fu/id1033784965?mt=8';
        }
        $data['force'] = false;//是否强制升级

        $response = array('success' => 'true', 'data' => $data);
        $response = ch_json_encode($response);
        exit($response);
    }
}
