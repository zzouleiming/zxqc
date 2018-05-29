
<?php
/**
 * BAIDU ueditor
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');        
class Ueditor extends CI_Controller
{
        function __construct()
        {
                parent::__construct();
                $this->load->helper('url');
        }

        function index()
        {
                $this->load->view('ueditor');
        }       }
?>
