<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Classify extends Model
{
	public function cate()
	{
		$data = Db::name('classify')->select();


		return $this->getTree($data,$classid=0);
		//var_dump($con);die;
	}
	function getTree($data, $classid=0)
	{
		$tree = '';
		foreach($data as $k => $v){
			  if($v['classid'] == $classid)
			  {   //父找子
				   $v['classid'] = $this->getTree($data, $v['id']);


				   $tree[] = $v;
				   //var_dump($v);
			  }


		}
		    return $tree;
	}
}

