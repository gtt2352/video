<?php
namespace app\index\model;
use think\Model;
//use traits\model\SoftDelete;

class User extends Model
{	
	//use SoftDelete;
	//protected $deleteTime = 'delete_time';

	//protected $autoWriteTimestamp = 'false';

	

	public function getStatusAttr($value)
	{
		$status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];

		return $status[$value];
	}

	

	



}
