<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model{



	function check_category($classname,$sort){
        if($sort){
            $date=array(
                "classname"=>$classname,
                "sortrank"=>$sort
            );
        }else{
            $date=array(
                "classname"=>$classname
            );
        }

            $rs=$this->where($date)->find();
            return $rs;

		}
		
	//获得二级分类
	function get_category($category){
        foreach($category as $k => $v){
			if($v['fid']==0){
				$arr[$v['id']]=$v;
				}
			}
		foreach($category as $k => $v){
			if($v['fid']!=0){
				$arr[$v['fid']]['cate'][$k]=$v;
				}
			}
            return $arr;

		}
		
		
	
	
	}
	
	
	
	



 ?>