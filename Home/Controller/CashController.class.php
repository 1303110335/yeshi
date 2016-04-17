<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class CashController extends CommonController {
    /**
     *
     *会员充值页面
     * @author Chandler_qjw  ^_^
     */
    public function chong(){

        $m=M('chong');
        $uid=$_SESSION['member_id'];
        $date['userid']=$uid;

        $result=$m->where($date)->select();
//        var_dump($result);
        $this->assign('paymentlist',$result);

        $this->display();
    }

 /**
     *
     *会员充值
     * @author Chandler_qjw  ^_^
     */
    public function addcash(){
        $cash=I('post.payment');

        $uid=$_SESSION['member_id'];
        $action=D('Cash');
        $rs=$action->addpayment($uid,$cash);
//       echo  $m->_sql();die;
        if($rs){
            $date['userid']=$uid;
            $date['cash']=$cash;
            $date['addtime']=Gettime();
            $date['beizhu']='微信充值';
            $m=M('chong');
            $rs=$m->add($date);
            $this->success("充值成功");
        }else{
            $this->error("充值失败");
        }




    }

    /**
     *
     *分销中心首页
     * @author Chandler_qjw  ^_^
     */
    public function agent(){
        $uid=$_SESSION['member_id'];
       $info =$this->GetInfo($uid);
//        var_dump($info);

        $getmembercount=$this->getmembercount($uid);

        $this->assign('getmembercount',$getmembercount);


        $count=$this->getmemberordercount($uid);
        $this->assign('memberordercount',$count);

        $this->assign('agentinfo',$info);
        $this->display();
    }

 /**
     *
     *会员中心订单列表页面
     * @author Chandler_qjw  ^_^
     */
    public function orderlist(){

//        $user = new Model('orderlist');
//        $list = $user->join('RIGHT JOIN user_profile ON user_stats.id = user_profile.typeid' );

        $orderno=I('get.orderno');
        if($orderno!=''){
            $ordernos=$orderno;
        }
        $memberid=$_SESSION['member_id'];
        $action=D('orderlist');
        $Allorder=$action->getAllorder($memberid,'',$ordernos); //全部订单
//        var_dump($Allorder[0]);
        $Waitepayorder=$action->getAllorder($memberid,1);//待付款

        $Waitesendorder=$action->getAllorder($memberid,2);//待发货
        $sendorder=$action->getAllorder($memberid,3);//已经发货
        $tuiorder=$action->getAllorder($memberid,6);//退货
        $waitepingorder=$action->getAllorder($memberid,7);//已经评价





//        var_dump($rs);
//        var_dump($rs[0]['orderlist']);

        $this->assign('allorder', $Allorder);
        $this->assign('Waitepayorder', $Waitepayorder);
        $this->assign('Waitesendorder', $Waitesendorder);
        $this->assign('sendorder', $sendorder);
        $this->assign('tuiorder', $tuiorder);
        $this->assign('waitepingorder', $waitepingorder);


        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;"><img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }

    /**
     *
     *分销中心分销商页面
     * @author Chandler_qjw  ^_^
     */
    public function fxs(){
        $uid=$_SESSION['member_id'];
        $info =$this->GetInfo($uid);
//        var_dump($info);
        $action=D('Member');
        $rs=$action->getMemberlist($uid);

        $getmembercount=$this->getmembercount($uid);

//var_dump($getmembercount);
        $count=$this->getmemberordercount($uid);


        $this->assign('memberordercount',$count);
        $this->assign('getmembercount',$getmembercount);
        $this->assign('memberlist',$rs);
        $this->assign('info',$info);
        $this->display();
    }
  /**
     *
     *分销中心分销商订单页面
     * @author Chandler_qjw  ^_^
     */
    public function fxdd(){



       $memberid=$_SESSION['member_id'];


        $memberorderlist=$this->getmemberorderlist($memberid);
        $count=$this->getmemberordercount($memberid);
        $getmembercount=$this->getmembercount($memberid);

        $info =$this->GetInfo($memberid);


        $this->assign('memberdetail',$info);
        $this->assign('getmembercount',$getmembercount);
        $this->assign('memberorderlist',$memberorderlist);
        $this->assign('memberordercount',$count);
//        $this->assign('info',$info);
        $this->display();
    }
 /**
     *
     *分销中心分销商佣金详情页面
     * @author Chandler_qjw  ^_^
     */
    public function yong(){



        $uid=$_SESSION['member_id'];
        $info =$this->GetInfo($uid);

        $this->assign('memberyongjindetail',$info);
//        $this->assign('info',$info);
        $this->display();
    }


}