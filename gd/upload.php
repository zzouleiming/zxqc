<?php

//$fileName = "file";
//$upload_list = _aFiles($fileName);
//print_r($upload_list );
	
function _aFiles($fileName) {
	$file_post = $_FILES[$fileName];
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
		$upload[] = _uploader($file_ary[$i]);
    }
	if(count($upload)>0){
		return json_encode(array('errcode'=>200,'msg'=>'上传成功','data'=>$upload));
	}else{
		return json_encode(array('errcode'=>400,'msg'=>'上传失败','data'=>''));
	}
}
	
function _uploader($files) {
		$file = array();
        $file['info'] = ""; //和该文件上传相关的信息
        $file['success'] = false;            			//这个用于标志该图片是否上传成功
		$file['name'] = $files['name'];
		$file['size'] = $files['size'];					//已上传文件的大小，单位为字节
        $files['type'] = $files['type'];   //文件的 MIME 类型
        $file['path'] =  '/uploads/';                	//存图片路径

        //：0:表示没有发生错误
        if($files['error']==0){
            //is_uploaded_file — 判断文件是否是通过 HTTP POST 上传的
            if(is_uploaded_file($files['tmp_name'])) {
                    //扩展名
                 $extension = '';
                 //strcmp — 二进制安全字符串比较 （区分大小写）
               // 如果 str1 小于 str2 返回 < 0； 如果 str1 大于 str2 返回 > 0；如果两者相等，返回 0。
                 if(strcmp($files['type'], 'image/jpeg') == 0) {
                     $extension = '.jpg';
                  }else if(strcmp($files['type'], 'image/png') == 0) {
                     $extension = '.png';
                  }else if(strcmp($files['type'], 'image/gif') == 0) {
                     $extension = '.gif';
                 }else{
                     //如果type不是以上三者，我们就从图片原名称里面去截取判断去取得(处于严谨性)
                     //strrchr — 查找指定字符在字符串中的最后一次出现
                     $substr = strrchr($files['name'], '.');
                     if(FALSE != $substr) {
                         $file['info'] = "其他文件类型";
                     }
                      //strcasecmp — 二进制安全比较字符串（不区分大小写），比较字符串是否相同
                     //如果 str1 小于 str2 返回 < 0； 如果 str1 大于 str2 返回 > 0；如果两者相等，返回 0。
                     //取得原名字的扩展名后，再通过扩展名去给type赋上对应的值
                     if(strcasecmp($substr, '.jpg') == 0 || strcasecmp($substr, '.jpeg') == 0 || strcasecmp($substr, '.jfif') == 0 || strcasecmp($substr, '.jpe') == 0 ) {
                         $files['type'] = 'image/jpeg';
                     }else if(strcasecmp($substr, '.png') == 0) {
                         $files['type'] = 'image/png';
                     } else if(strcasecmp($substr, '.gif') == 0) {
                         $files['type'] = 'image/gif';
                     }else {
                         $file['info'] = "其他文件类型";
                     }
                     $extension = $substr;//赋值扩展名

                 }
				 
                 //对临时文件名加密，用于后面生成复杂的新文件名
                 $md5 = md5_file($files['tmp_name']);
                 if(trim($file['info'])==""){
                    //取得图片的大小
                    $imageInfo = getimagesize($files['tmp_name']);
                    $rawImageWidth = $imageInfo[0];
                    $rawImageHeight = $imageInfo[1];
					$name = $md5."_{$rawImageWidth}x{$rawImageHeight}{$extension}";
                 }else{
					$name = $md5."{$extension}";
				 }
				 //获取相对路径
				 $path = 'uploads/' . @date("Ymd"). '/';
                    ///确保目录可写
                    if(ensure_writable_dir($path)){
                        
                        //加入图片文件没变化到，也就是存在，就不必重复上传了，不存在则上传
                        $ret = file_exists($path . $name) ? true : move_uploaded_file($files['tmp_name'], $path . $name);
                        if ($ret === false) {
                            $file['info'] = "已存在相同的文件";
                        } else {
                            $file['path'] = $file['path'] . @date("Ymd"). '/'. $name;        //存图片路径
                            $file['success'] = true;            //图片上传成功标志
                            $file['width'] = $rawImageWidth?$rawImageWidth:'';    //图片宽度
                            $file['height'] = $rawImageHeight?$rawImageHeight:'';     //图片高度
							$file['ext'] = str_replace('.','',$extension);
                            $file['info'] = "上传成功";//写入成功
                        }
                    }else{
                        $file['info'] = "目录不可写";//目录不可写
                    }

            }else{
                $file['info'] = "上传失败";//上传失败
            }
        }
        return $file;
    }


    /**
     * 判断是否可写
     * @param $dir
     * @return bool
     */
function ensure_writable_dir($dir) {
        if(!file_exists($dir)) {
            mkdir($dir, 0766, true);
            chmod($dir, 0766);
            chmod($dir, 0777);
        }else if(!is_writable($dir)) {
            chmod($dir, 0766);
            chmod($dir, 0777);
            if(!is_writable($dir)) {
               return false;
            }
        }
        return true;
    }

