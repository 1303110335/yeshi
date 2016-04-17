<?php
namespace Home\Controller;
use Think\Controller;
class CommonaddpidController extends Controller{

    /**
     * 分销商前台 初始化，主要用于检测链接从哪里进去。
     *
     * @author Chandler_qjw  ^_^
     */

/*    public function _initialize(){
        $pid=I('get.pid');
        $gid=I('get.gid');


        $rs=$this->GetInfo($pid);
        if(!$rs){
            if(!empty( $_SESSION ['pid'])){
                $pid=$_SESSION['pid'];
            }else{
                $pid=1;
            }

            if($gid!=''){
                $this->redirect(CONTROLLER_NAME."/".ACTION_NAME);
            }else{
                $this->redirect(CONTROLLER_NAME."/".ACTION_NAME);
            }
        }else{
            $_SESSION['pid']=$pid;
        }
    }*/

    public function  GetInfo($id){
        $action=D('Member');
        $returninfo=$action->GetInfomation($id);
        return $returninfo;
    }
}