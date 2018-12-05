<?php

/**
 * 上传处理类
 * @version 1.0
 */
namespace libraries\util;
class fileUpload
{

    /*
     * @str 保存目录
     */
    private $path = '';

    /*
     * @str 文件网络目录
     */
    private $imgUrl = '';

    /*
     * @str 操作类型
     */
    private $type = '';

    /*
     * @str 文件流键值
     */
    private $fileKey = '';

    /*
     * @str 文件流
     */
    private $fileStreams = '';

    /*
     * @str 上传人ID
     */
    private $id = '';

    /*
     * @str 文件后缀
     */
    private $suffix = '';

    /*
     * @str 原文件名
     */
    private $fileName = '';

    /*
     * @str 盒子宽
     */
    private $boxW = '428';

    /*
     * @str 盒子高
     */
    private $boxH = '293';

	/**
	 * 初始化
     * @str   $type 上传类型
	 * @ str $type 上传文件类型
	 */
	function __construct($type,$id='')
	{
        global $user;
        $this->id = empty($id) ? $user['userId'] : $id;
        $this->path = CTR_UPLOAD_PATH.$type.'/'. $this->id;
        $this->imgUrl = CTR_UPLOAD_URI.$type.'/'. $this->id;
        $this->type = $type;
        $this->fileKey = $this->type.'Upload';

	}

    /*
     * @arr $tempFile   文件信息
     * @str $thumb  是否缩略
     * */
    public function startUpload($info,$tempFile,$thumb=true){
		if(!empty($info['boxH'])){
		    $this->boxH = $info['boxH'];
		}
		if(!empty($info['boxW'])){
		    $this->boxW = $info['boxW'];
		}
		
        $fileFunction = $this->type.'FileUpload';
        $result = $this->isFileSuffix($tempFile); //判断后缀
        if($result != true){
            return $result;
        }
        $this->fileStreams = $tempFile [$this->fileKey] ['tmp_name'];   //文件流

        $this->isDirPath($this->path);  //检查文件夹是否存在
        return $this->$fileFunction($thumb);
    }



    /*
     * 头像上传
     * @str $thumb   是否缩略
     * */
    private function avatarFileUpload($thumb){
        /*文件新名称*/
        $TempFileName = time(). rand(1000,9999) . "." . $this->suffix;
        /*原图路径文件*/
        $filPath = $this->path . "/" . $TempFileName;

        if ( move_uploaded_file ( $this->fileStreams, $filPath ) ) {
            if($thumb){ /*生成缩略图*/
                /*设置前缀*/
                $prefix = 'thumb_';
                //缩略图路径文件
                $thumb_path = $this->path . "/" .$prefix.$TempFileName;

                $result = $this->img2thumb($filPath,$thumb_path,$this->boxW,$this->boxH);
                if(!$result){
                    return array( "status"=>'n', "info"=>$this->fileName.'文件缩略图生成失败' );
                }
                $img_url = $this->imgUrl.'/'.$prefix.$TempFileName;
            }else{

                $img_url = $this->imgUrl.'/'.$TempFileName;

            }
            return array( "status"=>'y',"img_url"=>$img_url,"filename"=>$TempFileName );

        }else{
            return array( "status"=>'n', "info"=>$this->fileName.'文件上传失败' );
        }


    }



    /*
     * 产品上传
     * @str $tempFile   上传文件流
     * @str $id
     * @str $path   保存路径(物理)
     * */
    private function goodsFileUpload($tempFile,$id,$path){
        /*根据需求再开发*/
    }



