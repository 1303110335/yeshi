<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class ShopController extends CommonController {
    public function index(){




        $this->assign('munetype',2);
        $this->display();

    }

    public function shezhi(){
    	if(IS_POST){
    		$upload = new \Think\Upload();// 实例化上传类    
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
			$upload->savePath  =      '/Index/'; // 设置附件上传目录    // 上传文件     
			$info   =   $upload->upload();
			$pic_1 = I('post.pic_1');
			$pic_2 = I('post.pic_2');
			$pic_3 = I('post.pic_3');
			$pic_4 = I('post.pic_4');
			$pic_5 = I('post.pic_5');
    		if(empty($info) && $pic_1 && $pic_2 && $pic_3 && $pic_4 && $pic_5){
                $this->error("请上传图片!");
            }
            if($info['pic1']){
            	$pic_1 = $info['pic1']['savepath'].$info['pic1']['savename'];
            }
            if($info['pic2']){
            	$pic_2 = $info['pic2']['savepath'].$info['pic2']['savename'];
            }
            if($info['pic3']){
            	$pic_3 = $info['pic3']['savepath'].$info['pic3']['savename'];
            }
            if($info['pic4']){
            	$pic_4 = $info['pic4']['savepath'].$info['pic4']['savename'];
            }
            if($info['pic5']){
            	$pic_5 = $info['pic5']['savepath'].$info['pic5']['savename'];
            }
            $data = array(
            	'pic1' => $pic_1,
            	'pic2' => $pic_2,
            	'pic3' => $pic_3,
            	'pic4' => $pic_4,
            	'pic5' => $pic_5
            );    
            
            $ret = M('Seller')->where('id=1')->save($data);
            if($ret){
            	$this->success();
            }else{
            	$this->error('修改失败!');
            }
    	}else{
	    	$pic = M('Seller')->where('id=1')->find();
	    	$this->assign('pic',$pic);
	    	$this->display();
	    }
    }
}