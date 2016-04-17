<?php
namespace Admin\Controller;
//use Think\Controller;
use Common\Controller\CommonController;
class MemberController extends CommonController {
    public function index(){


        $action=D('member');
        $rsdate=$action->getmemberlist();
		
		
        $rs=$rsdate['list'];
		$count=$rsdate['count'];
        $page=$rsdate['page'];

        $this->assign('memberlist',$rs);
		$this->assign('count',$count);
		if($count>10)
		{
		$this->assign('page',$page);
		}
        $this->assign('munetype',5);
        $this->display();
    }

    

    public function jifen(){
        $openid = I('openid');
        if(!empty($openid)){
            $member = M('Member')->where('weixin_openid="%s"',$openid)->find();
            if(empty($member)){
                $this->assign('empty',"无该用户数据!");
            }else{
                $this->assign('member',$member);
            }
            $this->assign('openid',$openid);
        }
        $this->display();
    }
    public function updatejifen(){
        $jifen = intval(I('post.jifen'));
        if(!empty($jifen)){
            $ret = M('Member')->where('id=%d',I('post.id',0,'int'))->setInc('jifen',$jifen);
            if($ret){
                echo '0';
            }else{
                echo '1';
            }
        }
    }




	public function delmember(){
        $memberid=I('get.id');
        $m = M("member"); // 实例化User对象
        $rs=$m->where("id=$memberid")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Member/index'));
        }else{
            $this->error('删除失败');
        }
    }

    public function agent_list(){
			
		$action=D('member');
        $rsdate=$action->getmember();
		$lv=I('post.dengji');

		$ar=$rsdate;
		
		//重组数组，放入上级分销商
		$superior=$action->getMemberSuperior($ar);
//        var_dump($rsdate);
        $rs=$superior['list'];
		$count=$superior['count'];
        $page=$superior['page'];
		
		$this->assign('member',$rs);
        $this->assign('page',$page);
		$this->assign('lv',$lv);
		$this->assign('count',$count);
        $this->assign('munetype',6);
        $this->display();
    }

    public function jiangli(){
        if(IS_POST){
            $ret = M('Systemglobal')->where("blname='yongjinshezhi'")->setField('blvalue',json_encode($_POST));
            if($ret){
                $this->assign('修改成功!');
                $this->success();
            }else{
                $this->error("修改失败!");
            }
        }else{
            $blvalue = M('Systemglobal')->where("blname='yongjinshezhi'")->getField('blvalue');
            $blvalue = json_decode($blvalue,true);
            $this->assign('blvalue',$blvalue);
            $this->display();
        }
    }
	
	
	public function edit(){


        $mid=I('get.id');
        $act=D('Member');
        $memberdetail=$act->where('id='.$mid)->find();
		//$superior=$act->where('id='.$memberdetail['fid'])->find();
		//$memberdetail['superior']=$superior['telephone'];
		
		//$dit=M('Orderaddress');
		//$country=$dit->where("userid=$mid")->find();
		//$memberdetail['country']=$country['country'];
		
        $this->assign('memberdetail',$memberdetail);
		
		$this->assign('categorylist',$rs);
		$this->assign('munetype',5);
		$this->display();

    }

    public function updatehdid(){
        $data['hdid'] = I('post.hdid',0,'int');
        $memberModel = M('Member');
        $whe = $memberModel->where('hdid=%d',$data['hdid'])->getField('id');
        if($whe){
            echo '3';//已存在
        }else{
            $ret = $memberModel->where("id=%d",I('post.id',0,'int'))->save($data);
            if($ret){
                echo '0';//成功
            }else{
                echo '1';//失败
            }
        }
    }
	
	public function agent_edit(){


        $mid=I('get.id');
        $act=D('Member');
        $memberdetail=$act->where('id='.$mid)->find();
		$superior=$act->where('id='.$memberdetail['fid'])->find();
		$memberdetail['superior']=$superior['telephone'];

        $this->assign('memberdetail',$memberdetail);


		$this->assign('munetype',6);
		$this->display();

    }

    public function memberAddMoney(){
        $id = I('post.id',0,'int');
        $money = I('post.money',0);
        if(is_numeric($money)){
            $upRet = M('Member')->where("id=%d",$id)->setInc('balance',$money);
            if($upRet){
                $data = array(
                    'yongjintype'=>1,
                    'addtime'=>date('Y-m-d H:i:s'),
                    'userid'=>$id,
                    'num'=>$money,
                    'description'=>I('post.beizhu')
                );
                $ret = M('Balance')->add($data);
            }
            if($ret){
                echo '1';//成功
            }else{
                echo '2';//失败
            }
        }else{
            echo "3";
        }
    }

	
}