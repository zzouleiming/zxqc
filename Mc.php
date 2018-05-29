<?php 
class Mc extends CI_Controller {

    public function hashCode( $str)
    {
        if(empty($str)) return '';
        $mdv = md5($str);
        $mdv1 = substr($mdv,0,16);
        $mdv2 = substr($mdv,16,16);
        $crc1 = abs(crc32($mdv1));
        $crc2 = abs(crc32($mdv2));
        return bcmul($crc1,$crc2);
    }


    public function test()
    {
        echo'<pre>';
      $rs=$_SERVER['REQUEST_METHOD'];
        print_r($rs);
    }
}