<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class CashController extends CommonController {
	
	
	
    public function index(){
		
		$dealerid = I('get.dealerid');
		if($dealerid){$wheres="dealerid=".$dealerid." and ";}
		if($dealerid!='' && $dealerid==0){$wheres="dealerid=0 and ";}
		
		$start_time = I('get.start_time');
		$end_time = I('get.end_time');
		
		
		$action=D('orderlist');
		
		$counts = $action->num_count('',$dealerid);
        $count1 = $action->num_count(1,$dealerid);
        $count2 = $action->num_count(2,$dealerid);
        $count4 = $action->num_count(4,$dealerid);
        $count5 = $action->where($wheres."is_refund=2")->count();
		//$count5 = $action->num_count_array_gt(array("orderstate"=>4),$dealerid);
		
		//总消费金额
		$total_count1=$action->where($wheres."(pay_status=1)")->sum('total_price');

		$total_count2=$action->where($wheres."refund_fee>0")->sum('refund_fee');
        //总盈利
        $count=$action->where($wheres."(pay_status=1 and orderstate in(2,3,4))")->sum('total_price');
		//$count=$total_count1;-$total_count2;
        //冻结金额 查找为团购的并且还没有成功的
        $dongjie = $action->where($wheres."is_groupbuy=1 and groupbuy_ok=0")->sum('total_price');
        $this->assign('dongjie',$dongjie);
		$arr=$action->thisday_count($dealerid);
		
		if($start_time || $end_time){
			$search=$action->search_cout($start_time,$end_time,$dealerid);
			}
		//echo M()->getlastsql();
		$action = D('Dealer');
        $dealerlist = $action->getdealerlist_nopage();
		
        //查询所有商品
        $goodslist = M('Goods')->field('id,good_name')->select();
        $this->assign('goodslist',$goodslist);
		
		$this->assign('dealerid',$dealerid);
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		$this->assign('counts',$counts);
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count4',$count4);
        $this->assign('count5',$count5);
		$this->assign('search',$search);
		$this->assign('total_count1',$total_count1);
		$this->assign('total_count2',$total_count2);
		$this->assign('count',$count);
		$this->assign('this_count',$arr);
		$this->assign('dealerlist',$dealerlist);
		
        $this->assign('munetype', 1);
		$this->assign('urlname', 'index');
        $this->display();    
		
		}
    //测试
    public function indexcs(){
        
        $dealerid = I('get.dealerid');
        if($dealerid){$wheres="dealerid=".$dealerid." and ";}
        if($dealerid!='' && $dealerid==0){$wheres="dealerid=0 and ";}
        
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        
        
        $action=D('orderlist');
        
        $counts = $action->num_count('',$dealerid);
        $count1 = $action->num_count(1,$dealerid);
        $count2 = $action->num_count(2,$dealerid);
        $count4 = $action->num_count(4,$dealerid);
        $count5 = $action->where($wheres."is_refund=2")->count();
        //$count5 = $action->num_count_array_gt(array("orderstate"=>4),$dealerid);
        
        //总消费金额
        $total_count1=$action->where($wheres."(pay_status=1)")->sum('total_price');

        $total_count2=$action->where($wheres."refund_fee>0")->sum('refund_fee');
        //总盈利
        $count=$action->where($wheres."(pay_status=1 and orderstate in(2,3,4))")->sum('total_price');
        //$count=$total_count1;-$total_count2;
        //冻结金额 查找为团购的并且还没有成功的
        $dongjie = $action->where($wheres."is_groupbuy=1 and groupbuy_ok=0")->sum('total_price');
        $this->assign('dongjie',$dongjie);
        $arr=$action->thisday_count($dealerid);
        
        if($start_time || $end_time){
            $search=$action->search_cout($start_time,$end_time,$dealerid);
            }
        //echo M()->getlastsql();
        $action = D('Dealer');
        $dealerlist = $action->getdealerlist_nopage();

        //查询所有商品
        $goodslist = M('Goods')->field('id,good_name')->select();
        $this->assign('goodslist',$goodslist);
        
        $this->assign('dealerid',$dealerid);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('counts',$counts);
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count4',$count4);
        $this->assign('count5',$count5);
        $this->assign('search',$search);
        $this->assign('total_count1',$total_count1);
        $this->assign('total_count2',$total_count2);
        $this->assign('count',$count);
        $this->assign('this_count',$arr);
        $this->assign('dealerlist',$dealerlist);
        
        $this->assign('munetype', 1);
        $this->assign('urlname', 'index');
        $this->display();    
        
    }

    public function excel(){
        $dealerid = I('get.dealerid');
        $wheres = "";
        if($dealerid){$wheres="dealerid=".$dealerid." and ";$wheres2="dealerid=".$dealerid." and ";}
        if($dealerid!='' && $dealerid==0){$wheres="dealerid=0 and ";$wheres2="dealerid=0 and ";}       
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        $goodsid = I('get.goodsid');

        $action=D('orderlist');
        if($goodsid){
            $wheres .= "goods_id=$goodsid and ";
            $wheres2 .= "goods_id=$goodsid and ";
        }
        if($start_time || $end_time){
            if($start_time){
                $wheres .= "addtime > '$start_time' and ";
                $wheres2 .= "refundtime > '$start_time' and ";
            }
            if($end_time){
                $wheres .= "addtime < '$end_time' and ";
                $wheres2 .= "refundtime < '$end_time' and ";
            }else{
                $end_time = "至今";
            }
        }else{
            //找出今天的记录
            $start_time = date('Y-m-d 00:00:00');
            $end_time = "至今";
            $wheres .= "addtime > '$start_time' and ";
            $wheres2 .= "refundtime > '$start_time' and ";
        }
        //查询所有交易成功记录
        $orderlist = $action->field('addtime,orderno,total_price,goods_id,userid')->where($wheres."pay_status = 1")->select();
        
        $money = 0;
        $refund_fee = 0;
        if(is_array($orderlist)){
            foreach($orderlist as $key => $val){
                //查询商品名称
                $val['goods_name'] = M('goods')->where("id=$val[goods_id]")->getField('good_name');
                $val['orderno'] = '`'.$val['orderno'];
                $val['refund_fee'] = $val['total_price']*0.006;
                $orderlist[$key] = $val;
                $money += $val['total_price'];
                $refund_fee += $val['refund_fee'];
            }
        }
        $data = $orderlist;
        array_push($data,
            array(),
            array('goods_name'=>'商品名称','addtime'=>'交易时间','orderno'=>'订单号','total_price'=>'总金额','refund_fee'=>'退款金额','feev'=>'退款时间')
        );
        //查询所有退款记录
        $orderlist2 = $action->field('addtime,refundtime,orderno,total_price,goods_id,userid,refund_fee')->where($wheres2."refund_fee is not null")->select();
        $money2 = 0;
        $refund_fee2 = 0;
        if(is_array($orderlist2)){
            foreach($orderlist2 as $key => $val){
                //查询商品名称
                $val['goods_name'] = M('goods')->where("id=$val[goods_id]")->getField('good_name');
                $val['orderno'] = '`'.$val['orderno'];
                $val['feev'] = '`'.$val['refundtime'];
                $money2 += $val['refund_fee'];
                $val['refund_fee'] = $val['refund_fee']*0.006;
                $refund_fee2 += $val['refund_fee'];
                array_push($data,$val);
            }
        }

        $expFileName="订单统计";
        $expCellName = array(
            array('goods_name','商品名称'),
            array('addtime','交易时间'),
            array('orderno','订单号'),
            array('total_price','总金额'),
            array('refund_fee','手续费'),
            array('feev','')
        );
        array_push($data,
            array(),
            array('goods_name'=>$start_time." ~ ".$end_time),
            array('goods_name'=>'总交易单数','addtime'=>'总交易金额','orderno'=>'退款单数','total_price'=>'退款金额','refund_fee'=>'手续费','feev'=>'盈利'),
            array('goods_name'=>count($orderlist),'addtime'=>$money,'orderno'=>count($orderlist2),'total_price'=>$money2,'refund_fee'=>($refund_fee-$refund_fee2),'feev'=>$money-$money2-($refund_fee-$refund_fee2))
        );
        $expTableData = $data;
        exportExcel(date('Ymd',time()),$expFileName,$expCellName,$expTableData);
    }
	
	
	
    public function cashlist(){
        $p = I('p',1,'int');
        $post = $_POST;

        $where = "";
        if(!empty($post['mobile'])){
            $where .= "b.telephone = '".$post['mobile']."' and ";
        }
        if(!empty($post['alipay_account'])){
            $where .= "a.zhang = '".$post['alipay_account']."' and ";
        }
        if(!empty($post['start_time'])){
            $where .= "a.addtime > '".$post['start_time']."' and ";
        }
        if(!empty($post['end_time'])){
            $where .= "a.addtime < '".$post['end_time']."' and ";
        }

        $chongModel = M('Chong');
        $chong = $chongModel->field('a.*,b.telephone')->alias('a')->join('ys_member b on a.userid=b.id')->where($where.'a.typeid=2')->page($p,10)->order('id desc')->select();
        $count = $chongModel->alias('a')->join('ys_member b on a.userid=b.id')->where($where.'a.typeid=2')->getField('count(*)');
        $yeshu = intval(($count-1)/10+1);

        $this->assign('p',$p);//当前页数
        $this->assign('count',$count);//总记录数
        $this->assign('yeshu',($yeshu)?$yeshu:1);//总页数
        $this->assign('list',$chong);//数据
        $this->display();
    }

public function cashrecharge(){
    $p = I('p',1,'int');

    $m=M('chong');
    $starttime=I('post.starttime');
    $endtime=I('post.endtime');
    $mobile=I('post.mobile');
    if(!empty($mobile)){
        $where .= "b.telephone = '".$mobile."' and ";
    }
    if (!empty($starttime)) {
        $where .= "a.addtime > '".$starttime."' and ";
    }
    if (!empty($endtime)) {
        $where .= "a.addtime < '".$endtime."' and ";
    }
    $where .= "a.typeid=1";
    $result=$m->field('a.*,b.telephone')->alias('a')->join('ys_member b on a.userid=b.id')->where($where)->order('id desc')->page($p,10)->select();
    $count = $m->alias('a')->join('ys_member b on a.userid=b.id')->where($where)->getField("count(*)");
    $yeshu = intval(($count-1)/10+1);
//    echo $m->_sql();die;

    
//    var_dump($result);
    $this->assign('paymentlist',$result);
    $this->assign('empty','<td colspan="5">暂无信息</td></tr>');
        $this->assign('p',$p);//当前页数
        $this->assign('count',$count);//总记录数
        $this->assign('yeshu',($yeshu)?$yeshu:1);//总页数


        $this->assign('munetype',7);
        $this->display();
}
	
	
	
	
public function cashapply(){
    $m=M('chong');
    $starttime=I('post.starttime');
    $endtime=I('post.endtime');
	$user=I('get.mobile');
	
    if (empty($endtime)) {
//        $date['addtime'] = array('lt', $endtime);;
        $endtime=Gettime();
    }
    if (empty($starttime)) {
        $starttime=0;
//        $date['addtime'] = array('gt', $starttime);;
    }
	if(!empty($user)){
		$date['tel']=array("like", "%".$user.'%');
		}
	$date['typeid']=2;
    $date['addtime']= array('between',array($starttime,$endtime));
    $result=$m->where($date)->order('is_pay asc,addtime desc')->select();
//    echo $m->_sql();die;

    for($i=0;$i<count($result);$i++){
        $userid=$result[$i]['userid'];
        $resultinfo=$this->GetInfo($userid);
//        var_dump($resultinfo);
        $result[$i]['telephone']=$resultinfo['telephone'];
    }
//    var_dump($result);
    $this->assign('paymentlist',$result);
    $this->assign('empty','<td colspan="5">暂无信息</td></tr>');


        $this->assign('munetype',7);
        $this->display();
    }

	
	public function tixian(){
        $disid=I('get.id');
        $m = M("Chong"); // 实例化User对象
		
		//扣去提现余额
		$res=$m->where("id=$disid")->find();		
		$ms=M("Member");
		$userid=$res['userid'];
		$res1=$ms->where("id=$userid")->find();	
		$money=$res1['dongjieyongjin']-$res['cash'];
		$re1=$ms->where("id=$userid")->setField('dongjieyongjin',$money);

        $data = array(
            'yongjintype'=>0,
            'addtime'=>date('Y-m-d H:i:s'),
            'userid'=>$userid,
            'num'=>$res['cash'],
            'description'=>"余额提现成功"
        );
        $ret = M('Balance')->add($data);
		
        $rs=$m->where("id=$disid")->setField('is_pay',1); // 标记id为5的提现记录为成功
        if($rs && $re1){
            $this->success('操作成功',U('/Admin/Cash/cashapply'));
        }else{
            $this->error('操作失败');
        }
    }

	

    public function delgoods(){
        $goosid=I('get.id');
        $m = M("goods"); // 实例化User对象
        $rs=$m->where("id=$goosid")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Goods/index'));
        }else{
            $this->error('删除失败');
        }
    }



}