<?php

namespace Home\Controller;

use Home\Controller\CommonController;

class OrderController extends CommonController {

    public function order(){

//        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></ script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></ script>','utf-8');

        $this->display();

    }

    public function addorder(){

        $goodsid=I('get.goodsid');
        $price=I('get.price');
		$old_price=I('get.old_price');
        $weight=$_GET['weight'];
		$guige=$_GET['guige'];
        $memberid=$_SESSION['member_id'];
        if(empty($memberid)){
            $url = U('Home/Order/addorder',array('goodsid'=>$goodsid,'price'=>$price,'weight'=>$weight,'old_price'=>$old_price,'guige'=>$guige));
            session('zhuceerror',$url);
            $this->error("请先登陆!",U("Home/Member/weixin",array('tiaozhuan'=>1)));
        }
		$action=D('orderaddress');
		$addresscount = $action->addresscount($memberid);
		if($addresscount==0)
		{	
			$url = U('Home/Order/addorder',array('goodsid'=>$goodsid,'price'=>$price,'weight'=>$weight,'old_price'=>$old_price,'guige'=>$guige));
            session('addresserror',$url);
			$this->redirect('Home/Order/address',array('goodsid'=>$goodsid,'price'=>$price,'weight'=>$weight,'old_price'=>$old_price,'guige'=>$guige));
		}

        $action=D('Goods');

        $rs=$action->getGoodsdetail($goodsid,$price);

		$action=D('store');

        $store=$action->select();

        $rs['pics'] = explode(',',$rs['pic']);

        $member = D("Member")->GetInfomation($memberid);

        $action=D("orderaddress");

        if(!empty($member['def'])){
            $rsaddress=$action->getOneaddress($member['def']);
            $this->assign('addressdetail',$rsaddress);

            $dingwei = explode(',', $rsaddress['dingwei']);
            $this->assign('xz',$dingwei[0]);
            $this->assign('yz',$dingwei[1]);
        }
        $dealer = M('dealer')->field('id,longitude,latitude')->select();
        $ary = array();
        foreach($dealer as $key => $val){
            /*$longitude = explode(',',substr($val['longitude'],0,-1));
            $latitude = explode(',',substr($val['latitude'],0,-1));
            $temp = array();
            for($i=0;$i<count($longitude);$i++){
                $temp[$i] = array($longitude[$i],$latitude[$i]);
            }
            $ary[$key] = $temp;*/
            $ary[$key]['longitude'] = substr($val['longitude'],0,-1);
            $ary[$key]['latitude'] = substr($val['latitude'],0,-1);
            $aryids[$key] = $val['id'];
        }
        $this->assign('aryids',$aryids);
        $this->assign('dealerlist',$ary);
        $this->assign('dealerlistcot',count($ary));

        $memberinfo=$this->GetInfo($memberid);

        if(is_array($rsaddress)){

            $this->assign('succ','0000');

        }

        $totalprice= $rs['good_nowprice']*$num;

        $jifen = sprintf('%.3f',$totalprice/10);

        if(0.001>$jifen){

            $kyjf = 0;

            $jifen = 0;

        }else{

            if($jifen>$member['jifen']){

                $kyjf = $member['jifen'];

            }else{

                $kyjf = $jifen;

            }

        }



        $rs['yunfei'] = 0;
            $def = M('member')->where("id={$memberid}")->getField('def');
            $freightId = 0;
            if($def){

                $province = M('orderaddress')->where("id=$def")->getField('province');

                if($province){

                    $freight = M('freight')->where("privonce like '%{$province}%'")->find();
                    $freightId = $freight['id'];
                    $this->assign('freightId',$freightId);
                    if($freight){

                        $num = $rs['weight'] - $freight['sweight'];

                        if($num>0){

                            $num = ceil($num);

                            $rs['yunfei'] = $freight['sprice']+ceil($num)*$freight['xwprice'];

                        }else{

                            $rs['yunfei'] = $freight['sprice'];

                        }

                    }

                }

            }
        $rs['newprice'] = $rs['yunfei']+$rs['guige_price'];
        //同城配送的运费
            $freight2 = M('freight')->where("is_samecity=1")->find();
            $rs['yunfei2'] = 0;
            if($freight2){
                $num = $rs['weight'] - $freight2['sweight'];
                if($num>0){
                    $num = ceil($num);
                    $rs['yunfei2'] = $freight2['sprice']+ceil($num)*$freight2['xwprice'];
                }else{
                    $rs['yunfei2'] = $freight2['sprice'];
                }
            }

        $rs['newprice2'] = $rs['yunfei2']+$rs['guige_price'];

        

        $coupon = M('coupon')->field("a.*")->alias('a')->join('ys_coupon_record b on a.id=b.coupon_id')

            ->where("b.user_id={$memberid} and b.is_use=0 and a.end_time>".date('Y-m-d',time()))->select();

        $this->assign('coupon',$coupon);

        

        $this->assign('jifen',$jifen);//可获得的积分

        $this->assign('kyjf',$kyjf);//可使用的积分

        $_SESSION['orderurl']=$_SERVER['REQUEST_URI'];

        $this->assign('goodsdetail',$rs);

        $this->assign('balance',$memberinfo['balance']);

        $this->assign('goodsid',$goodsid);

        $this->assign('price',$price);

        $this->assign('guige',$guige);

		$this->assign('store',$store);

        $this->assign('totalprice',$totalprice);
        
            $this->display();
            //$this->display("addorderCeSshi");
       

    }

    public function ajaxQueryCoupon(){

        $id = I('post.id',0,'int');

        $ret = M('coupon')->where("id=%d",$id)->find();

        if($ret){

            if(!empty($ret['goods_id'])){

                $ret['goods_ids'] = explode(',',$ret['goods_id']);

            }

            echo json_encode($ret);

        }else{

            echo 0;

        }

    }

	public function addorder1(){

        $goodsid=I('get.goodsid');
        $price=I('get.groupprice');
        $group_num=$_GET['group_num'];
		$old_price=$_GET['old_price'];
		$weight=$_GET['weight'];
		$guige=$_GET['guige'];
        $memberid=$_SESSION['member_id'];
        if(empty($memberid)){
            $url = U('Home/Order/addorder1',array('goodsid'=>$goodsid,'groupprice'=>$price,'group_num'=>$group_num,'old_price'=>$old_price,'weight'=>$weight,'guige'=>$guige));
            session('zhuceerror',$url);
            $this->error("请先登陆!",U("Home/Member/weixin",array('tiaozhuan'=>1)));
        }
		$action=D('orderaddress');
		$addresscount = $action->addresscount($memberid);
		if($addresscount==0)
		{	
			$url = U('Home/Order/addorder1',array('goodsid'=>$goodsid,'groupprice'=>$price,'group_num'=>$group_num,'old_price'=>$old_price,'weight'=>$weight,'guige'=>$guige));
            session('addresserror',$url);
			$this->redirect('Home/Order/address',array('goodsid'=>$goodsid,'groupprice'=>$price,'group_num'=>$group_num,'old_price'=>$old_price,'weight'=>$weight,'guige'=>$guige));
		}

        $action=D('Goods');

		//查询限购

        $restriction_num = $action->where("id=%d and is_restriction=1",$goodsid)->getField('restriction_num');
        if($restriction_num)
		{
            $yigounum = M('orderlist')->where("userid={$memberid} and orderstate!=6 and goods_id={$goodsid} and is_head=1")->count();
            if($restriction_num<=$yigounum)
			{
                $this->error("团购已达限购次数!");exit;
            }
        }

        $rs=$action->getGoodsdetail2($goodsid,$price);

		$action=D('store');

        $store=$action->select();

        $rs['pics'] = explode(',',$rs['pic']);

        $member = D("Member")->GetInfomation($memberid);

        $action=D("orderaddress");

        if(!empty($member['def'])){

            $rsaddress=$action->getOneaddress($member['def']);
            $dingwei = explode(',', $rsaddress['dingwei']);
            $this->assign('xz',$dingwei[0]);
            $this->assign('yz',$dingwei[1]);
            $this->assign('addressdetail',$rsaddress);

        }
        $dealer = M('dealer')->field('id,longitude,latitude')->select();
        $ary = array();
        foreach($dealer as $key => $val){
            /*$longitude = explode(',',substr($val['longitude'],0,-1));
            $latitude = explode(',',substr($val['latitude'],0,-1));
            $temp = array();
            for($i=0;$i<count($longitude);$i++){
                $temp[$i] = array($longitude[$i],$latitude[$i]);
            }
            $ary[$key] = $temp;*/
            $ary[$key]['longitude'] = substr($val['longitude'],0,-1);
            $ary[$key]['latitude'] = substr($val['latitude'],0,-1);
            $aryids[$key] = $val['id'];
        }
        $this->assign('aryids',$aryids);
        $this->assign('dealerlist',$ary);
        $this->assign('dealerlistcot',count($ary));
		
        $memberinfo=$this->GetInfo($memberid);

        if(is_array($rsaddress)){

            $this->assign('succ','0000');

        }

        $totalprice= $rs['good_nowprice']*$num;

        $jifen = sprintf('%.3f',$totalprice/10);

        if(0.001>$jifen){

            $kyjf = 0;

            $jifen = 0;

        }else{

            if($jifen>$member['jifen']){

                $kyjf = $member['jifen'];

            }else{

                $kyjf = $jifen;

            }

        }



        $rs['yunfei'] = 0;
            $def = M('member')->where("id={$memberid}")->getField('def');
            $freightId = 0;
            if($def){

                $province = M('orderaddress')->where("id=$def")->getField('province');

                if($province){

                    $freight = M('freight')->where("privonce like '%{$province}%'")->find();

                    if($freight){
                    $freightId = $freight['id'];
                    $this->assign('freightId',$freightId);
                        $num = $rs['weight'] - $freight['sweight'];

                        if($num>0){

                            $num = ceil($num);

                            $rs['yunfei'] = $freight['sprice']+ceil($num)*$freight['xwprice'];

                        }else{

                            $rs['yunfei'] = $freight['sprice'];

                        }

                    }

                }

            }

        $rs['newprice'] = $rs['yunfei']+$rs['guige_price'];


            //同城配送的运费
            $freight2 = M('freight')->where("is_samecity=1")->find();
            $rs['yunfei2'] = 0;
            if($freight2){
                $num = $rs['weight'] - $freight2['sweight'];
                if($num>0){
                    $num = ceil($num);
                    $rs['yunfei2'] = $freight2['sprice']+ceil($num)*$freight2['xwprice'];
                }else{
                    $rs['yunfei2'] = $freight2['sprice'];
                }
            }

        $rs['newprice2'] = $rs['yunfei2']+$rs['guige_price'];
        $this->assign('jifen',$jifen);//可获得的积分

        $this->assign('kyjf',$kyjf);//可使用的积分

        $_SESSION['orderurl']=$_SERVER['REQUEST_URI'];

//        var_dump($orderurl);

        /*$huase = M('Goods_huase')->where('goodsid='.$gid)->getField('huasename');

        if($huase){

            $huase = explode(',',$huase);

        }

        $this->assign('huase',$huase);*/

        $this->assign('goodsdetail',$rs);

        $this->assign('balance',$memberinfo['balance']);

        $this->assign('goodsid',$goodsid);

        $this->assign('price',$price);

		$this->assign('group_num',$group_num);

        $this->assign('guige',$guige);

		$this->assign('store',$store);

        $this->assign('totalprice',$totalprice);
        $this->display();

    }

	

