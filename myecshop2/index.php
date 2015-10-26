<?php
	// 初始化当前的绝对路径
    // 换成正斜线是因为 win/linux都支持正斜线,而linux不支持反斜线
    header("Access-Control-Allow-Origin: *");//允许所有域名发起跨域请求，h5需要
    header('Access-Control-Allow-Headers: accept, xhr, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS");
    header("Content-type: text/html; charset=utf-8");
    date_default_timezone_set('PRC');
    ini_set("display_errors", "On");//显示所有错误信息  Off为屏蔽所有错误信息

    define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');
    define('MROOT',dirname(ROOT).'/');

    define('NROOT','http://'.$_SERVER['HTTP_HOST'].'/pcwstore');//这是阿里云服务器的路径
    //define('NROOT','http://'.$_SERVER['HTTP_HOST'].'/ecshop2');


    //修正部份计算机 DOCUMENT_ROOT 无值的问题
    if(empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
        if(PATH_SEPARATOR==':') $_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
        else $_SERVER['DOCUMENT_ROOT'] = substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF']));
    }else if(empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['PATH_TRANSLATED'])) {
        if(PATH_SEPARATOR==':') $_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
        else $_SERVER['DOCUMENT_ROOT'] = substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF']));
    }

    // 过滤参数,用递归的方式过滤$_GET,$_POST,$_COOKIE  （get_magic_quotes_gpc()  函数主要 解决转义字符 ‘\’带来的问题）
    if (get_magic_quotes_gpc()) {
        function stripslashes_deep($value){
            $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);//stripslashes() 函数去掉'\'  如果是"\\", 则去掉一个\
            return $value;
        }
        $_POST = array_map('stripslashes_deep', $_POST);
        $_GET = array_map('stripslashes_deep', $_GET);
        $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    }
    //手动设置 Session 的生存期一天
   $lifeTime = 24 * 3600 * 7;
   session_set_cookie_params($lifeTime);
   // 开启session
   session_start();


    // 设置报错级别
    if(defined('DEBUG')) {
        error_reporting(E_ALL);
    } else {
        error_reporting(0);
    }

	//开启调试模式
    define('APP_DEBUG', true);
    define('CONF_PATH','./Conf/');
    //加载框架入口文件
    require './ThinkPHP/ThinkPHP.php';
    //require './Runtime/~runtime.php';

