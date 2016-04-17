<?php
namespace Home\Model;
use Think\Model;
class GoodsModel extends Model{



	function getgoodslist($goodtype){
        if(isset($goodtype)){
            $date['goodstype']=$goodtype;
        }
        $title=I('post.title');
        $goods_no=I('post.goods_no');
        $classid=I('post.class_id');
        if(!empty($goodtype)){
            $date['goodstype']=$goodtype;
        }
        if(!empty($title)){
            $date['good_name']=array('like',"%$title%");
        }
         if(!empty($goods_no)){
            $date['sku']=$goods_no;
        }

        if(!empty($classid)){
            $date['classid']=$classid;
        }
        $date['is_show']=1;
        $date['kucun']=array('gt',0);

//        var_dump($date);
        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
			
      $arr['list']=$list; // 赋值数据集
        $arr['page']= $p->show();// 赋值分页输出

            return $arr;

		}


    function getGoodsdetail($gid,$guigeid){
        $date['id']=$gid;
		$data['price']=$price;
        $arr=$this->where($date)->find();
		
		$arrs=M('goods_guige')->where("goodsid=".$gid." and id=".$guigeid)->find();
		$arr['guige_price']=$arrs['price'];
		$arr['guige']=$arrs['guige'];
		$arr['weight'] = $arrs['weight'];
		
        return $arr;
    }
	
	function getGoodsguige($arr){
		$m=M('goods_guige');
        foreach($arr as $k=>$v){
			$ar1=$m->where('goodsid='.$v['id'])->order('price asc')->find();
			$v['weight']=$ar1['weight'];
			$v['price']=$ar1['price'];
			$v['old_price']=$ar1['old_price'];
			$v['guige']=$ar1['guige'];
			$v['groupprice']=$ar1['groupprice'];
			$arrs[$k]=$v;
			}
        
        return $arrs;
    }
	
    function getGoodsdetail2($gid,$guigeid){
        $date['id']=$gid;
		$data['groupprice']=$price;
        $arr=$this->where($date)->find();
		
		$arrs=M('goods_guige')->where('goodsid='.$gid.' and id='.$guigeid)->find();
		$arr['guige_price']=$arrs['groupprice'];
		$arr['guige']=$arrs['guige'];
		$arr['weight'] = $arrs['weight'];
		
        return $arr;
    }
	
	function getGoodsguigeone($arr){
		$m=M('goods_guige');
		$ar1=$m->where('goodsid='.$arr['id'])->order('price asc')->find();
		$arr['weight']=$ar1['weight'];
		$arr['price']=$ar1['price'];
		$arr['old_price']=$ar1['old_price'];
		$arr['guige']=$ar1['guige'];
		$arr['guigeid']=$ar1['id'];
		$arr['groupprice']=$ar1['groupprice'];
		$arrs=$arr;
        
        return $arrs;
    }
	
	
	
	
	function add_collection(){
		
		$userid=$_SESSION['member_id'];
		$goodsid=$_POST['goodsid'];
		
		$users=$this->where('goodsid='.$goodsid)->find();
		
		if(!$users){
					$arr['goodsid']=$goodsid;
					$arr['addtime']=Gettime();
					$this->add($arr);
					$data['info']   =   '收藏成功！'; // 提示信息内容
					$data['status'] =   1;  // 状态 如果是success是1 error 是0
					
					}else{
						$this->where('goodsid='.$goodsid)->delete();
						$data['info']   =   '取消收藏！'; // 提示信息内容
						$data['status'] =   0;  // 状态 如果是success是1 error 是0
						
						}
		
        
        return $users;
    }
	
	
	
	
	
	}




 ?>