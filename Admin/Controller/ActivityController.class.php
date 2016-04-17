<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class ActivityController extends CommonController {
	
    public function index(){
        $action=D('Activity');
        $rsdate=$action->getActivityList();
        $rs=$rsdate['list'];
		$count=count($rs);
        $this->assign('categorylist',$categorylist);
        $this->assign('goodslist',$rs);
		$this->assign('count',$count);
        $this->assign('page',$rsdate['page']);
		$this->assign('urlname',"indexActivity");
        $this->assign('munetype',8);
        $this->display();
    }

    public function ajaxUpdateShop(){
		
        $goosid=I('post.goodsid');
        $m = D("Activity"); 
		
		$res=$m->where('id='.$goosid)->find();
		if($res['is_show']==1){
        	$rs=$m->where('id='.$goosid)->setField('is_show',0);
		}else{
			$rs=$m->where('id='.$goosid)->setField('is_show',1);
		}
		$data['info']=$res['is_show'];
		
        $this->ajaxReturn($data);
		return;
    }

    public function add(){

        //调用Category控制器中的方法,获取类列表
		$action=A('Category');     
        $rs=$action->categorylist();
		
        //调用Category控制器中的方法,获取水果类列表   
        //$fruittype=$action->fruittypelist();

        $title=I('post.title');

        if(!empty($title)){
			
            $date['goodsid']=I('post.goodsid');
            $date['title']=$title;
			$date['subtitle']=I('post.subtitle');
			$date['pic']=I('post.pic');
			$date['guige']=I('post.guige');
			$date['is_show']=I('post.is_show');
            $date['sequence']=I('post.rank');
            $m=M('Activity');
            $rs=$m->add($date);
			
            if($rs){
                $this->success('添加成功',U('/Admin/Activity/index'));
            }else{
                $this->error('添加失败');
            }

        }else{
			
			$goodsModel = M('goods');
			$goodsRecord = $goodsModel->select();
			$this->assign('goods', $goodsRecord);
            $this->assign('urlname',"addActivity");
			$this->assign('categorylist',$rs);
            $this->assign('munetype',8);
            $this->display();
        }

    }
	
    public function delgoods(){
        $goosid=I('get.id');
        $m = M("goods"); // 实例化User对象
        $rs=$m->where("id=$goosid")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Goods/index'));
        }else{
            $this->error('删除失败');
        }
    }

    public function edit(){

        $id=I('get.id');
		$title=I('post.title');
		$action=M('Activity');
		
		if(!empty($title)){
			
            $data['id'] = I('post.id');
			$data['goodsid']=I('post.goodsid');
            $data['title']=$title;
			$data['subtitle']=I('post.subtitle');
			$data['pic']=I('post.pic');
			$data['guige']=I('post.guige');
			$data['is_show']=I('post.is_show');
            $data['sequence']=I('post.rank');	
			$result=$action->save($data);
			if($result)
			{
				$this->success('修改成功',U("/Admin/Activity/index"));exit;
			}
			else
			{
				$this->success('没有修改',U("/Admin/Activity/index"));exit;
			}
		}
		else
		{
			$goodsModel = M('goods');
			$goodsRecord = $goodsModel->select();
			$this->assign('goods', $goodsRecord);
	
			$result=$action->where('id='.$id)->find();
	
			$goodsone=$goodsModel->where('id='.$result['goodsid'])->find();
			$result['goodsname'] = $goodsone['good_name'];
			$this->assign('editmarketing', $result);
			$this->assign('urlname',"indexActivity");
			$this->assign('munetype', 8);
	
			$this->display();
		}
		

    }
	public function activitys(){
		if(IS_POST){
			$data = $_POST;
			$data['activitys_goods_ids'] = implode(',',$data['activitys_goods_ids']);
			$data['addtime'] = time();
			$data['activitys_img'] = $data['pic'];
			$result = M('Activitys')->add($data);
			echo $result;
		}else{
			$goodsRecord = M('goods')->where('is_show=1')->select();
			$this->assign('goods', $goodsRecord);
			$this->assign('urlname',"activitys");
			$this->display();
		}
	}
	public function activitys_datainfo(){
		$model = M('Activitys');
		if(I('get.title')){
			$where = 'activitys_name like "%'.I('get.title').'%"';
		}else{
			$where = "1=1";
		}
		$count = $model->where($where)->count();
        $p = getpage($count,10);
        $list = $model->where($where)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
		$this->assign('list',$list);
		$this->assign('count',$count);
		$this->assign('page',$p->show());
		$this->assign('urlname',"activitys_datainfo");
		$this->display();
	}
	public function activity_detail(){
		if(IS_POST){
			$data = $_POST;
			$data['activitys_goods_ids'] = implode(',',$data['activitys_goods_ids']);
			$data['activitys_img'] = $data['pic'];
			$result = M('Activitys')->save($data);
			echo $result;
		}else{
			$goodsRecord = M('goods')->where('is_show=1')->select();
			$id = intval(I('get.id'));
			if($id){
				$data = M('Activitys')->where("id=$id")->find();
				if($data['activitys_goods_ids']){
					$data['goods'] = M('Goods')->field('id,good_name')->where("id in($data[activitys_goods_ids])")->select();
					$data['activitys_goods_ids'] = explode(',',$data['activitys_goods_ids']);
					foreach($goodsRecord as $key => $val){
						if(in_array($val['id'],$data['activitys_goods_ids'])){
							$goodsRecord[$key]['is_checked'] = 1;
						}else{
							$goodsRecord[$key]['is_checked'] = 0;
						}
					}
				}
				$this->assign('data',$data);
			}
			$this->assign('goods', $goodsRecord);
			$this->display();
		}
	}


}