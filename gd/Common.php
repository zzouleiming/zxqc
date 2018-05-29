<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends CI_Controller
{
    public function __construct(){
        parent::__construct();

        $this->load->helper('url');

    }

    public function image_resize(){
        $imgUrl = $this->input->get('imgUrl', true);
        $imgPath = $this->_get_image_real_path($imgUrl);
        $imgRs = $this->_image_resize($imgPath, 2);
        $this->_image_paint($imgRs, $imgPath);
    }

    private function _image_watermark($imgPath, $text, $left, $top, $color=null, $font=null){
    
    }

    private function _image_merge($imgPath1, $imgPath2, $left, $top){  
        $imgRs1 = imagecreatefromstring(file_get_contents($imgPath1));
        $imgRs2 = imagecreatefromstring(file_get_contents($imgPath2));
        list($imgWidth1, $imgHight1, $imgType1) = getimagesize($imgPath1);
        imagecopymerge($imgRs1, $imgRs2, $left, $top, 0, 0, $imgWidth1, $imgHight1, 100);
        imagedestroy($imgRs2);
        return $imgRs1;
    }

    private function _image_resize($imgPath, $size=1){
        $imgRs = imagecreatefromstring(file_get_contents($imgPath));
        list($bgWidth, $bgHight, $bgType) = getimagesize($imgPath);
        $imgRs2 = imagecreatetruecolor($bgWidth/$size, $bgHight/$size);
        imagecopyresized($imgRs2, $imgRs, 0, 0, 0, 0, floor($bgWidth/$size), floor($bgHight/$size), $bgWidth, $bgHight);
        imagedestroy($imgRs);
        return $imgRs2;
    }

    /**
    * Strong Blur
    *
    * @param  $gdImageResource  图片资源
    * @param  $blurFactor          可选择的模糊程度 
    *  可选择的模糊程度  0使用   3默认   超过5时 极其模糊
    * @return GD image 图片资源类型
    */
    private function _gaussian_image_blur($imgPath, $blurFactor=3){
        // blurFactor has to be an integer
        $gdImageResource = imagecreatefromstring(file_get_contents($imgPath));
        $blurFactor = round($blurFactor);

        $originalWidth = imagesx($gdImageResource);
        $originalHeight = imagesy($gdImageResource);

        $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
        $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

        // for the first run, the previous image is the original input
        $prevImage = $gdImageResource;
        $prevWidth = $originalWidth;
        $prevHeight = $originalHeight;

        // scale way down and gradually scale back up, blurring all the way
        for($i = 0; $i < $blurFactor; $i += 1){    
            // determine dimensions of next image
            $nextWidth = $smallestWidth * pow(2, $i);
            $nextHeight = $smallestHeight * pow(2, $i);

            // resize previous image to next size
            $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
            imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0, 
              $nextWidth, $nextHeight, $prevWidth, $prevHeight);

            // apply blur filter
            imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

            // now the new image becomes the previous image for the next step
            $prevImage = $nextImage;
            $prevWidth = $nextWidth;
            $prevHeight = $nextHeight;
        }

        // scale back to original size and blur one more time
        imagecopyresized($gdImageResource, $nextImage, 
        0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
        imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);

        // clean up
        imagedestroy($prevImage);

        // return result
        return $gdImageResource;
    }

    private function _image_paint($imgRs, $imgPath){
        list($bgWidth, $bgHight, $bgType) = getimagesize($imgPath);
        switch($bgType) {
            case 1: //gif
                header('Content-Type:image/gif');
                imagegif($imgRs);
            break;
            case 2: //jpg
                header('Content-Type:image/jpg');
                imagejpeg($imgRs);
            break;
            case 3: //jpg
                header('Content-Type:image/png');
                imagepng($imgRs);
            break;
            default:
            break;
        }
        imagedestroy($imgRs);
    }

    private function _get_image_real_path($imgUrl){
        $imgUrl = str_replace(array('http://'.$_SERVER['HTTP_HOST'], 'https://'.$_SERVER['HTTP_HOST']), array('', ''), $imgUrl);
        return dirname(APPPATH).$imgUrl;
    }
}