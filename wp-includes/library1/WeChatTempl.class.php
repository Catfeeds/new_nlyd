<?php
/**
 * 
 * 微信模版消息
 */

class WeChatTempl{

  private $appId = 'wx0bd09c8eb544aa6e';
  private $appSecret = 'b120c7e485a08ac4fec5fd6b0eebaa12';

  public $access_token;



  public function __construct(){

    //获取access_token
    /*$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
    $res = request_get($url);
    $this->access_token = $res['access_token'];*/
    $share = new \Think\WeChatShare(); 
    $this->access_token = $share->getAccessToken();
  }


  /**
  * 设置所属行业
  * @param $industry_id1 int  公众号模板消息所属行业编号
  * @param $industry_id2 int  公众号模板消息所属行业编号
  * http请求方式: POST
  */
  public function set_industry($industry_id1,$industry_id2 = ''){


      $url = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token='.$this->access_token;
      
      $map['industry_id1'] = $industry_id1;
      if( !empty($industry_id2) ) $map['industry_id2'] = $industry_id2;
      
      $data = request_post($url,json_encode($map));

      return $data;

  }

  /**
  * 获取所属行业
  * http请求方式: GET
  */
  public function get_industry(){

      $url = 'https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token='.$this->access_token;
      
      $data = request_get($url);

      return $data;

  }

  /**
  * 获得模板ID
  * @param $template_id_short str  模板库中模板的编号
  * http请求方式: POST
  */
  public function get_template_id($template_id_short){


      $url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$this->access_token;
      
      //$map['access_token'] = $this->access_token;
      $map['template_id_short'] = $template_id_short;
      
      $data = request_post($url,json_encode($map));

      return $data;

  }

  /**
  * 获取模板列表
  * 获取已添加至帐号下所有模板列表
  * http请求方式: GET
  */
  public function get_templates(){

      $url = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$this->access_token;
      
      $data = request_get($url);

      return $data;

  }

  /**
  * 删除模版
  * @param $template_id str  公众帐号下模板消息ID
  * http请求方式: POST
  */
  public function delete_template($template_id){

      $url = 'https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token='.$this->access_token;
      
      $map['template_id'] = $template_id;

      $data = request_post($url,json_encode($map));

      return $data;

  }

  /**
  * 发送模板消息
  * @param $touser          str  公接收者openid
  * @param $template        str  模板ID
  * @param $data            str  模板数据
  * @param $jump_url        str  模板跳转链接
  * @param $miniprogram     str  跳小程序所需数据，不需跳小程序可不用传该数据 $miniprogram['appid']='',$miniprogram['pagepath']=''
  * @param $appid           str  所需跳转到的小程序appid
  * @param $pagepath        str  所需跳转到小程序的具体页面路径，支持带参数,
  * http请求方式: POST
  */
  public function send_message($touser,$template,$data,$jump_url,$miniprogram=''){

    if(is_numeric($template)){

      switch ($template) {
        case 1:
          $template_id = 'vFqu4F-QKgDmY6zpp45AOvrtKstmyDODrvle51_gYRw';
          $template_title = '成为会员通知';
          break;
        case 2:
          $template_id = 'zhTkRP4p0v-HS_NqZ5RouuizyLF7Opnt5LhgGzx4W0s';
          $template_title = '购买成功通知';
          break;
        default:
          # code...
          break;
      }
    }elseif (strlen($template) > 3 && strlen($template) < 13) {
      
      $arr = $this->get_template_id($template);
      $template_id = $arr['template_id'];
    }else{
      $template_id = $template;
    }


    $map = array (
          'touser' => $touser,
          'template_id' => $template_id,
          'url' => $jump_url,
          'topcolor' => '#FF0000',
          'data' => $data
        );

      //var_dump($map);die;
      $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->access_token;
      
      
      $data = request_post($url,json_encode($map));

      return $data;

  }

  
}

