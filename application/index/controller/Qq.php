<?php
namespace app\index\controller;
// use think\Controller;
use think\Qqlogin;

class Qq extends Base
{
    
    public function index(Qqlogin  $qqlogin) {
   	$url = $qqlogin->getcode();
   		//echo $url;
		$this->redirect($url);


   	}


   	public function qqlogin (Qqlogin  $qqlogin) {
				


   		$result = $qqlogin->getUserInfo($_GET['code']);
      session('username' , $result['nickname']);
   		
		  session('picture' , $result['figureurl_qq_2']);
			
		echo '<script>window.location.href="http://www.gtt2352.xyz"</script>';
			
   		}




}