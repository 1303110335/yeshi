<?php
namespace Home\Model;
use Think\Model;
class MemberModel extends Model{
    /**
     * 登录
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
    public function login(){
			if(isset($_POST['username'])){
				$data['telephone'] = $_POST['username'];
				$data['password'] = md5($_POST['password']);
//                echo $data['password'];
				$rs = $this->where($data)->select();
//				 echo $this->_sql();exit();
				if($rs){
					$_SESSION['member'] = $rs[0]['username'];
					$_SESSION['member_id'] = $rs[0]['id'];
					return true;
				}
			}
		}


    /**
     *根据ID获取用户信息
     * @param  $uid 用户ID
     * @author Chandler_qjw  ^_^
     */
            public function GetInfomation($uid){
            $where = array( //条件数组
                'id' => $uid,
            );
            $rs = $this->where($where)->find(); //查询， 用find()只能查出一条数据，select()多条
            return $rs;
        }

    /**
     *检测该手机号码是否注册过
     * @param  $phone 手机号码
     * @author Chandler_qjw  ^_^
     */
        public function CheckMemberIsRegist($phone){
            $where = array( //条件数组
                'telephone' => $phone,
            );
            $rs = $this->where($where)->find(); //查询， 用find()只能查出一条数据，select()多条
            $uid=$rs['id'];
            return $uid;
        }

    /**
     *  手机端注册
     * @param  $phone 手机号码
     * @author Chandler_qjw  ^_^
     */
        public function addMember($phone,$password,$sid){


            $password=md5($password);
            $date = array( //条件数组
                'telephone' => $phone,
                'fid' => $sid,
                'password' => $password,
                'addtime' => gettime()
            );
            $uid = $this->add($date); //添加用户

            return $uid;
        }
 /**
     *  根据子id查父id
     * @param  $sid  id
     * @author Chandler_qjw  ^_^
     */
        public function getfid($sid){

            $rs = $this->where("id=$sid")->find(); //查询， 用find()只能查出一条数据，select()多条
            $fid=$rs['fid'];
            return $fid;


        }
/**
     *  根据id查下级分销商
     * @param  $uid  父id
     * @author Chandler_qjw  ^_^
     */
        public function getMemberlist($uid){

            $rs = $this->where("fid=$uid")->select(); //查询， 用find()只能查出一条数据，select()多条
//            $fid=$rs['fid'];
            return $rs;


        }
/**
     *  根据id付款
     * @param  $uid  父id
     * @author Chandler_qjw  ^_^
     */
        public function payorder($uid,$price){

            $rs = $this->where("id=$uid")->getField('balance'); //查询， 用find()只能查出一条数据，select()多条
//            $fid=$rs['fid'];

            if($rs>=$price){
//                $es1=$this->where('id=5')->setInc('balance',3); // 用户的积分加3
               $es1= $this->where("id=$uid")->setDec('balance',$price); //

                if($es1){


                    $date['type']=1;   //付款成功
                    $date['message']='付款成功';
                }else{
                    $date['type']=2;   //付款失败
                    $date['message']='付款失败';
                }
            }else{
                $date['type']=2;   //错误
                $date['message']='余额不足';
            }
            return $date;


        }
	}
 ?>