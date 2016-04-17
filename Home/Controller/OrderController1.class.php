<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class OrderController extends CommonController {
    public function order(){
//        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');

        $this->display();
    }

    public function addorder(){
        $gid=I('get.gid');
        $num=I('get.num');
        $cenghigh=$_GET['cenghigh'];

        $action=D('Goods');
        $rs=$action->getGoodsdetail($gid);

        $memberid=$_SESSION['member_id'];
        $action=D("orderaddress");
        $rsaddress=$action->getOneaddress($memberid);




        $totalprice= $rs['good_nowprice']*$num;

        $_SESSION['orderurl']=$_SERVER['REQUEST_URI'];
//        var_dump($orderurl);

        $this->assign('addressdetail',$rsaddress);
        $this->assign('goodsdetail',$rs);
        $this->assign('num',$num);
        $this->assign('cenghigh',$cenghigh);
        $this->assign('totalprice',$totalprice);
        $this->display();
    }
    public  function address(){



        $memberid=$_SESSION['member_id'];
//echo $memberid;die;
        $action=D("orderaddress");
        $rs=$action->getOneaddress($memberid);
//        echo $action->_sql();die;
//        var_dump($rs);

        $type=I('post.type');
        if($type!=''){
            if($type=='add'){

                $date['province']=I('post.province');
                $date['city']=I('post.city');
                $date['country']=I('post.country');
                $date['address']=I('post.address');
                $date['postcode']=I('post.postcode');
                $date['telephone']=I('post.telephone');
                $date['xiangqing']=I('post.address');
                $date['consignee']=I('post.consignee');
                $date['userid']=$memberid;
                $date['addtime']=Gettime();
                $action=D("orderaddress");
                $result=$action->addaddress($date);

                if( $result){
                    $this->redirect($_SESSION['orderurl']);
                }else{
                    $this->error("添加失败","{:U('Order/address')}");
                }
            }else if($type=='edit'){
                $date['province']=I('post.province');
                $date['city']=I('post.city');
                $date['country']=I('post.country');
                $date['address']=I('post.address');
                $date['postcode']=I('post.postcode');
                $date['telephone']=I('post.telephone');
                $date['xiangqing']=I('post.address');
                $date['consignee']=I('post.consignee');
                $date['id']=I('post.address_id');
                $action=D("orderaddress");
                $result=$action->editaddress($date);

                if( $result>0 || $result ===0 ){
                    $this->redirect($_SESSION['orderurl']);
                }else{
                    $this->error("更新失败");
                }


            }
        }
        $orderurl=$_SESSION['orderurl'];


        if($rs){
            $this->assign('address',$rs);
            $this->display('editaddress');
        }else{
//            $this->assign('num',$rs);

            $this->display('addaddress');
        }


//        $this->assign('orderurl',$orderurl);


    }

    public function orderadd()
    {


//        orderlist
        $memberid = $_SESSION['member_id'];
        $order['userid'] = $memberid;
        $order['orderno'] = date('YmdHis', time()) . rand(1000, 9999);
        $order['consignee'] = I('post.consignee');
        $order['telephone'] = I('post.telephone');
        $order['address'] = I('post.address');

        $order['total_price'] = I('post.total_price');
        $order['comment'] = I('post.message');
        $order['orderstate'] = 1;
        $order['addtime'] = Gettime();

//        var_dump($order);
        $action = D('Orderlist');
        $rs = $action->addorderlist($order);   //插入订单
//        var_dump($order);
//      orderdetail
        $orderd['goodsid'] = I('post.goods_id');
        $orderd['orderno'] = $order['orderno'];
        $orderd['good_nowprice'] = I('post.good_nowprice');
        $orderd['goodsname'] = I('post.goods_name');
        $orderd['cenghigh'] = I('post.cenghigh');
        $orderd['img'] = I('post.img');
        $orderd['num'] = I('post.num');
        $orderd['allprice'] = $orderd['num'] * $orderd['good_nowprice'];
        $orderd['addtime'] = Gettime();
        $orderd['orderid'] = $rs;
        $action1 = D('Orderdetail');
        $rsd = $action1->addorderdetail($orderd);   //插入订单细节

        if ($rsd) {

            $actionpay = D('Member');
            $returnpay = $actionpay->payorder($memberid, $order['total_price']);

            if ($returnpay['type'] == 1) {
                //account表插入记录
                $actionacount = D('account');
                $rsacount = $actionacount->addcountdetail($memberid, $order['total_price'], '支付订单成功');

                if($rsacount){
//                    更新订单状态
                    $action->updateOrderState($orderd['orderid'],2);
                }
                $this->success("支付成功", U("Member/main"));

            } else {
                $errormsg=$returnpay['message'];
                $this->error($errormsg, U("Member/main"));
            }
        } else {
            $this->error("订单插入失败");
        }

    }


//    public function getorderlist($userid,$state){
//
//        $action
//    }
}