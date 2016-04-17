<?php
namespace Home\Model;
use Think\Model;
class AccountModel extends Model{
		function addcountdetail($memberid,$unm,$descrption){

            $data['accounttype']=1;
            $data['userid']=$memberid;
            $data['num']=$unm;
            $data['addtime']=Gettime();
            $data['descrption']=$descrption;
            $rs=$this->add($data);

            return $rs;
		}



	}
 ?>