    /*
     * 一般文件上传
     * @str $tempFile   上传文件流
     * @str $path   保存路径(物理)
     * */
    public function plainFileUpload($data,$path=''){
        
        $fileFunction = $this->type.'FileUpload';
        $key=$this->type.'Upload';
        $_FILES[$key]['name'] = $data['name'];
        $result = $this->isFileSuffix(); //判断后缀
        
        if($result != true){
            return $result;
        }
        $this->fileStreams = $data['image'];   //文件流

        $this->isDirPath($this->path);  //检查文件夹是否存在

        $base64 = htmlspecialchars($data['image']);

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
            /*文件新名称*/
            $TempFileName = time(). rand(1000,9999) . "." . $this->suffix;
            /*原图路径文件*/
            $filPath = $this->path.'/thumb_'. $TempFileName;
            $imgUrl =  $this->imgUrl.'/thumb_'. $TempFileName;
            if (file_put_contents($filPath, base64_decode(str_replace($result[1], '', $base64)))) {

                return  array('status'=>'y','info'=>'上传成功','fileName'=>$TempFileName,'imgUrl'=>$imgUrl);
                
            }

        }
        return array('status'=>'n','info'=>'上传失败');

    }

    /**
     * 图片旋转
     * @param string $srcPath    源图绝对完整地址{带文件名及后缀名}
     * @param string  $rota_path   目标图绝对完整地址
     * @param string  $artwork   原图文件名
     * @param string  $rotate   旋转角度
     * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
     * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
     * @param int        是否裁切{宽,高必须非0}
     * @param int/  截取{$x1  X始,$y1 Y始,$x2 X止,$y2 Y止}
     * @return boolean
     */
    public function imageRota($srcPath,$rota_path,$artwork,$rotate,$boxW, $boxH){
        $file = getimagesize($srcPath);
        
        switch($file['mime']){
            case 'image/jpg':
            case 'image/jpeg':
                $funName="jpeg";
                break;
            case 'image/gif':
                $funName='gif';
                break;
            case 'image/png':
                $funName="png";
                break;
            default :
                return false;
        }
        
        
        //创建源图片资源
        $trueFunName="imagecreatefrom".$funName;
        $src=$trueFunName($srcPath);
       
        //$rotate = 90;
        //执行旋转
        $handle = imagerotate($src, $rotate, 0);
        if($rotate<=360){
            if($rotate%360 == 0){
                $direction = '';
            }else if($rotate%270 == 0){
                $direction = 'Left';
            }elseif ($rotate%180 == 0){
                $direction = 'Down';
            }elseif ($rotate%90 == 0){
                $direction = 'Right';
            }    
        }else {
            switch ($rotate%360){
                case 90:
                    $direction = 'Right';
                    break;
                case 180:
                    $direction = 'Down';
                    break;
                case 270:
                    $direction = 'Left';
                    break;
                case 0:
                    $direction = '';
                    break;
            }
        }
        
        
        //保存旋转后图片
        $rotaFile_name = 'thumb'.$direction.'_'.$artwork;
        $rotaFile_path = $rota_path.$rotaFile_name;
        
        $result = imagejpeg($handle,$rotaFile_path);
        
        /* //缩略路径
        $thumb_path = $rota_path.'rotaThumb_'.$artwork;
        
        $this->boxW = $boxW;
        $this->boxH = $boxH; */
        //缩略
        
        //$result = $this->img2thumb($rotaFile_path,$thumb_path,$boxW, $boxH);
        if($result){
            $rotaFile = getimagesize($rotaFile_path);
            $img_url = $this->imgUrl.'/'.$rotaFile_name;
            return array('info'=>'旋转成功','status'=>'y','fileName'=>$rotaFile_name,'rotaUrl'=>$img_url,'boxW'=>$rotaFile['0'],'boxH'=>$rotaFile['1']);
        }else {
            return array('info'=>'旋转失败','status'=>'n');
        }
        exit;
    }
    
    
    /**
     * 生成缩略图或截取
     * @param string $srcPath    源图绝对完整地址{带文件名及后缀名}
     * @param string  $thumb_path   目标图绝对完整地址{带文件名及后缀名}
     * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
     * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
     * @param int        是否裁切{宽,高必须非0}
     * @param int/  截取{$x1  X始,$y1 Y始,$x2 X止,$y2 Y止}
     * @return boolean
     */
    public function img2thumb($srcPath,$thumb_path,$boxW, $boxH,$x=0,$y=0)
    {
        $file = getimagesize($srcPath);
        $imgW = $file[0];
        $imgH = $file[1];

            /*缩略图一定比原图小*/
            if($imgW<$boxW && $imgH<$boxH){
                $toW = $imgW;
                $toH = $imgH;
            }
            else{

                if($imgW<$imgH){
                    $toW = $imgW*$boxH/$imgH;
                    $toH = $boxH;
                }else if($imgW>$imgH){
                    $toW = $boxW;
                    $toH = $imgH*$boxW/$imgW;
                }else{
                    $toW = $imgW;
                    $toH = $imgH;
                }
            }

        $toW = $toW>$boxW ? $boxW : $toW;
        $toH = $toH>$boxH ? $boxH : $toH;
            
        switch($file['mime']){
            case 'image/jpg':
            case 'image/jpeg':
                $funName="jpeg";
                break;
            case 'image/gif':
                $funName='gif';
                break;
            case 'image/png':
                $funName="png";
                break;
            default :
                return false;
        }
        

        //创建源图片资源
        $trueFunName="imagecreatefrom".$funName;
        $src=$trueFunName($srcPath);
        imagesavealpha($src,true);//这里很重要 意思是不要丢了$sourePic图像的透明色;

        /*创建画布*/
        if($boxW != $this->boxW && $boxH != $this->boxH){   //截取
            $toW = $boxW;
            $toH = $boxH;
            $imgW = $boxW;
            $imgH = $boxH;
            /*文件新名称路径*/
            $thumb_path = $thumb_path . "." . $funName;
            $arr = explode('_',basename($thumb_path));

        }
        
        $thumb = imagecreatetruecolor($toW,$toH);
        imagealphablending($thumb,false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
        imagesavealpha($thumb,true);//这里很重要,意思是不要丢了$thumb图像的透明色;
        imagecopyresampled($thumb, $src, 0, 0, $x, $y, $toW, $toH, $imgW, $imgH); //复制图像并改变大小

        $saveFunName="image".$funName;
        $result=$saveFunName($thumb,$thumb_path);//保存缩略图

        if(!$result){
            return false;
        }else{
            return array('dirPath'=>$thumb_path,'fileName'=>$arr[1]);
        }
        imagedestroy($thumb);

    }

    /*
     * 判断路径
     * @str $path 路径(物理)
     * */
	private function isDirPath($path){
        if ( !file_exists( $path ) ){ mkdir( $path,0777,true ); }
    }

    /*
     * 获取文件
     * @str $fileName 文件名
     * */
    public function getUploadFiles($fileName,$prefix='',$id=''){

        if(!empty($id)){

            $this->path = CTR_UPLOAD_PATH.$this->type.'/'. $id;
            $this->imgUrl = CTR_UPLOAD_URI.$this->type.'/'. $id;
        }

        $dirpath = $this->path.'/'.$prefix.$fileName;
        $path = $this->imgUrl.'/'.$prefix.$fileName;
        
		//print_r($dirpath);exit;
       
        if(is_file($dirpath) ){

            return array('urlPath'=>$path,'dirPath'=>$dirpath,'path'=>$this->path.'/');
        }else{
            return false;
        }
    }

    /*
     * 判断后缀
     * @str $tempFile 上传文件信息
     * @str $type 需对比的类型
     * */
    private function isFileSuffix($tempFile=''){
        $fileTypes = array ( 'jpg', 'jpeg', 'gif', 'png' );
        // 得到文件原名
        $key = $this->type.'Upload';
        $fileName = iconv ( "UTF-8", "GB2312", $_FILES ["$key"] ["name"] );
        //获取后缀
        $suffix = pathinfo( $fileName, PATHINFO_EXTENSION );
        if(!in_array($suffix,$fileTypes)){
            $info = '文件类型只支持'.implode(',',$fileTypes);
            return array('status'=>'n','info'=>$info);
        }else{
            $this->suffix = $suffix;
            $this->fileName = $fileName;
            return true;
        }
    }
}