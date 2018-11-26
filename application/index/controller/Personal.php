<?php
namespace app\index\controller;
// use think\Controller;
use app\index\model\Classify;
use app\index\model\Car;
use think\Request;
use think\Db;
use think\Captcha;
use think\Cookie;
use pay\aop\AopClient;
use pay\aop\request\AlipayTradePagePayRequest;
class Personal extends Base
{
	protected $is_check_login = ['*'];
	// 购物车详情页
	public function shopcar(Classify $classify)
	{
		$arr = $classify->cate();
		//购物车信息查询
		$uid = input('post.uid');
		$goods_info =Db::table('shop_car')
         ->alias('c')
         ->join('shop_show s','c.gid = s.gId')
         ->join('shop_name n','n.id=s.sid' )
        
         ->select();
        $store_group = array();
        foreach ($goods_info as $item){
            $store_group[$item['sid']][]=$item;
        }

       
        foreach($store_group as $store_id=>&$item){
            $storeInfo=Db::table('shop_name')->field('id as store_id, name')->where(array('id'=>$store_id))->find();
            $item=array_merge($storeInfo,array('goods_list'=>$item));
        }
        // $store_group=array_values($store_group);
     	
		$count  = Db::name('car')->count();
		
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
		$this->assign(['username'=>$username,'uid'=>$uid]);
		$this->assign(['arr'=>$arr,'store_group'=>$store_group,'count'=>$count]);

		return $this->fetch('personal/shopcar');
	}

