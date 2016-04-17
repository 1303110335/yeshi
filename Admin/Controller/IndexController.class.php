<?php
namespace Admin\Controller;
//use Think\Controller;
use Common\Controller\CommonController;
class IndexController extends CommonController {
    /**
     * 后台首页
     *
     * @author Chandler_qjw  ^_^
     */
    public function index(){
		
		$action=D('orderlist');
		$orderlist=$action->orderlist();
		
		$counts = $action->num_count('',$dealerid);
        $count1 = $action->num_count(1,$dealerid);
        $count2 = $action->num_count(2,$dealerid);
        $count4 = $action->num_count(4,$dealerid);
        $count5 = $action->num_count(5,$dealerid);
		
		$total_count1=$action->where('pay_status=1')->sum('total_price');
		$total_count2=$action->sum('refund_fee');
		//$count=$total_count1-$total_count2;
		//总盈利
        $count=$action->where($wheres."(pay_status=1 and orderstate in(2,3,4))")->sum('total_price');
		
		$arr=$action->this_count();
		
		$dealer = M('dealer')->field('id,longitude,latitude')->select();
        foreach($dealer as $key => $val)
		{
            $ary[$key]['longitude'] = substr($val['longitude'],0,-1);
            $ary[$key]['latitude'] = substr($val['latitude'],0,-1);
            $aryids[$key] = $val['id'];
        }
		$getIp=$_SERVER["REMOTE_ADDR"];
		$content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ&ip={$getIp}&coor=bd09ll");
		$json = json_decode($content);
        $this->assign('dealerlist',$ary);
		$xz = $json->{'content'}->{'point'}->{'x'};
		$yz = $json->{'content'}->{'point'}->{'y'};
		/*if($xz=='120.21937542' && $yz=='30.25924446'){
			$xz = '121.062586';
			$yz = '30.578004';
		}*/
		$this->assign('xz',$xz);
		$this->assign('yz',$yz);
        $this->assign('dealerlistcot',count($ary));
		
		$this->assign('counts',$counts);
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count4',$count4);
        $this->assign('count5',$count5);
		$this->assign('total_count1',$total_count1);
		$this->assign('total_count2',$total_count2);
		$this->assign('count',$count);
		$this->assign('this_count',$arr);
        $this->assign('munetype', 1);
		$this->assign('urlname', 'index');
        $this->display();

    }

    public function remotepost($ary,$url){
    	$postdata = http_build_query(
            $ary
        );
        $opts = array('http' =>
			array(
			  'method'  => 'POST',
			  'header'  => 'Content-type: application/json',
			  'content' => $postdata
			)
        );
        $context = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    }
	
/*
	online_order
	online_order_no	String(64)	是	订单号 必须唯一
	online_order_client_name	String(50)	是	订货人名称
	online_order_phone	String(20)	是	联系电话
	online_order_address	String(250)	否	收货地址
	online_order_memo	String(250)	否	订单备注
	online_order_total_money	Double	是	订单总金额(元)
	online_order_payment_money	Double	是	买家实付金额(元)
	online_order_create_time	Date	是	下单时间
	online_order_payment_time	Date	否	付款时间
	online_order_details	 	 	 
	item_num	Integer	是	商品唯一码
	online_item_price	Double	是	基本单位单价
	online_item_qty	Double	是	基本单位数量
	online_item_discount_money	Double	是	折扣金额(元)
	online_item_total_money	Double	是	订货金额(元)  服务端会验证online_item_qty * online_item_price = online_item_total_money   明细的online_item_total_money之和必须等于online_order_total_money
	online_order_total_money - 明细中的online_item_discount_money之和     =   online_order_payment_money
*/
	public function ceshi(){
		//erpaddorder(60507);
	}
	
	/**
     * 首页轮换图
     *
     * @author Chandler_qjw  ^_^
     */
    public function pic(){
		$type=I('get.type');
		$where['classid']=array('eq',$type);
		
		$action=M('pic');
		$result=$action->where($where)->order('sequence desc')->select();
		$count=count($result);
		
		$this->assign('piclist', $result);
		$this->assign('count', $count);
        $this->assign('munetype', 1);
		if($type==1){
		$this->assign('urlname', 'indexpic');
		}else{
		$this->assign('urlname', 'indextwopic');	
		}
        $this->display();

    }
	
