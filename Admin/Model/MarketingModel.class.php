<?php
namespace Admin\Model;
use Think\Model;
class MarketingModel extends Model{



	function getmarketinglist($goodtype){

        $title=I('post.title');
		$is_show=I('get.is_show');
        if(!empty($title)){
            $date['title']=array('like',"%$title%");
        }
		
		if(!empty($is_show)){
            $date['is_show']=$is_show;
        }
		var_dump($date);exit;
		
        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

		$date['list']=$list; // 赋值数据集
		$date['page']= $p->show();// 赋值分页输出

        return $date;

	}

    function getonegoodsdetail($goodid){
        $date['id']=$goodid;
        $rs=$this->where($date)->find();
        return $rs;
    }

}


 ?>