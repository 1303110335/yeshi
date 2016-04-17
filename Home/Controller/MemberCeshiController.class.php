<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class MemberCeshiController extends CommonController {
    /**
     *
     *会员中心首页
     * @author Chandler_qjw  ^_^
     */
    public function main(){
        $uid=$_SESSION['member_id'];
        $isguanzhu = panduanguanzhu($uid);
		$this->assign('isguanzhu',$isguanzhu);
        $info =$this->GetInfo($uid);
//        var_dump($info);

//        echo $_SESSION['pid'];
		$url = U('Home/Member/address');
            session('addresserror',$url);

        $action=D('orderlist');
        $allordercount=$action->getordercount($uid); //所有订单
        $waitpayordercount=$action->getordercount($uid,1);//等待支付
        $waitsendordercount=$action->getordercount($uid,2);//等待发货
        $deliverpayordercount=$action->getordercount($uid,3);       //已经发货

		$action=D('qiandao');
			$todysign=$action->getQiandao();
			if(empty($todysign)){
				$issign=0;
			}else{
				$issign=1;
			}
			$ary = explode('http://',$info['touimg']);
			if(1<count($ary)){
				$this->assign('status','0');//使用外部头像
			}
		$this->assign('issign',$issign);

//        echo $waitpayordercount;
        $this->assign('allordercount',$allordercount);
        $this->assign('waitpayordercount',$waitpayordercount);
        $this->assign('waitsendordercount',$waitsendordercount);
        $this->assign('deliverpayordercount',$deliverpayordercount);
        $this->assign('info',$info);
		$this->assign('foottype',4);
        $this->display();
    }
	
	//团购订单页面
	public function grouplist(){
		$userid = session('member_id');
		if($userid){
			$list = M('Orderlist')->where("userid=$userid and pay_status=1 and is_groupbuy=1")->order('id desc')->select();
			for($i=0;$i<count($list);$i++){
                $orderid=$list[$i]['id'];

                $actiond=D('orderdetail');

                $rsd=$actiond->getorderdetail($orderid);
                $list[$i]['orderlist']=$rsd;
            }
			$this->assign('list',$list);
			$this->display();
		}
		
	}
	
	
	/**
     *
     *会员中心积分
     * @author Chandler_qjw  ^_^
     */
    public function jifen(){
        $uid=$_SESSION['member_id'];
        $info =$this->GetInfo($uid);

        $this->assign('info',$info);
		$this->assign('foottype',4);
        $this->display();
    }
	
	/**
     *
     *会员中心积分规则
     * @author Chandler_qjw  ^_^
     */
    public function jifengz(){
		$this->assign('foottype',4);
        $this->display();
    }
	
	/**
     *
     *会员中心积分兑换记录
     * @author Chandler_qjw  ^_^
     */
    public function duihuan(){
		$this->assign('foottype',4);
        $this->display();
    }
	
	
	/**
     *
     *我的会员卡
     * @author Chandler_qjw  ^_^
     */
    public function hyk(){
		$uid=$_SESSION['member_id'];
		$m = M('Member');
		$Memberone = $m->where('id='.$uid)->select();
		if(empty($Memberone[0]['telephone']))
		{
			$this->assign('is_vip',0);
		}
		else
		{
			$this->assign('is_vip',1);
		}
		$this->assign('Memberone',$Memberone);
		$this->assign('foottype',4);
        $this->display();
    }
	
	
    public function hyk2(){
		
		
		sendmessage(1,$phone);
		
		$this->assign('foottype',4);
        $this->display();
    }
	
    public function hyk_getcode(){
		
		$phone = I('post.phone');
		$rs = sendmessage(1,$phone);
		$arr = explode(",",$rs);
		if($arr[1]==0)
		{
			$data['type'] = 1;
			$data['info'] = "验证码发送成功！";
		}
		else
		{
			$data['type'] = 0;
			$data['info'] = "验证码发送失败,请重试！";
		}

        $this->ajaxReturn($data);
        return;
		
    }
	
    public function hyk_checkcode(){
		
		$code = I('post.code');
		if($code == $_SESSION['code'])
		{
			$arr['id'] = $_SESSION['member_id'];
			$arr['telephone'] = I('post.phone');
			$arr['vipcard'] = time().rand(1000,9999);
			$m = M('Member');
			$rs = $m->save($arr);
			if($rs!=false)
			{
				$data['type'] = 1;
				$data['info'] = "手机绑定成功！";		
			}
			else
			{
				$data['type'] = 0;
				$data['info'] = "手机绑定失败！";
			}			
		}
		else
		{
			$data['type'] = 0;
			$data['info'] = "验证码错误！";
		}

        $this->ajaxReturn($data);
        return;
		
    }
	
	/**
     *
     * 优惠券
     * @author Chandler_qjw  ^_^
     */
    public function yhq(){
		$uid=$_SESSION['member_id'];
		$date = date('Y-m-d H:i:s',time());
    	$couponlist = M("coupon_record")->field('a.*,b.end_time,b.title,b.price')->alias('a')->join("LEFT JOIN ys_coupon b on a.coupon_id=b.id")->where(" a.user_id='{$uid}'")->order('is_use asc,is_end asc,id desc')->select();
    	$this->assign('couponlist',$couponlist);
		$this->assign('foottype',4);
        $this->display();
    }


	
	/**
     *
     *客服中心
     * @author Chandler_qjw  ^_^
     */
    public function service(){
		$this->assign('foottype',4);
        $this->display();
    }
	
	/**
     *
     *退换货政策
     * @author Chandler_qjw  ^_^
     */
    public function thzc(){
		$this->assign('foottype',4);
        $this->display();
    }
	
	
	
	/**
     *
     *收藏夹
     * @author Chandler_qjw  ^_^
     */
    public function favorites(){
		
		$uid=$_SESSION['member_id'];
		$action=D('collection');
		$collection_list=$action->get_collectionlist($uid);
		
		$this->assign('collection_list',$collection_list);
		$this->assign('foottype',4);
		$this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无收藏商品</p></div>');
        $this->display();
    }
	
	
	
	/**
     *
     *收货地址
     * @author Chandler_qjw  ^_^
     */
    public function address(){
        
		$uid=$_SESSION['member_id'];
        $info =$this->GetInfo($uid);
		
/*		$action=D('qiandao');
			$todysign=$action->getQiandao();
			if(empty($todysign)){
				$issign=0;
			}else{
				$issign=1;
			}
			$ary = explode('http://',$info['touimg']);
			if(1<count($ary)){
				$this->assign('status','0');//使用外部头像
		}*/
		
		$order=M('orderaddress')->where('userid='.$uid)->select();	
		
		$this->assign('issign',$issign);
		$this->assign('order',$order);
        $this->assign('info',$info);
		$this->assign('foottype',4);
        $this->display();
    }
	
    /**
     *
     *修改地址
     * @author Chandler_qjw  ^_^
     */
    public function address3(){
        
        $uid=$_SESSION['member_id'];
        $id = I('get.id',0,'int');
        $orderress = M('orderaddress')->where("id=%d",$id)->find();
        $def = M('member')->where("id={$uid}")->getField('def');

        $this->assign('addressedit',$orderress);
        $this->assign('def',$def);
       
        $this->assign('foottype',4);
        $this->display();
    }
    public function address3ceshi(){
        
        $uid=$_SESSION['member_id'];
        $id = I('get.id',0,'int');
        $orderress = M('orderaddress')->where("id=%d",$id)->find();
        $def = M('member')->where("id={$uid}")->getField('def');

        $this->assign('addressedit',$orderress);
        $this->assign('def',$def);
       
        $this->assign('foottype',4);
        $this->display();
    }
    public function update_ress(){
        if(IS_POST){
            $user_id = session('member_id');
            $id = I('post.id');
            if($user_id && $id){
                $RadioGroup1 = I('post.RadioGroup1');//默认收货地址
                if($RadioGroup1){
                    $ret = M('member')->where("id={$user_id}")->setField('def',$id);
                }
                $date['province']=I('post.province');
                $date['city']=I('post.city');
                $date['country']=I('post.country');
                $date['telephone']=I('post.telephone');
                $date['consignee']=I('post.consignee');
                $date['add_type']=I('post.address_type');
                $date['xiangqing']=I('post.xiangqing');
				$date['dingwei']=I('post.dingwei');
                $result=M("orderaddress")->where("id = %d",$id)->save($date);
                if($result>0 || $ret>0){
                    echo 1;
                }else{
                    echo 0;
                }
                
            }
        }
    }
    public function delete_ress(){
        if(IS_POST){
            $user_id = session('member_id');
            $id = I('post.id');
            if($user_id && $id){
                $result=M("orderaddress")->where("id = %d and userid={$user_id}",$id)->delete();
                if($result){
                    $def = M('member')->where("id={$user_id}")->getField('def');
                    if($id==$def){
                        $ress = M('orderaddress')->where("userid={$user_id}")->order('id desc')->find();
                        if(is_array($ress)){
                            $ret = M('member')->where("id={$user_id}")->setField('def',$ress['id']);
                        }
                    }
                }
                if($result>0){
                    echo 1;
                }else{
                    echo 0;
                }
                
            }
        }
    }
	
	/**
     *
     *新增收货地址
     * @author Chandler_qjw  ^_^
     */
    public function address1(){
        $uid=$_SESSION['member_id'];
        $info =$this->GetInfo($uid);
		
		$action=D('qiandao');
			$todysign=$action->getQiandao();
			if(empty($todysign)){
				$issign=0;
			}else{
				$issign=1;
			}
			$ary = explode('http://',$info['touimg']);
			if(1<count($ary)){
				$this->assign('status','0');//使用外部头像
			}
			
			
			
		$lastname=I('post.lastname');
			if($lastname){
                $date['province']=I('post.province');
                $date['city']=I('post.city');
                $date['country']=I('post.county');
                $date['address']=I('post.address');
                $date['telephone']=I('post.tel');
                $date['xiangqing']=I('post.address');
                $date['consignee']=I('post.lastname');
                $date['userid']=$uid;
                $date['addtime']=Gettime();
				
				$mr_id=I('post.mr_id');
                $action=D("orderaddress");
                $result=$action->addaddress($date);
				if($mr_id){
					M('member')->where('id='.$uid)->setField('def',$result);
					}

                if( $result){
                	$this->assign("zhuangtai","添加成功");
                    //$this->redirect($_SESSION['orderurl']);
                }else{
                	$this->assign("zhuangtai","添加失败");
                    //$this->error("添加失败","{:U('Order/address')}");
                }
                $this->success();exit;
       
			}
		
		
		
		
		
		$this->assign('issign',$issign);
		
        $this->assign('info',$info);
		$this->assign('foottype',4);
        $this->display();
    }
	
	
	/**
     *
     *删除收货地址
     * @author Chandler_qjw  ^_^
     */
    public function deladdress(){
        
		$id=I('post.id');
		$uid=$_SESSION['member_id'];
		$m = M("User");
		$arr = $m->where('id='.$uid)->find();
		if($arr['def'] == $id)
		{
			$data['id'] = $uid;
			$data['def'] = 0;
			$m->save($data);
		}
		$m = M("Orderaddress");
		$m->where('id='.$id)->delete();
		
    }
	
	
	
	
	/**
     *
     *售后问题
     * @author Chandler_qjw  ^_^
     */
    public function problem(){

        $this->assign('info',$info);
		$this->assign('foottype',4);
        $this->display();
    }
	
	
	
	
	
	/**
     *
     *我的购物车
     * @author Chandler_qjw  ^_^
     */
    public function shopping(){
		
		$uid=$_SESSION['member_id'];
		$action=D('Shopping');
		$shop=$action->shop_list($uid);
		$total_count=$action->shop_list_count($shop);
		$count=count($shop);

        $this->assign('shop',$shop);
		$this->assign('total_count',$total_count);
		$this->assign('count',$count);
		$this->assign('foottype',3);
        $this->display();
    }
	
	
	
	
	/**
     *
     *我的购物车
     * @author Chandler_qjw  ^_^
     */
    public function addshopping(){
		
		
		$uid=$_SESSION['member_id'];
		if(empty($uid)){
			$data['info']   =   '亲，请先登录！'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   'http://goead.ysxdgy.com/Member/weixin'; // 成功或者错误的跳转地址
            $this->ajaxReturn($data);
            return;
			}else{
				$action=D('Shopping');
				$rs = $action->addshopp($uid);
				if($rs['type']){
					$data['info']   =   '添加成功！'; // 提示信息内容
					$data['status'] =   1;  // 状态 如果是success是1 error 是0
					$data['url']    =   ''; // 成功或者错误的跳转地址
					$data['count']    =   $rs['count']; // 成功或者错误的跳转地址
					
					}else{
						$data['info']   =   '添加失败！'; // 提示信息内容
						$data['status'] =   0;  // 状态 如果是success是1 error 是0
						$data['url']    =   ''; // 成功或者错误的跳转地址
						$data['count']    =   $rs['count']; // 成功或者错误的跳转地址
						
						}
				
				}
		
		$this->ajaxReturn($data);
        return;
    }
	
	
	
	
	
	
	/**
     *
     *我的购物车
     * @author Chandler_qjw  ^_^
     */
    public function addnums(){
		
		$uid=$_SESSION['member_id'];
		$goodid=$_POST['goodid'];
				$action=D('Shopping');
				$rs = $action->addnums($uid,$goodid);
		
		$this->ajaxReturn($rs);
        return;
    }
	
	
	
	/**
     *
     *我的购物车
     * @author Chandler_qjw  ^_^
     */
    public function decnums(){
		
		$uid=$_SESSION['member_id'];
		$goodid=$_POST['goodid'];
				$action=D('Shopping');
				$rs = $action->decnums($uid,$goodid);
		
		$this->ajaxReturn($rs);
        return;
    }
	
	
	
	
	/**
     *
     *删除购物车
     * @author Chandler_qjw  ^_^
     */
    public function delshop(){
		
		$id=$_POST['shop_id'];
		$action=D('Shopping');
		$rs = $action->where('id='.$id)->delete();
		
		$this->ajaxReturn($rs);
        return;
    }
	
	
	
	
	
	
	
	/**
     *
     *会员等级
     * @author Chandler_qjw  ^_^
     */
    public function hydj2(){

        $this->assign('info',$info);
		$this->assign('foottype',4);
        $this->display();
    }
	
	
	
	public function qiandao()
        {

        	$memberid = session('member_id');
        	if(empty($memberid)){
        		$data = array('status'=>2,'textt'=>"请先登录!");
        	}else{
	        	$qd = M('Qiandao')->field('id')->where('member_id='.$memberid)->find();
	        	if(empty($qd)){
	        		$jifen = 10;
	        	}else{
	        		$jifen = 5;
	        	}
	                //$rs = $m->where($where)->select();
	                $action = D('Qiandao');
					$action1 = D('Member');
					$rs = $action->getQiandao();//获取今日签到记录
					if(empty($rs)){
						$id = $action->addSign($jifen);
						$ary = array(
							'jifentype' => 1,
							'userid' => $memberid,
							'num' => $jifen,
							'addtime' => date('Y-m-d H:i:s'),
							'miaoshu' => "签到获得积分",
							'status' => 1
						);
						M('Jifen')->add($ary);
						if($id){
							$data['status']=1;
							}else{
								$data['status']=0;
								}

						 }else{
						$data['status']=0;
							 }
			}

				$this->ajaxReturn($data);




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
        $Allorders=$action->getAllorder($memberid,'',$ordernos); //全部订单
		
		
//        var_dump($Allorder[0]);
        $Waitepayorder=$action->getAllorder($memberid,1);//待付款
        $Waitesendorder=$action->getAllorder($memberid,2);//待发货
        $sendorder=$action->getAllorder($memberid,3);//已经发货
        $tuiorder=$action->getAllorder($memberid,6);//退货
        $waitepingorder=$action->getAllorder($memberid,7);//已经评价

        $this->assign('allorder', $Allorder);
        $this->assign('Waitepayorder', $Waitepayorder);
        $this->assign('Waitesendorder', $Waitesendorder);
        $this->assign('sendorder', $sendorder);
        $this->assign('tuiorder', $tuiorder);
        $this->assign('waitepingorder', $waitepingorder);
		$this->assign('foottype',4);
		//print_r($Waitepayorder);

        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
	
	
 /**
     *
     *会员中心订单列表页面
     * @author Chandler_qjw  ^_^
     */
    public function ordersend(){

        $orderno=I('get.orderno');
        if($orderno!=''){
            $ordernos=$orderno;
        }
        $memberid=$_SESSION['member_id'];
        $action=D('orderlist');
        $Waitesendorder=$action->getAllorder($memberid,2);//待发货
        $this->assign('Waitesendorder', $Waitesendorder);
		$this->assign('foottype',4);

        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
	
	
	
/**
     *
     *会员中心订单列表页面
     * @author Chandler_qjw  ^_^
     */
    public function dsh(){

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
		$this->assign('foottype',4);


        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
    
    //待收货页面
    public function due(){
        $orderno=I('get.orderno');
        if($orderno!=''){
            $ordernos=$orderno;  
        }
        $memberid=$_SESSION['member_id'];
        $action=D('orderlist');
        $Waitesendorder=$action->getAllorder($memberid,2);//待发货
        $sendorder=$action->getAllorder($memberid,3);//已经发货
        //将两个数据的数组合并并且按照id倒序
        $sendorder = array_merge($Waitesendorder,$sendorder);
        if(is_array($sendorder)){
        	foreach ($sendorder as $key => $value) {
        		$px[$key] = $value['id'];
        	}
        	array_multisort($px,SORT_DESC,$sendorder);
        }
        $this->assign('sendorder', $sendorder);
		$this->assign('foottype',4);

        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
    //待收货页面
    public function wait(){
        $orderno=I('get.orderno');
        if($orderno!=''){
            $ordernos=$orderno;  
        }
        $memberid=$_SESSION['member_id'];
        $action=D('orderlist');
        $Waitesendorder=$action->getAllorder($memberid,2);//待发货
        $sendorder=$action->getAllorder($memberid,3);//已经发货
        //将两个数据的数组合并并且按照id倒序
        $sendorder = array_merge($Waitesendorder,$sendorder);
        if(is_array($sendorder)){
        	foreach ($sendorder as $key => $value) {
        		$px[$key] = $value['id'];
        	}
        	array_multisort($px,SORT_DESC,$sendorder);
        	//查询订单是否是团购，是否组团到时间并且未成功
        	/*foreach($sendorder as $key => $val){
        		if($val['is_groupbuy']==1 && $val['groupbuy_ok']==0 && $val['groupendtime']<time()){
        			//查询失败团购订单的所有参团人
        			$members=$action->where('groupbuyid='.$val['groupbuyid'].' and orderstate !=7 ')->order('id asc')->select();
        			//到了团购时间则退款 并且修改每个人的订单状态
                    foreach($members as $k=>$v)
                    {
                        $succ = $this->refund($v['orderno'],$v['pay_fee']*100);
                        if($succ)
                        {
                            $data['orderstate'] = 6;
                            $data['id'] = $v['id'];
                            $data['groupbuy_ok'] = 2;
                            $data['is_refund'] = 2;
                            $data['refund_fee'] = $v['pay_fee'];
                            $data['refundtime'] = date('Y-m-d H:i:s');
                            $action->save($data);
							
                        }
                    }
                    unset($sendorder[$key]);
        		}
        	}*/
        }
        $this->assign('sendorder', $sendorder);
		$this->assign('foottype',4);

        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
    
    public function refund($no,$fee){
        vendor('Wxpay.WxPayPubHelper.WxPayPubHelper');
        //输入需退款的订单号
        if (!isset($no) || !isset($fee))
        {
            $out_trade_no = " ";
            $refund_fee = "1";
        }else{
            $out_trade_no = $no;
            $refund_fee = $fee;
            //商户退款单号，商户自定义，此处仅作举例
            $out_refund_no = "$out_trade_no"."123";
            //总金额需与订单号out_trade_no对应，demo中的所有订单的总金额为1分
            $total_fee = $fee;
            
            //使用退款接口
            $refund = new \Refund_pub();
            $refund->setParameter("out_trade_no","$out_trade_no");//商户订单号
            $refund->setParameter("out_refund_no","$out_refund_no");//商户退款单号
            $refund->setParameter("total_fee","$total_fee");//总金额
            $refund->setParameter("refund_fee","$refund_fee");//退款金额
            $refund->setParameter("op_user_id",\WxPayConf_pub::MCHID);//操作员
            
            //调用结果
            $refundResult = $refund->getResult();
            if('SUCCESS'==$refundResult['result_code']){
                return true;
            }else{
                return false;
            }
        }
    }
    public function dshcs(){
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
        $this->assign('allorder', $Allorder);
        $this->assign('Waitepayorder', $Waitepayorder);
        $this->assign('Waitesendorder', $Waitesendorder);
        $this->assign('sendorder', $sendorder);
        $this->assign('tuiorder', $tuiorder);
        $this->assign('waitepingorder', $waitepingorder);
		$this->assign('foottype',4);
        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
	
	public function achieve(){
        $memberid=$_SESSION['member_id'];
        $action=D('orderlist');
        $Waitepayorder=$action->getAllorder($memberid,4);
        $this->assign('Waitepayorder', $Waitepayorder);
		$this->assign('foottype',4);


        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
<p style="padding:10px;font-size:16px;color:#666">暂无订单</p></div>');
        $this->display();
    }
	public function achieveorder(){
        $memberid=$_SESSION['member_id'];
        $action=D('orderlist');
        $Waitepayorder=$action->getAllorder($memberid,5);
        $this->assign('Waitepayorder', $Waitepayorder);
		$this->assign('foottype',4);


        $this->assign('empty', ' <div style="padding:50px 0 0;text-align:center;border:0;">
 <img src="/Public/Home/Images/whitenorder.png" width="30%" alt="">
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
	
	
	 public function lxwm(){


        $this->assign('foottype',4);

        $this->display();
    }
	
	
	
	
	
	
	/**
     *
     *订单详情
     * @author Chandler_qjw  ^_^
     */
    public function trade1(){
		
        $orderid=I('get.orderid');
		$action=D('orderlist');
		$rs=$action->getOneorder($orderid);
		
		$this->assign('order_det',$rs);
		$this->assign('foottype',4);
		
        $this->display();
    }
	
	
	
	
	
	


 /**
     *
     *删除订单  (并不是真的删除。而是标记is_delete为1)
     * @author Chandler_qjw  ^_^
     */
    public function delorder(){
        $orderid=I('get.orderid');

        $data['id']=$orderid;
        $data['is_delete']=1;

        $m=M('orderlist');

        $del_order=$m->save($data);
        if($del_order){
            $this->success("支付成功");
        }else{
            $this->error("订单删除失败");

        }


    }


}