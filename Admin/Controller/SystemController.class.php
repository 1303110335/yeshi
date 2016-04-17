<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class SystemController extends CommonController {
    public function set(){

        $this->assign('munetype',9);
        $this->display();
    }
	
	public function updatepwd(){
		
		$action=D('User');
		$pass=I('post.password');
		if($pass){
			$md5_pass=md5($pass);
			$re=$action->where("username='".$_SESSION['admin']."'")->setField('password',$md5_pass);
			if($re){
			$this->success('修改成功',U('/Admin/System/updatepwd'));
			}else{
				$this->error('修改失败');
				}
			}
		
		
        $this->assign('munetype',9);
        $this->display('updatepwd');
    }
	
	public function administrator(){
        
		$action=D('User');
		$user_list = $action->user_list();
		$m = M('admin_cate');
		foreach($user_list as $key => $value)
		{
			$catename = $m->field('per_name')->where('id='.$value['cate'])->find();
			$user_list[$key]['catename'] = $catename['per_name'];
		}
		
		$cate_list = $m->field('id,per_name')->select();
		
		$this->assign('user_list',$user_list);
		$this->assign('cate_list',$cate_list);
		$this->display();
    }
	
	public function addAdmin(){

		$username=I('post.username');
		$password=I('post.password');
		$cate=I('post.cate');
		$action = D('User');
		$rs = $action->addAdmin($username,$password,$cate);
		if($rs==0)
		{
			$data['info']   =   '账号已存在'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		elseif($rs==2)
		{
			$data['info']   =   '添加失败'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		elseif($rs==1)
		{
			$data['info']   =   '添加成功'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		else
		{
			$data['info']   =   '未知错误'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		
        $this->ajaxReturn($data);
        return;
		
    }
	

 	public function editAdmin(){
		

		$username = I('post.username');
		$password = I('post.password');
		$cate = I('post.cate');
		$id = I('post.id');

		$action=D('User');
		$rs=$action->editAdmin($id,$username,$password,$cate);
		if($rs==0)
		{
			$data['info']   =   '账号不存在'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		elseif($rs==2)
		{
			$data['info']   =   '修改失败'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		elseif($rs==1)
		{
			$data['info']   =   '修改成功'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		else
		{
			$data['info']   =   '未知错误'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}

        $this->ajaxReturn($data);
        return;
    }
	
 	public function ableAdmin(){

		$id = I('post.id');
		$able=I('post.able');
		$action=D('User');
		$rs=$action->able($id,$able);
		if($rs)
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

	public function deladmin(){

		$id=I('post.id');
		$action = D("User");
		$rs = $action->del($id);
		if($rs)
		{
			$data['info']   =   '删除成功'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
		}
		else
		{
			$data['info']   =   '删除失败'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
		}
        $this->ajaxReturn($data);
        return;
    }
    public function ceshi(){
    	//查询所有设置过取货时间的商品
    	$goodsList = M("Goods")->field('id,keeptime')->where("keeptime!=0")->select();
    	if(!empty($goodsList)){
    		$orderModel = M("Orderlist");
	    	foreach($goodsList as $val){
	    		$gid .= $val['id'].',';
	    	}
	    	$gid = substr($gid,0,-1);
	    	//查询条件为（所有商品id集合，订单为2万之后的，上门取货，待收货）
    		$orderList = $orderModel->field('id,goods_id,orderno,paytime,pay_fee,is_groupbuy')->where("goods_id in($gid) and id>20000 and is_delivery=1 and orderstate=3")->select();
    		foreach($goodsList as $val){
    			foreach($orderList as $key => $va){
    				//goodsList里保存了商品的取货时间 将商品集合与订单集合对应
    				if($val['id']==$va['goods_id']){
    					//$keeptime = $val['keeptime']*3600;//该商品取货时间戳
    					//$paytime = strtotime($va['paytime']);//该商品订单付款时间戳
    					//判断当前时间是否大于付款时间戳+取货时间戳则需要取消订单
    					if(time()>$val['keeptime']*3600+strtotime($va['paytime'])){
    						//取消订单
    						/*$succ = $this->refund($va['orderno'],$va['pay_fee']*100);
			                if($succ){
			                    $data['orderstate'] = 7;
			                    $data['id'] = $va['id'];
			                    $data['is_refund'] = 2;
			                    $data['refund_fee'] = $va['pay_fee'];
			                    $data['refundtime'] = date('Y-m-d H:i:s');
			                    $data['reason'] = "付款$val[keeptime]后未取货";
			                    if($va['is_groupbuy']){
			                    	//$data['groupbuy_ok'] = 2;
			                    }
			                    $orderModel->save($data);
			                }*/
    						array_push($va,$val['keeptime'],date('Y-m-d H:i:s',$val['keeptime']*3600+strtotime($va['paytime'])));
    						var_dump($va);
    					}
    					unset($orderList[$key]);
    				}
    			}
    		}
    		unset($gid);
    		unset($data);
    		unset($orderModel);
    		unset($orderList);
    		unset($goodsList);
	    }
    }
    public function refund($no,$fee){
        vendor('Wxpay.WxPayPubHelper.WxPayPubHelper');
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
}