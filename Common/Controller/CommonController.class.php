<?php
namespace Common\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function _initialize(){
        //判断用户是否已经登录

        $uid =$_SESSION['admin_id'] ;
        if(!$uid){
            $this->redirect('/Admin/User/login');
        }
    }
    public function  checkMemberlogin(){

        $uid = $_SESSION['member_id'] ;
            if(!$uid){
                $this->redirect("Member/login"); //直接跳转，不带计时后跳转

//                $this->error("请先登录",U('Member/login'));
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
        return $info;
    }

    public function del() {
        $src=str_replace(__ROOT__.'/', '', str_replace('//', '/', $_GET['src']));
        if (file_exists($src)){
            unlink($src);
        }
        print_r($_GET['src']);
        exit();
    }
    public function  GetInfo($id){
        $action=D('Member');
        $returninfo=$action->GetInfomation($id);
        return $returninfo;
    }

}