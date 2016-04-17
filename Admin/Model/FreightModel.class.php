<?php
namespace Admin\Model;
use Think\Model;
class FreightModel extends Model{
		
		
		function Freightlist(){

        $count = $this->where($date)->count();
        $p = getpage($count,10);
        $list = $this->field(true)->where($date)->order('id desc')->limit($p->firstRow, $p->listRows)->select();

		$date['list']=$list; // 赋值数据集
		$date['page']= $p->show();// 赋值分页输出
		$date['count']= $count;
        return $date;
		}
		
		
		// 添加
		function addFreight($privonce,$sweight,$sprice,$xwprice){
			
			$data=array(
				"privonce"=>$privonce,
				"sweight"=>$sweight,
				"sprice"=>$sprice,
				"xwprice"=>$xwprice
			);
				$is_ok = $this->data($data)->add();	
				if($is_ok)
				{
					return 1;
				}else{
					return 2;
				}
		
		}
		
		//编辑
		function editFreight($id,$privonce,$sweight,$sprice,$xwprice){

				$postdata=array(
					"id"=>$id,
					"privonce"=>$privonce,
					"sweight"=>$sweight,
					"sprice"=>$sprice,
					"xwprice"=>$xwprice
				);
				$this->save($postdata);
				return 1;	

	
		}

		
		// 删除
		function del($id){

			$result = $this->where("id=$id")->delete();
			if($result)
			{
				return 1; // 成功
			}
			else
			{
				return 0; // 失败
			}
			
		
		}
		
			
		function getone($name){
			$userid = $_SESSION['admin_id'];
			$rs = $this->where('id='.$userid)->getField($name);
			return $rs;
		}	
		

	}
 ?>