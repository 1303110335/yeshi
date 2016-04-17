<?php
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model{



	function getgoodslist($goodtype){
        if(isset($goodtype)){
            $date['goodstype']=$goodtype;
        }
        $title=I('post.title');
		$is_show=I('get.is_show');
        $goods_no=I('post.goods_no');
        $classid=I('post.class_id');
        if(!empty($goodtype)){
            $date['goodstype']=$goodtype;
        }
		if(!empty($is_show)){
            $date['is_show']=$is_show;
        }
		if($is_show===0){
			$date['is_show']=$is_show;
			}
		if($is_show==2){
			$date['is_show']=0;
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
		//获取到当前登录管理员所在经销商的id
		$dealerId = session('dealerId');
		if($dealerId){
			$date['dealers_id'] = array('like',"%$dealerId%");
		}

//        var_dump($date);
        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
		
		foreach($list as $k=>$v){
			
			$arr=M('goods_guige')->where('goodsid='.$v['id'])->order('price asc')->find();
			$v['old_price']=$arr['old_price'];
			$v['price']=$arr['price'];
			$v['weight']=$arr['weight'];
			$v['guige']=$arr['guige'];
			$v['gourpprice']=$arr['groupprice'];
			$lists[$k]=$v;
			
			}
		
		
      $date['list']=$lists; // 赋值数据集
      $date['page']= $p->show();// 赋值分页输出

            return $date;

		}

    function getonegoodsdetail($goodid){
        $date['id']=$goodid;
        $rs=$this->where($date)->find();
        return $rs;

    }
	
	function is_new($goosid){
		
		$res=$this->where('id='.$goosid)->find();
		if($res['is_new']==1){
        $rs=$this->where('id='.$goosid)->setField('is_new',0);
		}else{
			$rs=$this->where('id='.$goosid)->setField('is_new',1);
			}
		
			$data['info']=$res['is_new'];
			
			return $data;

    }
    function recommend_g($goosid){
		
		$res=$this->where('id='.$goosid)->find();
		if($res['recommend_g']==1){
        $rs=$this->where('id='.$goosid)->setField('recommend_g',2);
		}else{
			$rs=$this->where('id='.$goosid)->setField('recommend_g',1);
			}
		
			$data['info']=$res['recommend_g'];
			
			return $data;

    }
	
	
	}


 ?>