<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class MarketingController extends CommonController {
	
    public function index(){
		
		$is_show=I('get.is_show');
		
        $action=D('Coupon');
        $rsdate=$action->getCouponList();
        $rs=$rsdate['list'];
		
		
		$counts=$action->where('1=1')->count();
		$count1=$action->where('is_show=1')->count();
		$count2=$action->where('is_show=2')->count();
		
		
		
        $this->assign('categorylist',$categorylist);
        $this->assign('goodslist',$rs);
		$this->assign('count',$rsdate['count']);
		$this->assign('counts',$counts);
		$this->assign('count1',$count1);
		$this->assign('count2',$count2);
		$this->assign('is_show',empty($is_show)?1:$is_show);
        $this->assign('page',$rsdate['page']);
        $this->assign('munetype',8);
        $this->display();
    }


    public function add(){

		$action=M('coupon');
		
		$title=I('post.title');
		if(!empty($title)){
			$data['goods_id'] = implode(',',$_POST['goodsid']);
			$data['title']=$title;
			$data['price']=I('post.price');
			$data['is_order']=I('post.is_order');
			$data['is_order_price']=I('post.is_order_price');
			if($data['is_order']==1){$data['is_order_price']='';}
			$data['kucun']=I('post.kucun');
			$data['pic']=I('post.pic');
			$data['rank']=I('post.rank');
			$data['exchange']=I('post.exchange');
			$data['is_exchange']=I('post.is_exchange');
			$data['content']=I('post.content');
			$data['start_time']=I('post.start_time');
			$data['end_time']=I('post.end_time');
			$data['addtime']=Gettime();
			
			$result=$action->add($data);
			if($result){
				$this->success('添加成功',U('/Admin/Marketing/index'));exit;
				}else{
					$this->error('添加失败');
					}
			}
		$goodsModel = M('goods');

		$goodsRecord = $goodsModel->select();

		$this->assign('goods', $goodsRecord);
        $this->assign('munetype', 8);
        $this->display();

    }
	
    public function delmarketing(){
        $couponid=I('get.id');
		$couponids=I('get.ids');
        $m = M("coupon"); 
		if($couponid){
        $rs=$m->where("id=$couponid")->setField('is_show',2); 
		}else{
			$rs=$m->where("id=$couponids")->delete(); 
			}
        if($rs){
            $this->success('作废成功',U('/Admin/Marketing/index'));
        }else{
            $this->error('作废失败');
        }
    }

    public function edit(){

        $markid=I('get.id');
		
		$action=M('coupon');
		
		$title=I('post.title');
		if(!empty($title)){
			$data['goods_id'] = implode(',',$_POST['goodsid']);
			$data['title']=$title;
			$data['price']=I('post.price');
			$data['is_order']=I('post.is_order');
			$data['is_order_price']=I('post.is_order_price');
			if($data['is_order']==1){$data['is_order_price']='';}
			$data['kucun']=I('post.kucun');
			$data['start_time']=I('post.start_time');
			$data['end_time']=I('post.end_time');
			//$data['pic']=I('post.pic');
			$data['rank']=I('post.rank');
			$data['exchange']=I('post.exchange');
			$data['is_exchange']=I('post.is_exchange');
			$data['content']=I('post.content');
			$result=$action->where('id='.$markid)->save($data);
			
			if($result){
				$this->success('修改成功',U("/Admin/Marketing/index"));exit;
				}else{
						$this->success('没有修改',U("/Admin/Marketing/index"));exit;
					}
			}
		
		$goodsModel = M('goods');

		$goodsRecord = $goodsModel->select();
		$this->assign('goods', $goodsRecord);

		$result=$action->where('id='.$markid)->find();
		$result['goods_ids'] = explode(',',$result['goods_id']);
		$this->assign('editmarketing', $result);

        $this->assign('munetype', 8);

        $this->display();
        }



}