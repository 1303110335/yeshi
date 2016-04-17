<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class CategoryController extends CommonController {
    
	public function addCategory(){
            $classname=I('post.classname');
			$fid=I('post.fid');
            $sort=I('post.sort');
			$pic=I('post.pic');

            $action=D('category');
            $rs=$action->check_category($classname);    //判断类名是否重复
			
            
               $m=M('category');
                $arr['classname']=$classname;
				$arr['fid']=$fid;
                $arr['sortrank']=$sort;
				$arr['pic']=$pic;
                $arr['addtime']=Gettime();
                $rt=$m->add($arr);
                if($rt){
                    $data['info']   =   '添加成功'; // 提示信息内容
                    $data['status'] =   1;  // 状态 如果是success是1 error 是0
                    $data['url']    =   ''; // 成功或者错误的跳转地址
                }else{
                    $data['info']   =   '添加失败'; // 提示信息内容
                    $data['status'] =   0;  // 状态 如果是success是1 error 是0
                    $data['url']    =   ''; // 成功或者错误的跳转地址
                }

            

        $this->ajaxReturn($data);
        return;
    }

 	public function editCategory(){
            $classname=I('post.classname');
            $sort=I('post.sort');
			$fid=I('post.fid');
			$pic=I('post.pic');
            $categoryid=I('post.categoryid');

            $action=D('category');
            //$rs=$action->check_category($classname,$sort);    //判断类名是否重复

            
               $m=M('category');
                $arr['classname']=$classname;
                $arr['sortrank']=$sort;
				$arr['fid']=$fid;
				$arr['pic']=$pic;
                $arr['id']=$categoryid;

                $result = $m->where("id=$categoryid")->save($arr);
				
				$data['info']   =   '修改成功'; // 提示信息内容
				$data['status'] =   1;  // 状态 如果是success是1 error 是0
				$data['url']    =   ''; // 成功或者错误的跳转地址



        $this->ajaxReturn($data);
        return;
    }

    public function category(){

        $m = D('category');
        $where = "1=1";
        
        $list = $m->field(true)->where($where)->order('addtime desc')->select();
		
        $lists=$m->get_category($list);
		
		$this->assign('urlname', "ccategory"); // 赋值数据集
		
		$this->assign('category', $lists); // 赋值数据集
        
		$this->display();

    }
    public function categorylist(){
        $action=M('category');
        $rs=$action->order('sortrank desc')->select();
		foreach($rs as $k => $v){
			if($v['fid']==0){
				$arr[$v['id']]=$v;
				}
			}
		foreach($rs as $k => $v){
			if($v['fid']!=0){
				$arr[$v['fid']]['cate'][$k]=$v;
				}
			}
        return $arr;
    }
	
	public function fruittypelist(){
        $action=M('fruittype');
        $rs=$action->order('sortrank desc')->select();
		
        return $rs;
    }

    public   function get_category_name($id){
        $action=M('Category');
        $rs=$action->where("id=$id")->getField('classname');
        return $rs;
    }
	
	public   function get_fruit_name($id){
        $action=M('Fruittype');
        $rs=$action->where("id=$id")->getField('classname');
        return $rs;
    }

    public function delcategory(){

        $cid=I('get.cid');
        $m=M('category');
        $result = $m->where("id=$cid")->delete();

        if($result){
            $this->success('删除成功',U('/admin.php/Category/category'));
        }else{
            $this->error('删除失败',U('/admin.php/Category/category'),2);

        }

    }
	
	
    public function fruittype(){

        $m = M('fruittype');
        $where = "1=1";
        $count = $m->where($where)->count();
        $p = getpage($count,10);
        $list = $m->field(true)->where($where)->order('addtime desc')->limit($p->firstRow, $p->listRows)->select();
        
		$this->assign('urlname', "cfruittype");
		$this->assign('category', $list); // 赋值数据集
        if($count>10){
		$this->assign('page', $p->show()); // 赋值分页输出
		}
		$this->display();

    }
	
	public function addFruittype(){
            $classname=I('post.classname');
            $sort=I('post.sort');

			$date=array(
                "classname"=>$classname
            );
            $m=M('fruittype');
			$rs = $m->where($date)->find(); //判断类名是否重复
            if($rs){
                $data['info']   =   '该类名已经存在'; // 提示信息内容
                $data['status'] =   0;  // 状态 如果是success是1 error 是0
                $data['url']    =   ''; // 成功或者错误的跳转地址

            }else{
               $m=M('fruittype');
                $arr['classname']=$classname;
                $arr['sortrank']=$sort;
                $arr['addtime']=Gettime();
                $rt=$m->add($arr);
                if($rt){
                    $data['info']   =   '添加成功'; // 提示信息内容
                    $data['status'] =   1;  // 状态 如果是success是1 error 是0
                    $data['url']    =   ''; // 成功或者错误的跳转地址
                }else{
                    $data['info']   =   '添加失败'; // 提示信息内容
                    $data['status'] =   0;  // 状态 如果是success是1 error 是0
                    $data['url']    =   ''; // 成功或者错误的跳转地址
                }

            }
        $this->ajaxReturn($data);
        return;
    }
	
 	public function editFruittype(){
            $classname=I('post.classname');
            $sort=I('post.sort');
            $categoryid=I('post.categoryid');
			
			$m = M('fruittype');
			$rs = $m->where('id='.$categoryid)->find(); //判断类名是否重复
				
            if($rs){

                $arr['classname']=$classname;
                $arr['sortrank']=$sort;
                $arr['id']=$categoryid;

                $result = $m->where("id=$categoryid")->save($arr);

                $data['info']   =   '修改成功'; // 提示信息内容
                $data['status'] =   1;  // 状态 如果是success是1 error 是0
                $data['url']    =   ''; // 成功或者错误的跳转地址

			}else{

                $data['info']   =   '记录不存在'; // 提示信息内容
                $data['status'] =   0;  // 状态 如果是success是1 error 是0
                $data['url']    =   ''; // 成功或者错误的跳转地址

            }

        $this->ajaxReturn($data);
        return;
    }
	
    public function delFruittype(){

        $cid=I('get.cid');
        $m=M('fruittype');
        $result = $m->where("id=$cid")->delete();

        if($result){
            $this->success('删除成功',U('/admin.php/Category/fruittype'));
        }else{
            $this->error('删除失败',U('/admin.php/Category/fruittype'),2);

        }

    }
	
	
    public function addImage(){


		$data = $this->uploadImg();
		$this->ajaxReturn($data);
		//return;

    }
	

}