	public function addorder2(){

        $orderid=I('get.orderid');
		$orderlist_m=M('orderlist');
		$goods_m=M('goods');
		$goods_guige_m=M('goods_guige');
		$orderlist_con=$orderlist_m->where('id='.$orderid)->find();
		$goods_con=$goods_m->where('id='.$orderlist_con['goods_id'])->find();
		$goods_guige_con=$goods_guige_m->where('goodsid='.$goods_con['id'])->find();
		$goodsid=$goods_con['id'];
        $price=$goods_guige_con['groupprice'];
        $group_num=$goods_con['group_num'];
		$old_price=$goods_guige_con['old_price'];
		$weight=$goods_guige_con['weight'];
		$guige=$goods_guige_con['guige'];
        $memberid=$_SESSION['member_id'];
		
        if(empty($memberid)){
            $url = U('Home/Order/addorder2',array('orderid'=>$orderid));
            session('zhuceerror',$url);
            $this->error("请先登陆!",U("Home/Member/weixin",array('tiaozhuan'=>1)));
        }
		$action=D('orderaddress');
		$addresscount = $action->addresscount($memberid);
		if($addresscount==0)
		{	
			$url = U('Home/Order/addorder2',array('orderid'=>$orderid));
            session('addresserror',$url);
			$this->redirect('Home/Order/address',array('orderid'=>$orderid));
		}

        $action=D('Goods');
        $rs=$action->getGoodsdetail2($goodsid,$price);
		$action=D('store');
        $store=$action->select();
        $rs['pics'] = explode(',',$rs['pic']);
        $member = D("Member")->GetInfomation($memberid);
        $action=D("orderaddress");
        if(!empty($member['def']))
		{
            $rsaddress=$action->getOneaddress($member['def']);
            $this->assign('addressdetail',$rsaddress);
            $dingwei = explode(',', $rsaddress['dingwei']);
            $this->assign('xz',$dingwei[0]);
            $this->assign('yz',$dingwei[1]);
        }
		
        $dealer = M('dealer')->field('id,longitude,latitude')->select();
        $ary = array();
        foreach($dealer as $key => $val)
		{
            $ary[$key]['longitude'] = substr($val['longitude'],0,-1);
            $ary[$key]['latitude'] = substr($val['latitude'],0,-1);
            $aryids[$key] = $val['id'];
        }
        $this->assign('aryids',$aryids);
        $this->assign('dealerlist',$ary);
        $this->assign('dealerlistcot',count($ary));
        $memberinfo=$this->GetInfo($memberid);
        if(is_array($rsaddress))
		{
            $this->assign('succ','0000');
        }
        $totalprice= $rs['good_nowprice']*$num;
        $jifen = sprintf('%.3f',$totalprice/10);
        if(0.001>$jifen)
		{
            $kyjf = 0;
            $jifen = 0;
        }
		else
		{
            if($jifen>$member['jifen'])
			{
                $kyjf = $member['jifen'];
            }
			else
			{
                $kyjf = $jifen;
            }
        }	
       
		// 普通模式下的运费价格
		$rs['yunfei'] = 0;
		//默认地址
        $def = M('member')->where("id={$memberid}")->getField('def');
		//如果有默认地址
        $freightId = 0;
		if($def)
		{
			$province = M('orderaddress')->where("id=$def")->getField('province');
			if($province)
			{
				// 获取运费模板里面的重量计算方式
				$freight = M('freight')->where("privonce like '%{$province}%'")->find();
				if($freight)
				{
                    $freightId = $freight['id'];
                    $this->assign('freightId',$freightId);
					$num = $rs['weight'] - $freight['sweight'];
					if($num>0)
					{
						// 计算运费
						$num = ceil($num);
						$rs['yunfei'] = $freight['sprice']+ceil($num)*$freight['xwprice'];
					}
					else
					{
						// 计算运费
						$rs['yunfei'] = $freight['sprice'];
					}
				}
			}
		}
		// 获取最新的价格 运费+产品对应规格价格
        $rs['newprice'] = $rs['yunfei']+$rs['guige_price'];
		
		
		//同城配送的运费
		$freight2 = M('freight')->where("is_samecity=1")->find();
		$rs['yunfei2'] = 0;
		if($freight2)
		{
			$num = $rs['weight'] - $freight2['sweight'];
			if($num>0)
			{
				$num = ceil($num);
				$rs['yunfei2'] = $freight2['sprice']+ceil($num)*$freight2['xwprice'];
			}
			else
			{
				$rs['yunfei2'] = $freight2['sprice'];
			}
		}
		// 获取最新的价格 运费+产品对应规格价格
		$rs['newprice2'] = $rs['yunfei2']+$rs['guige_price'];
		
		// 优惠券
        $coupon = M('coupon')->field("a.*")->alias('a')->join('ys_coupon_record b on a.id=b.coupon_id')
            ->where("b.user_id={$memberid} and b.is_use=0 and a.end_time>".date('Y-m-d',time()))->select();
       
	    $this->assign('coupon',$coupon);
        $this->assign('jifen',$jifen);//可获得的积分
        $this->assign('kyjf',$kyjf);//可使用的积分
        $_SESSION['orderurl']=$_SERVER['REQUEST_URI'];
        $this->assign('goodsdetail',$rs);
        $this->assign('balance',$memberinfo['balance']);
        $this->assign('orderid',$orderid);
		$this->assign('goodsid',$goodsid);
        $this->assign('price',$price);
		$this->assign('group_num',$group_num);
        $this->assign('guige',$guige);
		$this->assign('store',$store);
        $this->assign('totalprice',$totalprice);
        $this->display();
    }

