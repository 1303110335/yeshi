<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class PublicController extends CommonController {

	//文件上传
	public function upload(){    
		$upload = new \Think\Upload();// 实例化上传类    
		$upload->maxSize   =     5242880 ;// 设置附件上传大小    
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
		$upload->subName = array('date', 'Ymd');
		$upload->savePath  =      '/Picture/uploads/'; // 设置附件上传目录    // 上传文件     
		$info   =   $upload->upload();        
		return $info;    
	}

}