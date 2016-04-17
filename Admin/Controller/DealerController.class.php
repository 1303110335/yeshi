<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class DealerController extends CommonController {


	public function index(){
        $action=D('dealer');
        $rsdate=$action->getdealerlist();
		$count=$rsdate['count'];
        $page=$rsdate['page'];

        $this->assign('dealerlist',$rsdate['list']);
		$this->assign('count',$count);
		if($count>10)
		{
		 $this->assign('page',$page);
		}
        $this->assign('munetype',6);
        $this->display();
    }
	
	public function add(){
		
		$username=I('post.username');
		if($username)
		{
			$arr['username'] = $username;
			$arr['truename'] = I('post.truename');
			$arr['mobilephone'] = I('post.mobilephone');
			$arr['password'] = md5(I('post.password'));
			$arr['longitude'] = I('post.longitude');
			$arr['latitude'] = I('post.latitude');
			$arr['is_show'] = I('post.is_show');
			$arr['addtime'] = Gettime();
			$arr['erpappid'] = I('post.erpappid');
			$arr['erpkey'] = I('post.erpkey');
            $m=M('dealer');
            $rs=$m->add($arr);
			if($rs)
			{
				$this->success('添加成功',U('/Admin/Dealer/index'));	
			}
			else
			{
				$this->error('添加失败');
			}
		}
		else
		{
			$this->display();
		}
			
    }
	
	public function edit(){

		$id=I('get.id');
		$username=I('post.username');
		if(!$username)
		{
            $m=M('dealer');
            $rs=$m->where("id=".$id)->select();
			$this->assign('dealer',$rs);
			$this->display();
		}
		else
		{
			$arr['id'] = I('post.id');
			$arr['username'] = $username;
			$arr['truename'] = I('post.truename');
			$arr['mobilephone'] = I('post.mobilephone');
			$password = I('post.password');
			if($password!="")
			{
			$arr['password'] = md5($password);
			}
			$arr['longitude'] = I('post.longitude');
			$arr['latitude'] = I('post.latitude');
			$arr['sweight'] = I('post.sweight');
			$arr['sprice'] = I('post.sprice');
			$arr['xwprice'] = I('post.xwprice');
			$arr['is_show'] = I('post.is_show');
			$arr['erpappid'] = I('post.erpappid');
			$arr['erpkey'] = I('post.erpkey');
            $m=M('dealer');
            $m->save($arr);
			$this->success('修改成功',U('/Admin/Dealer/index'));	
		}
    }
	
	
	public function del(){

		$id=I('get.id');
        $m = M("dealer"); // 实例化User对象
        $rs=$m->where("id=$id")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Dealer/index'));
        }else{
            $this->error('删除失败');
        }
    }
	
	
	public function store(){
		
        $action=D('store');
        $rsdate=$action->getstorelist();
		$count=$rsdate['count'];
        $page=$rsdate['page'];

		$m = M('dealer');
		foreach ($rsdate['list'] as $key => $value)
		{
			$dealer = $m->field('username')->where('id='.$value['dealerid'])->find();	
			$rsdate['list'][$key]['dealer'] = $dealer['username'];
		}
		
        $this->assign('storelist',$rsdate['list']);
		$this->assign('count',$count);
		if($count>10)
		{
		 $this->assign('page',$page);
		}
        $this->assign('munetype',6);
        $this->display();
    }
	
	public function addstore(){

        
		$storename=I('post.storename');
		if($storename)
		{
			$arr['dealerid'] = I('post.dealerid');
			$arr['storename'] = $storename;
			$arr['truename'] = I('post.truename');
			$arr['telephone'] = I('post.telephone');
			$arr['mobilephone'] = I('post.mobilephone');
			$arr['address'] = I('post.address');
			$arr['opentime'] = I('post.opentime');
			$arr['is_show'] = I('post.is_show');
			$arr['pic'] = I('post.pic');
			$arr['erpstoreid'] = I('post.erpstoreid');
			$m = M("store");
			$rs = $m->add($arr);
			if($rs)
			{
				$this->success('添加成功',U('/Admin/Dealer/store'));	
			}
			else
			{
				$this->error('添加失败');
			}
		
		}
		else
		{
			
			$m = M("dealer");
			$dealerlist=$m->select();
			$this->assign('dealerlist',$dealerlist);
			$this->display();
		}

		
		
    }
	
	public function editstore(){

		$id=I('get.id');
		$storename=I('post.storename');
		if(!$storename)
		{
			$m = M("dealer");
			$dealerlist=$m->select();
			$this->assign('dealerlist',$dealerlist);
			$m=M('store');
            $rs=$m->where("id=".$id)->select();
			$this->assign('store',$rs);
			$this->display();
		}
		else
		{
			$arr['id'] = I('post.id');
			$arr['dealerid'] = I('post.dealerid');
			$arr['storename'] = $storename;
			$arr['truename'] = I('post.truename');
			$arr['telephone'] = I('post.telephone');
			$arr['mobilephone'] = I('post.mobilephone');
			$arr['address'] = I('post.address');
			$arr['opentime'] = I('post.opentime');
			$arr['is_show'] = I('post.is_show');
			$arr['pic'] = I('post.pic');
			$arr['erpstoreid'] = I('post.erpstoreid');
            $m=M('Store');
            $m->save($arr);
			$this->success('修改成功',U('/Admin/Dealer/store'));	
		}
    }
	
	public function delstore(){

		$id=I('get.id');
        $m = M("store"); // 实例化User对象
        $rs=$m->where("id=$id")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Dealer/store'));
        }else{
            $this->error('删除失败');
        }
		
    }
	

}