<?php
defined('BASEPATH') or exit('No direct script access allowed');

define('SECRET_KEY', 'SoAxVBnw8PYHzHHTFBQdG0MFCLNdmGFf');
define('SECRET_IV', 'T1g994xo2UAqG81M');
define('ENCRYPT_METHOD', 'AES-256-CBC');

class GoodsController extends CI_Controller
{

    public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Manila');
		$this->load->model('GoodsModel');
	}

	public function decrypt($string)
	{
		return openssl_decrypt($string, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
	}

}