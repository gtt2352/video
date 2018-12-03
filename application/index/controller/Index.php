<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\index\model\Comment;
class Index extends Controller
{
	//用户登录后将用户信息保存到本地数据库
	public function useradd()
	{
		$data = input('post.');
		$openId = input('post.openId');
		$result = Db::name('user')->where(['openId'=>$openId])->select();
		if(!$result){
			$res = Db::name('user')->insert($data);
		}else{
			$res = '';
		}

		echo $res;
		exit;
	}
    //主页所有视频图片接口
	public function index()
	{
		$list = Db::name('list')->select();
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$list]);
		exit;
		
	}
	//首页轮播图页面接口
	public function lunbo()
	{
		$list = Db::name('list')->limit(0,3)->select();
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$list]);
		exit;
	}

	//获取单个视频详情
	public function single() 
	{

	    $id  = input('post.id');
	   	$likeCount = Db::name('like')->where(['lid'=>$id])->count();
	   	$commentNum = Db::name('comment')->where(['lid'=>$id])->count();
	   	$collectionNum = Db::name('up')->where('lid',$id)->count();
		$list = Db::name('list')->where(['id'=>$id])->select();
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$list,'likeCount'=>$likeCount,'commentNum'=>$commentNum,'collectionNum'=>$collectionNum]);
		exit;

	}
	
   //通过用户的openId查看对单个视频的喜欢情况
	public function like()
	{
		$openId = input('post.openId');
		$lid = input('post.lid');
		$res = Db::name('like')->where(['uid'=>$openId,'lid'=>$lid])->select();
		if($res){
			echo json_encode(['error'=>0,'message'=>'喜欢','status'=>1]);
		}else{
			echo json_encode(['error'=>1,'message'=>'不喜欢','status'=>0]);
		}
		exit;
	}

	//通过用户的openId查看对单个视频的点赞情况
	public function collection()
	{
		$openId = input('post.openId');
		$lid = input('post.lid');
		
		
		$res = Db::name('up')->where(['uid'=>$openId,'lid'=>$lid])->select();
		if($res){
			echo json_encode(['error'=>0,'message'=>'已收藏','status'=>1]);
		}else{
			echo json_encode(['error'=>1,'message'=>'未收藏','status'=>0]);
		}
		exit;
	}

	//修改用户的点赞状态
	public function changeCollection()
	{
		$status = input('post.status');
		$uid = input('post.uid');
		$lid = input('post.lid');

		if($status){
			$delete = Db::name('up')->where(['uid'=>$uid])->delete();
			$collectionNum = input('post.collectionNum')-1;
			echo json_encode(['error'=>1,'message'=>'未收藏','status'=>0,'collectionNum'=>$collectionNum]);
			exit;
		}else{
			$insert = Db::name('up')->insert(['uid'=>$uid,'lid'=>$lid]);
			$collectionNum = input('post.collectionNum')+1;
			echo json_encode(['error'=>0,'message'=>'已收藏','status'=>1,'collectionNum'=>$collectionNum]);
			exit;
		}


	}

	public function changeLike()
	{
		$status = input('post.status');
		$uid = input('post.uid');
		$lid = input('post.lid');
		
		if($status){
			$delete = Db::name('like')->where(['uid'=>$uid])->delete();
			$likeCount = input('post.likeCount')-1;
			echo json_encode(['error'=>1,'message'=>'不喜欢','status'=>0,'likeCount'=>$likeCount]);
			exit;
		}else{
			$insert = Db::name('like')->insert(['uid'=>$uid,'lid'=>$lid]);
			$likeCount = input('post.likeCount')+1;
			echo json_encode(['error'=>0,'message'=>'喜欢','status'=>1,'likeCount'=>$likeCount]);
			exit;
		}
	}

	//通过id获取所有的评论
	public function comment()
	{
		$lid = input('post.id');
		
		$res = Db::name('comment')
        ->alias('a')
		->join('user u','a.uid = u.openId')
		->where('a.lid',$lid)
		->select();

		for($i=0;$i<count($res);$i++){
			$res[$i]['img'] = explode(',',$res[$i]['img']);
		}
		
		
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$res]);
		exit;
	}

	//将评论添加到数据库
	public function addComment(Comment $comment)
	{
		
		$res = $comment->save(input('post.'));
		echo $res;
		exit;
	}
	

	//实现频道搜索的功能
	public function search()
	{
		$key = input('post.keyword');
		$res = Db::name('list')->where('title',$key)->field('id')->select();
		echo json_encode(['error'=>0,'message'=>'获取信息成功','data'=>$res]);
		exit;
	}
  	


  	public function upload()
  	{
  		  
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/images');
        if($info){
            echo $info->getSaveName();
            die();
        }else{
            echo $file->getError();
            die();
        }
    

  	}


  	public function app()
  	{
  		$appid = input('post.appid');

  		$secret = input('post.secret');
  		$js_code = input('post.js_code');

  		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret='.$secret. '&js_code='.$js_code.'&grant_type=authorization_code';
  		echo $url;
  		
  		// $res = $this->redirect($url);
  		echo json_encode($res);
  	}
 }

