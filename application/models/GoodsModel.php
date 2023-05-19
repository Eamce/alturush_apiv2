<?php
defined('BASEPATH') or exit('No direct script access allowed');

include 'vendor/autoload.php';

class GoodsModel extends CI_Model
{

	private $profileImage 	= 'https://app1.alturush.com/';
	private $buImage 	  	= 'https://apanel.alturush.com/';
	private $productImage 	= 'https://storetenant.alturush.com/storage/';
	private $gcproductImage = 'https://admins.alturush.com/ITEM-IMAGES/';
	private $cssadmin 		= 'https://customerservice.alturush.com/';


    private function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

}
