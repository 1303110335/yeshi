<?php
return array(

        'MODULE_ALLOW_LIST' => array('Home'),
    'DEFAULT_MODULE'     => 'Home', //默认模块
    'URL_MODEL'          => '2', //URL模式
    'SESSION_AUTO_START' => true, //是否开启session

//    定义手机错误页面
//    'TMPL_EXCEPTION_FILE'=>'./Public/Tpl/error.html',
    /* 错误页面模板 */
    'TMPL_ACTION_SUCCESS'     =>  MODULE_PATH.'View/Public/error.html', //
    'TMPL_ACTION_ERROR'     =>  MODULE_PATH.'View/Public/error.html', //
    'TMPL_PARSE_STRING'  =>array(
        '__JS__' => '/Public/Home/Js',
        '__CSS__' => '/Public/Home/Css',
        '__IMAGES__' => '/Public/Home/Images',
        '__UPLOADS__' => '/Public/Home/Uploads',
        '__DEFAULT__' => '/Public/Home/Default',
    ),

    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,xls,xlsx', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Download/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('ydid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）


    'URL_HTML_SUFFIX'=>'',//去掉后缀

);