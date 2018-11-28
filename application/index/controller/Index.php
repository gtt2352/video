<?php
namespace app\index\controller;
// use think\Controller;
use app\index\model\Classify;
use think\Request;
use think\Db;
use app\index\model\Show;
use app\index\model\Order;
use think\Cookie;
use think\helper\Time;
class Index extends Base
{
	protected $is_check_login = ['pay'];
    //主页
	public function index(Classify $classify)
	{
		$list = Db::name('list')->select();
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$list]);
		exit;
		
	}
	//执行自动拉取文件的工作
	public function hook()
	{
		echo exec('git pull');
	}
	public function lunbo()
	{
		$list = Db::name('list')->order('like desc')->limit(0,3)->select();
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$list]);
		exit;
	}

	//获取单个视频详情
	public function single() 
	{
		echo input('post.id');
		var_dump(input('post.id'));
		$list = Db::name('list')->where(['id'=>input('post.id')])->select();
		exit;

	}
	
   

  
 }

