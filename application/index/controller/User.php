<?php
namespace app\index\controller;
// use think\Controller;
use app\index\model\User as UserModel;
use think\Request;
use think\Validate;
use think\Db;
use think\Session;
use think\Cookie;
use app\index\model\Classify;
class User extends Base
{
 	
 	protected $is_check_login = ['set'];
 	//用户登录
    public function login(UserModel $user)
    {
    	
		$data['username'] = input('post.username');
		$data['password'] = md5(input('post.password'));
		$result = Db::table('shop_user')->where('username',$data['username'])->where('password',$data['password'])->find();
		//var_dump($result);
		$auto = input('post.auto');

		if ($result) {
			if($auto){
				 Cookie::set('username',$result['username'],7*24*3600);
  				 Cookie::set('uid',$result['id'],7*24*3600);
				}

			session('uid',$result['id']);
			session('username' , $data['username']);
			return json(['status'=>1 , 'msg'=>'登陆成功' , 'redirect_url'=>url('index/index')]);


			} else {

				return json(['status'=>0 , 'msg'=>'登录失败' , 'redirect_url'=>url('index/index')]);
			}
		
    }

    //用户注册
    public function doregister(UserModel $user)
	{

		$validate = new Validate([
			'username' => 'require|max:20',
			'password' => 'require|max:20',
			'email'=>'email'
		]);

		$data = [
			'username'=>input('post.username'),
			'password'=>input('post.password'),
			'email'=>input('post.email')
		];

		if(!$validate->check($data)){
			return json(['status'=>0 , 'msg'=>$validate->getError(),'redirect_url'=>url('index/index/index')]);
		}else{
			$data['password'] = md5(input('post.password'));

			$result = Db::name('user')->insert($data);
			$id = db::name('user')->order('id desc')->field('id')->find()['id'];
			
			Db::name('user_role')->insert(['uid'=>$id,'rid'=>7]);
			if($result){
				return json([
					'status'=>1,
					'msg'=>'注册成功',
					'redirect_url'=>url('index/login')
				]);
			}else{
				return json([
					'status'=>0,
					'msg'=>'注册失败',
					'redirect_url'=>url('index/register');
				]);
			}

		}

		
	}

	//验证名称重复
	public function doname(UserModel $user)
	{
		$username = input('post.username');
		// echo $username;
		//Ajax检测用户名称
		$result  = Db::name('user')->where(['username' => $username])->select();
		//var_dump($result);

		if($result){
			
			return json(['status'=>0 , 'msg'=>'用户名已注册']);
			
		}else{
			return json(['status'=>1 , 'msg'=>'用户名通过']);
			
		}
		
	}

	//头像上传
	public function upload(){
	// 获取表单上传文件 例如上传了001.jpg
	$file = request()->file('image');
	// 移动到框架应用根目录/public/uploads/ 目录下
	$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info){
			// 成功上传后 获取上传信息
			// 输出 jpg
			echo $info->getExtension();
			// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
			echo $info->getSaveName();
			// 输出 42a79759f284b767dfcb2a0197904287.jpg
			echo $info->getFilename(); 
		}else{
		    // 上传失败获取错误信息
		    echo $file->getError();
		}
	}

	//用户资料设置
	public function set(Classify $classify)
	{
		$arr = $classify->cate();
		if(session('?username')){
			$username = session('username');
			$uid = session('uid');
		}else if(Cookie::has('username')){
			 $username =  Cookie::get('username');
			 $uid =  Cookie::get('uid');
		}else{
			$username = null;
			$uid = null;
		}	
		$info = session('username');
		$select = Db::name('user')->where('username' , $info)->select();
		// var_dump($select);
		$this->assign(['select'=>$select,'username'=>$username,'uid'=>$uid,'arr'=>$arr]);

		return $this->fetch('personal/set');
	}

	//退出
	public function loginout()
	{
		session(null);
		Cookie::clear('username');
		Cookie::clear('uid');
		$this->success('退出成功' ,url('index/index'));
	}




}