	public function addorder3(){

        $orderid=I('get.orderid');
		$orderlist_m=M('orderlist');
		$goods_m=M('goods');
		$goods_guige_m=M('goods_guige');
		$orderlist_con=$orderlist_m->where('id='.$orderid)->find();
		$goods_con=$goods_m->where('id='.$orderlist_con['goods_id'])->find();
		$goods_guige_con=$goods_guige_m->where('goodsid='.$goods_con['id'])->find();
		$goodsid=$goods_con['id'];
        $price=$goods_guige_con['groupprice'];
        $group_num=$goods_con['group_num'];
		$old_price=$goods_guige_con['old_price'];
		$weight=$goods_guige_con['weight'];
		$guige=$goods_guige_con['guige'];
        $memberid=$_SESSION['member_id'];		
        if(empty($memberid))
		{
            $url = U('Home/Order/addorder3',array('goodsid'=>$goodsid,'groupprice'=>$price,'group_num'=>$group_num,'old_price'=>$old_price,'weight'=>$weight,'guige'=>$guige));
            session('zhuceerror',$url);
            $this->error("请先登陆!",U("Home/Member/weixin",array('tiaozhuan'=>1)));
        }
		$action=D('orderaddress');
		$addresscount = $action->addresscount($memberid);
		if($addresscount==0)
		{	
			$url = U('Home/Order/addorder3',array('goodsid'=>$goodsid,'groupprice'=>$price,'group_num'=>$group_num,'old_price'=>$old_price,'weight'=>$weight,'guige'=>$guige));
            session('addresserror',$url);
			$this->redirect('Home/Order/address',array('goodsid'=>$goodsid,'groupprice'=>$price,'group_num'=>$group_num,'old_price'=>$old_price,'weight'=>$weight,'guige'=>$guige));
		}
		
		$action=D('Goods');
		
		//查询团购次数
        $restriction_num = $action->where("id=%d and is_restriction=1",$goodsid)->getField('restriction_num');
        if($restriction_num)
		{
            $yigounum = M('orderlist')->where("userid={$memberid} and orderstate!=6 and goods_id={$goodsid} and is_head=1")->count();
            if($restriction_num<=$yigounum)
			{
                $this->error("团购已达限购次数!");exit;
            }
        }
		
        
        $rs=$action->getGoodsdetail2($goodsid,$price);
		$action=D('store');
        $store=$action->select();
        $rs['pics'] = explode(',',$rs['pic']);
        $member = D("Member")->GetInfomation($memberid);
        $action=D("orderaddress");
        if(!empty($member['def']))
		{
            $rsaddress=$action->getOneaddress($member['def']);
            $this->assign('addressdetail',$rsaddress);
            $dingwei = explode(',', $rsaddress['dingwei']);
            $this->assign('xz',$dingwei[0]);
            $this->assign('yz',$dingwei[1]);
        }
        $dealer = M('dealer')->field('id,longitude,latitude')->select();
        $ary = array();
        foreach($dealer as $key => $val){
            /*$longitude = explode(',',substr($val['longitude'],0,-1));
            $latitude = explode(',',substr($val['latitude'],0,-1));
            $temp = array();
            for($i=0;$i<count($longitude);$i++){
                $temp[$i] = array($longitude[$i],$latitude[$i]);
            }
            $ary[$key] = $temp;*/
            $ary[$key]['longitude'] = substr($val['longitude'],0,-1);
            $ary[$key]['latitude'] = substr($val['latitude'],0,-1);
            $aryids[$key] = $val['id'];
        }
        $this->assign('aryids',$aryids);
        $this->assign('dealerlist',$ary);
        $this->assign('dealerlistcot',count($ary));
        $memberinfo=$this->GetInfo($memberid);
        if(is_array($rsaddress))
		{
            $this->assign('succ','0000');
        }
        $totalprice= $rs['good_nowprice']*$num;
        $jifen = sprintf('%.3f',$totalprice/10);
        if(0.001>$jifen)
		{
            $kyjf = 0;
            $jifen = 0;
        }
		else
		{
            if($jifen>$member['jifen'])
			{
                $kyjf = $member['jifen'];
            }
			else
			{
                $kyjf = $jifen;
            }
        }
        $rs['yunfei'] = 0;
            $def = M('member')->where("id={$memberid}")->getField('def');

            if($def){

                $province = M('orderaddress')->where("id=$def")->getField('province');

                if($province){

                    $freight = M('freight')->where("privonce like '%{$province}%'")->find();

                    if($freight){

                        $num = $rs['weight'] - $freight['sweight'];

                        if($num>0){

                            $num = ceil($num);

                            $rs['yunfei'] = $freight['sprice']+ceil($num)*$freight['xwprice'];

                        }else{

                            $rs['yunfei'] = $freight['sprice'];

                        }

                    }

                }

            }
        $rs['newprice'] = $rs['yunfei']+$rs['guige_price'];

            //同城配送的运费
            $freight2 = M('freight')->where("is_samecity=1")->find();
            $rs['yunfei2'] = 0;
            if($freight2){
                $num = $rs['weight'] - $freight2['sweight'];
                if($num>0){
                    $num = ceil($num);
                    $rs['yunfei2'] = $freight2['sprice']+ceil($num)*$freight2['xwprice'];
                }else{
                    $rs['yunfei2'] = $freight2['sprice'];
                }
            }
		$rs['newprice2'] = $rs['yunfei2']+$rs['guige_price'];

        $coupon = M('coupon')->field("a.*")->alias('a')->join('ys_coupon_record b on a.id=b.coupon_id')

            ->where("b.user_id={$memberid} and b.is_use=0 and a.end_time>".date('Y-m-d',time()))->select();

        $this->assign('coupon',$coupon);

        $this->assign('jifen',$jifen);//可获得的积分

        $this->assign('kyjf',$kyjf);//可使用的积分

        $_SESSION['orderurl']=$_SERVER['REQUEST_URI'];

//        var_dump($orderurl);

        /*$huase = M('Goods_huase')->where('goodsid='.$gid)->getField('huasename');

        if($huase){

            $huase = explode(',',$huase);

        }

        $this->assign('huase',$huase);*/

        $this->assign('goodsdetail',$rs);

        $this->assign('balance',$memberinfo['balance']);

        $this->assign('orderid',$orderid);

		$this->assign('goodsid',$goodsid);

        $this->assign('price',$price);

		$this->assign('group_num',$group_num);

        $this->assign('guige',$guige);

		$this->assign('store',$store);

        $this->assign('totalprice',$totalprice);

        $this->display();

    }

	

	public function addshoppingorder(){

        $ids=I('get.ids');
        $memberid=$_SESSION['member_id'];
        if(empty($memberid)){
            $url = U('Home/Order/addorder',array('goodsid'=>$goodsid,'price'=>$price,'guige'=>$guige));
            session('zhuceerror',$url);
            $this->error("请先登陆!",U("Home/Member/weixin",array('tiaozhuan'=>1)));
        }
		$action=D('orderaddress');
		$addresscount = $action->addresscount($memberid);
		if($addresscount==0)
		{	
			$url = U('Home/Order/addshoppingorder',array('ids'=>$ids));
            session('addresserror',$url);
			$this->redirect('Home/Order/address',array('goodsid'=>$goodsid,'price'=>$price,'weight'=>$weight,'old_price'=>$old_price,'guige'=>$guige));
		}
        $action=D('Shopping');
        $rs=$action->shop_lists($ids);
		$to_price=$action->shop_list_counts($ids);
		$action=D('store');
        $store=$action->select();
        $member = D("Member")->GetInfomation($memberid);
        $action=D("orderaddress");
        if(!empty($member['def'])){
            $rsaddress=$action->getOneaddress($member['def']);
            $dingwei = explode(',', $rsaddress['dingwei']);
            $this->assign('xz',$dingwei[0]);
            $this->assign('yz',$dingwei[1]);
            $this->assign('addressdetail',$rsaddress);
        }		
		
		
        $dealer = M('dealer')->field('id,longitude,latitude')->select();
        $ary = array();
        foreach($dealer as $key => $val){
            /*$longitude = explode(',',substr($val['longitude'],0,-1));
            $latitude = explode(',',substr($val['latitude'],0,-1));
            $temp = array();
            for($i=0;$i<count($longitude);$i++){
                $temp[$i] = array($longitude[$i],$latitude[$i]);
            }
            $ary[$key] = $temp;*/
            $ary[$key]['longitude'] = substr($val['longitude'],0,-1);
            $ary[$key]['latitude'] = substr($val['latitude'],0,-1);
            $aryids[$key] = $val['id'];
        }
        $this->assign('aryids',$aryids);
        $this->assign('dealerlist',$ary);
        $this->assign('dealerlistcot',count($ary));
		
        $memberinfo=$this->GetInfo($memberid);
        if(is_array($rsaddress)){
            $this->assign('succ','0000');
        }
        $totalprice= $rs['good_nowprice']*$num;
        $jifen = sprintf('%.3f',$totalprice/10);
        if(0.001>$jifen){
            $kyjf = 0;
            $jifen = 0;
        }else{
            if($jifen>$member['jifen']){
                $kyjf = $member['jifen'];
            }else{
                $kyjf = $jifen;
            }
        }
        $coupon = M('coupon')->field("a.*")->alias('a')->join('ys_coupon_record b on a.id=b.coupon_id')
           ->where("b.user_id={$memberid} and b.is_use=0 and a.end_time>".date('Y-m-d',time()))->select();
        if(!empty($coupon)){
            foreach($coupon as $key => $val){
                if(!empty($val['goods_id'])){
                    $coupon[$key]['goods_ids'] = explode(',',$val['goods_id']);
                }
            }
            $this->assign('coupon',$coupon);
        }
        $zongyunfei = 0;
        foreach($rs as $key => $val){
            $val['yunfei'] = 0;
            $def = M('member')->where("id={$memberid}")->getField('def');
            if($def){ // 收货地址id
                $province = M('orderaddress')->where("id=$def")->getField('province'); //省
                if($province){
                    $freight = M('freight')->where("privonce like '%{$province}%'")->find(); //省
                    if($freight){
                        $num = $val['weight'] - $freight['sweight'];
                        if($num>0){
                            $num = ceil($num);
                            $val['yunfei'] = $freight['sprice']+ceil($num)*$freight['xwprice'];
                        }else{
                            $val['yunfei'] = $freight['sprice'];
                        }
                    }
                }
            }
            $zongyunfei += $val['yunfei']+$val['guige_price'];
        }
		
		//同城配送的运费
		foreach($rs as $key => $val)
		{
			$allweight += ($val['guige']*$val['num']);
		}
		$freight2 = M('freight')->where("is_samecity=1")->find();
		$yunfei2 = 0;
		if($freight2)
		{
			$num = $allweight - $freight2['sweight'];
			if($num>0)
			{
				$num = ceil($num);
				$yunfei2 = $freight2['sprice']+ceil($num)*$freight2['xwprice'];
			}
			else
			{
				$yunfei2 = $freight2['sprice'];
			}
		}
        $newprice2 = $yunfei2+$to_price;
		//同城配送的运费
		
        $this->assign('allweight',$yunfei2);
        $this->assign('newprice2',$newprice2);
		
        $this->assign('zprice',$to_price+$zongyunfei);
        $this->assign('zongyunfei',$zongyunfei);
		
        $this->assign('jifen',$jifen);//可获得的积分
        $this->assign('kyjf',$kyjf);//可使用的积分
        $_SESSION['orderurl']=$_SERVER['REQUEST_URI'];
//        var_dump($orderurl);
        /*$huase = M('Goods_huase')->where('goodsid='.$gid)->getField('huasename');
        if($huase){
            $huase = explode(',',$huase);
        }
        $this->assign('huase',$huase);*/
        $this->assign('goodsdetail',$rs);
        $this->assign('balance',$memberinfo['balance']);
        $this->assign('goodsid',$goodsid);
		$this->assign('ids',$ids);
        $this->assign('price',$price);
        $this->assign('guige',$guige);
		$this->assign('store',$store);
        $this->assign('to_price',$to_price);
        $this->display();
    }

