<?php
namespace Home\Controller;
use Home\Controller\CommonaddpidController;
class IndexController extends CommonaddpidController {
    public function index(){
		
		//定位
		$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $_SERVER["REMOTE_ADDR"]);  
		if(empty($res)){ return false; }  
		$jsonMatches = array();  
		preg_match('#\{.+?\}#', $res, $jsonMatches);  
		if(!isset($jsonMatches[0])){ return false; }  
		$json = json_decode($jsonMatches[0], true);  
		if(isset($json['ret']) && $json['ret'] == 1){  
			$json['ip'] = $ip;  
			unset($json['ret']);  
		}else{  
			return false;  
		}  
		
		session('address_city',$json['city']);
		
		
        $action=D('Goods');
		$pic_act=D('Pic');
		$pic1s=$pic_act->where('classid=1')->order('sequence desc')->select();
		$pic2s=$pic_act->where('classid=2')->order('sequence desc')->select();
		
		$pic1=$pic_act->urls_name($pic1s); //加入链接地址
		$pic2=$pic_act->urls_name($pic2s); //加入链接地址
		
        $rs=$action->getgoodslist(0);   //获取在售的商品

		$D = D('Activity');
		$hd1 = $D->getList(1);
		$hd2 = $D->getList(2);
		$hd3 = $D->getList(3);
		
		$m = M('goods_guige');
		foreach($hd1 as $key=>$value)
		{
			$guigelist = $m->where('goodsid='.$value['goodsid'])->order('groupprice asc')->select();
			$hd1[$key]['price'] = intval($guigelist[0]['groupprice']);
		}
		
		foreach($hd2 as $key=>$value)
		{
			$guigelist = $m->where('goodsid='.$value['goodsid'])->order('groupprice asc')->select();
			$hd2[$key]['price'] = $guigelist[0]['groupprice'];
		}
		
		foreach($hd3 as $key=>$value)
		{
			$guigelist = $m->where('goodsid='.$value['goodsid'])->order('groupprice asc')->select();
			$hd3[$key]['price'] = $guigelist[0]['groupprice'];
		}
		

		$this->assign('hd1',$hd1);
		$this->assign('hd2',$hd2);
		$this->assign('hd3',$hd3);
		
        $this->assign('goodslist',$rs['list']);
		$this->assign('pic1',$pic1);
		$this->assign('pic2',$pic2);
		$this->assign('foottype',1);
        $this->display();
    }
	
	
	/**
     *
     *优惠券
     * @author Chandler_qjw  ^_^
     */
    public function yhq(){
		$date = date('Y-m-d H:i:s',time());
    	$couponlist = M("coupon")->field('a.*')->alias('a')->join("left join ys_coupon_record b on a.id=b.coupon_id")->where("receive < kucun and '{$date}' > start_time and '{$date}' < end_time and b.id is null")->order('rank desc,id desc')->select();
    	$this->assign('couponlist',$couponlist);
		$this->assign('foottype',1);
        $this->display();
    }

    //领取优惠券
    public function receiveYhq(){
    	$id = I('post.id',0,'int');
    	$member_id = session('member_id');
    	if($member_id){
	    	$couponModel = M('coupon');
	    	$date = date('Y-m-d H:i:s',time());
	    	$id = $couponModel->where("id = %d and receive < kucun and '{$date}' > start_time and '{$date}' < end_time",$id)->getField('id');
    		if($id){
    			M('coupon_record')->add(array('user_id'=>$member_id,'coupon_id'=>$id,'addtime'=>$date));
    			$ret = $couponModel->where('id = %d',$id)->setInc('receive',1);
    			if($ret){
    				echo 1;
    			}else{
    				echo 0;
    			}
    		}
    	}
    }
}    