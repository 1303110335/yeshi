<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class OrderController extends CommonController {
    public function lists(){

		$dealerid = I('get.dealerid');
		$ordertype = I('get.pay_type');
        
		$action=D('orderlist');
        $orderlist=$action->orderlist();
		
		$m = M('Dealer');
		foreach( $orderlist['list'] as $key => $value )
		{
			if($value['dealerid']!=0)
			{
				$dealername = $m->field('username')->where('id='.$value['dealerid'])->select();
				$orderlist['list'][$key]['dealername'] = $dealername[0]['username'];
			}
			else
			{
				$orderlist['list'][$key]['dealername'] = "总部";
			}
		}
		
		$counts = $action->num_count('',$dealerid);
		$count1 = $action->num_count(1,$dealerid);
		$count2 = $action->num_count(2,$dealerid);
		$count3 = $action->num_count(3,$dealerid);
		$count4 = $action->num_count(4,$dealerid);
		$count5 = $action->num_count(5,$dealerid);
		
		$action = D('Dealer');
		$dealerlist = $action->getdealerlist_nopage();

        $this->assign('dealerid',$dealerid);
		$this->assign('dealerlist',$dealerlist);
		$this->assign('orderlist',$orderlist['list']);
        $this->assign('empty',' <tr> <td colspan="5">暂无信息</td>  </tr>');
        $this->assign('page',$orderlist['page']);
		$this->assign('pay_type',$ordertype);
		$this->assign('count',$orderlist['count']);
		$this->assign('counts',$counts);
		$this->assign('count1',$count1);
		$this->assign('count2',$count2);
		$this->assign('count3',$count3);
		$this->assign('count4',$count4);
		$this->assign('count5',$count5);
        $this->assign('munetype',4);
        $this->display();
    }
	
    public function retreatgoods(){
        $oid = I('get.id',0,'int');
        $where = ($oid==0)?"":" and a.id=".$oid;
        $list = M('Orderlist')->field('a.id,a.userid,a.orderno,a.telephone phone,a.total_price,a.addtime,a.paytype,a.tuihuo,a.tuihuoyuanyin,b.telephone,b.truename,b.dengji,b.addtime zcsj,c.pic')->alias('a')->join('yd_member b on a.userid=b.id')->join('yd_goods c on a.goods_id=c.id')->where('(a.tuihuo=0 or a.tuihuo=5)'.$where)->order('a.id desc')->select();
        if($list){
            foreach ($list as $key => $vo) {
                $list[$key]['pics'] = explode(',',$vo['pic']);
            }
        }
        $this->assign('list',$list);
        if($oid==0){
            $this->display();
        }else{
            $this->display("retreatgood");
        }
    }

    public function retreat(){
        $oid = I('get.id',0,'int');
        $uid = I('get.uid',0,'int');
        $type = I('get.type',0,'int');
        $status = I('get.status',0,'int');
        if($status=='1'||$status=='2'){
            $ret = M('Orderlist')->where('id=%d',$oid)->setField('tuihuo',$status);
        }else if($status=='3'||$status=='4'){
            if($status=='3'){
                $succ = true;
                if($type!=1){
                    $orderModel = M('Orderlist')->field("orderno,total_price")->where("id=%d",$oid)->find();
                    $succ = $this->refund($orderModel['orderno'],$orderModel['total_price']*100);
                }else{
                    $total_price = M('Orderlist')->where('id=%d',$oid)->getField('total_price');
                    $succ = M('Member')->where('id=%d',$uid)->setInc('balance',$total_price);
                }
                if($succ){
                    $ary = array(
                        'tuihuo' => $status,
                        'orderstate' => 6,
                    );
                    $ret = M('Orderlist')->where('id=%d',$oid)->save($ary);
                }
            }
        }
        if($ret){
            $y_num = M('Jifen')->where('orderid=%d',$oid)->getField('num');
            if(0<$y_num){
                M('Member')->where('id=%d',$uid)->setDec('jifen',$y_num);
            }
            $this->assign('zhuangtai',"修改成功!");
            $this->success('',U('/Admin/Order/retreatgoods'));
        }else{
            $this->error("修改失败!");
        }
    }

    public function orderdetail()
    {
        $oid=I('get.id');
		$dealerid=I('get.dealerid');
        $action=D('orderlist');
        $result=$action->getoneorderdetail($oid);
		$action = D('Dealer');
		$dealerlist = $action->getdealerlist_nopage();
		
        /*$huase['huases'] = explode(',',$huase['huase']);
        $huase['huasenames'] = explode(',',$huase['huasename']);*/
        //$result['color'] = str_replace('#','',$result['color']);
        $this->assign('dealerid',$dealerid);
		$this->assign('orderdetail',$result);
        $this->assign('dealerlist',$dealerlist);
		$this->assign('munetype',4);
        $this->display();
    }
    public function delivergoods()
    {
        $oid=I('get.id');
        $action=D('orderlist');
        $result=$action->delivergoods($oid);
        if($result){
            $this->success('修改成功',U('/Admin/Order/lists'));
        }else{
            $this->error('添加失败');
        }

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
            //设置必填参数
            //appid已填,商户无需重复填写
            //mch_id已填,商户无需重复填写
            //noncestr已填,商户无需重复填写
            //sign已填,商户无需重复填写
            $refund->setParameter("out_trade_no","$out_trade_no");//商户订单号
            $refund->setParameter("out_refund_no","$out_refund_no");//商户退款单号
            $refund->setParameter("total_fee","$total_fee");//总金额
            $refund->setParameter("refund_fee","$refund_fee");//退款金额
            $refund->setParameter("op_user_id",\WxPayConf_pub::MCHID);//操作员
            //非必填参数，商户可根据实际情况选填
            //$refund->setParameter("sub_mch_id","XXXX");//子商户号 
            //$refund->setParameter("device_info","XXXX");//设备号 
            //$refund->setParameter("transaction_id","XXXX");//微信订单号
            
            //调用结果
            $refundResult = $refund->getResult();
            //商户根据实际情况设置相应的处理流程,此处仅作举例
            /*if ($refundResult["return_code"] == "FAIL") {
                echo "通信出错：".$refundResult['return_msg']."<br>";
            }
            else{
                echo "业务结果：".$refundResult['result_code']."<br>";
                echo "错误代码：".$refundResult['err_code']."<br>";
                echo "错误代码描述：".$refundResult['err_code_des']."<br>";
                echo "公众账号ID：".$refundResult['appid']."<br>";
                echo "商户号：".$refundResult['mch_id']."<br>";
                echo "子商户号：".$refundResult['sub_mch_id']."<br>";
                echo "设备号：".$refundResult['device_info']."<br>";
                echo "签名：".$refundResult['sign']."<br>";
                echo "微信订单号：".$refundResult['transaction_id']."<br>";
                echo "商户订单号：".$refundResult['out_trade_no']."<br>";
                echo "商户退款单号：".$refundResult['out_refund_no']."<br>";
                echo "微信退款单号：".$refundResult['refund_idrefund_id']."<br>";
                echo "退款渠道：".$refundResult['refund_channel']."<br>";
                echo "退款金额：".$refundResult['refund_fee']."<br>";
                echo "现金券退款金额：".$refundResult['coupon_refund_fee']."<br>";
            }*/
            if('SUCCESS'==$refundResult['result_code']){
                return true;
            }else{
                return false;
            }
        }
    }
	
    public function addshipping()
    {
        
		$action = D('User');
		$shipping_name = $action->getone('realname');
		
		$arr['id']=I('post.orderid');
		$arr['expressname']=I('post.expressname');
		$arr['expressno']=I('post.expressno');
		$arr['shippingtime'] = Gettime();
		$arr['shipping_status'] = 1;
		$arr['orderstate'] = 3;
		$arr['shipping_name'] = $shipping_name;
		
		$m = M('Orderlist');
		$rs = $m->save($arr);

		if($rs!=false)
		{	
			$data['info']   =   '修改成功'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
		}
		else
		{
			$data['info']   =   '修改失败'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
		}
		
 		$this->ajaxReturn($data);
        return;

    }
	
    public function confirmreceipt()
    {
        

		$code=I('post.code');
		$arr['id']=I('post.orderid');
		$action = D('Orderlist');
		$ordercode = $action->getone($arr['id'],'code');
		
		if($code == $ordercode)
		{
			$action = D('User');
			$confirm_name = $action->getone('realname');
			$arr['confirmtime'] = Gettime();
			$arr['confirm_status'] = 1;
			$arr['orderstate'] = 4;
			$arr['is_code'] = 1;
			$arr['confirm_name'] = $confirm_name;
			
			$m = M('Orderlist');
			$rs = $m->save($arr);
	
			if($rs!=false)
			{	
				$data['info']   =   '验证成功'; // 提示信息内容
				$data['status'] =   1;  // 状态 如果是success是1 error 是0
			}
			else
			{
				$data['info']   =   '修改失败'; // 提示信息内容
				$data['status'] =   0;  // 状态 如果是success是1 error 是0
			}	
		}
		else
		{
				$data['info']   =   '验证码错误'; // 提示信息内容
				$data['status'] =   0;  // 状态 如果是success是1 error 是0
		}
		
 		$this->ajaxReturn($data);
        return;

    }

    public function goodsRefund(){
        $action=D('orderlist');
        $orderlist=$action->selectGoodsRefund();

        $action = D('Dealer');
        $dealerlist = $action->getdealerlist_nopage();

    
        $this->assign('dealerlist',$dealerlist);
        
        $m = M('Dealer');
        foreach( $orderlist['list'] as $key => $value )
        {
            if($value['dealerid']!=0)
            {
                $dealername = $m->field('username')->where('id='.$value['dealerid'])->select();
                $orderlist['list'][$key]['dealername'] = $dealername[0]['username'];
            }
            else
            {
                $orderlist['list'][$key]['dealername'] = "总部";
            }
        }
        $this->assign('orderlist',$orderlist['list']);
        $this->assign('empty',' <tr> <td colspan="5">暂无信息</td>  </tr>');
        $this->assign('page',$orderlist['page']);
        $this->assign('pay_type',$ordertype);
        $this->assign('count',$orderlist['count']);

        $this->display();
    }
    public function goodsRefundDetail(){
        $id = I('get.id',0,'int');
        $action = D('Dealer');
        $dealerlist = $action->getdealerlist_nopage();

        $this->assign('dealerlist',$dealerlist);

        $rs = M('orderlist')->where("id={$id} and is_refund=1")->find();
        $orderid=$rs['id'];
        $actiond = D('orderdetail');
        $rsd = $actiond->getorderdetail($orderid);

        $rs['orderdatails'] = $rsd;

        $this->assign('orderdetail',$rs);

        $this->display();
    }
    public function orderUpdateRefund(){
        if(IS_POST){
            $id = I('post.id',0,'int');
            $type = I('post.type',0,'int');
            $money = I('post.money',0,'float');
            if($type==2 || $type==3){
                $orderlistModel = M('orderlist');
                if($money>0 || $type==3){
                    if($type==2){
                        $price = $orderlistModel->where("id={$id} and is_refund=1")->getField('total_price');
                    }else{
                        $money = 0;$price = 1;
                    }
                    if($money<=$price){
                        $orderlistModel->is_refund = $type;
                        if($type==2){
                            $orderlistModel->refund_fee = $money;
                            $orderlistModel->orderstate = 5;
                        }
                        $ret = $orderlistModel->where("id={$id}")->save();
                        if($ret){
                            //$this->success('操作完成');
                            echo 1;
                        }else{
                            //$this->success('操作失败');
                            echo 0;
                        }
                    }else{
                        //$this->error("退款金额不能大于商品总价");
                        echo 2;
                    }
                }else{
                    //$this->error("请输入正确的金额");
                    echo 3;
                }
            }
        }
    }

}