<?php
namespace Common\Controller;
use Think\Controller;
class CommonpcController extends Controller {
    public function _initialize(){
        //判断用户是否已经登录

        $uid =$_SESSION['member_id'] ;
        if(!$uid){
            $this->redirect('/Pc/User/login');
        }
    }



    public function uploadImg() {

        $upload = new \Think\UploadFile;
//        $upload = new upload();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $savepath='./Uploads/Picture/uploads/'.date('Ymd').'/';
        if (!file_exists($savepath)){
            mkdir($savepath);
        }
        $upload->savePath =  $savepath;// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
        }
        print_r(J(__ROOT__.'/'.$info[0]['savepath'].'/'.$info[0]['savename']));
    }

    public function del() {
        $src=str_replace(__ROOT__.'/', '', str_replace('//', '/', $_GET['src']));
        if (file_exists($src)){
            unlink($src);
        }
        print_r($_GET['src']);
        exit();
    }

}