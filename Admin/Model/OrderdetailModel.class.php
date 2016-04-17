<?php
namespace Admin\Model;
use Think\Model;
class OrderdetailModel extends Model{
		function addorderdetail($data){

            $rs=$this->add($data);

            return $rs;
		}
    	function getorderdetail($id){

            $rs=$this->where("orderid=$id")->select();

            return $rs;
		}




	}
 ?>