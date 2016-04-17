<?php
namespace Home\Model;
use Think\Model;
class OrderaddressModel extends Model{
		
		function getOneaddress($memberid){
            $action=M("orderaddress");
            $rs=$action->where("id=$memberid")->find();
            return $rs;
		}
		
    	function editaddress($data){
            $action=M("orderaddress");
            $rs=$action->save($data);
            return $rs;
		}
		
	    function addaddress($data){
            $action=M("orderaddress");
            $rs=$action->add($data);
            return $rs;
		}

	    function addresscount($memberid){
			$conut = $this->where('userid='.$memberid)->count();
            return $conut;
		}

		function add_order(){
			
			$urls=$_SESSION['addresserror'];
			$uid=$_SESSION['member_id'];
			
			
			$arr['consignee']=$_POST['consignee'];
			$arr['telephone']=$_POST['telephone'];
			$arr['province']=$_POST['province'];
			$arr['city']=$_POST['city'];
			$arr['country']=$_POST['county'];
			$arr['xiangqing']=$_POST['xiangqing'];
			$arr['userid']=$uid;
			$arr['addtime']=Gettime();
			$arr['add_type']=$_POST['address_type'];
			$kk=$_POST['RadioGroup1'];
			$arr['dingwei']=$_POST['dingwei'];
			
			$rs=$this->add($arr);
			
			if($rs){
				if($kk){
					M('member')->where('id='.$uid)->setField('def',$rs);
				}
			$data['info']=1;
			$data['content']='添加成功！';
			$data['url']=$urls;
			}else{
				$data['info']=0;
			$data['content']='添加失败！';
			$data['url']=$urls;
				}
			return $data;
			}
		
		

	}
 ?>