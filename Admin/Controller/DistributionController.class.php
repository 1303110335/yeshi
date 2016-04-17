<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class DistributionController extends CommonController {
    public function index(){


        $action=D('Distribution');
        $rsdate=$action->getdistributionlist();

        $rs=$rsdate['list'];
		$count=count($rs);
        $this->assign('distributionlist',$rs);
		$this->assign('count',$count);
        $this->assign('page',$rsdate['page']);
        $this->assign('munetype',3);
        $this->display();
    }

    public function integList(){
        $list = M('Integ')->field('a.*,b.telephone,c.province,c.city,c.country,c.xiangqing')->alias('a')->join('yd_member b on a.mid=b.id')->join('left join yd_orderaddress c on b.def=c.id')->order('a.id desc')->select();
        $this->assign('list',$list);
        $this->display();
    }
    public function updateInteg(){
        $status = I('get.status');
        $data['status'] = $status;
        if('3'==$status){
            $data['errortext'] = I('get.text');
        }
        $ret = M('Integ')->where('id=%d',I('get.id',0,'int'))->save($data);
        if($ret){
            $this->assign("修改成功!");
            $this->success();
        }else{
            $this->error("修改失败!");
        }
    }


      public function add(){

        $action=A('Category');      //调用Category控制器中的方法,获取类列表
        $rs=$action->categorylist();
//        var_dump($rs);


        $goodsname=I('post.title');
//          echo $goodsname.'a';die;
        if(!empty($goodsname)){
            $date['classid']=I('post.class_id');
            $date['good_name']=$goodsname;
            $date['pic']=I('post.pic');
            $date['sku']=I('post.goods_no');
            $date['dianzan']=I('post.dianzan');
            $date['detail']=$_POST['detail'];
            $date['is_show']=I('post.is_show');
            $date['addtime']=Gettime();
            $date['rank']=I('post.rank');

            $m=M('distribution');
            $rs=$m->add($date);
            if($rs){
                $this->success('添加成功',U('/Admin/Distribution/index'));
            }else{
                $this->error('添加失败');
            }

        }else{
            $this->assign('munetype',3);
            $this->display();
        }

    }
    public function deldistributions(){
        $disid=I('get.id');
        $m = M("distribution"); // 实例化User对象
        $rs=$m->where("id=$disid")->delete(); // 删除id为5的用户数据
        if($rs){
            $this->success('删除成功',U('/Admin/Distribution/index'));
        }else{
            $this->error('删除失败');
        }
    }

     public function edit(){
        $disid=I('get.id');
        $act=D('Distribution');
        $distributiondetail=$act->getonedistributiondetail($disid);

		//编辑器内容html实体转换
		$det=str_ireplace('\"','"',htmlspecialchars_decode($distributiondetail['detail']));
		
        $this->assign('distributiondetail',$distributiondetail);
		$this->assign('det',$det);
		
        $disname=I('post.title');
		
//          echo $goodsname.'a';die;
        if(!empty($disname)){
            $date['good_name']=$disname;
			if(I('post.pic')!=''){
            $date['pic']=I('post.pic');
			}
            $date['dianzan']=I('post.dianzan');
            $date['detail']=$_POST['detail'];
            $date['is_show']=I('post.is_show');
            $date['addtime']=Gettime();
            $date['rank']=I('post.rank');
            $date['id']=I('get.id');

            $m=M('distribution');
            $rs=$m->save($date);
            if($rs){
                $this->success('编辑成功',U('/Admin/Distribution/index'));
            }else{
                $this->error('编辑失败');
            }

        }else{
            $this->assign('munetype',3);
            $this->display();
        }

    }



}