    public function addcartorder(){

		$cart_arr=$_SESSION["mycar"];

        $post = $_POST;

        $pids = I('post.pid');

        if(!is_array($pids)){

            $this->error("请选择确认付款的商品!");

        }

        if(!empty($post)){

            foreach($cart_arr as $key=>$value){

                $value['nums'] = $post['num'][$key];

                $value['pics'] = explode(',',$value['pic']);

                $value['pjifen'] = intval($value['nums'])*$value['good_nowprice']/10;

                if(!empty($post['shigong'][$key])){

                    $value['shigong'] = $post['shigong'][$key];

                }

                $cart_arr[$key] = $value;

            }

            session('mycar',$cart_arr);

            foreach ($pids as $key => $value) {

                $queren[] = $cart_arr[$value];

                $totalprice+=(intval($cart_arr[$value]['nums'])*$cart_arr[$value]['good_nowprice']);

            }

            session('mycar',$cart_arr);

            session('queren',$queren);

        }

        $memberid=$_SESSION['member_id'];

        $rsaddress=D("orderaddress")->where("userid=".$memberid)->find();

        if($memberid){

            $ret = M('Member')->where('id='.$memberid)->getField('password2');

            if(empty($ret)){

                $this->error("请设置支付密码!",U('Home/Member/update')."/urstatus/2");

            }

        }

        if(is_array($rsaddress)){

            //$this->error("请设置收货地址!",U('/Order/u_address/urstatus').'/2');exit;

            $this->assign('succ','0000');

        }

		$memberinfo=$this->GetInfo($memberid);

		$this->assign('addressdetail',$rsaddress);

		$this->assign('balance',$memberinfo['balance']);

		$this->assign('cart_arr',$queren);

		$this->assign('totalprice',$totalprice);

        $this->display();

    }

    public  function address(){

		$this->display();

		/*

        $memberid=$_SESSION['member_id'];

		$addid=I('get.add_id');

//echo $memberid;die;

        $action=D("orderaddress");

        $rs=$action->getOneaddress($addid);

//        echo $action->_sql();die;

//        var_dump($rs);

        $type=I('post.type');

        if($type!=''){

            if($type=='add'){

                $date['province']=I('post.province');

                $date['city']=I('post.city');

                $date['country']=I('post.country');

                $date['address']=I('post.address');

                $date['postcode']=I('post.postcode');

                $date['telephone']=I('post.telephone');

                $date['xiangqing']=I('post.address');

                $date['consignee']=I('post.consignee');

                $date['userid']=$memberid;

                $date['addtime']=Gettime();

                $action=D("orderaddress");

                $result=$action->addaddress($date);

                if( $result){

                	$this->assign("zhuangtai","添加成功");

                    //$this->redirect($_SESSION['orderurl']);

                }else{

                	$this->assign("zhuangtai","添加失败");

                    //$this->error("添加失败","{:U('Order/address')}");

                }

                $this->success();exit;

            }else if($type=='edit'){

                $date['province']=I('post.province');

                $date['city']=I('post.city');

                $date['country']=I('post.country');

                $date['address']=I('post.address');

                $date['postcode']=I('post.postcode');

                $date['telephone']=I('post.telephone');

                $date['xiangqing']=I('post.address');

                $date['consignee']=I('post.consignee');

                $date['id']=I('post.address_id');

                $action=D("orderaddress");

                $result=$action->editaddress($date);

                if( $result>0 || $result ===0 ){

                	$this->error('更新成功！',U('/Home/Order/u_address'));

                    //$this->redirect($_SESSION['orderurl']);

                }else{

                	$this->assign("zhuangtai","更新失败");

                    //$this->error("更新失败");

                }

                $this->success();

                exit;

            }

        }

        $orderurl=$_SESSION['orderurl'];

        if($rs){

            $this->assign('address',$rs);

            $this->display('editaddress');

        }else{

//            $this->assign('num',$rs);

            $this->display('addaddress');

        }

//        $this->assign('orderurl',$orderurl);

*/

    }

	public  function add_order(){

		$action=D('orderaddress');

		$rs=$action->add_order();

		$this->ajaxReturn($rs);

        return;

	}

	public function u_address(){

			$member_id = $_SESSION['member_id'];

            if(empty($member_id)){

                $this->error("请先进行手机认证!",U("Home/Member/update"));

            }else{

    			$action=D("orderaddress");

    			$mem=D("Member");

    			$def=I('post.def');

    			$del_id=I('get.del_id');

    			if($def!=''){

    				$rr=$mem->where('id='.$member_id)->setField('def',$def);

    				if($rr){

                    	$this->error('修改成功！',U('/Home/Order/u_address'));

                        //$this->redirect($_SESSION['orderurl']);

                    }else{

                    	$this->error('修改失败！',U('/Home/Order/u_address'));

                        //$this->error("添加失败","{:U('Order/address')}");

                    }

    				}

    			if($del_id!=''){

    				$rr=$action->where('id='.$del_id)->delete(); 

    				if($rr){

                    	$this->error('删除成功！',U('/Home/Order/u_address'));

                        //$this->redirect($_SESSION['orderurl']);

                    }else{

                    	$this->error('删除失败！',U('/Home/Order/u_address'));

                        //$this->error("添加失败","{:U('Order/address')}");

                    }

    				}

    			$member=$mem->where('id='.$member_id)->find();

    			$rs=$action->where('userid='.$member_id)->select();

    			$this->assign('urstatus',I('urstatus'));

    			$this->assign('addresss',$rs);

    			$this->assign('member',$member);

    			$this->display();

            }

	}

