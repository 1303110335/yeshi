<?php
namespace Home\Model;
use Think\Model;
class ShoppingModel extends Model{
    /**
     * 加入购物车
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
    public function addshopp($uid){
			$data['userid']=$uid;
			$data['goodsid']=$_POST['goodsid'];
			$data['price']=$_POST['price'];
			$data['group_price']=$_POST['group_price'];
			$data['group_num']=$_POST['group_num'];
			$data['weight']=$_POST['weight'];
			$data['nums']=$_POST['nums'];
			$data['pic']=$_POST['pic'];
			$data['good_name']=$_POST['good_name'];
			$data['kucun']=$_POST['kucun'];
			$data['address']=$_POST['address'];
			$data['total_price']=$_POST['price'];
			
			$rs=$this->add($data);
			
			$date['userid'] = $uid;
			$date['b.is_show'] = 1;
			$arr=$this->field("a.*,b.restriction_num")->alias('a')->join("ys_goods b on a.goodsid=b.id and b.is_show=1 and b.kucun>0")->where($date)->select();
			$count=count($arr);
			
			if($rs){
			$arr['type']=1;
			$arr['count']=$count;	
				}else{
					$arr['type']=0;
					$arr['count']=$count;	
					}
			return $arr;
		}
		
		
		
		
	/**
     * 购物车增加商品数量
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
    public function addnums($uid,$where){
			
			$id=$_POST['shop_id'];
			$this->where('id='.$id)->setInc('num');
			$rs=$this->where('id='.$id)->find();
			
			$pp=$rs['num']*$rs['price'];
			
			$this->where('id='.$id)->setField('total_price',$pp);
			
			$res=$this->where('id='.$id)->find();
			
			if($where){
				$data['id']=array('in',$where);
			$kk=$this->where($data)->select();
			}else{
				$kk=$this->where('userid='.$uid)->select();
				}
			foreach($kk as $k=>$v){
				$count+=$v['total_price'];
				}
			
			$res['to_price']=$count;
			
			return $res;
		}
		
		
	
	
	/**
     * 购物车减少商品数量
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
    public function decnums($uid,$where){
			
			$id=$_POST['shop_id'];
			$this->where('id='.$id)->setDec('num');
			$rs=$this->where('id='.$id)->find();
			if($rs['num']==0){
				$data['total_price']=$rs['price'];
				$data['num']=1;
				$this->where('id='.$id)->setField($data);
				}else{
			$pp=$rs['num']*$rs['price'];
			$this->where('id='.$id)->setField('total_price',$pp);
				}
			$res=$this->where('id='.$id)->find();
			
			if($where){
				$data['id']=array('in',$where);
			$kk=$this->where($data)->select();
			}else{
				$kk=$this->where('userid='.$uid)->select();
				}
			foreach($kk as $k=>$v){
				$count+=$v['total_price'];
				}
			
			$res['to_price']=$count;
			
			return $res;
		}
		
		
		
	

	
	
		
		
	
	
	/**
     * 购物车列表
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
    public function shop_list($uid){
			$data['userid'] = $uid;
			$data['b.is_show'] = 1;
			$arr=$this->field("a.*,b.restriction_num")->alias('a')->join("ys_goods b on a.goodsid=b.id and b.is_show=1 and b.kucun>0")->where($data)->select();
			return $arr;
		}
		
		
	//总价
	public function shop_list_count($arr){
			foreach($arr as $k=>$v){
				$count+=$v['total_price'];
				}
				
			return $count;
		}
	
	
	
	
	
	/**
     * 订单列表
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
    public function shop_lists($id){
		
			$data['id']=array('in',$id);
			$arr=$this->where($data)->select();
			foreach($arr as $k=>$v){
				$weight=$v['weight'];
				$nums=$v['nums'];
				if($nums){
					$guige=$weight.'KG/'.$nums.'个';
					}else{
						$guige=$weight.'KG';
						}
				$v['guige']=$guige;
				$arrs[$k]=$v;
				}
			return $arrs;
		}
		
		
	//总价
	public function shop_list_counts($id){
		
			$data['id']=array('in',$id);
			$arr=$this->where($data)->select();
			foreach($arr as $k=>$v){
				$count+=$v['total_price'];
				}
				
			return $count;
		}
	
	
	
	
	
	
	
	

    
	}
 ?>