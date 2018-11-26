<?php 
namespace app\index\Controller;
use think\Controller;
use think\Request;
use think\Cookie;
class Base extends Controller
{
	//变量用来存放需要用户登录之后才能操作的方法的集合
	protected $is_check_login = ['']; 
	public function _initialize()
	{
		
		if(!$this->isLogin() && Cookie::has('username')=='' && ((in_array(Request::instance()->action(),$this->is_check_login)  || $this->is_check_login[0]=='*'))){
			return $this->error('请先登录','index/Index/login');
		}
	}

	public function isLogin()
	{

		return session('?username');
	}
}