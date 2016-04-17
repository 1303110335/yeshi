<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class GoodsController extends CommonController {
    public function index(){
		
		$is_show=I('get.is_show');
		
        $action=D('Goods');
        $rsdate=$action->getgoodslist();
        $rs=$rsdate['list'];
		$count=count($rs);
        $action=A('Category');      //调用Category控制器中的方法,获取类列表
        for($i=0;$i<$count;$i++){
            $rs[$i]['pics'] = explode(',',$rs[$i]['pic']);
            $retuenclassname=$action->get_category_name($rs[$i]['classid']);
            $rs[$i]['classname']=$retuenclassname;
			//$retuenfruitname=$action->get_fruit_name($rs[$i]['fruitid']);
            //$rs[$i]['fruitname']=$retuenfruitname;
        }
        $categorylist=$action->categorylist();
		$action=D('Goods');
		//获取到当前登录管理员所在经销商的id
		$dealerId = session('dealerId');
		if($dealerId){
			$goodsWhere = " and dealers_id like '%$dealerId%'";
		}else{
			$goodsWhere = '';
		}
		$counts=$action->where('1=1'.$goodsWhere)->count();
		$count1=$action->where('is_show=1'.$goodsWhere)->count();
		$count2=$action->where('is_show=0'.$goodsWhere)->count();
		
		
        $this->assign('categorylist',$categorylist);
        $this->assign('goodslist',$rs);
		$this->assign('is_show',$is_show);
		$this->assign('count',$count);
		$this->assign('counts',$counts);
		$this->assign('count1',$count1);
		$this->assign('count2',$count2);
        $this->assign('page',$rsdate['page']);
		$this->assign('urlname', "gindex"); // 赋值数据集
        $this->assign('munetype',3);
        $this->display();
    }


    public function add(){

        //调用Category控制器中的方法,获取类列表
		$action=A('Category');      
        $rs=$action->categorylist();	//产品分类	
		
		$fruit=$action->fruittypelist();  //水果分类
		
        //调用Category控制器中的方法,获取水果类列表   
        //$fruittype=$action->fruittypelist();

        $goodsname=I('post.title');
		
		
		
        if(!empty($goodsname)){
            
            $date['classid']=I('post.class_id');
			$date['othercid']=I('post.othercid');
			$pid_arr=M('category')->where('id='.$date['classid'])->find();
			$date['parid']=$pid_arr['fid'];
            $date['good_name']=$goodsname;
			$date['good_name1']=I('post.good_name1');
			$date['resource']=I('post.resource');
			$date['group_num']=I('post.group_num');
			$date['group_time']=I('post.group_time');
			$date['address']=I('post.address');
			$date['kucun']=I('post.kucun');
			$date['virtual']=I('post.virtual');
            $date['pic']=I('post.pic');
            $date['pic2']=I('post.pic2');
			$date['pic1']=implode(",",I('post.pic1'));
            $date['sku']=I('post.goods_no');
            $date['detail']=$_POST['detail'];
            $date['is_show']=I('post.is_show');
            $date['addtime']=Gettime();
            $date['rank']=I('post.rank');
			$date['is_restriction']=I('post.is_restriction');
			$date['is_restriction2']=I('post.is_restriction2');
			$date['restriction_num']=I('post.restriction_num');
			$date['restriction_num2']=I('post.restriction_num2');
			$date['is_delivery']=I('post.is_delivery');
			$date['buylimit']=I('post.buylimit');
			$date['keeptime']=I('post.keeptime');
			$date['recommend_g']=I('post.recommend_g');
			$date['recommend_gnum']=I('post.recommend_gnum');
			$date['dealers_id']=I('post.dealers_id',0,'int');
			$date['erpgoodsid']=I('post.erpgoodsid',0,'int');
			$date['certain_success']=I('post.certain_success');
            $m=M('goods');
			
            $rs=$m->add($date);
            
			
			// 存入对应规格
			$goods_guige = M('Goods_guige');
			$guige_num=I('post.nums');
			if($guige_num!=0){
				$j=1;
				for($i=0; $i < $guige_num; $i++){
					$guiges[$i]['guige']=I('post.guige'.$j);
					$guiges[$i]['weight']=I('post.weight'.$j);
					$guiges[$i]['old_price']=I('post.guige_old_price'.$j);
					$guiges[$i]['groupprice']=I('post.guige_groupprice'.$j);
					$guiges[$i]['price']=I('post.guige_price'.$j);
					$j++;
					}
				for ($i=0; $i < $guige_num; $i++) {
                $datas = array(
                    'goodsid'=>$rs,
					'guige'=>$guiges[$i]['guige'],
                    'weight'=>$guiges[$i]['weight'],
					'old_price'=>$guiges[$i]['old_price'],
					'groupprice'=>$guiges[$i]['groupprice'],
                    'price'=>$guiges[$i]['price']
                );
                $goods_guige->add($datas);
            }
			}
			
			
            if($rs){
                $this->success('添加成功',U('/Admin/Goods/index'));
            }else{
                $this->error('添加失败');
            }

        }else{
			$dealerIds = M('Dealer')->field('id,username')->select();
			$this->assign('dealerIds',$dealerIds);
            $this->assign('urlname',"gadd");
			$this->assign('categorylist',$rs);
			$this->assign('fruitlist',$fruit);
            $this->assign('munetype',3);
            $this->display();
        }

    }
	
    public function delgoods(){
        $goosid=I('get.id');
        $m = M("goods"); // 实例化User对象
        $rs=$m->where("id=$goosid")->delete(); // 删除id为5的用户数据
		
		$ms = M("goods_guige");
		$rss=$ms->where('goodsid='.$goosid)->delete();
		
        if($rs){
            $this->success('删除成功',U('/Admin/Goods/index'));
        }else{
            $this->error('删除失败');
        }
    }

    public function goodsoutdata(){
    	$goodsModel = M('Goods');
		//获取到当前登录管理员所在经销商的id
		$dealerId = session('dealerId');
		if($dealerId){
			$goodsWhere = " and dealers_id like '%$dealerId%'";
		}else{
			$goodsWhere = '';
		}
        if(trim(I('good_name'))){
        	$count = $goodsModel->where("good_name like '%".trim(I('good_name'))."%'".$goodsWhere)->count();
        	$p = getpage($count,10);
        	$goodsList = $goodsModel->order('id desc')->limit($p->firstRow, $p->listRows)->where("good_name like '%".trim(I('good_name'))."%'".$goodsWhere)->select();
        	$this->assign('good_name',I('good_name'));
        }else{
        	$count = $goodsModel->where('1=1'.$goodsWhere)->count();
        	$p = getpage($count,10);
        	$goodsList = $goodsModel->where('1=1'.$goodsWhere)->order('id desc')->limit($p->firstRow, $p->listRows)->select();
        }

        $orderlistModel = M("Orderlist");
        //待发货配送
		$stateOne = $orderlistModel->field('goods_id')->where("orderstate = 2 and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0))")->select();
		//待发货自取(团购中)
		$stateOne1 = $orderlistModel->field('goods_id')->where("orderstate=3 and is_groupbuy=1 and groupbuy_ok=0")->select();
		//待发货自取(团购成功)
		$stateOne2 = $orderlistModel->field('goods_id')->where("orderstate=3 and is_groupbuy=1 and groupbuy_ok=1")->select();
		//待发货自取(单买)
		$stateTwo = $orderlistModel->field('goods_id')->where("orderstate=3 and is_groupbuy=0")->select();
		//已出货
		$stateThree = $orderlistModel->field('goods_id')->where("(is_delivery=0 and orderstate in(3,4) or is_delivery=1 and orderstate=4)")->select();

		//按时间查询
		if(I('start_time')){
			$date = I('start_time');
		}else{
			$date = date('Y-m-d 00:00:00');
		}
		$this->assign('start_time',$date);
		if(I('end_time')){
			$time = I('end_time');
		}else{
			$time = date('Y-m-d 23:59:59');
		}
		$this->assign('end_time',$time);
		//待发货配送
		$stateOnee = $orderlistModel->field('goods_id')->where("orderstate = 2 and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0)) and addtime>'$date' and addtime<'$time'")->select();
		//待发货(团购中)
		$stateOne12 = $orderlistModel->field('goods_id')->where("orderstate=3 and is_groupbuy=1 and groupbuy_ok=0 and addtime>'$date' and addtime<'$time'")->select();
		//待发货(团购成功)
		$stateOne22 = $orderlistModel->field('goods_id')->where("orderstate=3 and is_groupbuy=1 and groupbuy_ok=1 and addtime>'$date' and addtime<'$time'")->select();
		//待发货(单买)
		$stateTwo2 = $orderlistModel->field('goods_id')->where("orderstate=3 and is_groupbuy=0 and addtime>'$date' and addtime<'$time'")->select();
		//已出货
		$stateThree2 = $orderlistModel->field('goods_id')->where("(is_delivery=0 and orderstate in(3,4) and shippingtime>'$date' and shippingtime<'$time' or is_delivery=1 and orderstate=4 and confirmtime>'$date' and confirmtime<'$time')")->select();
		//将查询到的出货数据与商品想匹配
    	for($i=0;$i<count($goodsList);$i++){
            $goodsList[$i]['pics'] = explode(',',$goodsList[$i]['pic']);
            //匹配每个商品出货的记录数
            $goodsList[$i]['stateOne'] = 0;
            foreach($stateOne as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateOne']+=1;
            		unset($stateOne[$key]);
            	}
			}
            $goodsList[$i]['stateOne1'] = 0;
            foreach($stateOne1 as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateOne1']+=1;
            		unset($stateOne1[$key]);
            	}
			}
			$goodsList[$i]['stateOne2'] = 0;
			foreach($stateOne2 as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateOne2']+=1;
            		unset($stateOne2[$key]);
            	}
			}
			$goodsList[$i]['stateTwo'] = 0;
			foreach($stateTwo as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateTwo']+=1;
            		unset($stateTwo[$key]);
            	}
			}
			$goodsList[$i]['stateThree'] = 0;
			foreach($stateThree as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateThree']+=1;
            		unset($stateThree[$key]);
            	}
			}
			$goodsList[$i]['stateOnee'] = 0;
            foreach($stateOnee as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateOnee']+=1;
            		unset($stateOnee[$key]);
            	}
			}
			$goodsList[$i]['stateOne12'] = 0;
            foreach($stateOne12 as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateOne12']+=1;
            		unset($stateOne12[$key]);
            	}
			}
			$goodsList[$i]['stateOne22'] = 0;
			foreach($stateOne22 as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateOne22']+=1;
            		unset($stateOne22[$key]);
            	}
			}
			$goodsList[$i]['stateTwo2'] = 0;
			foreach($stateTwo2 as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateTwo2']+=1;
            		unset($stateTwo2[$key]);
            	}
			}
			$goodsList[$i]['stateThree2'] = 0;
			foreach($stateThree2 as $key => $val){
            	if($val['goods_id']==$goodsList[$i]['id']){
            		$goodsList[$i]['stateThree2']+=1;
            		unset($stateThree2[$key]);
            	}
			}
			$goodsList[$i]['count2'] = $goodsList[$i]['stateOnee']+$goodsList[$i]['stateOne22']+$goodsList[$i]['stateTwo2'];
			$goodsList[$i]['count'] = $goodsList[$i]['stateOne']+$goodsList[$i]['stateOne2']+$goodsList[$i]['stateTwo'];
        }
        $this->assign('urlname',"goodsoutdata");
      	$page= $p->show();// 赋值分页输出
    	$this->assign('goodslist',$goodsList);
    	$this->assign('page',$page);
    	$this->display();
    }

    public function ajaxretrieval(){
    	$goodsList = M('Goods')->field('id,good_name,kucun,pic')->where("is_show=1")->select();
    	$chuhuo = M('Orderlist')->field('goods_id')->where("(is_delivery=0 and orderstate in(3,4) or is_delivery=1 and orderstate=4)")->select();
    	if(is_array($goodsList)){
	    	foreach($goodsList as $key => $val){
	    		$goodsList[$key]['chuhuo'] = 0;
	    		foreach($chuhuo as $k => $v){
	    			if($val['id']==$v['goods_id']){
	    				$goodsList[$key]['chuhuo']++;
	    				unset($chuhuo[$k]);
	    			}
	    		}
	    	}
	    	foreach($goodsList as $key => $val){
	    		if($val['kucun']-$val['chuhuo']>10){
	    			unset($goodsList[$key]);
	    		}else{
	    			$goodsList[$key]['pics'] = explode(',',$val['pic']);
	    			if($val['kucun']-$val['chuhuo']>0){
	    				$goodsList[$key]['yuhuo'] = $val['kucun']-$val['chuhuo'];
	    			}else{
	    				$goodsList[$key]['yuhuo'] = 0;
	    			}
	    		}
	    	}
	    	$this->assign('goodsList',$goodsList);
    	}
    	$result = $this->fetch();
    	echo $result;
    }

    public function edit(){
		header("Content-Type:text/html; charset=utf-8");
        $action=A('Category');      //调用Category控制器中的方法,获取类列表
        $rs=$action->categorylist();
		$fruit=$action->fruittypelist();  //水果分类

        $goodid=I('get.id');
        $act=D('Goods');
        $goodsdetail=$act->getonegoodsdetail($goodid);
		
		//获得分类名
		$cid=M('Category');
		$cids=$cid->where('id='.$goodsdetail['classid'])->select();
		$cids=$cids[0];
		
		//编辑器内容html实体转换
		$det=str_ireplace('\"','"',htmlspecialchars_decode($goodsdetail['detail']));
		//获取规格内容
		$guige=M('Goods_guige');
		$guigelist=$guige->where('goodsid='.$goodid)->select();
		$guige_count=count($guigelist);
		if($goodsdetail['pic1']){
			$pic1list = explode(",",$goodsdetail['pic1']);
		}
		$this->assign('pic1list',$pic1list);
        $this->assign('goodsdetail',$goodsdetail);
		$this->assign('cids',$cids);
		$this->assign('guigelist',$guigelist);
		$this->assign('guige_count',$guige_count);
		$this->assign('det',$det);

		$orderlistModel = M("Orderlist");
		//待发货配送
		$stateOne = $orderlistModel->where("goods_id=".$goodid." and orderstate = 2 and ((is_groupbuy = 1 and groupbuy_ok = 1) or (is_groupbuy = 0))")->count();
		//待发货(团购中)
		$stateOne1 = $orderlistModel->where("goods_id=".$goodid." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=0")->count();
		//待发货(团购成功)
		$stateOne2 = $orderlistModel->where("goods_id=".$goodid." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=1")->count();
		//待发货(单买)
		$stateTwo = $orderlistModel->where("goods_id=".$goodid." and orderstate=3 and is_groupbuy=0")->count();
		//已出货
		$stateThree = $orderlistModel->where('goods_id='.$goodid." and (is_delivery=0 and orderstate in(3,4) or is_delivery=1 and orderstate=4)")->count();
		//$stateAll = M('orderdetail')->where('goodsid='.$goodid)->count();
		$this->assign('stateOne',$stateOne);
		$this->assign('stateOne1',$stateOne1);
		$this->assign('stateOne2',$stateOne2);
		$this->assign('stateTwo',$stateTwo);
		$this->assign('stateThree',$stateThree);
		$this->assign('stateAll',$stateOne1+$stateOne2+$stateTwo+$stateThree+$stateOne);
        $goodsname=I('post.title');
//          echo $goodsname.'a';die;
        if(!empty($goodsname)){
			
			if(I('post.class_id')!=''){
            $date['classid']=I('post.class_id');
			$date['othercid']=I('post.othercid');
			$pid_arr=M('category')->where('id='.$date['classid'])->find();
			$date['parid']=$pid_arr['fid'];
			}
            $date['good_name']=$goodsname;
			$date['good_name1']=I('post.good_name1');
			$date['resource']=I('post.resource');
			$date['group_num']=I('post.group_num');
			$date['group_time']=I('post.group_time');
			$date['address']=I('post.address');
			$date['kucun']=I('post.kucun');
			$date['virtual']=I('post.virtual');
			$date['pic']=I('post.pic');
			$date['pic2']=I('post.pic2');
            $date['pic1']=implode(",",I('post.pic1'));
            $date['sku']=I('post.goods_no');
            $date['detail']=I('post.detail');
            $date['is_show']=I('post.is_show');
            $date['addtime']=Gettime();
            $date['rank']=I('post.rank');
			$date['is_restriction']=I('post.is_restriction');
			$date['is_restriction2']=I('post.is_restriction2');
			$date['restriction_num']=I('post.restriction_num');
			$date['restriction_num2']=I('post.restriction_num2');
			$date['is_delivery']=I('post.is_delivery');
			$date['buylimit']=I('post.buylimit');
			$date['keeptime']=I('post.keeptime');
			$date['recommend_g']=I('post.recommend_g');
			$date['dealers_id']=I('post.dealers_id');
			$date['erpgoodsid']=I('post.erpgoodsid',0,'int');
			$date['erpgoodsid2']=json_encode(array_combine(I('post.erpid'),I('post.erpgoodsid2')));
			$date['recommend_gnum']=I('post.recommend_gnum');
			$date['certain_success']=I('post.certain_success');
            $date['id']=I('get.id');
			
            $m=M('goods');
            $rs=$m->save($date);
            if($rs){
                $this->success('添加成功',U('/Admin/Goods'));
            }else{
                $this->error('添加失败');
            }
			
			
			// 存入对应规格
			$goodsid=I('get.id');
			$goods_guige = M('Goods_guige');
			$guige_num=I('post.nums');
			
			$goods_guige->where('goodsid='.$goodsid)->delete();
			
			if($guige_num!=0){
				$j=1;
				for($i=0; $i < $guige_num; $i++){
					$guiges[$i]['guige']=I('post.guige'.$j);
					$guiges[$i]['weight']=I('post.weight'.$j);
					$guiges[$i]['old_price']=I('post.guige_old_price'.$j);
					$guiges[$i]['groupprice']=I('post.guige_groupprice'.$j);
					$guiges[$i]['price']=I('post.guige_price'.$j);
					$j++;
					}
				for ($i=0; $i < $guige_num; $i++) {
                $datas = array(
                    'goodsid'=>$goodsid,
                    'guige'=>$guiges[$i]['guige'],
					'weight'=>$guiges[$i]['weight'],
					'old_price'=>$guiges[$i]['old_price'],
					'groupprice'=>$guiges[$i]['groupprice'],
                    'price'=>$guiges[$i]['price']
                );
                $goods_guige->add($datas);
            }
			}
			
			
			

        }else{
			$dealerIds = M('Dealer')->field('id,username')->select();
			$goodsErpIds = json_decode($goodsdetail['erpgoodsid2'],true);
			foreach($goodsErpIds as $key => $val){
				foreach($dealerIds as $bKey => $bVal){
					if($key==$bVal['id']){
						$dealerIds[$bKey]['value'] = $val;
					}
				}
			}
			$this->assign('dealerIds',$dealerIds);
            $this->assign('categorylist',$rs);
			$this->assign('fruitlist',$fruit);
			$this->assign('urlname',"gindex");
            $this->assign('munetype',3);
            $this->display();
        }

    }
    //测试
    public function edits(){
		header("Content-Type:text/html; charset=utf-8");
        $action=A('Category');      //调用Category控制器中的方法,获取类列表
        $rs=$action->categorylist();
		$fruit=$action->fruittypelist();  //水果分类

        $goodid=I('get.id');
        $act=D('Goods');
        $goodsdetail=$act->getonegoodsdetail($goodid);
		
		//获得分类名
		$cid=M('Category');
		$cids=$cid->where('id='.$goodsdetail['classid'])->select();
		$cids=$cids[0];
		
		//编辑器内容html实体转换
		$det=str_ireplace('\"','"',htmlspecialchars_decode($goodsdetail['detail']));
		//获取规格内容
		$guige=M('Goods_guige');
		$guigelist=$guige->where('goodsid='.$goodid)->select();
		$guige_count=count($guigelist);
		
		$pic1list = explode(",",$goodsdetail['pic1']);

		$this->assign('pic1list',$pic1list);
        $this->assign('goodsdetail',$goodsdetail);
		$this->assign('cids',$cids);
		$this->assign('guigelist',$guigelist);
		$this->assign('guige_count',$guige_count);
		$this->assign('det',$det);
		
		$orderlistModel = M("Orderlist");
		//待发货(团购中)
		$stateOne1 = $orderlistModel->where("goods_id=".$goodid." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=0")->count();
		//待发货(团购成功)
		$stateOne2 = $orderlistModel->where("goods_id=".$goodid." and orderstate=3 and is_groupbuy=1 and groupbuy_ok=1")->count();
		//待发货(单买)
		$stateTwo = $orderlistModel->where("goods_id=".$goodid." and orderstate=3 and is_groupbuy=0")->count();
		//已出货
		$stateThree = $orderlistModel->where("goods_id=".$goodid." and (is_delivery=0 and orderstate in(3,4) or is_delivery=1 and orderstate=4)")->count();
		//$stateAll = M('orderdetail')->where('goodsid='.$goodid)->count();
		$this->assign('stateOne1',$stateOne1);
		$this->assign('stateOne2',$stateOne2);
		$this->assign('stateTwo',$stateTwo);
		$this->assign('stateThree',$stateThree);
		$this->assign('stateAll',$stateOne1+$stateOne2+$stateTwo+$stateThree);

        $goodsname=I('post.title');
//          echo $goodsname.'a';die;
        if(!empty($goodsname)){
			
			if(I('post.class_id')!=''){
            $date['classid']=I('post.class_id');
			$date['othercid']=I('post.othercid');
			$pid_arr=M('category')->where('id='.$date['classid'])->find();
			$date['parid']=$pid_arr['fid'];
			}
            $date['good_name']=$goodsname;
			$date['good_name1']=I('post.good_name1');
			$date['resource']=I('post.resource');
			$date['group_num']=I('post.group_num');
			$date['group_time']=I('post.group_time');
			$date['address']=I('post.address');
			$date['kucun']=I('post.kucun');
			$date['virtual']=I('post.virtual');
			$data['pic']=I('post.pic');
            $date['pic1']=implode(",",I('post.pic1'));
            $date['sku']=I('post.goods_no');
            $date['detail']=I('post.detail');
            $date['is_show']=I('post.is_show');
            $date['addtime']=Gettime();
            $date['rank']=I('post.rank');
			$date['is_restriction']=I('post.is_restriction');
			$date['restriction_num']=I('post.restriction_num');
			$date['is_delivery']=I('post.is_delivery');
            $date['id']=I('get.id');
			
            $m=M('goods');
            $rs=$m->save($date);
            if($rs){
                $this->success('添加成功',U('/Admin/Goods'));
            }else{
                $this->error('添加失败');
            }
			
			
			// 存入对应规格
			$goodsid=I('get.id');
			$goods_guige = M('Goods_guige');
			$guige_num=I('post.nums');
			
			$goods_guige->where('goodsid='.$goodsid)->delete();
			
			if($guige_num!=0){
				$j=1;
				for($i=0; $i < $guige_num; $i++){
					$guiges[$i]['guige']=I('post.guige'.$j);
					$guiges[$i]['weight']=I('post.weight'.$j);
					$guiges[$i]['old_price']=I('post.guige_old_price'.$j);
					$guiges[$i]['groupprice']=I('post.guige_groupprice'.$j);
					$guiges[$i]['price']=I('post.guige_price'.$j);
					$j++;
					}
				for ($i=0; $i < $guige_num; $i++) {
                $datas = array(
                    'goodsid'=>$goodsid,
                    'guige'=>$guiges[$i]['guige'],
					'weight'=>$guiges[$i]['weight'],
					'old_price'=>$guiges[$i]['old_price'],
					'groupprice'=>$guiges[$i]['groupprice'],
                    'price'=>$guiges[$i]['price']
                );
                $goods_guige->add($datas);
            }
			}
			
			
			

        }else{
            $this->assign('categorylist',$rs);
			$this->assign('fruitlist',$fruit);
			$this->assign('urlname',"gindex");
            $this->assign('munetype',3);
            $this->display();
        }

    }
	public function is_new(){
		
        $goosid=I('post.goodsid');
        $m = D("goods"); // 实例化User对象
		
        $rs=$m->is_new($goosid); 
		
        $this->ajaxReturn($rs);
		return;
    }
    public function recommend_g(){
		
        $goosid=I('post.goodsid');
        $m = D("goods"); // 实例化User对象
		
        $rs=$m->recommend_g($goosid); 
		
        $this->ajaxReturn($rs);
		return;
    }
	


}