	public  function address1(){

        $gid = I('gid',0,'int');

        $num = I('num',0,'int');

        $urstatus = I('urstatus',0,'int');

        $cenghigh = I('cenghigh',0,'int');

        $color = I('color');

			$type=I('post.type');

			$memberid=$_SESSION['member_id'];

      		  if($type!=''){

            if($type=='add'){

                $date['province']=I('post.province');

                $date['city']=I('post.city');

                $date['country']=I('post.country');

                $date['address']=I('post.address');

                $date['postcode']=I('post.postcode');

                $date['telephone']=I('post.telephone');

                $date['xiangqing']=I('post.address');

                $date['consignee']=I('post.consignee');

                $date['userid']=$memberid;

                $date['addtime']=Gettime();

                $action=D("orderaddress");

                $result=$action->addaddress($date);

                if( $result){

                    D("Member")->where('id='.$_SESSION['member_id'])->setField('def',$result);

                    if('1'==$_GET['urstatus']){

                        $this->error('添加成功！',U('/Home/Order/addorder',array('gid'=>$gid,'num'=>$num,'cenghigh'=>$cenghigh,'color'=>$color)));exit;

                    }else if('2'==$_GET['urstatus']){

                        $this->assign("zhuangtai","添加成功！");

                	   $this->success('添加成功！',U('Goods/cart'));exit;

                    }else{

                        $this->assign('zhuangtai','添加成功！');

                    }

                    //$this->redirect($_SESSION['orderurl']);

                }else{

                	$this->assign("zhuangtai","添加失败");

                    //$this->error("添加失败","{:U('Order/address')}");

                }

                $this->success('',U('Order/u_address'));exit;

            }else if($type=='edit'){

                $date['province']=I('post.province');

                $date['city']=I('post.city');

                $date['country']=I('post.country');

                $date['address']=I('post.address');

                $date['postcode']=I('post.postcode');

                $date['telephone']=I('post.telephone');

                $date['xiangqing']=I('post.address');

                $date['consignee']=I('post.consignee');

                $date['id']=I('post.address_id');

                $action=D("orderaddress");

                $result=$action->editaddress($date);

                if( $result>0 || $result ===0 ){

                	$this->assign("zhuangtai","更新成功");

                    //$this->redirect($_SESSION['orderurl']);

                }else{

                	$this->assign("zhuangtai","更新失败");

                    //$this->error("更新失败");

                }

                $this->success('Order/u_address');

                exit;

            }

        }

        $orderurl=$_SESSION['orderurl'];

        if($rs){

            $this->assign('address',$rs);

            $this->display('u_address');

        }else{

//            $this->assign('num',$rs);

            $this->assign('gid',$gid);

            $this->assign('num',$num);

            $this->assign('cenghigh',$cenghigh);

            $this->assign('color',$color);

            $this->assign('urstatus',$urstatus);

            $this->display('addaddress');

        }

//        $this->assign('orderurl',$orderurl);

    }

    public function orderadd()

    {

        $memberid = $_SESSION['member_id'];
        $action = D('Orderlist');
        $action1 = D('Orderdetail');
        $order['userid'] = $memberid;
		//$order['fid'] = $_SESSION['shop_id'];
		$order['goods_id'] = I('post.goodsid');
		$order['is_delivery'] = I('post.RadioGroup1'); //是否上门取货
		
		if($order['is_delivery'])
		{
			$order['code'] = rand(100000,999999);
			$order['storeid'] = I('post.storeid');
			$ar=M('store')->where('id='.$order['storeid'])->find();
			$order['dealerid']=$ar['dealerid'];
			$order['shipping_fee']=0;
		}
		else
		{
            $order['shipping_fee']=I('post.shipping_fee');
        }
		
        if(empty($order['dealerid']))
		{
            $dealerNum = I('post.dealerNum','');
            if($dealerNum!='' && is_numeric($dealerNum))
			{
                $ids = M('dealer')->field('id')->select();
                $order['dealerid'] = $ids[$dealerNum]['id'];
            }
        }
		
		
        $order['orderno'] = date('YmdHis', time()) . rand(1000, 9999);
        $order['consignee'] = I('post.consignee');
        $order['telephone'] = I('post.telephone');
        $order['country'] = I('post.country');
		$order['province'] = I('post.province');
		$order['city'] = I('post.city');
		$order['district'] = I('post.district');
		$order['address'] = I('post.address');
        $order['comment'] = I('post.content');
		$order['pay_fee'] = I('post.priceall');
        $order['pay_status'] = 0;
		$order['shipping_status'] = 0;
		$order['coupon_fee'] = I('post.coupon_fee'); //优惠券
		
		//送货地址不能为空
		if($order['consignee']=="" || $order['telephone']=="" || $order['country']=="" || $order['province']=="" || $order['city']=="" || $order['district']=="" || $order['address']=="" )
		{
			echo 2;
			exit;
		}
		
		
		if($order['coupon_fee'])
		{
			$order['card_id'] = 1;
			$order['card_name'] = '立减'.$order['coupon_fee'];
			$order['card_message'] = '立减'.$order['coupon_fee'];
		}
		
        $order['addtime'] = Gettime();
		$order['paytime'] = Gettime();
		$order['total_price'] = $order['pay_fee']+$order['coupon_fee']; //总价
		$order['paytype'] = 2;
            //$order['total_price'] += $order['shipping_fee']; //总价
            //$order['pay_fee'] += $order['shipping_fee'];       
		$order['is_groupbuy'] = I('post.is_groupbuy');  //团购
		$order['is_head'] = I('post.is_head');
		
        $orderd['goodsid'] = I('post.goodsid');
        $orderd['orderno'] = $order['orderno'];
        $orderd['good_nowprice'] = I('post.d_price');
        $orderd['goodsname'] = I('post.good_name');
        $orderd['guige'] = I('post.guige');
        $orderd['img'] = I('post.pic');
        $orderd['num'] = I('post.nums');
		$orderd['group_num'] = I('post.group_num');
		
		// 如果是团购，默认状态为取消，支付后改状态
		if($order['is_groupbuy'] ==1)
		{
			$order['orderstate'] = 7;
			$order['groupbuy_ok'] = 2;
		}
		else
		{
			$order['orderstate'] = 1;
		}
		
		
		if($orderd['group_num'])
		{
			$orderd['type'] = 1;
		}
		
        $orderd['allprice'] = I('post.priceall');
        $orderd['addtime'] = Gettime();
        $rs = $action->addorderlist($order);   //插入订单

        if($order['is_groupbuy'])
		{
            M('orderlist')->where('id='.$rs)->setField('groupbuyid',$rs);
        }

		session('orderid',$rs);
		$orderd['orderid'] = $rs;
		$rsd = $action1->addorderdetail($orderd);   //插入订单细节
            /*if($paytype==1){

                $jifen = $jifen-$syjf;

            }*/
		if($order['is_groupbuy'])
		{
			if($rs && $rsd)
			{
				echo 1;//支付成功
			}
			else
			{
				echo 0;//支付失败
			}
		}
		else
		{
			if($rs && $rsd)
			{
				echo 1;//支付成功
			}
			else
			{
				echo 0;//支付失败
			}
		}

	}

	//参团订单

	public function orderadd_group()
    {
        $memberid = $_SESSION['member_id'];
        $action = D('Orderlist');
        $action1 = D('Orderdetail');
        $order['userid'] = $memberid;
		//$order['fid'] = $_SESSION['shop_id'];
		$order['goods_id'] = I('post.goodsid');
		$orderid = I('post.orderid');
		$order['is_delivery'] = I('post.RadioGroup1'); //是否上门取货
		
		$now_count = $action->where('groupbuyid='.$orderid)->count(); // 现在已参团人数
		$groupendtime = $action->where('id='.$orderid)->getField('groupendtime'); // 团购结束时间
		$group_num = I('post.group_num'); //总共参团人数
		
		// 判断参团人数是否已满
		if($now_count>=$group_num)
		{
			echo 3; //参团人数已满
			exit;
		}

		//如果参团人数不满足 则判断该团购的时间是否已到达
		if($groupendtime<time())
		{
			echo 4; //拼团已结束
			exit;
		}
		

		// 判断参团是否已失败
		if($now_count>=$group_num)
		{
			echo 3; //参团人数已满
			exit;
		}
		
		
		
		//是否上门自取
		if($order['is_delivery'])
		{
			$order['code'] = rand(100000,999999);
			$order['storeid'] = I('post.storeid');
			$ar=M('store')->where('id='.$order['storeid'])->find();
			$order['dealerid']=$ar['dealerid'];
		}
		
		$order['orderstate'] = 7;
		$order['groupbuy_ok'] = 2;
        $order['orderno'] = date('YmdHis', time()) . rand(1000, 9999);
        $order['consignee'] = I('post.consignee');
        $order['telephone'] = I('post.telephone');
        $order['country'] = I('post.country');
		$order['province'] = I('post.province');
		$order['city'] = I('post.city');
		$order['district'] = I('post.district');
		$order['address'] = I('post.address');
        $order['comment'] = I('post.content');
		$order['pay_fee'] = I('post.priceall');
        $order['pay_status'] = 0;
		$order['shipping_status'] = 0;
		$order['coupon_fee'] = I('post.coupon_fee'); //优惠券
		
		//送货地址不能为空
		if($order['consignee']=="" || $order['telephone']=="" || $order['country']=="" || $order['province']=="" || $order['city']=="" || $order['district']=="" || $order['address']=="" )
		{
			echo 2;
			exit;
		}
		
		//优惠券
		if($order['coupon_fee'])
		{
			$order['card_id'] = 1;
			$order['card_name'] = '立减'.$order['coupon_fee'];
			$order['card_message'] = '立减'.$order['coupon_fee'];
		}
		
        $order['addtime'] = Gettime();
		$order['paytime'] = Gettime();
		$order['total_price'] = $order['pay_fee']+$order['coupon_fee']; //总价
		$order['paytype'] = 2;
		$order['is_groupbuy'] = I('post.is_groupbuy');  //团购
		$order['is_head'] = 0;
		$order['groupbuyid']=$orderid;
        $orderd['goodsid'] = I('post.goodsid');
        $orderd['orderno'] = $order['orderno'];
        $orderd['good_nowprice'] = I('post.d_price');
        $orderd['goodsname'] = I('post.good_name');
        $orderd['guige'] = I('post.guige');
        $orderd['img'] = I('post.pic');
        $orderd['num'] = I('post.nums');
		$orderd['group_num'] = I('post.group_num');
		
		
		if($orderd['group_num'])
		{
			$orderd['type'] = 1;
		}
		
        $orderd['allprice'] = I('post.priceall');
        $orderd['addtime'] = Gettime();
		$rs = $action->addorderlist($order);   //插入订单
		session('orderid',$rs);
		$orderd['orderid'] = $rs;
		$rsd = $action1->addorderdetail($orderd);   //插入订单细节

        if($rs && $rsd)
		{
            echo 1;
        }
		else
		{
            echo 0;
        }
	}

