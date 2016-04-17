<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class GoodsController extends CommonController {


    public function prodet(){

        $gid=I('get.gid');
        $action=D('Goods');
//        echo $gid;
        $rs=$action->getGoodsdetail($gid);
//        var_dump($rs);
        $this->assign('goodsdetail',$rs);
        $this->display();
    }

    public function catgory(){

        $this->display();
    }
	
	public function category(){
		$isguanzhu = panduanguanzhu(session('member_id'));
		$this->assign('isguanzhu',$isguanzhu);

		$action=M('category');
		$cates=$action->where('fid=0 and is_show=1')->order('sortrank')->select();
		$this->assign('cates',$cates);
		$this->assign('foottype',2);
        $this->display();
    }
	public function activitys(){
		$id = I('get.id',0,'int');
		if($id){
			$ids = M('Activitys')->field('activitys_img,activitys_goods_ids')->where("id=$id")->find();
			if($ids){
				$action=D('goods');
				$goods = $action->where("id in($ids[activitys_goods_ids]) and is_show=1")->select();	
				$goods_list=$action->getGoodsguige($goods);	
				$this->assign('goods',$goods_list);
				$this->assign('activitysImg',$ids['activitys_img']);
			}
		}
        $this->display();
	}
	
	public function category_list(){
		$isguanzhu = panduanguanzhu(session('member_id'));
		$this->assign('isguanzhu',$isguanzhu);
		
		$pid=I('get.pid');
		$action=D('goods');
		$cate_act=M('category');
		$cate_list=$cate_act->where('fid='.$pid)->select();
		
		if($cate_list)
		{
			foreach($cate_list as $key=>$value)
			{
				$goods = $action->where('(classid='.$value['id'].' or othercid='.$value['id'].') and is_show=1')->select();	
				$goods_list=$action->getGoodsguige($goods);
				$cate_list[$key]['goodslist'] = $goods_list;
			}	
			
		}
		else
		{
				$goods = $action->where('(classid='.$pid.' or othercid='.$pid.') and is_show=1')->select();	
				$goods_list=$action->getGoodsguige($goods);
		}
			
		$this->assign('goods',$goods_list);
		$this->assign('cate_list',$cate_list);
		$this->assign('foottype',2);
		$this->assign('types',$type);
        $this->display();
    }
	
	
	public function pro_isnew(){
		
		
		$action=D('goods');
		
		
		$goods = $action->where('is_new=1')->select();	
		$goods_list=$action->getGoodsguige($goods);
		
		$this->assign('goods',$goods_list);
		$this->assign('foottype',2);
		$this->assign('types',$type);
        $this->display();
    }
	
	
	
	public function buy(){
		
		$uid=$_SESSION['member_id'];

		$isguanzhu = panduanguanzhu($uid);
		$this->assign('isguanzhu',$isguanzhu);

		$id=I('get.id');
		$act=D('goods');
		$act_guige=M('goods_guige');
		$goods_edit=$act->where('id='.$id)->find();
		
		if($goods_edit['parid']==0){$pid=$goods_edit['classid'];}else{$pid=$goods_edit['parid'];}
		
		$goods_edit1=$act->getGoodsguigeone($goods_edit);
		

		$picarr = explode(",",$goods_edit1['pic1']);
		$goods_edit1['pic1list'] = $picarr;
		$goods_guige=$act_guige->where('goodsid='.$id)->order('price asc')->select();

		$goods_edit1['detail']=str_ireplace('\"','"',htmlspecialchars_decode($goods_edit1['detail']));
		
		$this->assign('goods_edit',$goods_edit1);
		$this->assign('goods_guige',$goods_guige);
		if($uid)
		{
			
			// 获取购物车数量
			$data['userid'] = $uid;
			$data['is_show'] = 1;
			$shopping=D('shopping')->shop_list($uid);
			$counts=count($shopping);
			$this->assign('shopping',$counts);
			
			// 获取是否收藏
			$re=M('collection')->where('userid='.$uid.' and goodsid='.$id)->find();
			if($re)
			{
				$is_ty=1;
			}
			else
			{
				$is_ty=0;
			}
		}
		$orderlistModel = M("Orderlist");
		//待发货(团购中)
		$stateOne1 = $orderlistModel->where("goods_id=".$id." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=0")->count();
		//待发货(团购成功)
		$stateOne2 = $orderlistModel->where("goods_id=".$id." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=1")->count();
		//待发货(单买)
		$stateTwo = $orderlistModel->where("goods_id=".$id." and orderstate=3 and is_groupbuy=0")->count();
		//已出货
		$stateThree = $orderlistModel->where("goods_id=".$id." and (is_delivery=0 and orderstate in(3,4) or is_delivery=1 and orderstate=4)")->count();
		
		$ordercount = $stateOne1+$stateOne2+$stateTwo+$stateThree;//M('orderdetail')->where('goodsid='.$id)->count(); //商品已售量
		$ordercounts=$ordercount+$goods_edit['virtual'];   //总销售量=虚拟销售量+已销售量
		
		$confinfo = get_js_sdk("wx00dc312216f31fad","0dad1ca43940dfc356d47d29052fcc25");  // APPID,APP_SECRET
		$this->assign("confinfo",$confinfo);
		
		$remainingcount = $goods_edit1['kucun']-$ordercount; //商品剩余量
		$this->assign('uid',$uid);
		$this->assign('ordercount',$ordercounts);
		$this->assign('remainingcount',$remainingcount);
		$this->assign('is_ty',$is_ty);
		$this->assign('pid',$pid);
		$this->assign('foottype',2);
        $this->display();
		
    }
	
	
	public function buyceshi(){
		
		$uid=$_SESSION['member_id'];

		$id=I('get.id');
		$act=D('goods');
		$act_guige=M('goods_guige');
		$goods_edit=$act->where('id='.$id)->find();
		
		if($goods_edit['parid']==0){$pid=$goods_edit['classid'];}else{$pid=$goods_edit['parid'];}
		
		$goods_edit1=$act->getGoodsguigeone($goods_edit);
		

		$picarr = explode(",",$goods_edit1['pic1']);
		$goods_edit1['pic1list'] = $picarr;
		$goods_guige=$act_guige->where('goodsid='.$id)->order('price asc')->select();

		$goods_edit1['detail']=str_ireplace('\"','"',htmlspecialchars_decode($goods_edit1['detail']));
		
		$this->assign('goods_edit',$goods_edit1);
		$this->assign('goods_guige',$goods_guige);
		if($uid)
		{
			
			// 获取购物车数量
			$data['userid'] = $uid;
			$data['is_show'] = 1;
			$shopping=D('shopping')->shop_list($uid);
			$counts=count($shopping);
			$this->assign('shopping',$counts);
			
			// 获取是否收藏
			$re=M('collection')->where('userid='.$uid.' and goodsid='.$id)->find();
			if($re)
			{
				$is_ty=1;
			}
			else
			{
				$is_ty=0;
			}
		}
		$orderlistModel = M("Orderlist");
		//待发货(团购中)
		$stateOne1 = $orderlistModel->where("goods_id=".$id." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=0")->count();
		//待发货(团购成功)
		$stateOne2 = $orderlistModel->where("goods_id=".$id." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=1")->count();
		//待发货(单买)
		$stateTwo = $orderlistModel->where("goods_id=".$id." and orderstate=3 and is_groupbuy=0")->count();
		//已出货
		$stateThree = $orderlistModel->where("goods_id=".$id." and (is_delivery=0 and orderstate in(3,4) or is_delivery=1 and orderstate=4)")->count();
		
		$ordercount = $stateOne1+$stateOne2+$stateTwo+$stateThree;//M('orderdetail')->where('goodsid='.$id)->count(); //商品已售量
		$ordercounts=$ordercount+$goods_edit['virtual'];   //总销售量=虚拟销售量+已销售量
		
		$confinfo = get_js_sdk("wx00dc312216f31fad","0dad1ca43940dfc356d47d29052fcc25");  // APPID,APP_SECRET
		$this->assign("confinfo",$confinfo);
		
		$remainingcount = $goods_edit1['kucun']-$ordercount; //商品剩余量
		$this->assign('uid',$uid);
		$this->assign('ordercount',$ordercounts);
		$this->assign('remainingcount',$remainingcount);
		$this->assign('is_ty',$is_ty);
		$this->assign('pid',$pid);
		$this->assign('foottype',2);
        $this->display();
		
    }
	
	
	
	
	public function collection(){
		
		
		$act=D('collection');
		
		$rs=$act->add_collection();
		
		$this->ajaxReturn($rs);
		return;
		
		
    }
	
	
	
	
	
	public function buy_add(){
		
		$id=I('get.id');
		$act=D('goods');
		$act_guige=M('goods_guige');
		$goods_edit=$act->where('id='.$id)->find();
		$goods_edit1=$act->getGoodsguigeone($goods_edit);
		$goods_guige=$act_guige->where('goodsid='.$id)->select();
		
		$this->assign('goods_edit',$goods_edit1);
		$this->assign('goods_guige',$goods_guige);
		$this->assign('foottype',2);
        $this->display();
    }
	
	
	
	
	
    public function listdot(){
		$this->assign('foottype',2);
        $this->display();
    }
    public function search(){

        $action=D('Goods');
        $rs=$action->getgoodslist(0);   //获取在售的商品
//         var_dump($rs);

//        session_unset();
        $this->assign('goodslist',$rs['list']);
        $this->display();
    }

    public function cart(){

        $this->display();
    }
  public function lists(){
        $this->assign('foottype',2);
        $this->display();
    }
  public function gwc(){
        $this->assign('foottype',3);
        $this->display();
    }

	public function ajaxFreight(){
    	$guigeid = I("post.guigeid");
		//需要计算运费的商品
    	$goodsModel = M('goods')->where("id=".I('post.id',0,'int'))->find();
    	$goodsGuigeModel = M("goods_guige")->where('id='.$guigeid)->find();
		if(I('post.id')==174){
			echo 0;
		}else{
			if(is_array($goodsModel)){
				if(I('post.type',0,'int')){
				//代表同城运费
					//查找同城运费记录
					$freightModel = M('freight')->where("is_samecity=1")->find();
				}else{
					//根据id查询城市的运费
					$freightModel = M('freight')->where("id=".I('freightId'))->find();
				}
				//计算运费
				$temp = $goodsGuigeModel['weight'] * I('post.num',1,'int') - $freightModel['sweight'];
				if($temp>0){
					$temp = ceil($temp);//向上取整
					$yunfei = $freightModel['sprice']+$temp*$freightModel['xwprice'];
				}else{
					$yunfei = $freightModel['sprice'];
				}
				echo $yunfei;
				//echo $yunfei*I('post.num',1,'int');
			}
		}
	}


}