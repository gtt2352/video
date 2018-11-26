<?php
namespace app\index\controller;
// use think\Controller;
use pay\aop\AopClient;
use pay\aop\request\AlipayTradePagePayRequest;
use app\index\model\Order;
use app\index\model\Name;
use think\Cookie;
use think\Db;
use think\helper\Time;
class Order extends  Base
{
  protected $is_check_login = ['*'];
   public function payOrder(Order $order)
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
      $list = Db::name('order')->order('id desc')->find();
      $gId = $list['gid'];
      $username = $list['username'];
      $list = Db::name('show')->where('gId',$gId)->field('gName,gPrice')->select();
      // var_dump($list);
      $name = $list[0]['gName'];
      $price = $list[0]['gPrice'];
      
        require_once("../extend/pay/AopSdk.php");
        //构造参数  
        $aop = new AopClient ();  
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';  
        $aop->appId = '2016091900547488';  
        $aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEA4edca3PrjQeag34GKxEmurjlAxUaoOoo3stLGrX8udKbSv2pN21eC7VACMaQuzLA36z1NE5usLus4Hu7kbZWwTKoBkSaxTnH1oN5uf3Bn7ZpVjxm6tOn6K1UxYC4S1WggWTJwyfOV28+0wwW2NxDWnxDanQvf5P38omzTb6vI7Fc+p7EcfSM/dktdU9QyBjtyf+m1dZ5QkPcXc4LsS6y0KndvMAfIj7Gw0/PC6pgqU2RKE2U41zWAdvoostm0N8dKEGktwVQf+PbjZJf1wu69WWhDpF0lap1Cty8BvaHr1a9kOXILblqH4yXv/005vCSWefYmYfWGlIROc8nSljBpwIDAQABAoIBAELbelrC3//JNE2eSq0MUm4Isp6K7GqbYqUtfXjWS4mjbxpuoForIB5O8jimq8b7amHIinmEjPX5VVzKweSKFnrvRxMz0qp0Q1SVbL1DAL8H+XzwO3VcVuFiCyi4w9Jn8Szaa2w6p7Lw1aMYfNqJtdVd2YX0BOq5HDTH1C06NhNVqEojCOghSHWHDz7eRaTkUudA7RO3+4zcHlkVIul3aBI99Wo0aSaMBILtCnBgiKxxn/9EdmGOcBqYfEkgPG2tlS5R6Zlh7iqhHSfYvQbKJXomGndS+X8IVOVvTmKtmCNB/fKMMxZPN72cFDWojIWObNkRj8Mjy/Zq/v/btz1LxcECgYEA98tdp7cicXGCKOItAW662l9ugTBU0aIwS6Pybi7PRo/Ui5oLRzmygYrZRWmnL2we0AQDbrHZGpzPrnIrgqmqMVwL9FYVeX+0pdRGC+82G/5+Hk/OxlRUHPqRcvPjUt4EUqToKb8qvTJkAxDbJrKzQFfjWM5s421PkJl32iaOVq8CgYEA6WJryGAXUDMtMctJh4iKKW/mxnMgasuH1DvzNuU53aJD0vOkfFLpyByesjaJYb5/oTB6pT4spvMcFJQ9ns8Q+7Cpu2wHp/Qan3VTp5dH+FdrA3BlAzpKn+S8bRtdL9f/rEYwHxyJhwJU2e1rn0FYsFuu9hiqgfu9bb22L0wxAokCgYEAwbBB7aXVk20RO5nHahY+khEEVJENy6sGKynkhsGqHVKx5cksRalQ4bdBq+Mw3n2Z/CkW8cCsngOyxn3RitnKAbkcUwmy0XzEHNc3RX7fAbTEqjbhvt8NvHUTmhIPP6fxIxRpsLAP3UF1MXxMgQwv1vyb5b+9F2xLRBDhwTBgFFsCgYAaS5SJRwpZZuq4qYbnVmL6uDjwMxh77+Kl7vtAPCOA7T9nDTyDJXRhr5fRzIM+GBCZQLUQ2Sh+xvVq/CjVM89eDFGTZGCYc9BsFvQN9eWN/Yt5H7jB3Fd8HHF+eY7OTS/pYR8a4kQ8ie61fyKZFuu/MxVLdQwBCTQWoUFdouACwQKBgQCbApZycqFR/WQfvclcZH/S1n+dGD80HIYDZjpB4ExvvJ8qknrSNJP9zdqDoovCBYRJ6/qp+3U6FY86ulvcPDVfxnNwaQ4fhzWDOFTGmYB8q6JFTG5FM2uDCmiWRroiWMahZqV5d/UFBpRAR3ke6GiTdmNsQxTA8y4FzvGD231kZA==';  
        $aop->apiVersion = '1.0';  
        $aop->signType = 'RSA2';  
        $aop->postCharset= 'utf-8';  
        $aop->format='json';  
        $request = new AlipayTradePagePayRequest ();  
        $request->setReturnUrl('http://www.gtt2352.xyz/index/order/payafter');  
        $request->setNotifyUrl('http://www.gtt2352.xyz/index/order/payafter');  

        $request->setBizContent('{

          "product_code":"FAST_INSTANT_TRADE_PAY",
          "out_trade_no":'.$orderid.',
          "subject":"服装",
          "total_amount":'.$price.',
          "body":"'.$name.'"

        }');

        //请求  
        $result = $aop->pageExecute ($request);

        //输出  
         echo $result;
   }

   public function payafter(Order $order)
   {

          $list = Db::name('order')->order('id desc')->find();
          // var_dump($list);
          $id = $list['id'];
          $list = Db::name('order')->where('id',$id)->update(['status'=>1]);
         $this->redirect("http://www.gtt2352.xyz/");       
        


   }

   


}