	//参团订单

	public function orderadd_group1()

    {

        $memberid = $_SESSION['member_id'];
        $action = D('Orderlist');
        $action1 = D('Orderdetail');
        $order['userid'] = $memberid;
		//$order['fid'] = $_SESSION['shop_id'];
		$order['goods_id'] = I('post.goodsid');
		$orderid = I('post.orderid');
		$order['is_delivery'] = I('post.RadioGroup1'); //是否上门取货
		
		if($order['is_delivery'])
		{
			$order['code'] = rand(100000,999999);
			$order['storeid'] = I('post.storeid');
			$ar=M('store')->where('id='.$order['storeid'])->find();
			$order['dealerid']=$ar['dealerid'];
		}
		
		$order['orderstate'] = 7;
		$order['groupbuy_ok'] = 2;
        $order['orderno'] = date('YmdHis', time()) . rand(1000, 9999);
        $order['consignee'] = I('post.consignee');
        $order['telephone'] = I('post.telephone');
        $order['country'] = I('post.country');
		$order['province'] = I('post.province');
		$order['city'] = I('post.city');
		$order['district'] = I('post.district');
		$order['address'] = I('post.address');
        $order['comment'] = I('post.content');
		$order['pay_fee'] = I('post.priceall');
		$order['shipping_fee'] = I('post.shipping_fee');
        $order['pay_status'] = 0;
		$order['shipping_status'] = 0;
		$order['coupon_fee'] = I('post.coupon_fee'); //优惠券
		
		//送货地址不能为空
		if($order['consignee']=="" || $order['telephone']=="" || $order['country']=="" || $order['province']=="" || $order['city']=="" || $order['district']=="" || $order['address']=="" )
		{
			echo 2;
			exit;
		}

		if($order['coupon_fee'])
		{
			$order['card_id'] = 1;
			$order['card_name'] = '立减'.$order['coupon_fee'];
			$order['card_message'] = '立减'.$order['coupon_fee'];
		}

        $order['addtime'] = Gettime();
		$order['paytime'] = Gettime();
		$order['total_price'] = $order['pay_fee']+$order['coupon_fee']; //总价
		$order['paytype'] = 2;
		$order['is_groupbuy'] = I('post.is_groupbuy');  //团购
		$order['is_head'] = 1;	
        $orderd['goodsid'] = I('post.goodsid');
        $orderd['orderno'] = $order['orderno'];
        $orderd['good_nowprice'] = I('post.d_price');
        $orderd['goodsname'] = I('post.good_name');
        $orderd['guige'] = I('post.guige');
        $orderd['img'] = I('post.pic');
        $orderd['num'] = I('post.nums');
		$orderd['group_num'] = I('post.group_num');
		
		if($orderd['group_num'])
		{
			$orderd['type'] = 1;
		}
        $orderd['allprice'] = I('post.priceall');
        $orderd['addtime'] = Gettime();
		$rs = $action->addorderlist($order);   //插入订单
		session('orderid',$rs);
		if($order['is_groupbuy'])
		{
			M('orderlist')->where('id='.$rs)->setField('groupbuyid',$rs);
		}
        $orderd['orderid'] = $rs;
        $rsd = $action1->addorderdetail($orderd);   //插入订单细节
            /*if($paytype==1){

                $jifen = $jifen-$syjf;

            }*/
        if($rs && $rsd)
		{
            echo 1; //支付成功
        }
		else
		{
			//支付失败
            echo 0;
        }
	}

	public function orderadd_shop()

    {
        $memberid = $_SESSION['member_id'];
        $action = D('Orderlist');
        $action1 = D('Orderdetail');
		$order['is_delivery'] = I('post.RadioGroup1'); //是否上门取货
		if($order['is_delivery'])
		{
			$code = rand(100000,999999);
		}
		$arr=explode(',',$_POST['goodid']);
		$j=1;
        $order['userid'] = $memberid;
		//$order['fid'] = $_SESSION['shop_id'];
		$order['goods_id'] = I('post.goodsid'.$arr[0]);
		$order['is_delivery'] = I('post.RadioGroup1'); //是否上门取货
		
		if($order['is_delivery'])
		{
			$order['code'] = $code;
			$order['storeid'] = I('post.storeid');
			$ar=M('store')->where('id='.$order['storeid'])->find();
			$order['dealerid']=$ar['dealerid'];
			$order['shipping_fee']=0;
        }
		else
		{
            $order['shipping_fee']=I('post.shipping_fee');
		}
        if(empty($order['dealerid']))
		{
            $dealerNum = I('post.dealerNum','');
            if($dealerNum!='' && is_numeric($dealerNum))
			{
                $ids = M('dealer')->field('id')->select();
                $order['dealerid'] = $ids[$dealerNum]['id'];
            }
        }
		$order['orderstate'] = 1;
        $order['orderno'] = date('YmdHis', time()) . rand(1000, 9999);
        $order['consignee'] = I('post.consignee');
        $order['telephone'] = I('post.telephone');
        $order['country'] = I('post.country');
		$order['province'] = I('post.province');
		$order['city'] = I('post.city');
		$order['district'] = I('post.district');
		$order['address'] = I('post.address');
        $order['comment'] = I('post.content');
        $order['pay_status'] = 0;
		$order['shipping_status'] = 0;
		
		//送货地址不能为空
		if($order['consignee']=="" || $order['telephone']=="" || $order['country']=="" || $order['province']=="" || $order['city']=="" || $order['district']=="" || $order['address']=="" )
		{
			echo 2;
			exit;
		}
		
		if($j==1)
		{
			$order['coupon_fee'] = I('post.coupon_fee'); //优惠券
			if($order['coupon_fee'])
			{
				$order['card_id'] = 1;
				$order['card_name'] = '立减'.$order['coupon_fee'];
				$order['card_message'] = '立减'.$order['coupon_fee'];
			}
		}
		else
		{
			$order['card_id'] = 0;
			$order['card_name'] = '';
			$order['card_message'] = '';
		}
        $order['addtime'] = Gettime();
		$order['paytime'] = Gettime();
		$order['total_price'] = I('post.total'); //总价
		$order['pay_fee'] = $order['total_price']-$order['coupon_fee'];
		$order['paytype'] = 2;
        $order['total_price'] += $order['shipping_fee']; //总价
        $order['pay_fee'] += $order['shipping_fee'];
		$rs = $action->addorderlist($order);   //插入订单
        session('orderid',$rs);
        $orderd['orderid'] = $rs;
		foreach($arr as $k=>$v)
		{
			$orderd['goodsid'] = I('post.goodsid'.$v);
			$orderd['orderno'] = $order['orderno'];
			$orderd['good_nowprice'] = I('post.d_price'.$v);
			$orderd['goodsname'] = I('post.good_name'.$v);
			$orderd['guige'] = I('post.guige'.$v);
			$orderd['img'] = I('post.pic'.$v);
			$orderd['num'] = I('post.nums'.$v);
			$orderd['allprice'] = I('post.priceall'.$v);
			$orderd['addtime'] = Gettime();
			$rsd = $action1->addorderdetail($orderd);   //插入订单细节
			$j++;
		}

		if($rs && $rsd)
		{
			$data['id']=array('in',$_POST['goodid']);
			M('shopping')->where($data)->delete();
     		echo 1;

		}
		else
		{
      		echo 0;
		}
    }

