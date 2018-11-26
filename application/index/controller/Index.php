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
		echo '123456';
		
	}
	public function hook()
	{
		echo exec('git pull');
	}

	// 女装
	public function women(Classify $classify)
	{

		echo 1222;
	}

	//女装详情
	public function womens(Classify $classify)
	{	
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
		//服装导航栏信息查询
		$list = Db::name('classify')->where(['classid'=>0])->select();
		$arr = $classify->cate();
		$this->assign(['list'=>$list,'arr'=>$arr]);
		
		$gid = input('get.id');
		$wpage = Db::name('show')->where(['gId'=>$gid])->select();
		// var_dump($wpage);
		$this->assign(['wpage'=>$wpage,'username'=>$username,'uid'=>$uid]);
		return $this->fetch('index/womens');
	}

	
	//男装
	public function mens(Classify $classify)
	{
		
		 $arr = $classify->cate();
		 $list = Db::table('shop_show')
         ->alias('s')
         ->join('shop_classify c','s.gBelongId = c.id','LEFT')
        ->where('c.classid','1')
         ->select();
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
		$this->assign(['arr'=>$arr,'list'=>$list,'username'=>$username,'uid'=>$uid]);
		return $this->fetch('index/mens');
	}

	// 童装 
	public function kids(Classify $classify)
	{
		 $arr = $classify->cate();
		 $list = Db::table('shop_show')
         ->alias('s')
         ->join('shop_classify c','s.gBelongId = c.id','LEFT')
        ->where('c.classid','3')
         ->select();
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
		$this->assign(['arr'=>$arr,'list'=>$list,'username'=>$username,'uid'=>$uid]);
		return $this->fetch('index/kids');
	}
	//产品类别页

	public function ify(Classify $classify)
	{
		$arr = $classify->cate();
		$gBelongId = input('get.id');
		
		$list = Db::table('shop_show')->where('gBelongId',$gBelongId)->select();
		$classify = Db::table('shop_classify')->field('classname,classid')->where('id',$gBelongId)->select();
		$classid = $classify[0]['classid'];
		$c1 = Db::table('shop_classify')->field('classname')->where('id',$classid)->select();
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
		$this->assign(['arr'=>$arr,'list'=>$list,'classify'=>$classify,'c1'=>$c1,'username'=>$username,'uid'=>$uid]);
		return $this->fetch('index/ify');
	}

	//商品详情页
	public function kid(Classify $classify)
	{
		 $arr = $classify->cate();
		 $id = input('get.id');
		 $gid = input('get.id');

		 $gBelongId = Db::table('shop_show')->where('gId',$gid)->select()[0]['gBelongId'];
		 //var_dump($gBelongId);
		$list = db::table('shop_show')->where('gId',$id)->select();

		$classify = Db::table('shop_classify')->field('classname,classid')->where('id',$gBelongId)->select();
		$classid = $classify[0]['classid'];
		$ify = Db::table('shop_classify')->field('classname,classid')->where('id',$classid)->select();
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
		$this->assign(['arr'=>$arr,'list'=>$list,'classify'=>$classify,'ify'=>$ify,'username'=>$username,'uid'=>$uid]);
		
		return $this->fetch('index/kid');
	}

	public function paybefore()
	{
		$price = input('post.sprice/a');
		$picture = input('post.gPicture/a');
		$name = input('post.gName/a');
		$num = input('post.gNumber/a');
		$xjprice = input('post.xjprice/a');
		$gid = input('post.gid/a');
		session('price',$price );
		session('picture',$picture );
		session('name',$name );
		session('num',$num );
		session('xjprice',$xjprice);
		session('gid',$gid);
	}


	//支付页面详情页
	public function pay(Classify $classify)
	{
		
		

		$arr = $classify->cate();
		if(session('?username')){
			$username = session('username');
			$uid = session('uid');
		}else{
			 $username =  Cookie::get('username');
			 $uid =  Cookie::get('uid');
		}
		$add = Db::name('address')->where(['belonguser'=>$username,'status'=>1])->select();
		$price = session('price');
		$picture = session('picture');
		$name = session('name');
		$num = session('num');
		$xjprice = session('xjprice');
		$gid = session('gid');
		session('name',null);
		session('price',null);
		session('picture',null);
		session('num',null);
		session('xjprice',null);
		// var_dump($num);
		session('gid',null);
		$this->assign(['add'=>$add,'arr'=>$arr,'username'=>$username,'uid'=>$uid,

			'price'=>$price,'picture'=>$picture,'gid'=>$gid,
			'name'=>$name,'num'=>$num,'xjprice'=>$xjprice
	]);
		return $this->fetch();
	}
	

	
	// 关于
	public function about()
	{
		return $this->fetch('index/about');
	}

	// 博客
	public function blog()
	{
		return $this->fetch('index/blog');
	}

	// 联系
	public function contact()
	{
		return $this->fetch('index/contact');
	}

	//single page
	public function single()
	{
		return $this->fetch('index/single');
	}

	//shop
	public function shop()
	{
		return $this->fetch('index/shop');
	}



	//结算
	public function payment()
	{
		return $this->fetch('index/payment');
	}
    

    //搜索
	public function search(Classify $classify)
	{
		// $id = input('get.id');

		$arr = $classify->cate();
		$gid = input('get.id');
		// echo $gid;
		$goods = Db::table('shop_show')->where('gBelongId',$gid)->select();

		if($goods){
			return json([
				//success
				'status'=>1,
				'redirect_url'=>url('index/ify')
				]);
		}else{
			return json([
				//error
				'status'=>0,
				'redirect_url'=>url('index/women')
			]);
		}
	}

	public function register()
	{
		return $this->fetch('');
	}
    public function login()
    {

    	return $this->fetch();
    }

   

  
 }

