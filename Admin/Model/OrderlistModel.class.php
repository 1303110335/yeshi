<?php
namespace Admin\Model;
use Think\Model;
class OrderlistModel extends Model
{


    function orderlist($ordertype)
    {
        $starttime = I('get.starttime');
        $endtime = I('get.endtime');
        $orderno = I('get.orderno');
        $telephone = I('get.telephone');
        $ordertype = I('get.pay_type');
		$dealerid = I('get.dealerid');
		$groupbuy_type = I('get.groupbuy_type');

        if ($dealerid!="") {
            $date['dealerid'] = $dealerid;
        }
		if (!empty($ordertype)) {
			if($ordertype==5){
				$date['orderstate'] = array('gt',4);
				$date['is_groupbuy'] = 0;
			}else if($ordertype==3){
				$date['is_delivery'] = 1;
				//$date['is_groupbuy'] = 0;
				$date['groupbuy_ok'] = 1;
				$date['orderstate'] = 3;
			}else if($ordertype==4){
				$date['orderstate']=array('between',array(3,4));	
				$date['is_groupbuy'] = 0;
			}else if($ordertype==32){
				
			}else{
            	$date['orderstate'] = $ordertype;
				$date['is_groupbuy'] = 0;
				
				
			}
        }
        if (!empty($orderno)) {
            $date['orderno'] = $orderno;
        }
        if (!empty($telephone)) {
            $date['telephone'] = $telephone;
        }
		
		if (!empty($groupbuy_type)) {
            $date['is_groupbuy'] = 1;
            if($groupbuy_type==1){
            	$date['groupbuy_ok'] = 0;
            }
        }
		
        /*if (!empty($endtime)) {
            $date['addtime'] = array('lt', $endtime);
        }
        if (!empty($starttime)) {
            $date['addtime'] = array('gt', $starttime);
        }*/
        if(empty($endtime)){
        	$endtime = date('Y-m-d 23:59:59');
        }
        if(empty($starttime)){
        	$starttime = date('2015-01-01 00:00:00');
        }
        $date['addtime']=array('between',array($starttime,$endtime));

//        var_dump($date);
        if($ordertype == 2)
		{
			$where = "orderstate = {$ordertype} and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0) ) ";
			if($dealerid){
				$where .= " and dealerid=".$dealerid;
			}
			$count = $this->where($where)->count();
			$p = getpage($count, 8);
			$list = $this->field(true)->where($where)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
		}
		elseif($ordertype == 4)
		{
			$where = "(orderstate=4 or (orderstate=3 and is_delivery=0))";
			if($dealerid){
				$where .= " and dealerid=".$dealerid;
			}
			$count = $this->where($where)->count();
			$p = getpage($count, 8);
			$list = $this->field(true)->where($where)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
		}
		else if($ordertype==32){
			$count = $this->where('barcode!="" and is_delivery=0')->count();
			$p = getpage($count, 8);
			$list = $this->where('barcode!="" and is_delivery=0')->order('id desc')->limit($p->firstRow, $p->listRows)->select();
		}
		else
		{
			if($ordertype==1){
				unset($date['is_groupbuy']);
			}

			$count = $this->where($date)->count();
			$p = getpage($count, 8);
			$list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
		}

		

        for ($i = 0; $i < count($list); $i++) {
            $orderid = $list[$i]['id'];

            $actiond = D('orderdetail');

            $rsd = $actiond->getorderdetail($orderid);
            $list[$i]['orderdatail'] = $rsd;
        }

        $date['list'] = $list; // 赋值数据集
		$date['count'] = $count; // 统计个数
        $date['page'] = $p->show();// 赋值分页输出

        return $date;

    }

    function selectGoodsRefund($ary=array()){
    	$where = "";
    	//根据手机号查找
    	if($ary['telephone']){
    		$where .= " and telephone='".$ary['telephone']."'";
    	}
    	//根据订单号查找
    	if($ary['orderno']){
    		$where .= " and orderno='".$ary['orderno']."'";
    	}

        $count = $this->where("is_refund=1".$where)->count();
        $p = getpage($count, 8);
        $list = $this->field(true)->where("is_refund=1".$where)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

        for ($i = 0; $i < count($list); $i++) {
            $orderid = $list[$i]['id'];

            $actiond = D('orderdetail');

            $rsd = $actiond->getorderdetail($orderid);
            $list[$i]['orderdatail'] = $rsd;
        }

        $date['list'] = $list; // 赋值数据集
        $date['count'] = $count; // 统计个数
        $date['page'] = $p->show();// 赋值分页输出

        return $date;
    }

    function getoneorderdetail($oid){
        $date['id']=$oid;
//        var_dump($date);
        $rs = $this->where($date)->find();
        $orderid=$rs['id'];
        $actiond = D('orderdetail');
        $rsd = $actiond->getorderdetail($orderid);

        $rs['orderdatails'] = $rsd;
        return $rs;

    }
	
    function getone($id,$fieldname){

		$rs = $this->where('id='.$id)->getField($fieldname);
		return $rs;
		
    }
	
	
	
    function delivergoods($id){

        $data['id']=$id;
        $data['orderstate']=3;
        $rs=$this->save($data);

        return $rs;
    }
	