	public function pintuan(){

		$userid=$_SESSION['member_id'];
        $id=I('get.orderid');
        $action=D('Orderlist');
		$goods=$action->where('id='.$id)->find();
		//跳转到团长订单
		$groupid=$goods['groupbuyid'];
		$id=$groupid;
		$goods=$action->where('id='.$id)->find();
		
		if($goods['groupendtime']==0)
		{
			$goodsid=$goods['goods_id'];
			$group_time=M('goods')->where('id='.$goodsid)->find();
			$end_time=$group_time['group_time'];
			$times=$end_time*3600+time();
			$action->where('id='.$id)->setField('groupendtime',$times);
		}
        $rs=$action->getoneorderdetail($id);
		$endtime=date('Y/m/d H:i:s',$rs[0]['groupendtime']);
		$group=$action->where('userid='.$userid.' and groupbuyid='.$id.'  and orderstate !=7 ')->find();
		if($group)
		{
			$type=1;
		}
		else
		{
			$type=0;
		}
		$members=$action->where('groupbuyid='.$id.' and orderstate !=7 ')->order('id asc')->select();
		foreach($members as $k=>$v)
		{
				$rr=M('member')->where('id='.$v['userid'])->find();
				$v['touimg']=$rr['touimg'];
				$v['truename']=$rr['truename'];
				$member_list[$k]=$v;
		}

        $count=$action->where('groupbuyid='.$id.' and orderstate !=7 ')->count();
        $count=$rs['detail'][0]['group_num']-$count;
        $member=M('member')->where('id='.$rs[0]['userid'])->find();
        
		
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
                header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
            }else{
                //如果参团人数不满足 则判断该团购的时间是否已到达
                if($rs[0]['groupendtime']<time())
                {
                    
					//到了团购时间则退款 并且修改每个人的订单状态
                    foreach($members as $k=>$v)
                    {
                        $succ = $this->refund($v['orderno'],$v['pay_fee']*100);
                        if($succ)
                        {
                            $data['orderstate'] = 6;
                            $data['id'] = $v['id'];
                            $data['groupbuy_ok'] = 2;
                            $action->save($data);
							
                        }
                    }
					$end=6;
                }
            }
        }elseif(1==$members[0]['groupbuy_ok']){
            header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
        }else{
		    $end=6;
		}
		
		/*if($rs[0]['groupendtime']<time())
        {
			//判断该团购订单是否已经处理(成功,失败)
			if(0==$members[0]['groupbuy_ok'])
			{
				
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
					header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
				}else{

					//到了团购时间则退款 并且修改每个人的订单状态
					foreach($members as $k=>$v)
					{
						$succ = $this->refund($v['orderno'],$v['pay_fee']*100);
						if($succ)
						{
							$data['orderstate'] = 6;
							$data['id'] = $v['id'];
							$data['groupbuy_ok'] = 2;
							$action->save($data);
						}
					}
					$end=6;
				}
			}
		}
		else
		{
			//判断该团购订单是否已经处理(成功,失败)
			if(0==$members[0]['groupbuy_ok'])
			{
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
					header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
				}
			}
			else if(1==$members[0]['groupbuy_ok'])
			{
				header("Location:http://goead.ysxdgy.com/Order/pintuan1/orderid/$id"); exit;
			}
			else if(2==$members[0]['groupbuy_ok'])
			{
				$end=6;
			}
		}
		*/

		
		$rs=$action->getoneorderdetail($id);
        $this->assign('orderinfo',$rs);
		$this->assign('member',$member);
		$this->assign('member_list',$member_list);
		$this->assign('endtime',$endtime);
		$this->assign('count',$count);
		$this->assign('end',$end);
		$this->assign('type',$type);
        $this->display();

    }

	public function pintuan1(){

		$userid=$_SESSION['member_id'];
        $id=I('get.orderid');
		
        $action=D('Orderlist');
		$goods=$action->where('id='.$id)->find();

		if($goods['groupbuy_ok'] == 0 || $goods['groupbuy_ok'] == 2)
		{
			 header("Location:http://goead.ysxdgy.com/Order/pintuan/orderid/$id"); exit;
		}
		
		//跳转到团长订单
		$groupid=$goods['groupbuyid'];
		$id=$groupid;
        $rs=$action->getoneorderdetail($id);
		$group=$action->where('userid='.$userid.' and groupbuyid='.$id.' and orderstate !=6 ' )->find();
		$members=$action->where('groupbuyid='.$id .' and orderstate !=6 ')->order('id asc')->select();
		
		foreach($members as $k=>$v)
		{
			$rr=M('member')->where('id='.$v['userid'])->find();
			$v['touimg']=$rr['touimg'];
			$v['truename']=$rr['truename'];
			$member_list[$k]=$v;
		}
		
		$this->assign('orderinfo',$rs);
		$this->assign('member_list',$member_list);
		$this->assign('type',$type);
		$this->display();
    }

    public function orderxq(){

        $id=I('get.id');

        $action=D('Orderlist');

        $rs=$action->getoneorderdetail($id);

//        var_dump($rs);

         $this->assign('orderinfo',$rs);

        $this->display();

    }

    public function pay(){

        $orderid=I('id');

        $action=D('Orderlist');

        $rs=$action->getoneorderdetail($orderid);

        $price=$rs[0]['total_price'];

        $memberid = $_SESSION['member_id'];

        $actionpay = D('Member');

        $returnpay = $actionpay->payorder($memberid,$price);

        if ($returnpay['type'] == 1) {

            //account表插入记录

            $actionacount = D('account');

            $rsacount = $actionacount->addcountdetail($memberid, $price, '支付订单成功');

            if($rsacount){

//                    更新订单状态

                $action->updateOrderState($orderid,2);

            }

            $this->assign('zhuangtai','支付成功');

            $this->success("", U("Member/main"));

        } else {

            $this->assign('zhuangtai','支付失败');

            $this->error('', U("Member/main"));

        }

    }

    public function jsapi(){

		vendor('Wxpay.WxPayPubHelper.WxPayPubHelper');
		$orderid = I('get.id');
		$type = I('get.type');
        $gid = I('get.gid');
        $gchongzhi = I('get.gchongzhi');
		//session('type',$type);
        if(empty($orderid))
		{
		  $orderid = $_SESSION['orderid'];
        }
		else
		{
            session('orderid',$orderid);
        }
        if(empty($type))
		{
		  $type = $_SESSION['type'];
        }
		else
		{
            session('type',$type);
        }
        if(empty($gid))
		{
          $gid = $_SESSION['gid'];
          session('gid',null);
        }
		else
		{
            session('gid',$gid);
        }
        if(empty($gchongzhi))
		{
          $gchongzhi = $_SESSION['gchongzhi'];
          session('gchongzhi',null);
        }
		else
		{
            session('gchongzhi',$gchongzhi);
        }

		if(!empty($orderid))
		{
			if($type==1)
			{
				$m = M('orderlist');
				$arr = $m->where("id=$orderid")->find(); //根据订单号查找订单详情  
                $good_name = M('goods')->where("id=".$arr['goods_id'])->getField('good_name1');
				$out_trade_no=$arr['orderno'];
				$total_fee=$arr['total_price']*100;
			}
			else
			{
				$m = M('chong');
				$arr = $m->where("id=$orderid")->find(); //根据订单号查找订单详情  
				$out_trade_no=$arr['chongno'];
				$total_fee=$arr['cash']*100;
			}
		}
		else
		{
			$state=$_GET['state'];
			$str = stripslashes($state);
			$arr=json_decode($str,1);
			$out_trade_no=$arr['out_trade_no'];
			$total_fee=$arr['total_price']*100;
			$type=$arr['type'];
		}

		//echo $type;

		//使用jsapi接口 

		$jsApi = new \JsApi_pub();

		//=========步骤1：网页授权获取用户openid============

		//通过code获得openid

		if (!isset($_GET['code']))

		{

	//        echo $orderid;die;

			//触发微信返回code码

			$url= $jsApi->createOauthUrlForCode(\WxPayConf_pub::JS_API_CALL_URL);

			$state = json_encode(array(
	            "type" => $type,
				"orderid" => $orderid,
                "good_name" => $good_name,
				"out_trade_no" => $out_trade_no,
				"total_fee" => "$total_fee",
			));

			$url = str_replace("STATE", $state, $url);

	//        $url=$url1.'?orderid='.$orderid;

			//header("Location: $url");

			//$this->redirect($url);

			echo "<script>window.location.href='".$url."'</script>";

		}else

		{

			//获取code码，以获取openid

			$code = $_GET['code'];

			$jsApi->setCode($code);

			$openid = $jsApi->getOpenId();

			session("openid",$openid);

			//echo $openid;

		}

		//echo $url;	

	//echo $openid;	

		//=========步骤2：使用统一支付接口，获取prepay_id============

		//使用统一支付接口

		$unifiedOrder = new \UnifiedOrder_pub();

		//设置统一支付接口参数

		//设置必填参数

		//appid已填,商户无需重复填写

		//mch_id已填,商户无需重复填写

		//noncestr已填,商户无需重复填写

		//spbill_create_ip已填,商户无需重复填写

		//sign已填,商户无需重复填写

		$unifiedOrder->setParameter("openid","$openid");//商品描述

		$unifiedOrder->setParameter("body","$good_name");//商品描述

		//自定义订单号，此处仅作举例

	//	$timeStamp = time();

	//	$out_trade_no = WxPayConf_pub::APPID."$timeStamp";

	//    $orderid=$_GET['orderid'];

		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号

		$unifiedOrder->setParameter("total_fee","$total_fee");//总金额

		$unifiedOrder->setParameter("notify_url",\WxPayConf_pub::NOTIFY_URL);//通知地址

		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型

		//非必填参数，商户可根据实际情况选填

		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号

		//$unifiedOrder->setParameter("device_info","XXXX");//设备号

		//$unifiedOrder->setParameter("attach","XXXX");//附加数据

		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间

		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间

		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记

		//$unifiedOrder->setParameter("openid","XXXX");//用户标识

		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

		$prepay_id = $unifiedOrder->getPrepayId();

		//=========步骤3：使用jsapi调起支付============

		$jsApi->setPrepayId($prepay_id);

		$jsApiParameters = $jsApi->getParameters();

		//echo $jsApiParameters;

		//$ja = $unifiedOrder->createXml();

        $this->assign('out_trade_no',$out_trade_no);

		$this->assign("jsApiParameters",$jsApiParameters);

		$this->assign("type",$type);

		$this->assign("id",$orderid);

        //查询是否是参团 

        $ret = M('orderlist')->where("id={$orderid} and is_groupbuy=1")->getField();

        if($ret){

          $this->assign('ct',1);  

        }else{

            $this->assign('ct',0); 

        }



		//print_r($jsApiParameters);

		//echo $total_fee;

		//session('orderid','50');

		//var_dump(session('orderid'));

		//echo "1".C('orderid');

		//echo I('get.orderid');

        $this->assign('gid',$gid);

        $this->assign('gchongzhi',$gchongzhi);

		$this->display();

    }

    function delectCanTuan(){

        $userid = session('member_id');

        $id = I('get.id',0,'int');

        $ret = M('orderlist')->where("id=%d and userid={$userid} and orderstate not in(2,3)",$id)->delete();
        if($ret){
            M('orderdetail')->where("orderid=%d",$id)->delete();
        }

        header("Location: ".U("Home/Member/main"));

    }

