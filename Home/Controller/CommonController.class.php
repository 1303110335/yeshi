<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function _initialize(){

        $pid=I('get.pid');
        if(!empty( $_SESSION['pid'])){
           $pid=$_SESSION['pid'];
       }
	   //$_SESSION['member_id']=72;
        //判断用户是否已经登录
        $uid = $_SESSION['member_id'] ;
        if(!$uid){
            $_SESSION['surl']=$_SERVER['REQUEST_URI'];
            $this->redirect("User/weixin/"); //直接跳转，不带计时后跳转
        }


    }

    public function  GetInfo($id){
        $action=D('Member');
        $returninfo=$action->GetInfomation($id);
        return $returninfo;
    }
    public function  getmemberorderlist($memberid){
        $Model=M();
        $memberorderlist=$Model->query("select * from __PREFIX__orderlist where userid in(select id from __PREFIX__member where fid=$memberid )");

        return $memberorderlist;
    }
 public function  getmemberordercount($memberid){
        $Model=M();
        $count=$Model->query("select count(*) from __PREFIX__orderlist where userid in(select id from __PREFIX__member where fid=$memberid )");

        return $count[0]['count(*)'];
    }
public function  getmembercount($memberid){
    $action=M('Member');
    $returninfo=$action->where("fid=$memberid")->count();
    return $returninfo;
    }
}