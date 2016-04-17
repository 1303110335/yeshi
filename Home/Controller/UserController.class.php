<?php
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{

    /**
     *手机端登录页面
     *
     * @author Chandler_qjw  ^_^
     */
    public function login()
    {

        $this->display();
    }

    /**
     *手机端注册页面
     *
     * @author Chandler_qjw  ^_^
     */
    public function apply()
    {

        $this->display();
    }
	
	public function weixin(){
        $uri = urlencode("http://".$_SERVER['HTTP_HOST']."/Home/User/weixinLogin");
		//$uri = "http://goead.ysxdgy.com/Home/User/weixinLogin";
        $appid = "wx00dc312216f31fad";
		//echo $uri;
		header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$uri."&response_type=code&scope=snsapi_userinfo#wechat_redirect");
    }
	
	
	public function weixinLogin(){
        $post = $_POST;
        if(empty($post['openid'])){
            $this->display();
        }else{
			
			$member = D('Member')->where("weixin_openid='%s'",$post['openid'])->getField('id');
            
			
            if(empty($member)){
                $act=M('Member');
				$date['truename']=$_POST['truename'];
				$date['weixin_openid']=$_POST['openid'];
				$date['touimg']=$_POST['touimg'];
				$date['addtime']=date('Y-m-d H:i:s');
				$pid=$act->add($date);
                session('member_id',$pid);
                echo "<script>window.location.href='".$_SESSION['surl']."'</script>";
            }else{
                session('member_id',$member);
                echo "<script>window.location.href='".$_SESSION['surl']."'</script>";
            }
        }
    }
	
	public function phonerenzheng(){
    $tiaozhuan = I('tiaozhuan');
    if($_SESSION['message_code']==$_POST['code']){
        $member = M('Member')->field('id,truename,touimg,weixin_openid,weibo_uid,qq_openid')->where("telephone='%s'",I('post.shouji'))->find();
        $wmember = session('wmember');//获得微信信息
        if(empty($member)){
            //数据库中没有该手机号  添加用户记录
            $max = M('Member')->field('max(id) max')->find();
            $hdid = intval($max['max'])+1;//生成HDID
            $shop_id = session('shop_id');
            if(empty($shop_id)){
                $shop_id = 1;
            }
            $dengji = M('Member')->where('id='.$shop_id)->getField('dengji');
            if($dengji>=5){
                $dengji = 6;
            }else{
                $dengji = $dengji+1;
            }
            $data = array(
                'telephone' => I('post.shouji'),
                'fid' => $shop_id,
                'dengji' => $dengji,
                'addtime' => date('Y-m-d H:i:s'),
                'password' => md5('123456'),
                'hdid' => $hdid
            );
            if($dengji!=6){
                $data['is_fenxiao'] = 1;
            }
            //判读是添加微信资料，还是QQ资料，还是微博资料
            if(1==$wmember['type']){
                $data['weixin_openid'] = $wmember['openid'];
            }else if(2==$wmember['type']){
                $data['weibo_uid'] = $wmember['openid'];
            }else{
                $data['qq_openid'] = $wmember['openid'];
            }
            $data['truename'] = $wmember['truename'];
            $data['touimg'] = $wmember['touimg'];
            
            $id = M('Member')->add($data);
            if($id){
                session('member_id',$id);
                session('wmember',null);
                $this->assign('zhuangtai','认证成功!');
                if($tiaozhuan=='1'){
                    $zhuceerror = session('zhuceerror');
                    $this->success('',$zhuceerror);
                }else{
                    $this->success();
                }
            }else{
                $this->error('认证失败!');
            }
        }else{
            //session中 微信不为空weixin_openid字段不为0 或者 微博不为空weibo_uid不为0
            if((1==$wmember['type'] && '0'==$member['weixin_openid']) || (2==$wmember['type'] && '0'==$member['weibo_uid']) || (3==$wmember['type'] && '0'==$member['qq_openid'])){
                if(1==$wmember['type']){
                    $data['weixin_openid'] = $wmember['openid'];
                }else if(2==$wmember['type']){
                    $data['weibo_uid'] = $wmember['openid'];
                }else{
                    $data['qq_openid'] = $wmember['openid'];
                }
                if(empty($member['truename'])){
                    $data['truename'] = $wmember['truename'];//修改昵称
                }
                if(empty($member['touimg'])){
                    $data['touimg'] = $wmember['touimg'];//修改头像
                }
                $ret = M('Member')->where("id=".$member['id'])->save($data);
                if($ret){
                    session('member_id',$member['id']);
                    session('wmember',null);
                    $this->assign("zhuangtai","认证成功!");
                    if($tiaozhuan=='1'){
                        $zhuceerror = session('zhuceerror');
                        $this->success('',$zhuceerror);
                    }else{
                        $this->success();
                    }
                }else{
                    $this->error("认证失败!");
                }
            }else{
                $this->error("该手机已认证!");
            }
        }
    }else{
        $this->error("验证码错误!");
    }
}
	

    /**
     *前台手机端检测登录
     *
     * @author Chandler_qjw  ^_^
     */
    public function checkmblogin()
    {

        $action = D("Member");
        if ($action->login()) {
            $action1 = D("Member");
            $id = $_SESSION['member_id'];
            $rs = $action1->GetInfomation($id);
            $_SESSION['pid'] = $rs['fid'];

            $data['info'] = '登录成功'; // 提示信息内容
            $data['status'] = 1;  // 状态 如果是success是1 error 是0
            $data['url'] = $_SESSION['surl']; // 成功或者错误的跳转地址

        } else {
            $data['info'] = '帐号或者密码错误~'; // 提示信息内容
            $data['status'] = 0;  // 状态 如果是success是1 error 是0
            $data['url'] = ''; // 成功或者错误的跳转地址
        }
        $this->ajaxReturn($data);
        return;

    }

    /**
     *手机端注册发送短信
     *
     * @author Chandler_qjw  ^_^
     */
    public function SendMessageCode()
    {

        $phone = I('post.phone');
        $rs = sendmessage($phone);

        if ($rs == 1) {
            $data['info'] = '发送成功'; // 提示信息内容
            $data['type'] = 1;  // 状态 如果是success是1 error 是0
        } else {
            $data['info'] = '发送失败'; // 提示信息内容
            $data['type'] = 0;  // 状态 如果是success是1 error 是0
        }
        $this->ajaxReturn($data);
    }

    /**
     *手机端注册
     *
     * @author Chandler_qjw  ^_^
     */
    public function register()
    {
        $phone = I('post.phone');
        $password = I('post.password');
        $vcode = I('post.vcode');
//        $pid = I('post.pid');
//        if($pid==''){
//            $pid=1;
//        }
//        if(isset($_SESSION['pid'])){
//            $pid=$_SESSION['pid'];
//        }
//        if($_SESSION['message_code']!=$vcode){
//            $data['info']   =   '手机验证码错误'; // 提示信息内容
//            $data['type'] =   0;  // 状态 如果是success是1 error 是0
//
//            $this->ajaxReturn($data);
//            return false;
//        }

        $action = D('Member');
        $rs = $action->CheckMemberIsRegist($phone);
//       echo $rs.'a';die;
//        echo $rs.'a';die;
        if ($rs) {
            $data['info'] = '该用户已经注册过'; // 提示信息内容
            $data['type'] = 2;  // 状态 如果是success是1 error 是0
        } else {


            $action = D('Member');
            $uid = $action->addMember($phone, $password, $pid);
            if ($uid) {
                $data['info'] = '注册成功'; // 提示信息内容
                $data['type'] = 1;  // 状态 如果是success是1 error 是0
            } else {
                $data['info'] = '注册失败'; // 提示信息内容
                $data['type'] = 0;  // 状态 如果是success是1 error 是0
            }

        }
        $this->ajaxReturn($data);
    }

    public  function logout(){
        session('member',null); // 删除登录信息
        session('member_id',null); // 删除登录信息
//        session('fid',null); // 删除登录信息
        $this->redirect("/Index/index"); //直接跳转，不带计时后跳转

    }

}