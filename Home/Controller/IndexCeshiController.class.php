<?php
namespace Home\Controller;
use Home\Controller\CommonaddpidController;
class IndexCeshiController extends CommonaddpidController {
    public function index(){
    	

		//定位
		$getIp=$_SERVER["REMOTE_ADDR"];
		  $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ&ip={$getIp}&coor=bd09ll");
		  $json = json_decode($content);
		  $xx=$json->{'content'}->{'point'}->{'x'};//按层级关系提取经度数据
		  $yy=$json->{'content'}->{'point'}->{'y'};//按层级关系提取纬度数据
		  
		
		session('address_city_x',$xx);
		session('address_city_y',$yy);
		
		
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
			$hd1[$key]['price'] = $guigelist[0]['groupprice'];
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
		
		$this->assign('xx',$xx);
		$this->assign('yy',$yy);
		
		$this->assign('foottype',1);
        $this->display();
    }
    public function ceshi1(){
    	$this->display();
    }
    public function ceshi2(){
    	$this->display();
    }
    public function ceshi3(){
    	$this->display();
    }
    public function guanzhu(){
    	$token = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx00dc312216f31fad&secret=0dad1ca43940dfc356d47d29052fcc25");
    	$token_json = json_decode($token,true);
    	$openid = M('Member')->where("id=".session('member_id'))->getField('weixin_openid');
    	$json = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token_json['access_token']."&openid=".$openid."&lang=zh_CN");
		$ret = json_decode($json,true);
		var_dump($ret['subscribe']);
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
	
	
	/**
	
	 * 通用通知接口demo
	
	 * ====================================================
	
	 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
	
	 * 商户接收回调信息后，根据需要设定相应的处理流程。
	
	 * 
	
	 * 这里举例使用log文件形式记录回调信息。
	
	*/
	public function notify_url1(){


		vendor('Wxpay.WxPayPubHelper.WxPayPubHelper');	
		vendor ('Wxpay.demo.log_');
		
		//使用通用通知接口
		$notify = new \Notify_pub();

		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
		$notify->saveData($xml);
		
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){

			$notify->setReturnParameter("return_code","FAIL");//返回状态码

			$notify->setReturnParameter("return_msg","签名失败");//返回信息

		}else{

			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码

		}

		$returnXml = $notify->returnXml();

		echo $returnXml;

		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
		//以log文件形式记录回调信息
		$log_ = new \Log_();
		$log_name="./notify_url.log";//log文件路径
		$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");

		if($notify->checkSign() == TRUE)

		{

			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				//$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				//$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
			}

			else{

				//此处应该更新一下订单状态，商户自行增删操作
				$a=$notify->data["result_code"];
				if($a=='SUCCESS'){
					
					$out_trade_no = $notify->data["out_trade_no"];
					$orderlistModel = M('orderlist');
					$orderdetailtModel = M('orderdetail');
					//是否是上门提货
					$orderlist = $orderlistModel->field('id,is_delivery,orderno,code,is_groupbuy,groupbuyid,telephone')->where("orderno='{$out_trade_no}'")->find();

					//是否是上门提货 end
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
					$rs = $orderlistModel->where("orderno='{$out_trade_no}'")->save(array('orderstate'=>$orderstate,'pay_status'=>$pay_status,'groupbuy_ok'=>0));
					$sql = M()->getlastsql();
					$m = M('jilu');
					$data['orderid'] = $out_trade_no;
					$data['content'] = $out_trade_no.",".$rs;
					$data['addtime'] = date("Y-m-d H:i:s");
					$m->add($data);
					
					
				}
				$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");
			}
			//商户自行增加处理流程,
			//例如：更新订单状态
			//例如：数据库操作
			//例如：推送支付完成信息

		}
	}
	
	
	
	
	
}    