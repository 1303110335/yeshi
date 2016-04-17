<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
		function login(){
			if(isset($_POST['username'])){
				$data['username'] = $_POST['username'];
				$data['password'] = md5($_POST['password']);
//                echo $data['password'];
				$rs = $this->where($data)->select();
				// echo $this->_sql();exit();
				if($rs){

					$_SESSION['admin'] = $rs[0]['username'];
					$_SESSION['admin_id'] = $rs[0]['id'];
					return true;
				}
			}
		}




	}
 ?>