    function num_count($orderstate, $dealerid){
		
		
        if (!empty($orderstate)) {
			if($orderstate!=4){
            	$date['orderstate'] = $orderstate;
			}else{
				$date['orderstate']=array('between',array(3,4));
				$date['is_groupbuy'] = 0;
				}
        }
		
        if ($dealerid!="") {
            $date['dealerid'] = $dealerid;
        }
		
		if($orderstate == 4)
		{
			$where = "(orderstate=4 or (orderstate=3 and is_delivery=0))";
			if($dealerid){
				$where .= " and dealerid=".$dealerid;
			}
			$counts=$this->where($where)->count();
		}
		elseif($orderstate==2){
			$where = "orderstate = 2 and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0))";
			if($dealerid){
				$where .= " and dealerid=".$dealerid;
			}
			$counts=$this->where($where)->count();
		}
		elseif($orderstate==5){
			$counts=$this->where("is_refund=2")->count();
		}
		else
		{
			$counts=$this->where($date)->count();
			
		}

		
		return $counts;
        
    }
	
	
	
	function num_count_array($orderstate ,$array, $dealerid){
		
		
        if (!empty($array)) {
			
			foreach($array as $k=>$v){
            $date[$k] = $v;
			}
        }
		
        if ($dealerid!="") {
            $date['dealerid'] = $dealerid;
        }
		
		if($orderstate == "count2")
		{
			$where = "orderstate = 2 and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0))";
			if($dealerid){
				$where .= " and dealerid=".$dealerid;
			}
			$counts=$this->where($where)->count();
		}
		else
		{
			$counts=$this->where($date)->count();
		}
		
		
		
		return $counts;
    }
	
	
	
	function num_count_array_gt($array, $dealerid){
		
		
        if (!empty($array)) {
			
			foreach($array as $k=>$v){
            $date[$k] = array('gt',$v);
			
			}
			$date['is_groupbuy'] = 0;
        }
		
		
        if ($dealerid!="") {
            $date['dealerid'] = $dealerid;
        }
		
		$counts=$this->where($date)->count();

		
		return $counts;
        
    }
	
	
	
	
	
	function search_cout($start_time,$end_time,$dealerid){
		
		$where = "1=1";
		$where2 = "1=1";
		if (!empty($end_time)) {
            //$date['addtime'] = array('lt', $end_time);
			$where .= " and addtime < '$end_time' ";
			$where2 .= " and refundtime < '$end_time' ";
        }
        if (!empty($start_time)) {
            //$date['addtime'] = array('gt', $start_time);
			$where .= " and addtime > '$start_time' ";
			$where2 .= " and refundtime > '$start_time' ";
        }
		
        if ($dealerid!="") {
            $date['dealerid'] = $dealerid;
			$where .= " and dealerid = $dealerid";
			$where2 .= " and dealerid = $dealerid";
        }
		
		if($dealerid!='' && $dealerid==0){
			//$date['dealerid']=0;
			$where .= " and dealerid = 0";
			$where2 .= " and dealerid = 0";
		}
		
		$count2=$this->where($where2)->sum('refund_fee');
		//$count=$count1-$count2;
		
		

		//$date['pay_status'] = 1;
		$where .= " and pay_status = 1";
		$order_num=$this->where($where)->count();
		
		$count1=$this->where($where)->sum('total_price');

		//$date['orderstate']=array('in',array(2,3,4));
		$where .= " and orderstate in (2,3,4)";
		
		$count=$this->where($where)->sum('total_price');
		//$rs['yingli']=$count;
		$rs['total']=$count1;
		$rs['refund_fee']=$count2;
		$rs['order_num']=$order_num;
		$rs['poundage']=$count1*0.006-$count2*0.006;
		//盈利金额应减去微信支付手续费
		$rs['yingli']=$count1-$rs['poundage']-$count2;
		return $rs;
        
    }
	
	
	
	
	
	
	
	//获取本月消费
	function this_count(){
		
		$starttime=date('Y-m',time()).'-01';
		
		$endtime=date('Y-m',time()).'-31';
		
		$where['addtime']=array('between',array($starttime,$endtime));
		$where2['refundtime']=array('between',array($starttime,$endtime));
		
		$count2=$this->where($where2)->sum('refund_fee');
		//$count=$count1-$count2;
		$order_num=$this->where($where)->count();
		
		$where['pay_status'] = 1;

		$count1=$this->where($where)->sum('total_price');

		$where['orderstate']=array('in',array(2,3,4));
		$count=$this->where($where)->sum('total_price');

		$rs['yingli']=$count;
		$rs['total']=$count1;
		$rs['refund_fee']=$count2;
		$rs['order_num']=$order_num;
		
		
		return $rs;
		
    }
	
	
	//获取当日消费
	function thisday_count($dealerid){
		
		if(!empty($dealerid)){$where['dealerid']=$dealerid;$where2['dealerid']=$dealerid;}
		
		if($dealerid!='' && $dealerid==0){$where['dealerid']=0;$where2['dealerid']=0;}
		
		$starttime=date('Y-m-d',time());
		
		$endtime=date('Y-m-d',time()+86400);
		
		$where['addtime']=array('between',array($starttime,$endtime));
		$where2['refundtime']=array('between',array($starttime,$endtime));
		
		$count2=$this->where($where2)->sum('refund_fee');
		//$count=$count1-$count2;
		
		$order_num=$this->where($where)->count();
		$where['pay_status'] = 1;

		$count1=$this->where($where)->sum('total_price');

		$where['orderstate']=array('in',array(2,3,4));
		$count=$this->where($where)->sum('total_price');
		
		$rs['yingli']=$count;
		$rs['total']=$count1;
		$rs['refund_fee']=$count2;
		$rs['order_num']=$order_num;
		
		
		return $rs;
		
    }
	
	

}


 ?>