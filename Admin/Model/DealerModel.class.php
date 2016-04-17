<?php
namespace Admin\Model;
use Think\Model;
class DealerModel extends Model{



	function getdealerlist(){

        $keyword=I('post.keyword');
        if(!empty($keyword)){
            $date['username']=array('like',"%$keyword%");
        }

        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

		$date['list']=$list; // 赋值数据集
		$date['page']= $p->show();// 赋值分页输出
		$date['count']= $count;
        return $date;

	}
	
	function getdealerlist_nopage(){

        $list = $this->select();
        return $list;

	}
	
	
	function getmember(){
        if(isset($goodtype)){
            $date['goodstype']=$goodtype;
        }
        $title=I('get.title');
        $classid=I('get.dengji');
        if(!empty($goodtype)){
            $date['goodstype']=$goodtype;
        }
        if(!empty($title)){
            $date['telephone']=array('like',"%$title%");
        }
        if(!empty($classid)){
            $date['dengji']=$classid;
        }
			$date['is_fenxiao']=1;


        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

		
      $date['list']=$list; // 赋值数据集
      $date['page']= $p->show();// 赋值分页输出
	  $date['count']= $count;

            return $date;

		}
	
	
	
	/**
     *根据获取用户信息上级分销商
     * @param  $arr 用户数组
     * @author Chandler_qjw  ^_^
     */
	
	
	public function getMemberSuperior($redate){
		
		foreach($redate['list'] as $k=>$v){
			$sup=$this->where('id='.$v['fid'])->find(); //获取上级分销商
			$redate['list'][$k]['superior']=$sup['telephone']; //重组数组
			}
		$arrs=$redate;
		
		return $arrs;
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

    //获取所有会员
    public function memberGetIsZhen(){
        $count = $this->count();
        $p = getpage($count,10);
        $list = $this->alias('a')->field("a.id,a.telephone,a.truename,a.dengji,a.touimg,a.is_zhen,a.addtime")
            ->limit($p->firstRow, $p->listRows)->order('a.id desc')->select();
        $date['list']=$list; // 赋值数据集
        $date['page']= $p->show();// 赋值分页输出
        $date['count']= $count;
        return $date;
    }
    //获取某个提交实名认证的会员
    public function memberGetIsZhenUser($id){
        return $this->alias('a')->field("a.id,a.telephone,a.truename,a.dengji,a.touimg,a.is_zhen,b.addtime,b.updatetime,b.pic")
            ->join("left join yd_verified b on a.id=b.memberid")->where("a.id=%d",$id)->find();
    }
    //修改会员的认证状态
    public function upMemberIsZhen($id,$status){
        return $this->where("id=%d",$id)->setField('is_zhen',$status);
    }


}

	function getonememberdetail($mid){
        $date['id']=$mid;
        $rs=$this->where($date)->find();
        return $rs;

    }

    

 ?>