<?php
namespace Home\Controller;
use Home\Controller\CommonaddpidController;
class AddressController extends CommonaddpidController {


    public function index(){

        $this->assign('foottype',4);
        $this->display();
    }
   public function address(){

        $this->assign('foottype',4);
        $this->display();
    }
   public function address1(){

        $this->assign('foottype',4);
        $this->display();
    }


}