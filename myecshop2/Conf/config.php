<?php
return array(
	//'配置项'=>'配置值'
    'APP_GROUP_LIST' => 'Home,Admin,News', //项目分组设定
    'DEFAULT_GROUP'  => 'Home', //默认分组
    'LOAD_EXT_CONFIG' => 'db,debug,convention,cache', // 加载扩展配置文件
    'LOAD_EXT_FILE' => 'filter,reform',

    'URL_HTML_SUFFIX'=>'shtml|html|xml',//URL伪静态

    'URL_CASE_INSENSITIVE' =>true, //URL访问不再区分大小写了

    'SHOW_PAGE_TRACE'=>TRUE,  //trace调试信息

//    'DB_LIKE_FIELDS'=>'title|content', //开启模糊查询

//    'SITE_BRANCH' => 'www.myestest2.com',//门店地址

    /*Cookie配置*/
    'COOKIE_PATH'           => '/',     		// Cookie路径
    'COOKIE_PREFIX'         => '',      		// Cookie前缀 避免冲突

);
?>