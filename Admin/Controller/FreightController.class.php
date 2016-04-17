<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class FreightController extends CommonController {
    public function index(){

        $action=D('Freight');
        $Freightlist=$action->Freightlist();
		$count=$Freightlist['count'];
        $page=$Freightlist['page'];

        $this->assign('Freightlist',$Freightlist['list']);
		$this->assign('count',$count);
		if($count>10)
		{
		 $this->assign('page',$page);
		}
        $this->assign('munetype',6);
        $this->display();
    }
	

		public function addFreight(){

		$privonce=I('post.privonce');
		$sweight=I('post.sweight');
		$sprice=I('post.sprice');
		$xwprice=I('post.xwprice');
		$action = D('Freight');
		$rs = $action->addFreight($privonce,$sweight,$sprice,$xwprice);

		if($rs==1)
		{
			$data['info']   =   '添加成功'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		else
		{
			$data['info']   =   '添加失败'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		
        $this->ajaxReturn($data);
        return;
		
    }
	
	
	
	
	public function editFreight(){

		$id=I('post.id');
		$privonce=I('post.privonce');
		$sweight=I('post.sweight');
		$sprice=I('post.sprice');
		$xwprice=I('post.xwprice');

		$action=D('Freight');
		$rs=$action->editFreight($id,$privonce,$sweight,$sprice,$xwprice);
		if($rs==2)
		{
			$data['info']   =   '修改失败'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		elseif($rs==1)
		{
			$data['info']   =   '修改成功'; // 提示信息内容
			$data['status'] =   1;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}
		else
		{
			$data['info']   =   '未知错误'; // 提示信息内容
			$data['status'] =   0;  // 状态 如果是success是1 error 是0
			$data['url']    =   ''; // 成功或者错误的跳转地址
		}

        $this->ajaxReturn($data);
        return;
		
		

    }
	
	public function del(){

		$id=I('post.id');
        $m = M("Freight"); // 实例化User对象
        $rs=$m->where("id=$id")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Freight/index'));
        }else{
            $this->error('删除失败');
        }
    }
	
	
	

}