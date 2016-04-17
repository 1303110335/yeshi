<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class OrderController extends CommonController {
    public function lists(){

        $dealerid = I('get.dealerid');
		$groupbuy_type = I('get.groupbuy_type');
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
        //$count2 = $action->num_count(2,$dealerid);
		$count2 = $action->num_count_array("count2",array('orderstate'=>2,'is_groupbuy'=>0),$dealerid);
        //$count3 = $action->num_count(3,$dealerid);
        $count3 = $action->num_count_array("count3",array('is_delivery'=>1,/*'is_groupbuy'=>0,*/'orderstate'=>3,'groupbuy_ok'=>1),$dealerid);
		
		$count4 = $action->num_count(4,$dealerid);
        //$count5 = $action->num_count(5,$dealerid);
		$count5 = $action->num_count_array_gt(array("orderstate"=>4),$dealerid);
		$count6 = $action->num_count(6,$dealerid);
        $count_group = $action->num_count_array("count_group",array('is_groupbuy'=>1,'groupbuy_ok'=>0),$dealerid);
		//测试
		if(I('ceshi')){
			$this->assign('ceshi',1);
			$count32 = $action->where('barcode!="" and is_delivery=0')->count();
			$this->assign('count32',$count32);
		}
        $action = D('Dealer');
        $dealerlist = $action->getdealerlist_nopage();
        $this->assign('telephone',I('telephone'));
        $this->assign('orderno',I('orderno'));
        $this->assign('starttime',I('starttime'));
        $this->assign('endtime',I('endtime'));
        $this->assign('pay_type',I('pay_type'));
        $this->assign('dealerid',$dealerid);
		$this->assign('groupbuy_type',$groupbuy_type);
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
		$this->assign('count6',$count6);
		$this->assign('count_group',$count_group);
        $this->assign('munetype',4);
        $this->display();
    }
    public function listscs(){
        $dealerid = I('get.dealerid');
		$groupbuy_type = I('get.groupbuy_type');
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
        //$count2 = $action->num_count(2,$dealerid);
		$count2 = $action->num_count_array("count2",array('orderstate'=>2,'is_groupbuy'=>0),$dealerid);
        //$count3 = $action->num_count(3,$dealerid);
        $count3 = $action->num_count_array("count3",array('is_delivery'=>1,/*'is_groupbuy'=>0,*/'orderstate'=>3,'groupbuy_ok'=>1),$dealerid);
		$count4 = $action->num_count(4,$dealerid);
        //$count5 = $action->num_count(5,$dealerid);
		$count5 = $action->num_count_array_gt(array("orderstate"=>4),$dealerid);
		$count6 = $action->num_count(6,$dealerid);
        $count_group = $action->num_count_array("count_group",array('is_groupbuy'=>1,'groupbuy_ok'=>0),$dealerid);
		
        $action = D('Dealer');
        $dealerlist = $action->getdealerlist_nopage();
        $this->assign('telephone',I('telephone'));
        $this->assign('orderno',I('orderno'));
        $this->assign('starttime',I('starttime'));
        $this->assign('endtime',I('endtime'));
        $this->assign('pay_type',I('pay_type'));
        $this->assign('dealerid',$dealerid);
		$this->assign('groupbuy_type',$groupbuy_type);
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
		$this->assign('count6',$count6);
		$this->assign('count_group',$count_group);
        $this->assign('munetype',4);
        $this->display();
    }
    public function excel(){
		$dealerid = I('get.dealerid');
        $where = "orderstate=2 and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0) ) ";
		if($dealerid){
			$where .= " and dealerid=".$dealerid;
		}
		$list = M('Orderlist')->where($where)->order('id desc')->select();
        $actiond = D('orderdetail');
		$m = M('Dealer');
		for ($i = 0; $i < count($list); $i++) {
            $list[$i]['orderdatail'] = $actiond->getorderdetail($list[$i]['id']);
			if($list[$i]['dealerid']!=0){
                $dealername = $m->field('username')->where('id='.$list[$i]['dealerid'])->select();
                $list[$i]['dealername'] = $dealername[0]['username'];
            }else{
                $list[$i]['dealername'] = "总部";
            }
        }
		$expFileName="代发货订单";
		$expCellName = array(
            array('1','用户名称'),
            array('2','用户手机'),
			array('13','收货地址'),
            array('3','商品名称'),
            array('4','商品单价'),
            array('5','商品数量'),
            array('6','订单编号'),
            array('7','订单总价'),
            array('8','订单运费'),
            array('9','商品类型'),
            array('10','买家留言'),
            array('11','经销商'),
            array('12','日期')
        );
		$expTableData = array();
		foreach($list as $key => $val){
			if($val['is_groupbuy'] == 0){
				$val['groupbuytype'] = "单买";
			}elseif($val['groupbuy_ok'] == 0){
				$val['groupbuytype'] = "团购中";
			}elseif($val['groupbuy_ok'] == 1){
				$val['groupbuytype'] = "拼团成功";
			}elseif($val['groupbuy_ok'] == 2){
				$val['groupbuytype'] = "拼团失败";
			}
			array_push($expTableData,
				array(
					'1'=>$val['consignee'],
					'2'=>$val['telephone'],
					'3'=>$val['orderdatail'][0]['goodsname'],
					'4'=>$val['orderdatail'][0]['good_nowprice'],
					'5'=>$val['orderdatail'][0]['num'],
					'6'=>$val['orderno']."’",
					'7'=>$val['total_price'],
					'8'=>$val['shipping_fee'],
					'9'=>$val['groupbuytype'],
					'10'=>$val['comment'],
					'11'=>$val['dealername'],
					'12'=>$val['addtime'],
					'13'=>$val['province'].$val['city'].$val['address'],
				)
			);
		}
        exportExcel(date('Ymd',time()),$expFileName,$expCellName,$expTableData);
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
                        'refundtime' => Gettime()
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
    
    public function orderdel()
    {
        $oid=I('get.id');
        
        $oids=I('get.ids');
        $action=D('orderlist');
        
        $data['id']=array ('in',$oids);
        $date['orderid']=array ('in',$oids);
        
        if($oid){
            
        $result=$action->where('id='.$oid)->delete();
        $result1=M('orderdetail')->where('orderid='.$oid)->delete();
        }else{
            $result=$action->where($data)->delete();
        $result1=M('orderdetail')->where($date)->delete();
            }
        if($result && $result1){
            $this->success('删除成功',U('/Admin/Order/lists'));exit;
        }else{
            $this->error('删除失败');
        }
        
        
        $this->assign('munetype',4);
        $this->display('lists');
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

    public function refund($no,$fee,$total_price){
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
			$total_fee = $total_price;
            
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
/*            if ($refundResult["return_code"] == "FAIL") {
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
        $arr['shippingtime'] = date("Y-m-d H:i:s");
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
	
	 public function checktuikuan()
	 {
		$orderno = I("get.no");
		$pay_fee = "9.9";
		$succ = $this->refund($orderno,$pay_fee*100,$pay_fee*100);
		echo $succ;
		
		
	}
	
    public function checkgroup()
    {
        
        $id = I("get.id");
		$action=D('Orderlist');
		$m = M('orderdetail');
		
		$rs = $m->where('orderid='.$id)->select();

		$count=$action->where('groupbuyid='.$id.' and orderstate !=7 ')->count();
        $count=$rs[0]['group_num']-$count;
		
		$members=$action->where('groupbuyid='.$id.' and orderstate !=7 ')->order('id asc')->select();
		
		//判断该团购订单是否已经处理(成功,失败)
        if(0==$members[0]['groupbuy_ok']){
            //判断参团人数是否已经满足  当count小于0则代表参团人数已达上限
            if($count<=0)
            {
                foreach($members as $key=>$value)
                {
                    $data['id'] = $value['id'];
                    $data['groupbuy_ok'] = 1;
                    M("Orderlist")->save($data);
					if($value['code']!="")
					{
						sendmessage(2,$value['telephone'],$value['code'],$value['orderno']);
					}  
                }
                echo "OK1";
				//header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
            }else{
                //如果参团人数不满足 则判断该团购的时间是否已到达
                if($rs[0]['groupendtime']<time())
                {
                    
					//到了团购时间则退款 并且修改每个人的订单状态
                    foreach($members as $k=>$v)
                    {
                        $succ = $this->refund($v['orderno'],$v['pay_fee']*100,$v['pay_fee']*100);
                        if($succ)
                        {
                            $data['orderstate'] = 6;
                            $data['id'] = $v['id'];
                            $data['groupbuy_ok'] = 2;
                            $data['is_refund'] = 2;
                            $data['refund_fee'] = $v['pay_fee'];
                            $data['refundtime'] = Gettime();
                            $action->save($data);
							
                        }
                    }
					echo "FALSE1";
					//$end=6;
                }
            }
        }elseif(1==$members[0]['groupbuy_ok']){
            echo "OK2";
			//header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
        }else{
		    echo "FALSE2";
			//$end=6;
		}

    }
	
    
    public function confirmreceipt()
    {
        

        $code=I('post.code');
        $arr['id']=I('post.orderid');
        $action = D('Orderlist');
        $ordercode = $action->getone($arr['id'],'code');
        
        if($code == $ordercode)
        {
            //查询该订单是否有申请退款
            $is_refund = $action->where("id=".$arr['id'])->getField('is_refund');

            $action = D('User');
            $confirm_name = $action->getone('realname');
            $arr['confirmtime'] = Gettime();
            $arr['confirm_status'] = 1;
            $arr['orderstate'] = 4;
            $arr['is_code'] = 1;
            $arr['confirm_name'] = $confirm_name;

            if($is_refund==1){
                //有申请退款则修改为已收货取消申请退款
                $arr['is_refund'] = 4;
            }
            
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

    public function confirmreceiptcs()
    {
        

        $code=I('post.code');
        $arr['id']=I('post.orderid');
        $action = D('Orderlist');
        $ordercode = $action->getone($arr['id'],'code');
        if($code == $ordercode)
        {
            //查询该订单是否有申请退款
            $is_refund = $action->where("id=".$arr['id'])->getField('is_refund');

            $action = D('User');
            $confirm_name = $action->getone('realname');
            $arr['confirmtime'] = Gettime();
            $arr['confirm_status'] = 1;
            $arr['orderstate'] = 4;
            $arr['is_code'] = 1;
            $arr['confirm_name'] = $confirm_name;

            if($is_refund==1){
                //有申请退款则修改为已收货取消申请退款
                $arr['is_refund'] = 4;
            }
            echo $is_refund;exit;
            /*$m = M('Orderlist');
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
            } */  
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
        //判断是否有手机号的条件
        $ary = array();
        if(I('get.telephone')){
            $ary['telephone'] = I('get.telephone');
        }
        //判断是否有订单号的条件
        if(I('get.orderno')){
            $ary['orderno'] = I('get.orderno');
        }
        $orderlist=$action->selectGoodsRefund($ary);

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
    //测试
    public function goodsRefundcs(){
        $action=D('orderlist');
        //判断是否有手机号的条件
        $ary = array();
        if(I('get.telephone')){
            $ary['telephone'] = I('get.telephone');
        }
        //判断是否有订单号的条件
        if(I('get.orderno')){
            $ary['orderno'] = I('get.orderno');
        }
        $orderlist=$action->selectGoodsRefund($ary);

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
                        if($type==2){
                            $orderno = $orderlistModel->where("id={$id}")->getField('orderno');
    						$total_price = $orderlistModel->where("id={$id}")->getField('total_price');
                            $succ = $this->refund($orderno,$money*100,$total_price*100);
                        }else{
                            $succ = true;
                        }
                        if($succ){
                            $orderlistModel->is_refund = $type;
                            if($type==2){
                                $orderlistModel->refund_fee = $money;
                                $orderlistModel->orderstate = 5;
                                $orderlistModel->is_refund = 2;
                                $orderlistModel->refundtime = Gettime();
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

    //标记是否无货
    public function goodsnoexist(){
        if(I('post.states')==2){
            $states = 2;//无货
        }else{
            $states = 1;//有货
        }
        $res = M('Orderlist')->where("id=%d",I('post.id',0,'int'))->setField("is_exist",$states);
        if($res){
            echo 1;
        }else{
            echo 2;
        }
    }
    

}