//    public function jsapi(){

//		

//		vendor('WxpayAPI_php_v3.example.WxPay#JsApiPay');

//		vendor('WxpayAPI_php_v3.example.log');

//		$orderid = I('get.orderid');

//		

//		

//		//echo "$orderid";

//		if(!empty($orderid)){

//			$m = M('orderlist');

//			$arr = $m->where("id=$orderid")->find(); //根据订单号查找订单详情  

//			$out_trade_no=$arr['orderno'];

//			$total_fee=$arr['total_price'];

//		}else{

//		

//			$state=$_GET['state'];

//		//    echo $state;

//			$str = stripslashes($state);

//			$arr=json_decode($str,1);

//		//var_dump($arr);

//			$out_trade_no=$arr['out_trade_no'];

//			$total_fee=$arr['total_fee'];

//		}

//		

//		

//		//初始化日志

//		//$logHandler= new \CLogFileHandler("../logs/".date('Y-m-d').'.log');

//		//$log = Log::Init($logHandler, 15);

//		

//		//①、获取用户openid

//		$tools = new \JsApiPay();

//		$openId = $tools->GetOpenid();

//		

//		//②、统一下单

//		$input = new \WxPayUnifiedOrder();

//		$input->SetBody("test"); //商品描述

//		$input->SetAttach("test");

//		$input->SetOut_trade_no($out_trade_no);//商户订单号

//		$input->SetTotal_fee($total_fee); //总金额

//		

//		$input->SetTime_start(date("YmdHis")); //交易起始时间

//		$input->SetTime_expire(date("YmdHis", time() + 600)); //交易结束时间

//		$input->SetGoods_tag("test"); //商品标记

//		$input->SetNotify_url("http://www.hedeqb.com/Order/notify"); //通知地址

//		$input->SetTrade_type("JSAPI"); //交易类型

//		$input->SetOpenid($openId); //用户标识

//		$order = WxPayApi::unifiedOrder($input);

//		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';

//		//printf_info($order);

//		$jsApiParameters = $tools->GetJsApiParameters($order);

//		

//		$this->assign("jsApiParameters",$jsApiParameters);	

//		

//		

//		//获取共享收货地址js函数参数

//		$editAddress = $tools->GetEditAddressParameters();

//		

//		$this->assign("jsApiParameters",$jsApiParameters);	

//		

//		$this->display();

//    }

//	

//	

    public function notify_url(){

                    $orderid = session('orderid');
					$type = I('get.type');
					if(empty($orderid))
					{
						header("Location:http://goead.ysxdgy.com/member/orderlist");
						exit;
					}
					if($type==1){
                        $orderlistModel = M('orderlist');
						$orderdetailtModel = M('orderdetail');
                        $orderlist = $orderlistModel->field('is_delivery,orderno,code,is_groupbuy,groupbuyid,telephone')->where("id=%d",$orderid)->find();//是否是上门提货
                        
					    if($orderlist['is_delivery'])
						{
                            $telephone = $orderlist['telephone'];
                            $orderstate = 3;
							//判断团购订单情况下的状态
							if($orderlist['is_groupbuy']==0 && $orderlist['code']!="")
							{
								sendmessage(2,$telephone,$orderlist['code'],$orderlist['orderno']);
							}
                        }
						else
						{
                            $orderstate = 2;
                        }
						
                        $pay_status = 1;
						$pay_status = 1;
						$rs = $orderlistModel->where("id={$orderid}")->save(array('orderstate'=>$orderstate,'pay_status'=>$pay_status,'groupbuy_ok'=>0));
                        if($rs){
                            if($orderlist['is_groupbuy'])
							{
                                session("orderid",null);
								header("Location:http://goead.ysxdgy.com/Order/pintuan/orderid/$orderid");
								exit;
                            }
							else
							{
								session("orderid",null);
								header("Location:http://goead.ysxdgy.com/Member/dsh"); exit;
                            }
                        }
						else
						{
							session("orderid",null);
							header("Location:http://goead.ysxdgy.com/Member/dsh"); exit;
                         }

					}else{

					   $id=I('get.id');

                       $gid = I('get.gid');

                       $gchongzhi = I('get.gchongzhi');

					   $uid=$_SESSION['member_id'];

					   $action=D('Cash');

					   $info = $action->GetInfomation($id);

					   $cash = $info['cash'];

					   $action->addpayment($uid,$cash);

					  $m = M('chong');

					  $data['is_pay']=1;

					  $rs = $m->where("id=$id")->save($data);

					  if($rs !== false){

							$this->assign('zhuangtai','充值成功');

                        if(0!=$gid){

                            $shopping = session('shopping');

                            $this->success('',U('Home/Order/addorder',array('gid'=>$gid,'color'=>$shopping['color'],'num'=>$shopping['num'],'cenghigh'=>$shopping['cenghigh'])));

                        }else{

                            if(0!=$gchongzhi){

                                $this->success('',U('/Home/Order/addcartorder'));

                            }else{

							    $this->success('',U('Home/Cash/chong'));

                            }

                        }

					  }else{

							$this->assign('zhuangtai','充值成功1');

							$this->success('',U('Home/Cash/chong'));

					  }

					}

				/*}

				$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");

			}*/

			//商户自行增加处理流程,

			//例如：更新订单状态

			//例如：数据库操作

			//例如：推送支付完成信息

		//}

		//$this->display();

		}

	//取消订单

		function cancelOrder(){

            $userid = session('member_id');
            $data = I('post.data');
            if($userid){

                $id = I('post.id',0,'int');

                $orderlistModel = M('orderlist');

                $orderlist = $orderlistModel->field('id')->where("id=%d and userid={$userid}",$id)->find();

                if($orderlist){

                    $ret = $orderlistModel->where("id=%d",$id)->save(array('is_refund'=>1,'reason'=>$data));

                    if($ret){

                        echo 1;

                    }else{

                        echo 0;

                    }

                }

            }

        }
		
	//取消订单

		function cancelOrder1(){

            $userid = session('member_id');
            $data = I('post.data');
            if($userid){

                $id = I('post.id',0,'int');

                $orderlistModel = M('orderlist');

                $orderlist = $orderlistModel->field('id')->where("id=%d and userid={$userid}",$id)->find();

                if($orderlist){

                    $ret = $orderlistModel->where("id=%d",$id)->save(array('orderstate'=>6,'reason'=>$data));

                    if($ret){

                        echo 1;

                    }else{

                        echo 0;

                    }

                }

            }

        }
		
		

        //确认收货

        function affirmOrder(){

            $userid = session('member_id');

            if($userid){

                $id = I('post.id',0,'int');

                $orderlistModel = M('orderlist');

                $orderlist = $orderlistModel->field('id')->where("id=%d and userid={$userid}",$id)->find();

                if($orderlist){

                    $ret = $orderlistModel->where("id=%d",$id)->save(array('orderstate'=>4,'confirm_status'=>1));

                    if($ret){

                        echo 1;

                    }else{

                        echo 0;

                    }

                }

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

}