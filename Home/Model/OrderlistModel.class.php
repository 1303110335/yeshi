<?php
namespace Home\Model;
use Think\Model;
class OrderlistModel extends Model{
		function addorderlist($data){

            $rs=$this->add($data);
            return $rs;
		}
//获取所有订单
        function getAllorder($userid='',$state='',$orderno=''){

            if($userid!=''){

                $data['userid']=$userid;
            }
            if($state!=''){
                $data['orderstate']=$state;
            }
            if($orderno!=''){
                $data['orderno']=$orderno;
            }
            $data['is_delete']=0;
            $rs=$this->where($data)->order('addtime desc')->select();
            for($i=0;$i<count($rs);$i++){
                $orderid=$rs[$i]['id'];

                $actiond=D('orderdetail');

                $rsd=$actiond->getorderdetail($orderid);
                $rs[$i]['orderlist']=$rsd;
            }
			
            return $rs;
		}

    //活动订单数量
        function getordercount($userid='',$state=''){

            if($userid!=''){
                $data['userid']=$userid;
            }
            if($state!=''){
                $data['orderstate']=$state;
            }
            $data['is_delete']=0;
            $rs=$this->where($data)->count();
//            echo $action->_sql();exit;

            return $rs;
		}


//更新订单信息
            function updateOrderState($orderno,$state){

//                $data['orderno']=$orderno;
                $data['orderstate']=$state;

            $rs=$this->where("id=$orderno")->save($data);
//            echo $action->_sql();exit;

            return $rs;
		}

    function  getoneorderdetail($orderid){
        $data['id']=$orderid;
       $orderone= $this->where($data)->select();

        $m=D('Orderdetail');
        $rs=$m->getorderdetail($orderid);

        $orderone['detail']=$rs;
        return $orderone;

    }
		
		
		//获取订单详情
        function getOneorder($orderid){

            
            $rs=$this->where('id='.$orderid)->find();
			$actiond=D('orderdetail');
			$order_det=$actiond->where('orderid='.$orderid)->select();
			
			foreach($order_det as $k=>$v){
				$rs['orderdet'][$k]=$v;
				}
			
            return $rs;
		}
		
		
		
		
		

	}
 ?>