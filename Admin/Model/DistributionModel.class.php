<?php
namespace Admin\Model;
use Think\Model;
class DistributionModel extends Model{



	function getdistributionlist($goodtype){
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

//        var_dump($date);
        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

      $date['list']=$list; // 赋值数据集
      $date['page']= $p->show();// 赋值分页输出

            return $date;

		}

    function getonedistributiondetail($goodid){
        $date['id']=$goodid;
        $rs=$this->where($date)->find();
        return $rs;

    }

	}


 ?>