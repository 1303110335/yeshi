<?php
namespace Home\Model;
use Think\Model;
class CollectionModel extends Model{



	
	
	function add_collection(){
		
		$userid=$_SESSION['member_id'];
		$goodsid=$_POST['goodsid'];
		
		$users=$this->where('goodsid='.$goodsid.' and userid='.$userid)->find();
		
		if(!$users){
					$arr['goodsid']=$goodsid;
					$arr['userid']=$userid;
					$arr['addtime']=Gettime();
					$this->add($arr);
					$data['info']   =   '收藏成功！'; // 提示信息内容
					$data['status'] =   1;  // 状态 如果是success是1 error 是0
					
					}else{
						$this->where('goodsid='.$goodsid.' and userid='.$userid)->delete();
						$data['info']   =   '取消收藏！'; // 提示信息内容
						$data['status'] =   0;  // 状态 如果是success是1 error 是0
						
						}
		$data['sql'] = M()->getlastsql();
        
        return $data;
    }
	
	
	
	
	
	function get_collectionlist($uid){
		
		
		
		$coll=$this->where('userid='.$uid)->select();
		
		foreach($coll as $k=>$v){
			$goods=M('goods')->where('id='.$v['goodsid'])->find();
			$guige=M('goods_guige')->where('goodsid='.$goods['id'])->find();
			$v['good_name']=$goods['good_name'];
			$v['resource']=$goods['resource'];
			$v['pic']=$goods['pic'];
			$v['guige']=$guige['guige'];
			$v['groupprice']=$guige['groupprice'];
			
			$arr[$k]=$v;
			}
		
        
        return $arr;
    }
	
	
	
	
	
	}




 ?>