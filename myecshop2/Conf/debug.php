<?php
/**
 * Created by PhpStorm.
 * User: LG
 * Date: 2015/7/23
 * Time: 16:34
 */
defined('THINK_PATH') or exit();
// 调试模式下面默认设置 可以在项目配置目录下重新定义 debug.php 覆盖
return  array(
    'LOG_RECORD'			=>	true,  // 进行日志记录
    'LOG_EXCEPTION_RECORD'  => 	true,    // 是否记录异常信息日志
    'LOG_LEVEL'       		=>  'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL',  // 允许记录的日志级别

    'DB_SQL_LOG'			=>	true, // 记录SQL信息
    'APP_FILE_CASE'  		=>  true, // 是否检查文件的大小写 对Windows平台有效
    'TMPL_STRIP_SPACE'      => 	false,       // 是否去除模板文件里面的html空格与换行
    'SHOW_ERROR_MSG'        => 	true,    // 显示错误信息
);