	public function ajaxUpdateShop(){
		
        $goosid=I('post.goodsid');
        $m = D("Pic"); 
		
		$res=$m->where('id='.$goosid)->find();
		if($res['is_show']==1){
        	$rs=$m->where('id='.$goosid)->setField('is_show',0);
		}else{
			$rs=$m->where('id='.$goosid)->setField('is_show',1);
		}
		$data['info']=$res['is_show'];
		
        $this->ajaxReturn($data);
		return;
    }
	
	
	/**
     * 添加首页轮换图
     *
     * @author Chandler_qjw  ^_^
     */
    public function addpic(){
		$type=I('get.type');
		$action=M('pic');
		
		$title=I('post.title');
		if(!empty($title)){
			$data['title']=$title;
			$data['urls']=I('post.urls');
			$data['classid']=I('post.classid');
			$data['pic']=I('post.pic');
			$data['sequence']=I('post.sequence');
			$data['is_show']=I('post.is_show');
			$data['is_urls']=I('post.is_urls');
			$data['is_urls_type']=I('post.is_urls_type');
			
			if($data['is_urls_type']==4){
				$data['activitys_id']=I('post.activitys_id');
			}else{
				$data['goodsid']=I('post.goodsid');
			}
			if($data['is_urls']==2){
			$data['urls']=I('post.urls');
			$data['is_urls_type']=0;
			
			}else{
				$data['urls']='';
				}
			
			if($data['is_urls_type']==1){
			$data['is_urls_title']='优惠券链接';
			}elseif($data['is_urls_type']==2){
				$data['is_urls_title']='产品列表';
				}elseif($data['is_urls_type']==3){
					$data['is_urls_title']='单个产品';
					}elseif($data['is_urls_type']==4){
						$data['is_urls_title']='活动产品';
					}else{
						$data['is_urls_title']='';
						}
			$data['addtime']=Gettime();
			
			$result=$action->add($data);
			
			if($result){
				if($type==1){
				$this->success('添加成功',U('/Admin/Index/pic/type/1'));exit;
				}else{
					$this->success('添加成功',U('/Admin/Index/pic/type/2'));exit;
					}
				}else{
					$this->error('添加失败');
					}
			}
        $this->assign('munetype', 1);
		if($type==1){
		$this->assign('urlname', 'addpic');
		}else{
			$this->assign('urlname', 'addtwopic');
			}
			
		$activitysList = M('Activitys')->where('is_close=0')->order('id desc')->select();
		$this->assign('activitysList',$activitysList);
			
		$goodsModel = M('goods');
		$goodsRecord = $goodsModel->select();
		$this->assign('goods', $goodsRecord);
        $this->display();

    }
	
	
	
	
	/**
     * 修改首页轮换图
     *
     * @author Chandler_qjw  ^_^
     */
    public function editpic(){
		$picid=I('get.id');
		$type=I('post.classid');
		$action=M('pic');
		
		$title=I('post.title');
		if(!empty($title)){
			
			$data['title']=$title;
			$data['pic']=I('post.pic');
			$data['sequence']=I('post.sequence');
			$data['is_show']=I('post.is_show');
			$data['is_urls']=I('post.is_urls');
			$data['is_urls_type']=I('post.is_urls_type');
			
			if($data['is_urls_type']==4){
				$data['activitys_id']=I('post.activitys_id');
				$data['goodsid']=0;
			}else{
				$data['goodsid']=I('post.goodsid');
				$data['activitys_id']=0;
			}
			if($data['is_urls']==2){
			$data['urls']=I('post.urls');
			$data['is_urls_type']=0;
			
			}else{
				$data['urls']='';
				}
			
			if($data['is_urls_type']==1){
			$data['is_urls_title']='优惠券链接';
			}elseif($data['is_urls_type']==2){
				$data['is_urls_title']='产品列表';
				}elseif($data['is_urls_type']==3){
					$data['is_urls_title']='单个产品';
					}elseif($data['is_urls_type']==4){
						$data['is_urls_title']='活动产品';
					}else{
						$data['is_urls_title']='';
						}
			
			$result=$action->where('id='.$picid)->save($data);
			
			if($result){
				if($type==1){
				$this->success('修改成功',U("/Admin/Index/pic/type/1"));exit;
				}else{
					$this->success('修改成功',U("/Admin/Index/pic/type/2"));exit;
					}
				}else{
					if($type==1){
						$this->success('没有修改',U("/Admin/Index/pic/type/1"));exit;
						}else{
							$this->success('没有修改',U("/Admin/Index/pic/type/2"));exit;
							}
					}
			}
		
		$result=$action->where('id='.$picid)->find();
		if($result['goodsid']!="")
		{
			$goodsone=M('goods')->where('id='.$result['goodsid'])->find();
		}
		$result['goodsname'] = $goodsone['good_name'];
		
		$activitysList = M('Activitys')->where('is_close=0')->order('id desc')->select();
		$this->assign('activitysList',$activitysList);
		
		$this->assign('editpic', $result);
        $this->assign('munetype', 1);
		if($result['classid']==1){
		$this->assign('urlname', 'indexpic');
		}else{
		$this->assign('urlname', 'indextwopic');	
		}
		$goodsModel = M('goods');
		$goodsRecord = $goodsModel->select();
		$this->assign('goods', $goodsRecord);
		
        $this->display();

    }
	
	
	
	
	/**
     * 删除轮换图
     *
     * @author Chandler_qjw  ^_^
     */
    public function delpic(){
		$picid=I('get.id');
		$action=M('pic');
		
		if(!empty($picid)){
			
			$arr=$action->where('id='.$picid)->find();
			
			$result=$action->where('id='.$picid)->delete();
			
			if($result){
				if($arr['classid']==1){
				$this->success('删除成功',U('/Admin/Index/pic/type/1'));exit;
				}else{
					$this->success('删除成功',U('/Admin/Index/pic/type/2'));exit;
					}
				}else{
					$this->error('删除失败');
					}
			}
			
	
		
		

    }
	
	
	
	
	
	
	/**
     * 新进订单提示
     *
     * @author Chandler_qjw  ^_^
     */
    public function mp3(){
		
		
		$count=M('orderlist')->count();
		
		if($count!=$_SESSION['orderlist_count']){
		session('orderlist_count',$count);
		$data=1;
		$this->ajaxReturn($data);
		return;
		}else{
			$data=0;
			$this->ajaxReturn($data);
			return;
			}
    }
	
	public function setDealerid(){
		$index = I('post.index');
		if($index=='no'){
			$id = null;
		}else{
			$index = intval($index)+1;
			$id = M('dealer')->page($index.",1")->getField('id');
		}
		$id = null;
		session('dealerId',$id);
	}
	
	
	
	
	
	
	
	
	
	



}

