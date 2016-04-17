<?php
namespace Home\Model;
use Think\Model;
class PicModel extends Model{



		function urls_name($arr){
			foreach($arr as $k=>$v){
				if($v['is_urls_type']!=0){
					if($v['is_urls_type']==1){
						$v['urls']='http://goead.ysxdgy.com/Index/yhq';
					}elseif($v['is_urls_type']==2){
						$v['urls']='http://goead.ysxdgy.com/Goods/category';
						}elseif($v['is_urls_type']==3){
							$v['urls']='http://goead.ysxdgy.com/Goods/category';
							}
							$arrs[$k]=$v;
					}else{
						$arrs[$k]=$v;
						}
				}
				return $arrs;
		}
	}
 ?>