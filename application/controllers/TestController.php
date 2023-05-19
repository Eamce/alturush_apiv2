<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('SECRET_KEY', 'SoAxVBnw8PYHzHHTFBQdG0MFCLNdmGFf');
define('SECRET_IV', 'T1g994xo2UAqG81M');
define('ENCRYPT_METHOD', 'AES-256-CBC');

class TestController extends CI_Controller
{

    public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Manila');
		//$this->load->model('GoodsModel');
	}

	public function decrypt($string)
	{
		return openssl_decrypt($string, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
	}


    public function decryptPass(){
      //  $pass = 	$this->decrypt('21503be3eb3547800efa849fa6fb1a59');
        $pass = 	$this->decrypt('+DFriuyEQDIGbLhzD6FAHQ==');
      //  $pass = 	$this->decrypt('$2y$10$DJ56a/KmZYdeNH2enEjVXu86v7TzbLbamwm//pUu8Q4U3IftpbwTy');
        echo json_encode($pass);
    }

    public function encrypt_txt($str)
	{
		// $this->load->library('encryption');
		// $msg = 'Jenotxc@1893';

		// $encrypted_string = $this->encrypt->encode($msg);

		// return $encrypted_string;
	}
}