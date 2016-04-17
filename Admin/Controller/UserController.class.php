<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller {

    /**
     * 后台登录页面
     *
     * @author Chandler_qjw  ^_^
     */
    public function login(){

        $this->display();
    }

    /**
     *
     * 验证码生成
     */
    public function verify_c(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 130;
        $Verify->imageH = 50;
        //$Verify->expire = 600;
        $Verify->entry();
    }




    /**
     * 后台检测登录
     * @param
     * @author Chandler_qjw  ^_^
     */
    public function checkloginajax(){

        // 检查验证码
        $verify = I('param.vcode','');
        if(!check_verify($verify)){
//            $this->error("亲，验证码输错了哦！");
            $data['info']   =   '亲，验证码输错了哦！'; // 提示信息内容
            $data['status'] =   0;  // 状态 如果是success是1 error 是0
            $data['url']    =   ''; // 成功或者错误的跳转地址
            $this->ajaxReturn($data);
            return;
        }

		$action=D("User");
		$rs = $action->login();
		if($rs == 1)
		{
			$data['info']   =   '登录成功咯~'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址

		}
		elseif($rs == 0)
		{
			$data['info']   =   '帐号或者密码错误~'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		else
		{
			$data['info']   =   '帐号已禁用~'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		
        $this->ajaxReturn($data);
        return;
//        $this->display();
    }

    /**
     * 后台退出的登录
     *
     * @author Chandler_qjw  ^_^
     */
    public function  logout(){

        $username=I("get.sid");
        if(!empty($username)){
            session('admin',null); // 删除登录信息
            session('admin_id',null); // 删除name
            $this->redirect("/Admin/Index/login"); //直接跳转，不带计时后跳转

        }

    }
	
	
	public function updatepwd(){
		
		$action=D('User');
		$pass=I('post.password');
		$re=$action->getupdatepass($pass);
		if($re){
			$this->success('修改成功',U('/Admin/System/updatepwd'));
			}else{
				$this->error('修改失败');
				}
		
        $this->assign('munetype',9);
        $this->display();
    }
}