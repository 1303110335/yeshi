<?php
namespace Home\Model;
use Think\Model;
class ActivityModel extends Model{
		function getList($type){

            $arr = $this->where('classid='.$type.' and is_show=1')->order('sequence asc')->select();
			
/*			foreach($arr as $key=>$value)
			{
				$m=M('Goods');
				$goodsone = $m->where('id='.$value['goodsid'])->find();
				$arr[$key]['goodsname']
			}*/
            return $arr;
		}



	}
 ?>