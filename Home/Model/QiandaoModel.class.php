<?php
namespace Home\Model;
use Think\Model;
class QiandaoModel extends Model{
    /**
     * 获取单日签到记录
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */

        public function getQiandao(){
			

			$memberid = session('member_id');
			$signdate = date("Y-m-d",strtotime(gettime()));
			$where="member_id=$memberid and date_format(signdate, '%Y-%m-%d')='".$signdate."'";
			//$rs = $this->where($where)->select();
			$rs = $this->where($where)->find();
            return $rs;
			
        }
		
		
    /**	
     *  添加签到
     * @author Chandler_qjw  ^_^
     */
        public function addSign($jifen){


            $memberid = session('member_id');
			$password=md5($password);
            $date = array( //条件数组
                'sign' => $jifen,
                'member_id' => $memberid,
                'signdate' => gettime()
            );
			
            $uid = $this->add($date); //添加用户
			M('Member')->where('id='.$memberid)->setInc('jifen',$jifen);
            return $uid;
        }
		
	}
 ?>