	//我的订单
	public function order(Classify $classify)
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
			$this->assign(['username'=>$username,'uid'=>$uid,'arr'=>$arr]);
	$arr = $classify->cate();	
		return $this->fetch('personal/order');
	}

	//我的收藏
	public function love(Classify $classify)
	{
		//遍历search
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
		$arr = $classify->cate();
		$this->assign(['username'=>$username,'uid'=>$uid,'arr'=>$arr]);

		// 遍历收藏
		$user = session('uid');
		$collect = Db::table('shop_love')->where('userid' , $user)->select();
		// var_dump($collect);s
		$this->assign(['collect'=>$collect]);

		return $this->fetch('personal/love');
	}

	// 添加收藏
	public function addlove()
	{
		$data = [
			'userid'=>session('uid'),
			'gid'=>input('post.goodsid'),
			'gname'=>input('post.goodsname'),
			'gprice'=>input('post.goodsprice'),
			'gimg'=>input('post.goodsimg')
		];

		$result = Db::table('shop_love')->insert($data);
		// var_dump($result);

		if($result){
			return json([
				'status'=>0,
				'msg'=>'已收藏',
			]);
		}else{
			
			$result = Db::table('shop_love')->insert($data);
			// var_dump($result);

			if($result){
				return json([
					'status'=>1,
					'msg'=>'收藏成功',
				]);
			}else{
				return json([
					'status'=>2,
					'msg'=>'收藏失败'
				]);
			}
		}

	}
	// 取消收藏
	public function dellove()
	{
		$gid = input('post.goodsid');

		$result = Db::table('shop_love')->where('gid' , $gid)->delete();
		// var_dump($result);

		if($result){
			return json([
				'status'=>1,
				'msg'=>'取消收藏成功',
			]);
		}else{
			return json([
				'status'=>0,
				'msg'=>'取消收藏失败'
			]);
		}

	}
	// 收藏页面的取消收藏
	public function qx()
	{
		$qxid = input('post.qxid');

		$result = Db::table('shop_love')->where('gid' , $qxid)->delete();
		// var_dump($result);

		if($result){
			return json([
				'status'=>1,
				'msg'=>'取消收藏成功',
			]);
		}else{
			return json([
				'status'=>0,
				'msg'=>'取消收藏失败'
			]);
		}
	}


	// 我的足迹
	public function foot(Classify $classify)
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
		$this->assign(['username'=>$username,'uid'=>$uid,'arr'=>$arr]);

		return $this->fetch('personal/foot');
	}

	// 地址管理
	public function address(Classify $classify)
	{

		
		$belongname = session('username');
		$add = Db::table('shop_address')->where('belonguser' , $belongname)->select();
		// var_dump($add);
		$this->assign(['add'=>$add]);

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
		$arr = $classify->cate();
		$this->assign(['username'=>$username,'uid'=>$uid,'arr'=>$arr]);

		return $this->fetch('personal/address');
	}

	// 增加地址
	public function doaddress()
	{
		$data = [
			'country'=>input('post.country'),
			'city'=>input('post.city'),
			'address'=>input('post.address'),
			'emsid'=>input('post.emsid'),
			'getname'=>input('post.getname'),
			'tel'=>input('post.tel'),
			'belonguser'=>session('username'),
			'status'=>input('post.status')
		];

		// echo $data['tel'];

		$result = Db::table('shop_address')->insert($data);

		if($result){
			return json([
				'status'=>1,
				'msg'=>'成功',
			]);
		}else{
			return json([
				'status'=>0,
				'msg'=>'失败'
			]);
		}

	}

	// 删除地址
	public function dodel()
	{
		$data = [
			'addid'=>input('post.delid')
		];

		$result = Db::table('shop_address')->delete($data);

		// echo $result;

		if($result){
			return json([
				'status'=>1,
				'msg'=>'成功',
			]);
		}else{
			return json([
				'status'=>0,
				'msg'=>'失败'
			]);
		}
	}

	// 更新数据
	public function update()
	{
		$updateid = input('get.updateid');
		// echo $updateid;
		$update = Db::table('shop_address')->where('addid' , $updateid)->select();
		
		$this->assign('update' , $update);
		return $this->fetch('personal/update');
	}

	public function doupdate()
	{

		$data = [
			'addid'=>input('post.addid'),
			'country'=>input('post.country'),
			'city'=>input('post.city'),
			'address'=>input('post.address'),
			'emsid'=>input('post.emsid'),
			'getname'=>input('post.getname'),
			'tel'=>input('post.tel'),
			'status'=>input('post.status')
		];

		$result = Db::table('shop_address')->where('addid' , $data['addid'])->update($data);
		// var_dump($result);

		if($result){
			return json([
				'status'=>1,
				'msg'=>'成功',
				'redirect_url'=>url('personal/address')
			]);
		}else{
			return json([
				'status'=>0,
				'msg'=>'失败'
			]);
		}

	}



	// 退货
	public function goodsout(Classify $classify)
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
		$arr = $classify->cate();
		$this->assign(['username'=>$username,'uid'=>$uid,'arr'=>$arr]);
		return $this->fetch('personal/goodsout');
	}



	//用户信息
	public function userinfo(Classify $classify)
	{	
		// 搜索框
		 $arr = $classify->cate();	
		 //var_dump($gBelongId);	
		$this->assign(['arr'=>$arr]);

		$name = session('username');
		$info = Db::table('shop_user')->where('username' , $name)->select();
		// var_dump($info);
		$this->assign(['info'=>$info]);
		return $this->fetch('personal/userinfo');
	}

	public function douserinfo()
	{

		$verify = input('post.verify');
		// var_dump($verify);
		if(!captcha_check($verify)){
			return '验证码不正确';
		};


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
		$this->assign(['username'=>$username,'uid'=>$uid]);
		 $arr = $classify->cate();
		 $id = input('get.id');
		 $gid = input('get.id');

		 $gBelongId = Db::table('shop_show')->where('gId',$gid)->select()[0]['gBelongId'];
		 //var_dump($gBelongId);
		$list = db::table('shop_show')->where('gId',$id)->select();
		$classify = Db::table('shop_classify')->field('classname')->where('id',$gBelongId)->select();

		$this->assign(['arr'=>$arr,'list'=>$list,'classify'=>$classify]);


		// 用户信息
		$data = [
			"realname"=>input('post.realname'),
			'usernick'=>input('post.usernick'),
			'sex'=>input('post.sex'),
			"telephone"=>input('telephone'),
			'email'=>input('post.email'),
			'birthday'=>input('post.birthday')
		];
		

		$result = Db::table('shop_user')->where('username' , session('username'))->update($data);
		// var_dump($result);
		echo $result;
		if($result){
			return json([
				'status'=>1,
				'msg'=>'修改资料成功',
			]);
		}else{
			return json([
				'status'=>0,
				'msg'=>'修改资料失败'
			]);
		}

	}

	//购物车操作1
	public function doshopcar()
	{
		$result = null;
		//$username = input('post.username');
		$data['gid'] = input('post.gid');
		//$data['username'] = '马小跳';
		$data['gname'] = input('post.gname');;
		$data['gprice'] = input('post.gprice');;
		$data['gpicture'] = input('post.gpicture');
		$data['username'] = input('post.username');
		$data['uid'] = input('post.uid');
		$update = Db::name('car')->where('gid',$data['gid'])->select();
		if($update){
			$result = Db::name('car')->where('gid',$data['gid'])->setInc('count');
		}else{
			$result = Db::name('car')->insert($data);
		}
		
	
		
		if ($result) {

			
			
			return json(['status'=>1 , 'msg'=>'加购成功']);


		} else {

			return json(['status'=>0 , 'msg'=>'登陆后才可加入购物车']);
		}
		
	} 

	public function pay()
	{
		if(session('?username')){
		
			$uid = session('uid');
		}else {
			
			 $uid =  Cookie::get('uid');
		}
    	
    	$username = input('post.username');
    	$city = input('post.city').input('post.address');
    	$emsid = input('post.emsid');
    	$tel = input('post.tel');
    	$gid = input('post.gid/a');
    	$num = input('post.num/a');
    	$count = count($num);
    	$list = [
    		
    	];
 	   	for($i=0;$i<$count;$i++){
 	   		$orderid = time().rand(Time::daysToSecond(5), Time::daysToSecond(10));
 	   		$list[$i] = ['uid'=>$uid,'gid'=>$gid[$i],'number'=>$num[$i],'telephone'=>$tel,'address'=>$city,'emsid'=>$emsid,'orderid'=>$orderid,'username'=>$username];
 	   		
 	   		
    	}
    	
    	//var_dump($list);
    	$res = $order->saveAll($list);


		require_once("AopSdk.php");
		

	}
}