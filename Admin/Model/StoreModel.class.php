<?php
namespace Admin\Model;
use Think\Model;
class StoreModel extends Model{



	function getstorelist(){

        $keyword=I('post.keyword');
		$dealerid=I('post.dealerid');
        if(!empty($keyword)){
            $date['storename'] = array('like',"%$keyword%");
        }
        if(!empty($dealerid)){
            $date['dealerid'] = $dealerid;
        }

        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

		$date['list']=$list; // 赋值数据集
		$date['page']= $p->show();// 赋值分页输出
		$date['count']= $count;
        return $date;

	}
	


}

    

 ?>