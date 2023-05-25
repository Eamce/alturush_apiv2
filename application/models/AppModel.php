<?php
defined('BASEPATH') or exit('No direct script access allowed');

include 'vendor/autoload.php';

class AppModel extends CI_Model
{

	// Private $tenatImage   = 'http://172.16.43.112:7000/';
	// Private $buImage 	  = 'http://172.16.43.239:7000/';
	// Private $productImage =	'http://172.16.43.134:8000/storage/';
	// Private $gcproductImage = 'http://172.16.161.41:8001/ITEM-IMAGES/';


	// Private $tenatImage   = 'https://apanel.alturush.com/';
	// private $profileImage 	= 'http://172.16.43.147/rapida/';
	private $profileImage 	= 'https://app1.alturush.com/';
	private $buImage 	  	= 'https://apanel.alturush.com/';
	private $productImage 	= 'https://storetenant.alturush.com/storage/';
	private $gcproductImage = 'https://admins.alturush.com/ITEM-IMAGES/';
	private $cssadmin 		= 'https://customerservice.alturush.com/';

	private function hash_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public function appCreateAccountMod($firstName, $lastName, $email, $birthday, $contactNumber, $username, $password)
	{
		$data = array(
			'firstname' => $firstName,
			'lastname' =>  $lastName,
			'birthdate' => $birthday,
			'status' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('toms_customer_details', $data);
		$insert_id	=  $this->db->insert_id();

		// $cus_add = array(

		// 	'customer_id' => $insert_id,
		// 	'firstname' => $firstName,
		// 	'lastname' => $lastName,
		// 	'mobile_number' => $contactNumber,
		// 	'barangay_id' => $barrioId,
		// 	'shipping' => '1',
		// 	'address_type' => '1',
		// 	'created_at' => date('Y-m-d H:i:s'),
		// 	'updated_at' => date('Y-m-d H:i:s')
		// );
		// $this->db->insert('customer_addresses', $cus_add);

		$cust_num = array(
			'customer_id' => $insert_id,
			'mobile_number' => '+63' . $contactNumber,
			'in_use' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_numbers', $cust_num);

		$data1 = array(
			'customer_id' => $insert_id,
			'firstname' => $firstName,
			'lastname' => $lastName,
			'email'   => $email,
			'username' => $username,
			'password' => $this->hash_password($password),
			'password2' => md5($password),
			'user_from' => '2',
			'mobile_number' => '0' . $contactNumber,
			'status' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('app_users', $data1);
	}

	public function signInUserMod($usr, $password)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where("(appsu.username = '$usr' OR appsu.email = '$usr')");
		$query = $this->db->get();
		$res1 = $query->row_array();

		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.password2', md5($password));
		$query = $this->db->get();
		$res2 = $query->row_array();

		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where("(appsu.username = '$usr' OR appsu.email = '$usr')");
		$this->db->where('appsu.password2', md5($password));
		$query = $this->db->get();
		$res3 = $query->row_array();


		if (empty($res1['username'])) {
			echo "wrongusername";
		} else if ($res1['status'] == '0') {
			echo "accountblocked";
		} else if (empty($res2['password2'])) {
			echo "wrongpass";
		} else  if ($res1['active_status'] == '0' && $res2['active_status'] == '0') {
			echo "unverified";
		} else if (empty($res3)) {
			echo "wrongpass";
			// echo "wrongusername";
		} else  if (!empty($res3)) {
			echo $res3['customer_id'];
		}

		// { if ($res1['status'] == '0') 
		// 	echo "wrongusername";

		// 	//  
		// 	// if (empty($res1['username'])) {
		// 	// 	echo "wrongpass";
		// 	// } else if (!empty($res3)) {
		// 	// 	echo $res3['customer_id'];
		// 	// } else 
		// 	// if (empty($res3)) {
		// 	// 	echo "wrongpass";
		// 	// }
		// }
	}

	public function forTrap($id, $password)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.id', $id);
		$this->db->where('appsu.password2', md5($password));
		// $this->db->limit(1);
		$query = $this->db->get();
		$ress = $query->row_array();

		return $ress;
	}



	public function getUserDataMod($id)
	{
		$this->db->select('*', 'appsu.firstname', 'appsu.lastname');
		$this->db->from('app_users as appsu');
		$this->db->join('toms_customer_details as cus_det', 'cus_det.id = appsu.customer_id', 'left');
		$this->db->join('barangays as brgy', 'brgy.brgy_id = appsu.brgy_id', 'left');
		$this->db->where('appsu.customer_id', $id);
		$query = $this->db->get();
		$res = $query->result_array();
		// echo $res;
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_customerId' => $value['customer_id'],
				'd_firstname' => $value['firstname'],
				'd_lastname' => $value['lastname'],
				'd_contact' => $value['mobile_number'],
				'd_suffix' => $value['suffix'],
				'd_userNameUs' => $value['username'],
				'd_townId' => $value['town_id'],
				'd_brgId' => $value['brgy_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gcGetAddress_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->join('barangays', 'barangays.brgy_id = customer_addresses.barangay_id');
		$this->db->where('customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'customer_id' 	=> $value['customer_id'],
				'town_id'		=> $value['town_id'],
				'shipping'		=> $value['shipping'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getPlaceOrderDataMod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname, cust_add.mobile_number');
		$this->db->from('customer_addresses as cust_add');
		$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
		$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
		$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
		$this->db->join('customer_numbers as cust_num', 'cust_num.customer_id = cust_add.customer_id', 'inner');
		$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.brgy_id = cust_add.barangay_id', 'left');
		$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
		$this->db->where('cust_add.customer_id', $cusId);
		$this->db->where('cust_add.shipping', '1');
		$this->db->where('tblcharges.vtype', '1');
		$this->db->group_by('cust_add.id');
		$query = $this->db->get();
		$res = $query->result_array();
		if (count($res) == 0) {
			$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
			$this->db->select('*,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname, cust_add.mobile_number');
			$this->db->from('customer_addresses as cust_add');
			$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
			$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
			$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
			$this->db->join('customer_numbers as cust_num', 'cust_num.customer_id = cust_add.customer_id', 'inner');
			$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.town_id = twn.town_id', 'left');
			$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
			$this->db->where('cust_add.customer_id', $cusId);
			$this->db->where('cust_add.shipping', '1');
			$this->db->where('tblcharges.vtype', '1');
			$this->db->group_by('cust_add.id');
			$query2 = $this->db->get();
			$res2 = $query2->result_array();
			$post_data = array();
			foreach ($res2 as $value) {
				$post_data[] = array(
					'd_groupID' => $value['bunit_group_id'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'shipping' => $value['shipping'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname']
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
			// echo "heelo";
		} else {
			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'd_groupID' => $value['bunit_group_id'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'shipping' => $value['shipping'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname']
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function checkAllowedPlaceMod($townId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as tblcharges');
		$this->db->where('tblcharges.town_id', $townId);
		$this->db->limit(1);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkFeeMod($townId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as tbldeliveryCh');
		$this->db->where('tbldeliveryCh.town_id', $townId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		if (!empty($res)) {
			foreach ($res as $value) {
				$post_data[] = array(
					'd_charge_amt' => $value['charge_amt']
				);
			}
		} else {
			$post_data[] = array(
				'd_charge_amt' => 0
			);
		}

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getOrderDataMod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->join('fd_products as fbprod', 'fbprod.product_id = appcart.product_id', 'inner');
		$this->db->where('appcart.customerId', $cusId);

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'd_prod' => $value['product_name'],
				'd_price' => $value['price'],
				'd_qty' => $value['quantity'],

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getMobileNumber_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('customer_numbers as cus_numbers');
		$this->db->where('cus_numbers.customer_id', $cusId);

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'id' => $value['id'],
				'customer_id' => $value['customer_id'],
				'mobile_number' => $value['mobile_number'],
				'in_use' => $value['in_use'],

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getAppUser_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_users');
		$this->db->where('app_users.customer_id', $cusId);

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'id' => $value['id'],
				'customer_id' 	=> $value['customer_id'],
				'firstname' 	=> $value['firstname'],
				'lastname' 		=> $value['lastname'],
				'email' 		=> $value['email'],
				'mobile_number' => $value['mobile_number'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getSubtotalMod($cusId)
	{
		// error_reporting(0);
		$all_total = 0;
		$total = array();
		$this->db->select("cart.id, buId, prod.product_id, uom_id, tenantId, cart.customerId, fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom, (SUM(price) + IFNULL(SUM(addon_price), 0)) * cart.quantity as real_price,
				 (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
				 (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		// $this->db->where("product_id", $prod_data->productId);
		$this->db->where("cart.customerId", $cusId);
		// $this->db->group_by("tenantId");

		$result2 = $this->db->get();

		$prods = $result2->result();


		foreach ($prods as $value) {

			// endif;
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result4 = $this->db->get();

			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);


			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);

			$result6 = $this->db->get();
			$sides_addon = $result6->row();


			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);
			$result7 = $this->db->get();
			$tenant = $result7->row();

			$total[] =  $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1);
		}

		for ($i = 0; $i < count($total); $i++) {
			$all_total += $total[$i];
		}
		$item = array(
			'user_details' => array(
				[
					'd_subtotal' =>	$all_total
				],
			),
		);
		echo json_encode($item);
	}


	// public function getLastOrderId_mod($cusId){
	// 		// $this->db->select('*');
	// 		// $this->db->from('toms_customer_orders as toms_order');
	// 		// $this->db->limit(1);
	// 		// $this->db->order_by('id',"desc");
	// 		// $this->db->where('toms_order.customer_id', $cusId);
	// 		// // $this->db->where('toms_order.order_from', 'mobile_app');
	// 		// $query = $this->db->get();
	//   //      	$res = $query->result_array();
	//   //      	$post_data = array();
	// 	 // 	foreach($res as $value){
	// 	 // 			$post_data[] = array(
	// 	 // 				'd_ticket_id' => $value['ticket_id'],
	// 	 // 			);	
	// 		// }
	// 		// $item = array('user_details' => $post_data);
	// 		// echo json_encode($item);

	// 		$this->db->select('*');
	// 		$this->db->from('tickets as ticket');
	// 		$this->db->limit(1);
	// 		$this->db->order_by('id',"desc");
	// 		$this->db->where('ticket.customer_id', $cusId);
	// 		$query = $this->db->get();
	//        	$res = $query->result_array();
	//        	$post_data = array();
	// 	 	foreach($res as $value){
	// 	 			$post_data[] = array(
	// 	 				'd_ticket_id' => $value['ticket'],
	// 	 			);	
	// 		}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function getLastItems_mod($orderNo)
	{
		$this->db->select('*');
		$this->db->from('toms_customer_orders as toms_order');
		$this->db->join('fd_products as fbprod', 'fbprod.product_id = toms_order.product_id', 'inner');

		$this->db->join('locate_tenants as locateTenant', 'locateTenant.tenant_id = fbprod.tenant_id', 'inner');

		$this->db->where('toms_order.ticket_id', $orderNo);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_tenantId' => $value['tenant_id'],
				'd_tenantName' => $value['tenant'],
				'd_items' => $value['product_name'],
				'd_price' => $value['price'],
				'd_totalprice' => $value['total_price'],
				'd_qty' => $value['quantity']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getAllowedLoc_mod()
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as dlvcharg');
		$this->db->join('towns as twn', 'twn.town_id = dlvcharg.town_id', 'inner');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_towd_id' => $value['town_id'],
				'd_town' => $value['town_name'],
				'd_charge_amt' => $value['charge_amt'],
				// 'd_amount_limit' => $value['customer_to_pay']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBuGroupID_mod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,fdprod.tenant_id as t_id');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->group_by('locBu.group_id');
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_bu_id' => $value['bunit_code'],
				'd_bu_group_id' => $value['group_id'],
				'd_bu_name' => $value['business_unit'],
				'd_tenant_name' => $value['tenant'],
				'd_tenant_id' => $value['t_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gcLoadBu_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as gc_cart');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = gc_cart.buId');
		$this->db->where('gc_cart.customer_id', $cusId);
		$this->db->group_by('loc_bu.bunit_code');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		foreach ($res as $value) {
			$post_data[] = array(
				'buCode' => $value['bunit_code'],
				'buName'  => $value['business_unit'],
				'price_group'  => $value['price_group_code']
			);
		}
		$item = array('user_details'  =>  $post_data);
		echo json_encode($item);
	}

	public function gcLoadBu2_mod($cusId, $tempID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$tempID 	  = str_replace($search1, $replacewith1, $tempID);
		$tempId  	  = explode(',', $tempID);

		$this->db->select('*');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->where_in('cart_gc.id', $tempId);
		$this->db->group_by('loc_bu.bunit_code');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		foreach ($res as $value) {
			$post_data[] = array(
				'buCode' => $value['bunit_code'],
				'buName'  => $value['business_unit'],
				'price_group'  => $value['price_group_code']
			);
		}
		$item = array('user_details'  =>  $post_data);
		echo json_encode($item);
	}

	public function getBu_mod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,fdprod.tenant_id as t_id');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->group_by('locBu.bunit_code');
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_bu_id' => $value['bunit_code'],
				'd_bu_group_id' => $value['group_id'],
				'd_bu_name' => $value['business_unit'],
				'd_tenant_name' => $value['tenant'],
				'd_tenant_id' => $value['t_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBu_mod1($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(total_price) as sumpertenants, fdprod.tenant_id as t_id');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->where('appCart.customerId', $cusId);
		$this->db->group_by('fdprod.tenant_id');
		$this->db->order_by('appCart .id', 'desc');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_bu_id' => $value['bunit_code'],
				'd_bu_group_id' => $value['group_id'],
				'd_bu_name' => $value['business_unit'],
				'd_tenant_name' => $value['tenant'],
				'd_tenant_id' => $value['t_id'],
				'd_acroname'  => $value['acroname'],
				'total' => number_format($value['sumpertenants'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBu_mod2($cusId, $productID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");

		$productID 		  	= str_replace($search1, $replacewith1, $productID);

		$productId  	= explode(',', $productID);

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,fdprod.tenant_id as t_id');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->where_in('appCart.id', $productId);
		$this->db->order_by('appCart .id', 'desc');
		$this->db->group_by('fdprod.tenant_id');
		$this->db->where('appCart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_bu_id' => $value['bunit_code'],
				'd_bu_group_id' => $value['group_id'],
				'd_bu_name' => $value['business_unit'],
				'd_tenant_name' => $value['tenant'],
				'd_tenant_id' => $value['t_id'],
				'd_acroname'  => $value['acroname']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTenant_mod($cusId)
	{

		// $productID = ['495','201','192'];
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(total_price) as sumpertenants, count(*) as num');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->where('appCart.customerId', $cusId);
		// $this->db->where_in('appCart.product_id', $productID);
		$this->db->order_by('appCart.id', 'desc');
		$this->db->group_by('fdprod.tenant_id');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'count' => $value['num'],
				'bu_id' => $value['bunit_code'],
				'bu_name' => $value['business_unit'],
				'tenant_id' => $value['tenant_id'],
				'tenant_name' => $value['tenant'],
				'acroname' => $value['acroname'],
				'total' => number_format($value['sumpertenants'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTenant_mod2($cusId, $productID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");

		$productID 		  	= str_replace($search1, $replacewith1, $productID);

		$productId  	= explode(',', $productID);
		// $productId = ['495','191','201','192'];

		// for ($x = 0; $x < count($productID_array); $x++) {

		// 	$prod_id = $productID_array[$x];

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(total_price) as sumpertenants, count(*) as num, fdprod.product_id as prodID');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('locate_business_units as locBu', 'locBu.bunit_code = locTenant.bunit_code', 'left');
		$this->db->join('pick_up_schedules as pick_sched', 'pick_sched.tenant_id = locTenant.tenant_id');
		$this->db->where('pick_sched.status', '1');
		$this->db->where('appCart.customerId', $cusId);
		$this->db->where_in('appCart.id', $productId);
		$this->db->order_by('appCart.id', 'desc');
		$this->db->group_by('fdprod.tenant_id');

		$query = $this->db->get();

		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'count' => $value['num'],
				'bu_id' => $value['bunit_code'],
				'bu_name' => $value['business_unit'],
				'tenant_id' => $value['tenant_id'],
				'tenant_name' => $value['tenant'],
				'acroname' => $value['acroname'],
				'productID' => $value['product_id'],
				'total' => number_format($value['sumpertenants'], 2),
				'time_start'	=> $value['time_start'],
				'time_end'		=> $value['time_end']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);

		// }
	}

	public function getTicketNoOnFoods_mod($cusId)
	{

		// $query = $this->db->query("select * from tickets as toms_tickets
		// 								where id NOT IN (select ticket_id from toms_tag_riders)
		// 								and customer_id = '$cusId' and cancel_status != '1'
		// 								order by id desc");
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, tickets.mop as mop, tickets.id as id, tickets.created_at as date, SUM(IF(toms_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id');
		$this->db->where('tickets.customer_id', $cusId);
		$this->db->order_by('tickets.id', 'desc');
		$this->db->group_by('tickets.id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_ticket_id'  		=> $value['id'],
				'order_type_stat' 	=> $value['order_type_stat'],
				'd_ticket' 			=> $value['ticket'],
				'd_customerId' 		=> $value['customer_id'],
				'd_mop' 			=> $value['mop'],
				'date' 				=> $value['date'],
				'cancel_status' 	=> $value['cancel_status'],
				'total' 			=> $value['total_price']
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTicketNoOnGoods_mod($cusId)
	{

		// $query = $this->db->query("select * from tickets as toms_tickets
		// 								where id NOT IN (select ticket_id from toms_tag_riders)
		// 								and customer_id = '$cusId' and cancel_status != '1'
		// 								order by id desc");
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, tickets.mop as mop, tickets.id as id, tickets.created_at as date, SUM(IF(gc_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as tickets');
		$this->db->join('gc_final_order as gc_order', 'gc_order.ticket_id = tickets.id');
		$this->db->join('gc_order_statuses as gc_status', 'gc_status.ticket_id = tickets.id');
		$this->db->where('tickets.customer_id', $cusId);
		$this->db->order_by('tickets.id', 'desc');
		$this->db->group_by('tickets.id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_ticket_id'  		=> $value['id'],
				'order_type_stat' 	=> $value['order_type_stat'],
				'd_ticket' 			=> $value['ticket'],
				'd_customerId' 		=> $value['customer_id'],
				'd_mop' 			=> $value['mop'],
				'date' 				=> $value['date'],
				'cancel_status' 	=> $value['cancel_status'],
				'total' 			=> $value['total_price'],
				'cancelled_status'	=> $value['cancelled_status']
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTicketNoFood_ontrans_mod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, toms_tickets.mop as mop');
		$this->db->from('tickets as toms_tickets');
		$this->db->join('toms_tag_riders as tag_riders', 'tag_riders.ticket_id = toms_tickets.id', 'left');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = toms_tickets.id', 'inner');
		// $this->db->where('tag_riders.trans_status','1');
		$this->db->where('toms_order.pending_status', '0');
		// $this->db->where('toms_order.tag_pickup_status', '1');
		$this->db->where('tag_riders.delevered_status', '0');
		$this->db->where('toms_tickets.cancel_status', '0');
		$this->db->where('toms_tickets.customer_id', $cusId);
		$this->db->order_by('tag_riders.created_at', 'desc');
		$this->db->group_by('toms_order.ticket_id ');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' 	=> $value['order_type_stat'],
				'd_ticket_id' 		=> $value['ticket'],
				'd_customerId' 		=> $value['customer_id'],
				'd_mop' 			=> $value['mop'],
				'd_submit'			=> $value['submitted_at'],
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTicketNoFood_delivered_mod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('tickets as toms_tickets');
		$this->db->join('toms_tag_riders as tag_riders', 'tag_riders.ticket_id = toms_tickets.id', 'left');
		// $this->db->where('tag_riders.trans_status','1');
		// $this->db->where('tag_riders.delevered_status', '1');
		$this->db->where('tag_riders.complete_status', '1');
		$this->db->where('toms_tickets.customer_id', $cusId);
		$this->db->order_by('toms_tickets.id', 'desc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' => $value['order_type_stat'],
				'd_ticket_id' => $value['ticket'],
				'd_customerId' => $value['customer_id'],
				'd_mop' => $value['mop']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getTicket_cancelled_mod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('tickets as toms_tickets');
		$this->db->join('toms_customer_order as toms_order', 'toms_order.ticket_id = tickets.id');
		$this->db->where('toms_tickets.cancel_status', '1');
		$this->db->where('toms_tickets.customer_id', $cusId);
		$this->db->order_by('toms_tickets.updated_at', 'desc');
		$this->db->group_by('toms_order.ticket_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'order_type_stat' => $value['order_type_stat'],
				'd_ticket_id' => $value['ticket'],
				'd_customerId' => $value['customer_id'],
				'd_mop' => $value['mop']
				// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	// public function getTicketNoGood_mod($cusId){
	// 		$this->db->select('*');
	// 		$this->db->from('tickets as toms_tickets');
	// 		$this->db->where('toms_tickets.customer_id', $cusId);
	// 		$this->db->where('toms_tickets.order_type_stat','1');
	// 		$this->db->order_by('id', 'desc');
	// 		$query = $this->db->get();
	//        	$res = $query->result_array();
	//        	$post_data = array();
	//        	$status = '';
	// 	 	foreach($res as $value){

	// 	 			$post_data[] = array(
	// 	 					'd_ticket_id' => $value['ticket'],
	// 	 					'd_customerId' => $value['customer_id'],
	// 	 					'd_mop' => $value['mop']
	// 	 					// 'd_photo' => 'http://172.16.43.234:8000/'.$value['photo'] 
	// 	 			);	
	// 		}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function check_version_mod($appName)
	{
		$this->db->select('*');
		$this->db->from('app_version');
		$this->db->where('appName', $appName);
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'version_code' 	=> $value['version_code'],
				'changelog'		=> $value['changelog']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadProfile_mod($cusId)
	{
		$this->db->select('*,appsu.created_at');
		$this->db->from('toms_customer_details as toms_det');
		$this->db->join('app_users as appsu', 'appsu.customer_id = toms_det.id');
		$this->db->where('toms_det.id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			if ($value['picture'] == null) {
				$picture  = "https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg";
			} else {
				$picture = $this->profileImage . $value['picture'];
			}
			$post_data[] = array(
				'date_joined' => $value['created_at'],
				'd_fname' => $value['firstname'],
				'd_lname' => $value['lastname'],
				'd_photo' => $picture
				// 'd_photo' => $this->profileImage . $picture
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function get_status_mod($tenantID)
	{
		$this->db->select('*');
		$this->db->from('locate_tenants as loc_tenants');
		$this->db->where('tenant_id', $tenantID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();


		foreach ($res as $value) {
			$post_data[] = array(
				'active' => $value['active'],
			);
		}

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function get_status_mod2($bunitCode)
	{
		$this->db->select('*, loc_bu.bunit_code as bunit_code, loc_tenants.active as active');
		$this->db->from('locate_business_units as loc_bu');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.bunit_code = loc_bu.bunit_code', 'inner');
		$this->db->where('loc_bu.bunit_code', $bunitCode);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();


		foreach ($res as $value) {
			$post_data[] = array(
				'bunit_code' => $value['bunit_code'],
				'active' => $value['active'],
				'tenant_id'  => $value['tenant_id']
			);
		}

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getContainer_mod($ticketId, $tenantId)
	{
		// fd_container_types
		// fd_container_type_details


		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,tickets.id as ticketId, tickets.cancel_status as canceL');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_container_type_details as con_details', 'con_details.ticket_id = toms_order.ticket_id', 'inner');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->join('fd_container_types as con_type', 'con_type.tenant_id = 	locTenant.tenant_id', 'inner');
		$this->db->where('con_details.ticket_id', $ticketId);
		$this->db->where('con_type.tenant_id', $tenantId);
		$this->db->group_by('fdprod.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'container_type' => $value['container_type'],
				'quantity'		 => $value['quantity'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function orderTimeFrameDelivery_mod($ticketNo, $tenantId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,tickets.id as ticketId, tickets.cancel_status as canceL');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('toms_tag_riders as tag_rider', 'tag_rider.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->where('tickets.ticket', $ticketNo);
		$this->db->where('tag_rider.tenant_id', $tenantId);
		$this->db->group_by('tag_rider.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();

		if (count($res) == 0) {

			$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
			$this->db->select('*,tickets.id as ticketId, tickets.cancel_status as canceL');
			$this->db->from('tickets as tickets');
			$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
			$this->db->join('fd_products as fdprod', 'fdprod.product_id = toms_order.product_id', 'inner');
			$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
			$this->db->where('tickets.ticket', $ticketNo);
			$this->db->where('locTenant.tenant_id', $tenantId);
			$this->db->group_by('fdprod.tenant_id');
			$query = $this->db->get();
			$res = $query->result_array();
			$post_data = array();
			foreach ($res as $value) {

				$post_data[] = array(
					'pending_status'      => $value['pending_status'],
					'ticketId' 			  => $value['ticketId'],
					'submitted_at'		  => $value['submitted_at'],
					'prepared_status'     => $value['prepared_status'],
					'prepared_at' 	 	  => $value['prepared_at'],
					'r_setup'			  => $value['r_setup_stat'],
					'r_setup_at'		  => $value['r_setup_stat_at'],
					'tag_status'		  => $value['tag_status'],
					'tag_status_at'		  => $value['tag_status_at'],
					'tenant'		  	  => $value['tenant'],
				);
			}

			$item = array('user_details' => $post_data);
			echo json_encode($item);
		} else {


			$post_data = array();
			foreach ($res as $value) {

				$post_data[] = array(
					'pending_status'      => $value['pending_status'],
					'ticketId' 			  => $value['ticketId'],
					'submitted_at'		  => $value['submitted_at'],
					'prepared_status'     => $value['prepared_status'],
					'prepared_at' 	 	  => $value['prepared_at'],
					'tag_status'		  => $value['tag_status'],
					'tag_status_at'		  => $value['tag_status_at'],
					'r_setup'			  => $value['r_setup_stat'],
					'r_setup_at'		  => $value['r_setup_stat_at'],
					'trans_status'		  => $value['trans_status'],
					'trans_at'			  => $value['trans_at'],
					'delivered_status'	  => $value['delevered_status'],
					'delivered_at'		  => $value['delevered_at'],
					'complete_status'     => $value['complete_status'],
					'completed_at'        => $value['completed_at'],
					'cancelled_status'	  => $value['cancelled_status'],
					'cancelled_at'		  => $value['cancelled_at'],
					'tenant'		  	  => $value['tenant'],
					'tenant_id' 		  => $value['tenant_id']

				);
			}

			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function orderTimeFramePickUp_mod($ticketNo, $tenantId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,tickets.id as ticketId, tickets.cancel_status as canceL');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fdprod', 'fdprod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as locTenant', 'locTenant.tenant_id = 	fdprod.tenant_id', 'inner');
		$this->db->where('tickets.ticket', $ticketNo);
		$this->db->where('locTenant.tenant_id', $tenantId);
		$this->db->group_by('fdprod.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {

			$post_data[] = array(
				'pending_status'      => $value['pending_status'],
				'ticketId' 			  => $value['ticketId'],
				'submitted_at'		  => $value['submitted_at'],
				'prepared_at' 	 	  => $value['prepared_at'],
				'tag_pickup_at'		  => $value['tag_pickup_at'],
				'tenant'		  	  => $value['tenant'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function orderTimeFramePickUpGoods_mod($ticketId, $buId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,tickets.id as ticketId, tickets.cancel_status as canceL, order_status.created_at as submitted_at');
		$this->db->from('tickets as tickets');
		$this->db->join('gc_order_statuses as order_status', 'order_status.ticket_id = tickets.id', 'inner');
		$this->db->where('tickets.id', $ticketId);
		$this->db->where('order_status.bu_id', $buId);
		$this->db->group_by('order_status.bu_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {

			$post_data[] = array(

				'pending_status'      => $value['pending_status'],
				'ticketId' 			  => $value['ticketId'],
				'submitted_at'		  => $value['submitted_at'],
				'prepared_at' 	 	  => $value['prepared_at'],
				'ready_pickup_at'	  => $value['ready_for_pickup_at'],
				'paid_at'			  => $value['paid_at'],
				'cancel_status'		  => $value['cancel_status'],
				'cancelled_status'	  => $value['cancelled_status'],
				'released_status'	  => $value['released_status'],
				'released_at'		  => $value['released_at']

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getCancelStatusMod($ticketNo)
	{
		$this->db->select('*');
		$this->db->from('tickets');
		$this->db->where('tickets.ticket', $ticketNo);
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {

			$post_data[] = array(
				'cancel_status'       => $value['cancel_status'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function lookItems_mod($ticketNo)
	{

		$this->db->select('*,tickets.id as ticketId,
		toms_order.ticket_id as tik_id,
		toms_order.id as toms_id,
		toms_order.quantity as quantity,
		fd_prod.image as prod_image,
		loc_bu.business_unit as loc_bu,
		loc_tenants.tenant as tenant_name,
		toms_order.total_price as total_price,
		toms_order.product_id as  product_id,
		fd_prod.product_id as prod_id,
		fd_prod.product_name as prod_name');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		// $this->db->join('toms_tag_riders as tag_rider', 'tag_rider.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		// $this->db->join('fd_remittance_details as remit_details', 'remit_details.ticket_id = toms_order.ticket_id AND remit_details.tenant_id = loc_tenants.tenant_id');
		$this->db->where('tickets.id', $ticketNo);
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {

			$product_id = $value['product_id'];

			$deliverData = $this->checkDeliverData("toms_tag_riders", $value['tik_id'], $value['tenant_id']);
			if (empty($deliverData)) :
				$val1 = "false";
			else :
				$val1 = "true";
			endif;

			$transData = $this->checkTransData("toms_tag_riders", $value['tik_id'], $value['tenant_id']);
			if (empty($transData)) :
				$val2 = "false";
			else :
				$val2 = "true";
			endif;

			$cancelData = $this->checkCancelData("toms_tag_riders", $value['tik_id'], $value['tenant_id']);
			if (empty($cancelData)) :
				$val3 = "false";
			else :
				$val3 = "true";
			endif;

			$statusData = $this->checkRemitStatus("fd_remittance_details", $value['tik_id'], $value['tenant_id']);
			if (empty($statusData)) :
				$val4 = "false";
			else :
				$val4 = "true";
			endif;

			$addons = $this->countAddons($value['toms_id'], 'toms_customer_order_addons', 'addon', 'addon_id');
			$choices = $this->countChoices($value['toms_id'], 'toms_customer_order_choices', 'choice', 'choice_id');
			$suggestions = $this->countSuggestions($value['toms_id'], 'toms_customer_order_suggestions', 'suggestions', 'product_suggestion_id', $product_id);

			$post_data[] = array(
				'ticketId' 		  		  => $value['ticketId'],
				'pending_status'  		  => $value['pending_status'],
				'canceled_status' 		  => $value['canceled_status'],
				'cancel_status'			  => $value['cancel_status'],
				'tag_pickup'	  		  => $value['tag_pickup_status'],
				'toms_id'		  		  => $value['toms_id'],
				'ticket' 		  		  => $value['ticket'],
				'product_id'      		  => $value['product_id'],
				'prod_name' 	  		  => $value['prod_name'],
				'product_price'   		  => $value['product_price'],
				'total_price'	 		  => $value['total_price'],
				'tenant_name' 	  		  => $value['tenant_name'],
				'tenant_id'		  		  => $value['tenant_id'],
				'bu_name' 		  		  => $value['loc_bu'],
				'bu_id'			  		  => $value['bunit_code'],
				'd_qty' 		 		  => $value['quantity'],
				'icoos'			 		  => $value['icoos'],
				// 'status'				  => $value['status'],
				'prod_image' 	  		  => $this->productImage . $value['prod_image'],
				'ifDeliveryExists' 	 	  => $val1,
				'ifTransExists' 	 	  => $val2,
				'ifCancelExists' 	 	  => $val3,
				'ifRemitExists'			  => $val4,
				'addon_length' 			  => count($addons) + count($choices) + count($suggestions),
				'add_ons' 				  => $addons,
				'choices' 				  => $choices,
				'suggestions'			  => $suggestions,
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	private function countAddons(int $foreignKey, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = $alias.$column", 'inner');
		$this->db->join('fd_uoms as fd_uom', "$alias.uom_id = fd_uom.id AND IFNULL($alias.uom_id, 0) = IFNULL(fd_uom.id, 0)", "left");
		$this->db->where('order_id', $foreignKey);
		return $this->db->get()->result();
	}

	private function countChoices(int $foreignKey, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = $alias.$column", 'inner');
		$this->db->join('fd_uoms as fd_uom', "$alias.uom_id = fd_uom.id AND IFNULL($alias.uom_id, 0) = IFNULL(fd_uom.id, 0)", "left");
		$this->db->where('order_id', $foreignKey);
		return $this->db->get()->result();
	}

	private function countFlavors(int $foreignKey, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_flavors as fd_flavor', "fd_flavor.id = $alias.$column", 'inner');
		$this->db->where('order_id', $foreignKey);

		return $this->db->get()->result();
	}

	private function countSuggestions(int $foreignKey, string $table, string $alias, string $column, string $product_id)
	{
		$this->db->select("*");
		$this->db->from("$table as $alias");
		$this->db->join('fd_addon_suggestions as addon_suggestion', "addon_suggestion.product_suggestion_id = $alias.$column", 'inner');
		$this->db->join('fd_product_suggestions as prod_suggestion', 'prod_suggestion.id = addon_suggestion.product_suggestion_id', 'inner');
		$this->db->where("$alias.order_id", $foreignKey);
		$this->db->where('addon_suggestion.product_id', $product_id);
		return $this->db->get()->result();
	}

	public function checkDeliverData($table, $ticket_id, $tenant_id)
	{
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where("ticket_id", $ticket_id);
		$this->db->where("delevered_status", "1");
		$this->db->where("tenant_id", $tenant_id);

		$result = $this->db->get();

		return $result->row();
	}

	public function checkTransData($table, $ticket_id, $tenant_id)
	{
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where("ticket_id", $ticket_id);
		$this->db->where("trans_status", "1");
		$this->db->where("tenant_id", $tenant_id);

		$result = $this->db->get();

		return $result->row();
	}

	public function checkCancelData($table, $ticket_id, $tenant_id)
	{
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where("ticket_id", $ticket_id);
		$this->db->where("cancelled_status", "1");
		$this->db->where("tenant_id", $tenant_id);

		$result = $this->db->get();

		return $result->row();
	}

	public function checkRemitStatus($table, $ticket_id, $tenant_id)
	{
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where("ticket_id", $ticket_id);
		$this->db->where("status", "1");
		$this->db->where("tenant_id", $tenant_id);

		$result = $this->db->get();

		return $result->row();
	}

	public function lookItems_segregate_mod($ticketNo)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, SUM(IF(toms_order.canceled_status = 0, total_price, 0)) sumpertenants,  
		SUM(IF(toms_order.prepared_status = 1, prepared_status, 0)) sumprepared,
		SUM(toms_order.icoos) as icoos,
		loc_bu.business_unit as loc_bu, 
		loc_tenants.tenant as tenant_name, 
		tickets.cancel_status as cancel_status');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('toms_order.ticket_id', $ticketNo);
		// $this->db->where('toms_order.canceled_status', '0');
		$this->db->order_by('toms_order.id', 'desc');
		$this->db->group_by('fd_prod.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		// $count($post_data);
		foreach ($res as $value) {

			$post_data[] = array(
				'icoos'			  => $value['icoos'],
				'tenant_name' 	  => $value['tenant_name'],
				'tenant_id'		  => $value['tenant_id'],
				'bu_name' 		  => $value['loc_bu'],
				'acroname'        => $value['acroname'],
				'bu_id'			  => $value['bunit_code'],
				'total_price'     => $value['total_price'],
				'cancel_status'   => $value['cancel_status'],
				'sumpertenants'	  => number_format($value['sumpertenants'], 2),
				'sumprepared'	  => $value['sumprepared'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function lookItems_segregate2_mod($ticketNo)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, SUM(IF(gc_order.canceled_status = 0, total_price, 0)) sumperstore, tickets.id as ticket_id');
		$this->db->from('tickets as tickets');
		$this->db->join('gc_final_order as gc_order', 'gc_order.ticket_id = tickets.id', 'inner');
		$this->db->join('gc_order_statuses as gc_status', 'gc_status.ticket_id = tickets.id', 'inner');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = gc_order.bu_id');
		$this->db->join('gc_special_instructions as instructions', 'instructions.ticket_id = tickets.id  and instructions.bu_id = gc_order.bu_id');
		$this->db->where('gc_order.ticket_id', $ticketNo);
		// $this->db->where('toms_order.canceled_status', '0');
		$this->db->order_by('gc_order.id');
		$this->db->group_by('gc_order.bu_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		// $count($post_data);
		foreach ($res as $value) {

			$post_data[] = array(
				'ticket_id'		  	=> $value['ticket_id'],
				'bu_id'			  	=> $value['bu_id'],
				'business_unit'   	=> $value['business_unit'],
				'acroname'		  	=> $value['acroname'],
				'instructions'	  	=> $value['remarks'],
				'sumperstore'	  	=> number_format($value['sumperstore'], 2),
				'canceled_status'	=> $value['canceled_status'],
				'cancelled_status'	=> $value['cancelled_status'],
				'prepared_status'	=> $value['preparing_status']


			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTotalAmount_mod($ticketNo)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, SUM(IF(toms_order.canceled_status = 0, total_price, 0)) sumpertenants');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('toms_order.ticket_id', $ticketNo);
		// $this->db->where('toms_order.canceled_status', '0');
		$this->db->order_by('toms_order.id', 'desc');
		$this->db->group_by('fd_prod.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {

			$post_data[] = array(
				'sumpertenants'	  => number_format($value['sumpertenants'], 2),
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getAmountPerTenantmod($ticketNo)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(total_price) as sumpertenants,
		loc_bu.business_unit as loc_bu,
		loc_tenants.tenant as tenant_name');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('tickets.ticket', $ticketNo);
		$this->db->where('toms_order.canceled_status', '0');
		$this->db->group_by('loc_tenants.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'tenant_name' 	  => $value['tenant_name'],
				'tenant_id'		  => $value['tenant_id'],
				'bu_name' 		  => $value['loc_bu'],
				'bu_id'			  => $value['bunit_code'],
				'sumpertenants'	  => ceil($value['sumpertenants'])
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function lookitems_good_mod($ticketNo)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,gc_final.id as gc_final_id,gc_final.ticket_id as tik_id,
		gc_final.ticket_id as ticket,gc_prod_items.product_name as prod_name,
		gc_prod_items.image as prod_image,gc_final.quantity as quantity, gc_final.pending_status as pending_status');
		$this->db->from('tickets as tickets');
		$this->db->join('gc_final_order as gc_final', 'gc_final.ticket_id = tickets.id', 'inner');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.product_id = gc_final.product_id', 'inner');
		$this->db->join('gc_order_statuses as order_status', 'order_status.ticket_id = tickets.id  and order_status.bu_id = gc_final.bu_id');
		$this->db->where('tickets.id', $ticketNo);
		// $this->db->where('tickets.customer_id',$cusId);
		// $this->db->where('toms_order.canceled_status','0');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			// $toms_data = $this->checkTomsData("toms_tag_riders", $value['tik_id']);

			if (empty($toms_data)) :
				$val = "false";
			else :
				$val = "true";
			endif;

			$post_data[] = array(
				'toms_id'	  	  	=> $value['gc_final_id'],
				'canceled_status' 	=> $value['canceled_status'],
				'bu_id'			  	=> $value['bu_id'],
				'gc_final_id'	  	=> $value['gc_final_id'],
				'ticketId' 		  	=> $value['ticket'],
				'product_id'      	=> $value['product_id'],
				'prod_name' 	  	=> $value['prod_name'],
				'price'			  	=> $value['price'],
				'total_price' 	 	=> $value['total_price'],
				'd_qty' 		  	=> number_format($value['quantity'], 0),
				'prod_image' 	  	=> $this->gcproductImage . $value['prod_image'],
				'ifexists' 	 	  	=> $val,
				'pending_status'  	=> $value['pending_status'],
				'for_pickup'	  	=> $value['ready_for_pickup_status'],
				'paid_status'		=> $value['paid_status'],
				'released_status' 	=> $value['released_status'],
				'cancelled_status'	=> $value['cancelled_status'],
				'canceled_status'	=> $value['canceled_status'],
				'icoos'				=> $value['icoos']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadCartDataNew_mod($cusId)
	{

		$temp_orders = [];

		$this->db->select('*, fd_prod.product_id as product_id, temp_orders.id as temp_id');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = temp_orders.product_id", 'inner');
		$this->db->where('customerId', $cusId);
		$this->db->order_by('temp_orders.id', 'desc');

		$temp_orders = $this->db->get()->result();
		$main_items = [];

		foreach ($temp_orders as $temp_order) {

			$details = [];
			$temp_order_id = $temp_order->id;
			$product_id = $temp_order->product_id;


			$addons 			= $this->getTempOrderRelations($temp_order_id, 'app_customer_temp_order_addons', 'addon', 'addon_id');
			$choices 			= $this->getTempOrderChoiceRelations($temp_order_id, 'app_customer_temp_order_choices', 'choices', 'choice_id');
			$flavors 			= $this->getTempOrderFlavorRelations($temp_order_id, 'app_customer_temp_order_flavors', 'flavors', 'flavor_id');
			$suggestions 		= $this->getTempOrderSflavorRelations($temp_order_id, 'app_customer_temp_order_suggestions', 'suggestions', 'product_suggestion_id', $product_id);

			$temp_order->image  = $this->productImage . $temp_order->image;

			$details['main_item'] = $temp_order;

			$details['addons'] = $addons;
			$details['choices'] = $choices;
			$details['suggestions'] = $suggestions;


			$details['main_item']->addon_length = count($addons) + count($choices) + count($suggestions);
			$main_items[] = $details;
		}

		echo json_encode(['user_details' => $main_items]);
	}

	public function loadCartDataNew2_mod($cusId, $productID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");

		$productID 	= str_replace($search1, $replacewith1, $productID);

		$productId  = explode(',', $productID);

		$temp_orders = [];
		// $productId = ['335'];
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = temp_orders.product_id", 'inner');
		$this->db->where('customerId', $cusId);
		$this->db->where_in('temp_orders.id', $productId);
		$this->db->order_by('temp_orders.id', 'desc');
		$temp_orders = $this->db->get()->result();
		$main_items = [];

		foreach ($temp_orders as $temp_order) {

			$details = [];
			$temp_order_id = $temp_order->id;
			$product_id = $temp_order->product_id;

			$addons = $this->getTempOrderRelations($temp_order_id, 'app_customer_temp_order_addons', 'addon', 'addon_id');
			$choices = $this->getTempOrderChoiceRelations($temp_order_id, 'app_customer_temp_order_choices', 'choices', 'choice_id');
			// $flavors = $this->getTempOrderFlavorRelations($temp_order_id, 'app_customer_temp_order_flavors', 'flavors', 'flavor_id');
			$suggestions = $this->getTempOrderSflavorRelations($temp_order_id, 'app_customer_temp_order_suggestions', 'suggestions', 'product_suggestion_id', $product_id);

			$temp_order->image = $this->productImage . $temp_order->image;

			$details['main_item'] = $temp_order;

			$details['addons'] = $addons;
			$details['choices'] = $choices;
			$details['suggestions'] = $suggestions;

			$details['main_item']->addon_length = count($addons) + count($choices) + count($suggestions);
			$main_items[] = $details;
		}

		echo json_encode(['user_details' => $main_items]);
	}

	private function getTempOrderRelations(int $temp_order_id, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = $alias.$column");
		$this->db->join('fd_uoms as fd_uom', "$alias.uom_id = fd_uom.id AND IFNULL($alias.uom_id, 0) = IFNULL(fd_uom.id, 0)", "left");
		$this->db->where('temp_order_id', $temp_order_id);
		return $this->db->get()->result();
	}

	private function getTempOrderChoiceRelations(int $temp_order_id, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_products as fd_prod', "fd_prod.product_id = $alias.$column");
		$this->db->join('fd_uoms as fd_uom', "$alias.uom_id = fd_uom.id AND IFNULL($alias.uom_id, 0) = IFNULL(fd_uom.id, 0)", "left");
		$this->db->where('temp_order_id', $temp_order_id);
		return $this->db->get()->result();
	}

	private function getTempOrderFlavorRelations($temp_order_id, string $table, string $alias, string $column)
	{
		$this->db->select('*');
		$this->db->from("$table as $alias");
		$this->db->join('fd_flavors as fd_flavor', "fd_flavor.id = $alias.$column", 'inner');
		$this->db->where('temp_order_id', $temp_order_id);
		return $this->db->get()->result();
	}

	private function getTempOrderSflavorRelations($temp_order_id, string $table, string $alias, string $column, string $product_id)
	{
		$this->db->select("*");
		$this->db->from("$table as $alias");
		$this->db->join('fd_addon_suggestions as addon_suggestion', "addon_suggestion.product_suggestion_id = $alias.$column", 'inner');
		$this->db->join('fd_product_suggestions as prod_suggestion', 'prod_suggestion.id = addon_suggestion.product_suggestion_id', 'inner');
		$this->db->where("$alias.temp_order_id", $temp_order_id);
		$this->db->where('addon_suggestion.product_id', $product_id);
		return $this->db->get()->result();
	}

	public function loadCartData_mod($cusId)
	{
		$total = array();
		$this->db->select("cart.id,quantity ,cart.productId ,buId, prod.product_id, uom_id, buId, tenantId, cart.customerId, price as real_price");
		// fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom");
		// (SUM(price) as real_price");
		// (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
		// (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		$this->db->where("cart.customerId", $cusId);
		// $this->db->group_by("tenantId");
		$result2 = $this->db->get();
		$prods = $result2->result();
		// echo json_encode($result2);
		// exit();
		foreach ($prods as $value) {
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");




			$result4 = $this->db->get();

			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result6 = $this->db->get();
			$sides_addon = $result6->row();

			// var_dump($drinks->drinks_price);
			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);

			$result7 = $this->db->get();
			$tenant = $result7->row();

			$this->db->select("*");
			$this->db->from("locate_business_units");
			$this->db->where("bunit_code", $value->buId);

			$result8 = $this->db->get();
			$bu = $result8->row();

			$this->db->select("*");
			$this->db->from("fd_products");
			$this->db->where("product_id", $value->product_id);

			$result9 = $this->db->get();
			$prod_image = $result9->row();

			$total[] = array(
				"d_id"    	   => $value->id,
				'cart_qty'	   => $value->quantity,
				"tenant_id"   => $value->tenantId,
				"tenant_name" => $tenant->tenant,
				"bu_id"  	   => $value->buId,
				"bu_name"     => $bu->business_unit,
				"prod_image"  => $this->productImage . $prod_image->image,
				"prod_name"   => $prod_image->product_name,
				"prod_id"	   => $value->productId,
				"prod_uom"	   => $value->uom_id,
				"total"  	   =>  $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1)
				// + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1)
			);
		}
		$item = array('user_details' => $total);
		echo json_encode($item);
	}

	public function loadCartData_sides_mod($cusId)
	{
		$this->db->select('*,fd_prod_prices.uom_id as prod_uom,fd_prod_prices.price as prod_price');
		$this->db->from('app_cart_addons_side_items as appCart_addons');
		$this->db->join('app_cart_main as app_cart_main', 'app_cart_main.id = appCart_addons.cart_id', 'left');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = appCart_addons.side_id', 'left');
		$this->db->join('fd_product_prices as fd_prod_prices', 'fd_prod_prices.product_id = appCart_addons.side_id AND fd_prod_prices.uom_id = appCart_addons.side_uom', 'left');
		$this->db->where('app_cart_main.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'side_id' => $value['side_id'],
				'side_name' => $value['product_name'],
				'side_uom' => $value['prod_uom'],
				'prod_price' => $value['prod_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function removeItemFromCart_mod($cartId)
	{
		$this->db->where('app_cart_main.id', $cartId);
		$this->db->delete('app_cart_main');

		$this->db->where('app_cart_fries.cart_id', $cartId);
		$this->db->delete('app_cart_fries');

		$this->db->where('app_cart_addons_side_items.cart_id', $cartId);
		$this->db->delete('app_cart_addons_side_items');

		$this->db->where('app_cart_drink.cart_id', $cartId);
		$this->db->delete('app_cart_drink');

		$this->db->where('app_cart_sides.cart_id', $cartId);
		$this->db->delete('app_cart_sides');
	}

	public function displayOrder_mod($cusId, $tenantId)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appCart');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = appCart.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenant', 'loc_tenant.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->where('appCart.customerId', $cusId);
		$this->db->where('loc_tenant.tenant_id', $tenantId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'tenant_id'    => $tenantId,
				'price' 	   => $value['price'],
				'total_price'  => $value['total_price'],
				'product_name' => $value['product_name'],
				'quantity' 	   => $value['quantity']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getDiscountID_mod($cusId, $discountName)
	{
		$this->db->select('*');
		$this->db->from('discount_lists as discount');
		$this->db->where('discount.status', '1');
		$this->db->where('discount.discount_name', $discountName);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'discount_id'  => $value['id'],
				// 'price' 	   => $value['price'],
				// 'total_price'  => $value['total_price'],
				// 'product_name' => $value['product_name'],
				// 'quantity' 	   => $value['quantity']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function check_per_store_total($cusId, $credit_limit)
	{
		$ress = array();
		$total = array();
		$this->db->select("cart.id, buId, prod.product_id, uom_id, tenantId, cart.customerId, fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom, (SUM(price) + IFNULL(SUM(addon_price), 0)) * cart.quantity as real_price,
				 (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
				 (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		$this->db->where("cart.customerId", $cusId);
		$this->db->group_by("tenantId");
		$result2 = $this->db->get();
		$prods = $result2->result();
		foreach ($prods as $value) {
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result4 = $this->db->get();
			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result6 = $this->db->get();
			$sides_addon = $result6->row();


			$this->db->select("*");
			$this->db->from("locate_business_units");
			$this->db->where("bunit_code", $value->buId);

			$result8 = $this->db->get();
			$bu = $result8->row();


			// var_dump($drinks->drinks_price);
			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);

			$result7 = $this->db->get();
			$tenant = $result7->row();
			$total_price = $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1);
			if ($total_price < $credit_limit) {
				return 'true';
			}
		}
		return 'false';
	}


	public function trapTenantLimit_mod($townId, $customerId)
	{
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges');
		$this->db->where('town_id', $townId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$credit_limit = $res[0]['customer_to_pay'] - $res[0]['charge_amt'];
		$store_price = $this->check_per_store_total($customerId, $credit_limit);


		// foreach ($store_price as $value) {
		// 	# code...

		// }
		// foreach($res as $value){
		// 	// if($value['subtotalPerTenant'] < 300){
		// 			if($grandtotal < $value['customer_to_pay'] - $value['charge_amt'])
		// 			{
		// 				$limit = 'true';
		// 			}else{
		// 				$limit = 'false';
		// 			}
		$post_data[] = array(
			'limit' => $store_price,
			'town_limit' => $credit_limit
			// 'customer_to_pay' => $res[0]['customer_to_pay'],
			// 'charge_amt' => $res[0]['charge_amt']
		);
		// 	}

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTenant_perbu_mod($buId)
	{
		$this->db->select('*');
		$this->db->from('locate_tenants as loc_tenants');
		$this->db->where('loc_tenants.bunit_code', $buId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_tenant_id' =>  $value['tenant_id'],
				'd_bunit_code' =>  $value['bunit_code'],
				'd_tenant' =>  $value['tenant'],
				'd_logo' =>  $this->buImage . $value['logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getAmountPertenant_mod($cusId)
	{

		$total = array();
		$this->db->select("cart.id, buId, prod.product_id, uom_id, tenantId, cart.customerId, fries_price.fries_id, fries_price.fries_uom, drink_price.drink_id, drink_price.drink_uom, (SUM(price) + IFNULL(SUM(addon_price), 0)) * cart.quantity as real_price,
				 (SELECT price FROM fd_product_prices WHERE product_id = fries_price.fries_id AND IFNULL(uom_id, 0) = IFNULL(fries_price.fries_uom, 0)) as fries_price, 
				 (SELECT price FROM fd_product_prices WHERE product_id = drink_price.drink_id AND IFNULL(uom_id, 0) = IFNULL(drink_price.drink_uom, 0)) as drink_price");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_main as cart", "prod.product_id = cart.productId AND IFNULL(prod.uom_id, 0) = IFNULL(cart.uom, 0)", "inner");
		$this->db->join("fd_addon_flavors as flavor_price", "prod.product_id = flavor_price.product_id AND IFNULL(cart.flavor, 0) = IFNULL(flavor_price.flavor_id, 0)", "left");
		$this->db->join("app_cart_fries as fries_price", "prod.product_id = fries_price.fries_id AND IFNULL(prod.uom_id, 0) = IFNULL(fries_price.fries_uom, 0)", "left"); // AND cart.id = fries_price.cart_id
		$this->db->join("app_cart_drink as drink_price", "prod.product_id = drink_price.drink_id AND IFNULL(prod.uom_id, 0) = IFNULL(drink_price.drink_uom, 0)", "left"); // AND cart.id = drink_price.cart_id
		$this->db->where("cart.customerId", $cusId);
		$this->db->group_by("tenantId");
		$result2 = $this->db->get();
		$prods = $result2->result();
		foreach ($prods as $value) {
			$this->db->select("SUM(price) as fries_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result3 = $this->db->get();

			$fries = $result3->row();

			// var_dump($fries->fries_price);

			$this->db->select("SUM(price) as drinks_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result4 = $this->db->get();
			$drinks = $result4->row();


			$this->db->select("SUM(price) as sides_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");

			$result5 = $this->db->get();

			$sides = $result5->row();


			$this->db->select("SUM(price) as sides_addon_price");
			$this->db->from("fd_product_prices as prod");
			$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
			$this->db->where("cart_id", $value->id);
			// $this->db->group_by("cart_id");
			$result6 = $this->db->get();
			$sides_addon = $result6->row();


			$this->db->select("*");
			$this->db->from("locate_business_units");
			$this->db->where("bunit_code", $value->buId);

			$result8 = $this->db->get();
			$bu = $result8->row();


			// var_dump($drinks->drinks_price);
			$this->db->select("*");
			$this->db->from("locate_tenants");
			$this->db->where("tenant_id", $value->tenantId);

			$result7 = $this->db->get();
			$tenant = $result7->row();
			$total[] = array(
				"tenant_id"   => $value->tenantId,
				"loc_tenant_name" => $tenant->tenant,
				"bu_id"  	   => $value->buId,
				"loc_bu_name"     => $bu->business_unit,
				"total_price"  	   => $value->real_price + ($fries->fries_price * 1) + ($drinks->drinks_price * 1) + ($sides->sides_price * 1) + ($sides_addon->sides_addon_price * 1)
			);
		}
		$item = array('user_details' => $total);
		echo json_encode($item);
	}

	//node


	public function display_tenant_mod($buCode, $globalID)
	{
		$this->db->select('*');
		$this->db->from('locate_tenants as loc_tenants');
		$this->db->where('loc_tenants.bunit_code', $buCode);
		$this->db->where('loc_tenants.global_cat_id', $globalID);
		$this->db->where('loc_tenants.active', '1');
		$this->db->order_by('loc_tenants.tenant');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'tenant_id' =>  $value['tenant_id'],
				'bunit_code' =>  $value['bunit_code'],
				'global_cat_id' => $value['global_cat_id'],
				'd_tenant_name' => $value['tenant'],
				'logo' =>  $this->buImage . $value['logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getglobalcat_mod()
	{
		$this->db->select('*');
		$this->db->from('global_categories as global_cat');
		$this->db->where('global_cat.status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'id'			=> $value['id'],
				'category' 		=> $value['category'],
				'cat_picture'	=> $this->buImage . $value['cat_picture']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function display_store_mod()
	{
		$this->db->select('*');
		$this->db->from('locate_business_units as loc_bu');
		$this->db->where('loc_bu.active', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'bunit_code'  	=>  $value['bunit_code'],
				'business_unit' =>  $value['business_unit'],
				'acroname' 		=>  $value['acroname'],
				'logo'		    =>  $this->buImage . $value['logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function load_store_mod($groupID)
	{
		$this->db->select('*');
		$this->db->from('locate_business_units as loc_bu');
		// $this->db->join('locate_bunit_group_classes as bunit_group', 'bunit_group.bunit_code = loc_bu.bunit_code', 'inner');
		$this->db->where('loc_bu.group_id', $groupID);
		$this->db->where('loc_bu.active', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'bunit_code'  	=>  $value['bunit_code'],
				'business_unit' =>  $value['business_unit'],
				'acroname' 		=>  $value['acroname'],
				'group_code'	=>  $value['price_group_code'],
				'logo'		    =>  $this->buImage . $value['logo']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	// public function display_store_mod($unitGroupId, $globalCatID)
	// {
	// 	$this->db->select('*,loc_bu.logo as bu_logo');
	// 	$this->db->from('locate_tenants as loc_tenants');
	// 	$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code', 'left');
	// 	$this->db->where('loc_bu.active', '1');
	// 	$this->db->where('loc_bu.group_id', $unitGroupId);
	// 	$this->db->where('loc_tenants.global_cat_id', $globalCatID);
	// 	// $this->db->join('locate_tenants as loc_tenants', 'loc_tenants.bunit_code = loc_bu.bunit_code','inner');
	// 	$this->db->group_by('loc_tenants.bunit_code');
	// 	$query = $this->db->get();
	// 	$res = $query->result_array();
	// 	$post_data = array();
	// 	foreach ($res as $value) {
	// 		$post_data[] = array(
	// 			'bunit_code' =>  $value['bunit_code'],
	// 			'business_unit' =>  $value['business_unit'],
	// 			'd_tenant' =>  $value['acroname'],
	// 			'logo' =>  $this->buImage . $value['bu_logo']
	// 		);
	// 	}
	// 	$item = array('user_details' => $post_data);
	// 	echo json_encode($item);
	// }

	// public function display_restaurant_mod($tenantCode){
	// 		$this->db->select('*');
	// 		$this->db->from('fd_product_prices as pro_prices');
	// 		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = pro_prices.product_id','inner');
	// 		$this->db->join('fd_uoms as fd_uom','fd_uom.id = pro_prices.uom_id','inner');
	// 		$this->db->where('fd_prod.tenant_id',$tenantCode);
	// 		$this->db->where('fd_prod.active','1');
	// 		$this->db->where('pro_prices.price!=','0.00');
	// 		// $this->db->limit(50);
	// 		$query = $this->db->get();
	//        	$res = $query->result_array();
	//        	$post_data = array();
	// 	 	foreach($res as $value){
	// 	 			$post_data[] = array(
	// 	 				'unit_measure' => $value['unit_measure'],
	// 	 				'product_id' => $value['product_id'],
	// 	 				'product_uom' => $value['uom_id'],
	// 	 				'tenant_id' => $value['tenant_id'],
	// 	 				'product_name' => $value['product_name'],
	// 	 				'price' => $value['price'],
	// 	 				'image' => $this->productImage.$value['image']
	// 	 			);	
	// 		}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function display_restaurant_mod($categoryId)
	{

		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->where('fd_prod_cat.category_id', $categoryId);
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$this->db->where('fd_prod_price.available', '1');
		$this->db->order_by('fd_prod.product_name');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function display_item_data_mod($prodId, $productUom)
	{
		if ($productUom == 'null') {
			$productUom = null;
		} else {
			$productUom = $productUom;
		}

		// $this->db->select('*');
		// $this->db->from('fd_products');
		// $this->db->join('fd_product_prices', 'fd_product_prices.product_id = fd_products.product_id', 'left');
		// $this->db->join('fd_product_addons', 'fd_product_addons.product_id = fd_products.product_id', 'left');
		// $this->db->where('fd_products.product_id', $prodId);
		// $this->db->group_by('fd_products.product_id');

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('fd_product_prices as fd_prod_price');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_price.product_id', 'left');
		$this->db->join('fd_product_addons as fd_prod_addons', 'fd_prod_addons.product_id = fd_prod_price.product_id', 'left');
		$this->db->where('fd_prod_price.product_id', $prodId);
		$this->db->group_by('fd_prod_price.product_id');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$addon_sides = array();
		$choices_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' =>  $value['product_id'],
				'variation' => $value['variation'],
				'addon_side' => $value['addon_side'],
				'addon_dessert' => $value['dessert'],
				'tenant_id' => $value['tenant_id'],
				'product_name' =>  $value['product_name'],
				'description' => $value['description'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'price_per_gram' => $value['price_per_gram'],
				'no_specific_price' => $value['no_specific_price'],
			);
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name, unit_measure, fd_product_addons.uom_id, 
			fd_product_addons.addon_price, fd_product_addons.addon_sides');
			$this->db->from('fd_products');
			$this->db->join('fd_product_addons', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_addons.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_addons.product_id',  $prodId);
			$this->db->where('fd_product_addons.addon_sides', '1');
			$this->db->order_by('fd_product_addons.addon_price');

			$results = $this->db->get()->result();


			if (!empty($results)) :
				foreach ($results as $result) {
					$addon_sides['addon_sides_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'addon_sides'     => $result->addon_sides,
					);
				}
			else :
				$addon_sides['addon_sides_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name, unit_measure, fd_product_addons.uom_id, 
			fd_product_addons.addon_price, fd_product_addons.addon_dessert');
			$this->db->from('fd_products');
			$this->db->join('fd_product_addons', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_addons.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_addons.product_id',  $prodId);
			$this->db->where('fd_product_addons.addon_dessert', '1');
			$this->db->order_by('fd_product_addons.addon_price');

			$result1s = $this->db->get()->result();


			if (!empty($result1s)) :
				foreach ($result1s as $result) {
					$addon_dessert['addon_dessert_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'addon_dessert'     => $result->addon_dessert,
					);
				}
			else :
				$addon_dessert['addon_dessert_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name, unit_measure, fd_product_addons.uom_id, 
			fd_product_addons.addon_price, fd_product_addons.addon_drinks');
			$this->db->from('fd_products');
			$this->db->join('fd_product_addons', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_addons.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_addons.product_id',  $prodId);
			$this->db->where('fd_product_addons.addon_drinks', '1');
			$this->db->order_by('fd_product_addons.addon_price');

			$result2s = $this->db->get()->result();


			if (!empty($result2s)) :
				foreach ($result2s as $result) {
					$addon_drinks['addon_drinks_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'addon_drinks'     => $result->addon_drinks,
					);
				}
			else :
				$addon_drinks['addon_drinks_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name,fd_product_choices.uom_id,
			fd_product_choices.default_choice , unit_measure, fd_product_choices.addon_price');
			$this->db->from('fd_products');
			$this->db->join('fd_product_choices', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_choices.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_choices.product_id', $prodId);
			$this->db->where('fd_product_choices.choice_drinks', '1');
			$this->db->order_by('fd_product_choices.addon_price');


			$result3s = $this->db->get()->result();

			if (!empty($result3s)) :
				foreach ($result3s as $result) {
					$choices_drinks['choices_drinks_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'default'		  => $result->default_choice
					);
				}
			else :
				$choices_drinks['choices_drinks_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name,fd_product_choices.uom_id,
			fd_product_choices.default_choice , unit_measure, fd_product_choices.addon_price');
			$this->db->from('fd_products');
			$this->db->join('fd_product_choices', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_choices.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_choices.product_id', $prodId);
			$this->db->where('fd_product_choices.choice_fries', '1');
			$this->db->order_by('fd_product_choices.addon_price');


			$result4s = $this->db->get()->result();

			if (!empty($result4s)) :
				foreach ($result4s as $result) {
					$choices_fries['choices_fries_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'default'		  => $result->default_choice
					);
				}
			else :
				$choices_fries['choices_fries_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name,fd_product_choices.uom_id,
			fd_product_choices.default_choice , unit_measure, fd_product_choices.addon_price');
			$this->db->from('fd_products');
			$this->db->join('fd_product_choices', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_choices.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_choices.product_id', $prodId);
			$this->db->where('fd_product_choices.choice_sides', '1');
			$this->db->order_by('fd_product_choices.addon_price');


			$result5s = $this->db->get()->result();

			if (!empty($result5s)) :
				foreach ($result5s as $result) {
					$choices_sides['choices_sides_data'][] = array(
						'sub_productid'   => $result->product_id,
						'sub_productname' => $result->product_name,
						'unit'            => $result->unit_measure,
						'uom_id'		  => $result->uom_id,
						'addon_price'     => $result->addon_price,
						'default'		  => $result->default_choice
					);
				}
			else :
				$choices_sides['choices_sides_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name, fd_product_prices.primary_uom, 
			fd_product_prices.uom_id, unit_measure, fd_product_prices.price');
			$this->db->from('fd_products');
			$this->db->join('fd_product_prices', 'fd_products.product_id = fd_product_prices.product_id', 'inner');
			$this->db->join('fd_uoms', 'fd_product_prices.uom_id = fd_uoms.id', 'left');
			$this->db->where('fd_product_prices.product_id', $prodId);
			$this->db->order_by('fd_product_prices.price');

			$result6s = $this->db->get()->result();

			if (!empty($result6s)) :
				foreach ($result6s as $result) {
					$price_data['uom_data'][] = array(
						'price_productid'   => $result->product_id,
						'price_productname' => $result->product_name,
						'unit'              => $result->unit_measure,
						'uom_id'			=> $result->uom_id,
						'price'             => $result->price,
						'default'			=> $result->primary_uom
					);
				}
			else :
				$price_data['uom_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('fd_products.product_id, fd_products.product_name, fd_flavors.flavor,
			fd_addon_flavors.default_choice , fd_addon_flavors.flavor_id, fd_addon_flavors.addon_price');
			$this->db->from('fd_products');
			$this->db->join('fd_product_prices', 'fd_products.product_id = fd_product_prices.product_id', 'inner');
			$this->db->join('fd_addon_flavors', 'fd_addon_flavors.product_id = fd_products.product_id', 'inner');
			$this->db->join('fd_flavors', 'fd_flavors.id = fd_addon_flavors.flavor_id', 'inner');
			$this->db->where('fd_product_prices.product_id', $prodId);

			$result7s = $this->db->get()->result();

			if (!empty($result7s)) :
				foreach ($result7s as $result) {
					$flavor_data['flavor_data'][] = array(
						'price_productid' 	=> $result->product_id,
						'flavor_name'		=> $result->flavor,
						'flavor_id'         => $result->flavor_id,
						'price'             => $result->addon_price,
						'default'			=> $result->default_choice
					);
				}
			else :
				$flavor_data['flavor_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '1');
			$this->db->order_by('addon_suggestions.default_choice', 'DESC');
			// $this->db->group_by('prod_suggestions.suggestion_id');

			$results8 = $this->db->get()->result();

			if (!empty($results8)) :
				foreach ($results8 as $result) {
					$suggestion_flavor_data['suggestion_flavor_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_flavor_data['suggestion_flavor_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '2');
			$this->db->order_by('addon_suggestions.default_choice', 'DESC');
			// $this->db->group_by('prod_suggestions.suggestion_id');

			$results9 = $this->db->get()->result();

			if (!empty($results9)) :
				foreach ($results9 as $result) {
					$suggestion_woc_data['suggestion_woc_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_woc_data['suggestion_woc_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '3');
			$this->db->order_by('addon_suggestions.default_choice', 'DESC');
			// $this->db->group_by('prod_suggestions.suggestion_id');

			$results10 = $this->db->get()->result();

			if (!empty($results10)) :
				foreach ($results10 as $result) {
					$suggestion_tos_data['suggestion_tos_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_tos_data['suggestion_tos_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '4');
			$this->db->order_by('addon_suggestions.default_choice', 'DESC');
			// $this->db->group_by('prod_suggestions.suggestion_id');

			$results11 = $this->db->get()->result();

			if (!empty($results11)) :
				foreach ($results11 as $result) {
					$suggestion_ton_data['suggestion_ton_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_ton_data['suggestion_ton_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '5');
			$this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results12 = $this->db->get()->result();

			if (!empty($results12)) :
				foreach ($results12 as $result) {
					$suggestion_tops_data['suggestion_tops_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_tops_data['suggestion_tops_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '6');
			$this->db->group_by('addon_suggestions.product_suggestion_id');
			$this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results13 = $this->db->get()->result();

			if (!empty($results13)) :
				foreach ($results13 as $result) {
					$suggestion_coi_data['suggestion_coi_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_coi_data['suggestion_coi_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '7');
			$this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results14 = $this->db->get()->result();

			if (!empty($results14)) :
				foreach ($results14 as $result) {
					$suggestion_coslfm_data['suggestion_coslfm_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_coslfm_data['suggestion_coslfm_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '8');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results15 = $this->db->get()->result();

			if (!empty($results15)) :
				foreach ($results15 as $result) {
					$suggestion_sink_data['suggestion_sink_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_sink_data['suggestion_sink_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '9');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results16 = $this->db->get()->result();

			if (!empty($results16)) :
				foreach ($results16 as $result) {
					$suggestion_bcf_data['suggestion_bcf_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_bcf_data['suggestion_bcf_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '10');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results17 = $this->db->get()->result();

			if (!empty($results17)) :
				foreach ($results17 as $result) {
					$suggestion_cc_data['suggestion_cc_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_cc_data['suggestion_cc_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '11');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results18 = $this->db->get()->result();

			if (!empty($results18)) :
				foreach ($results18 as $result) {
					$suggestion_com_data['suggestion_com_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_com_data['suggestion_com_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '12');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results19 = $this->db->get()->result();

			if (!empty($results19)) :
				foreach ($results19 as $result) {
					$suggestion_coft_data['suggestion_coft_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_coft_data['suggestion_coft_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '13');
			$this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results20 = $this->db->get()->result();

			if (!empty($results20)) :
				foreach ($results20 as $result) {
					$suggestion_cymf_data['suggestion_cymf_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_cymf_data['suggestion_cymf_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '14');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results21 = $this->db->get()->result();

			if (!empty($results21)) :
				foreach ($results21 as $result) {
					$suggestion_tomb_data['suggestion_tomb_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_tomb_data['suggestion_tomb_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '15');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results22 = $this->db->get()->result();

			if (!empty($results22)) :
				foreach ($results22 as $result) {
					$suggestion_cosv_data['suggestion_cosv_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_cosv_data['suggestion_cosv_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '16');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results23 = $this->db->get()->result();

			if (!empty($results23)) :
				foreach ($results23 as $result) {
					$suggestion_top_data['suggestion_top_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_top_data['suggestion_top_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '17');
			// $this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results24 = $this->db->get()->result();

			if (!empty($results24)) :
				foreach ($results24 as $result) {
					$suggestion_tocw_data['suggestion_tocw_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_tocw_data['suggestion_tocw_data'][] = array();
			endif;
		}

		foreach ($res as $value) {
			$this->db->select('*');
			$this->db->from('fd_addon_suggestions as addon_suggestions');
			$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
			$this->db->join('fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
			$this->db->join('fd_product_prices as prod_prices', 'prod_prices.product_id = addon_suggestions.product_id');
			$this->db->where('addon_suggestions.product_id', $prodId);
			$this->db->where('fd_suggestions.id', '18');
			$this->db->group_by('addon_suggestions.product_suggestion_id');
			// $this->db->order_by('addon_suggestions.default_choice', 'DESC');

			$results25 = $this->db->get()->result();

			if (!empty($results25)) :
				foreach ($results25 as $result) {
					$suggestion_nameless_data['suggestion_nameless_data'][] = array(
						'price_productid'   => $result->product_id,
						'suggestion_name'   => $result->description,
						'unit'              => $result->uom_id,
						'prod_suggestion_id' => $result->product_suggestion_id,
						'price'				=> $result->addon_price,
						'default'			=> $result->default_choice,
						'suggestion_id'		=> $result->suggestion_id
					);
				}
			else :
				$suggestion_nameless_data['suggestion_nameless_data'][] = array();
			endif;
		}


		// var_dump($choices_data);
		$post_data[] = $addon_sides;
		$post_data[] = $addon_dessert;
		$post_data[] = $addon_drinks;
		$post_data[] = $choices_drinks;
		$post_data[] = $choices_fries;
		$post_data[] = $choices_sides;
		$post_data[] = $price_data;
		$post_data[] = $suggestion_flavor_data;
		$post_data[] = $suggestion_woc_data;
		$post_data[] = $suggestion_tos_data;
		$post_data[] = $suggestion_ton_data;
		$post_data[] = $suggestion_tops_data;
		$post_data[] = $suggestion_coi_data;
		$post_data[] = $suggestion_coslfm_data;
		$post_data[] = $suggestion_sink_data;
		$post_data[] = $suggestion_bcf_data;
		$post_data[] = $suggestion_cc_data;
		$post_data[] = $suggestion_com_data;
		$post_data[] = $suggestion_coft_data;
		$post_data[] = $suggestion_cymf_data;
		$post_data[] = $suggestion_tomb_data;
		$post_data[] = $suggestion_cosv_data;
		$post_data[] = $suggestion_top_data;
		$post_data[] = $suggestion_tocw_data;
		$post_data[] = $suggestion_nameless_data;

		// $post_data[] = $suggestions_data;

		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getSuggestion_mod($prodId)
	{
		$this->db->select('*');
		$this->db->from('fd_addon_suggestions as addon_suggestions');
		$this->db->join('fd_product_suggestions as prod_suggestions', 'prod_suggestions.id = addon_suggestions.product_suggestion_id', 'inner');
		$this->db->join('fd_suggestions as fd_suggestions', 'fd_suggestions.id = prod_suggestions.suggestion_id', 'inner');
		$this->db->where('addon_suggestions.product_id', $prodId);
		$this->db->group_by('prod_suggestions.suggestion_id');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'suggestion_id'  => $value['suggestion_id'],
				'suggestion'     => $value['suggestion']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function addToCartNew_mod(

		$userID,
		$prodId,
		$uomId,
		$_counter,
		$uomPrice,
		$measurement,

		$choiceUomIdDrinks,
		$choiceIdDrinks,
		$choicePriceDrinks,

		$choiceUomIdFries,
		$choiceIdFries,
		$choicePriceFries,

		$choiceUomIdSides,
		$choiceIdSides,
		$choicePriceSides,

		$suggestionIdFlavor,
		$productSuggestionIdFlavor,
		$suggestionPriceFlavor,

		$suggestionIdWoc,
		$productSuggestionIdWoc,
		$suggestionPriceWoc,

		$suggestionIdTos,
		$productSuggestionIdTos,
		$suggestionPriceTos,

		$suggestionIdTon,
		$productSuggestionIdTon,
		$suggestionPriceTon,

		$suggestionIdTops,
		$productSuggestionIdTops,
		$suggestionPriceTops,

		$suggestionIdCoi,
		$productSuggestionIdCoi,
		$suggestionPriceCoi,

		$suggestionIdCoslfm,
		$productSuggestionIdCoslfm,
		$suggestionPriceCoslfm,

		$suggestionIdSink,
		$productSuggestionIdSink,
		$suggestionPriceSink,

		$suggestionIdBcf,
		$productSuggestionIdBcf,
		$suggestionPriceBcf,

		$suggestionIdCc,
		$productSuggestionIdCc,
		$suggestionPriceCc,

		$suggestionIdCom,
		$productSuggestionIdCom,
		$suggestionPriceCom,

		$suggestionIdCoft,
		$productSuggestionIdCoft,
		$suggestionPriceCoft,

		$suggestionIdCymf,
		$productSuggestionIdCymf,
		$suggestionPriceCymf,

		$suggestionIdTomb,
		$productSuggestionIdTomb,
		$suggestionPriceTomb,

		$suggestionIdCosv,
		$productSuggestionIdCosv,
		$suggestionPriceCosv,

		$suggestionIdTop,
		$productSuggestionIdTop,
		$suggestionPriceTop,

		$suggestionIdTocw,
		$productSuggestionIdTocw,
		$suggestionPriceTocw,

		$suggestionIdNameless,
		$productSuggestionIdNameless,
		$suggestionPriceNameless,

		$selectedSideOnPrice,
		$selectedSideItems,
		$selectedSideItemsUom,

		$selectedSideSides,
		$selectedSideDessert,
		$selectedSideDrinks
	) {
		try {
			$this->db->trans_start();

			$temp_addon_id = "";
			$temp_addon_idd = "";
			$temp_choice_id = "";
			$cc_addon = "";
			$sameId = "";

			$sss_id = "";
			$aaa_id = "";
			$ccc_id = "";

			if ($uomId == 0) {
				$uom_id = null;
			} else {
				$uom_id = $uomId;
			}

			echo "ag uom id kay $uom_id\n";

			$search1 = array("[", "]");
			$replacewith1 = array("", "");

			$selectedSideItems = str_replace($search1, $replacewith1, $selectedSideItems);
			$selectedSideItemsUom = str_replace($search1, $replacewith1, $selectedSideItemsUom);
			$selectedSideOnPrice = str_replace($search1, $replacewith1, $selectedSideOnPrice);

			$selectedSideSides = str_replace($search1, $replacewith1, $selectedSideSides);
			$selectedSideDessert = str_replace($search1, $replacewith1, $selectedSideDessert);
			$selectedSideDrinks = str_replace($search1, $replacewith1, $selectedSideDrinks);

			$addon_sides_array  = explode(',', $selectedSideSides);

			$addon_dessert_array = explode(',', $selectedSideDessert);

			$addonn_drinks_array = explode(',', $selectedSideDrinks);

			$addon_idd  = explode(',', $selectedSideItems);

			$addon_uom_idd = explode(',', $selectedSideItemsUom);
			$addon_price  = explode(',', $selectedSideOnPrice);

			$aa_id = count($addon_idd);



			$s = array(" ");
			$r = array();
			$addon_id = str_replace($s, $r, $addon_idd);

			$arr = array_values(array_filter($addon_id));
			$a_id = count($arr);


			$ss = array("null");
			$rr = array();

			$choices_id = [$choiceIdDrinks, $choiceIdFries, $choiceIdSides];
			$choices_uom_id = [$choiceUomIdDrinks, $choiceUomIdFries, $choiceUomIdSides];

			$suggestions_id = [
				$suggestionIdFlavor,
				$suggestionIdWoc,
				$suggestionIdTos,
				$suggestionIdTon,
				$suggestionIdTops,
				$suggestionIdCoi,
				$suggestionIdCoslfm,
				$suggestionIdSink,
				$suggestionIdBcf,
				$suggestionIdCc,
				$suggestionIdCom,
				$suggestionIdCoft,
				$suggestionIdCymf,
				$suggestionIdTomb,
				$suggestionIdCosv,
				$suggestionIdTop,
				$suggestionIdTocw,
				$suggestionIdNameless
			];

			$product_suggestions_id = [
				$productSuggestionIdFlavor,
				$productSuggestionIdWoc,
				$productSuggestionIdTos,
				$productSuggestionIdTon,
				$productSuggestionIdTops,
				$productSuggestionIdCoi,
				$productSuggestionIdCoslfm,
				$productSuggestionIdSink,
				$productSuggestionIdBcf,
				$productSuggestionIdCc,
				$productSuggestionIdCom,
				$productSuggestionIdCoft,
				$productSuggestionIdCymf,
				$productSuggestionIdTomb,
				$productSuggestionIdCosv,
				$productSuggestionIdTop,
				$productSuggestionIdTocw,
				$productSuggestionIdNameless

			];

			$choice_idd = str_replace($ss, $rr, $choices_id);
			$choice_id = array_values(array_filter($choice_idd));
			$c_id = count($choice_id);

			$choice_uom_idd = str_replace($ss, $rr, $choices_uom_id);

			$choice_uom_id = array_values(array_filter($choice_uom_idd));
			$cuom_id = count($choice_uom_id);

			$suggestions_idd = str_replace($ss, $rr, $suggestions_id);
			$suggestion_id = array_values(array_filter($suggestions_idd));
			$s_id = count($suggestion_id);

			$product_suggestions_idd = str_replace($ss, $rr, $product_suggestions_id);
			$product_suggestion_id = array_values(array_filter($product_suggestions_idd));
			$ps_id = count($product_suggestion_id);

			$c_uom_id = str_replace($s, $r, $addon_uom_idd);
			$uom = str_replace($ss, $rr, $c_uom_id);
			$arr = array_values(array_filter($uom));
			$auom_id = count($arr);

			$sides_c = str_replace($s, $r, $addon_sides_array);
			$dessert_c = str_replace($s, $r, $addon_dessert_array);
			$drinks_c = str_replace($s, $r, $addonn_drinks_array);

			$sides_cc = str_replace($ss, $rr, $sides_c);
			$dessert_cc = str_replace($ss, $rr, $dessert_c);
			$drinks_cc = str_replace($ss, $rr, $drinks_c);


			$sides_ccc = array_values(array_filter($sides_cc));
			$dessert_ccc = array_values(array_filter($dessert_cc));
			$drinks_ccc = array_values(array_filter($drinks_cc));


			$sides_count = count($sides_ccc);
			$dessert_count = count($dessert_ccc);
			$drinks_count = count($drinks_ccc);

			$all_addons = [$a_id, $c_id, $s_id];
			$all_addons_select = str_replace($ss, $rr, $all_addons);
			$all_addons_selected = array_values(array_filter($all_addons_select));
			$all_addons_count = count($all_addons_selected);


			echo "ang a_id kay $a_id\n";
			echo "ang c_id kay $c_id\n";
			echo "ang s_id kay $s_id\n";
			echo "ang cuom_id kay $cuom_id\n";
			echo "ang auom_id kay $auom_id\n";
			echo "ang sides_count kay $sides_count\n";
			echo "ang dessert_count kay $dessert_count\n";
			echo "ang drinks_count kay $drinks_count\n";
			echo "ang all addons selected kay $all_addons_count\n";


			$quantity = 0;
			$total = 0.00;

			$this->db->select('*');
			$this->db->from('app_customer_temp_orders as appCart');
			$this->db->where('appCart.customerId', $userID);
			$this->db->where('appCart.product_id', $prodId);
			$this->db->where('appCart.uom_id', $uom_id);
			$query = $this->db->get();
			$res = $query->result_array();

			if ($query->num_rows() > 0) {
				echo "true\n";

				$addonCountIfSame = false;
				$boolAddonsIfExist = false;
				$boolAddonsIfSame = false;
				$tempIdAddons = "";


				$choicesCountIfSame = false;
				$boolChoicesIfExist = false;
				$boolChoicesIfSame = false;
				$tempIdChoices = "";

				$suggestionsCountIfSame = false;
				$boolSuggestionsIfExist = false;
				$boolSuggestionsIfSame = false;
				$tempIdsuggestions = "";

				$addonsResult = array('exist' => $boolAddonsIfExist, 'same' => $boolAddonsIfSame, 'temp_id' => $tempIdAddons);
				$choicesResult = array('exist' => $boolChoicesIfExist, 'same' => $boolChoicesIfSame, 'temp_id' => $tempIdChoices);
				$suggestionsResult = array('exist' => $boolSuggestionsIfExist, 'same' => $boolSuggestionsIfSame, 'temp_id' => $tempIdsuggestions);
				$arrayResult = array('exist' => false, 'temp_id' => null);

				$checkAddonIfEmpty = $this->checkAddonIfEmpty($prodId);

				foreach ($res as $value) {

					$quantity = $value['quantity'];
					$total = $value['total_price'];
				}

				if ($checkAddonIfEmpty == true) {
					echo "way sulod\n";

					if ($uom_id != null) {
						foreach ($res as $key => $value) {
							if (in_array($uomId, $value)) {
								$temp_id = $value['id'];
							}
						}

						$this->db->set('quantity', $_counter + $quantity);
						$this->db->set('total_price', (float) $uomPrice * (int) $_counter + $total);
						$this->db->set('updated_at', date('Y-m-d H:i:s'));
						$this->db->where('customerId', $userID);
						$this->db->where('product_id', $prodId);
						$this->db->where('id', $temp_id);
						$this->db->update('app_customer_temp_orders');
					} else {

						$this->db->set('quantity', $_counter + $quantity);
						$this->db->set('total_price', (float) $uomPrice * (int) $_counter + $total);
						$this->db->set('updated_at', date('Y-m-d H:i:s'));
						$this->db->where('customerId', $userID);
						$this->db->where('product_id', $prodId);
						$this->db->update('app_customer_temp_orders');
					}
				} else if ($s_id == 0 && $a_id == 0 && $c_id == 0) {

					$price = "";

					echo "ug wlay addons\n";

					$this->db->set('quantity', $_counter + $quantity);
					$this->db->set('total_price', (float) $uomPrice * (int) $_counter + $total);
					$this->db->set('updated_at', date('Y-m-d H:i:s'));
					$this->db->where('customerId', $userID);
					$this->db->where('product_id', $prodId);
					$this->db->update('app_customer_temp_orders');
				} else if ($uom_id != null && $checkAddonIfEmpty == false) {
					echo "update ug naay uom with addons\n";
					echo "$uomId\n";


					$temp_id_uom = "";
					$temp_id_uom_addons = "";
					$temp_id_uom_choices = "";
					$temp_id_uom_suggestions = "";
					$temp_id_uom_all = "";
					// $uom_idd ="";

					foreach ($res as $key => $value) {
						if (in_array($uomId, $value)) {
							$temp_id_uom = $value['id'];
						}
					}

					$this->db->select('*');
					$this->db->from('app_customer_temp_orders as temp_orders');
					$this->db->where('customerId', $userID);
					$this->db->where('product_id', $prodId);
					$this->db->group_by('id');
					$query = $this->db->get();
					$tempOrder = $query->result_array();

					$this->db->select('*, count(addon_sides) as c_sides, count(addon_dessert) as c_dessert, count(addon_drinks) as c_drinks, temp_order_id as temp_id');
					$this->db->from('app_customer_temp_order_addons as temp_addons');
					$this->db->where("temp_addons.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
					$this->db->group_by('id');
					$query1 = $this->db->get();
					$addons = $query1->result_array();

					$this->db->select('*, count(choice_id) as c_choice, count(uom_id) as c_uom, temp_order_id as temp_id');
					$this->db->from('app_customer_temp_order_choices as temp_choices');
					$this->db->where("temp_choices.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
					$this->db->or_where('uom_id IS NULL', null, false);
					$this->db->group_by('temp_order_id');
					$query2 = $this->db->get();
					$choices = $query2->result_array();

					$this->db->select('*, count(suggestion_id) as c_suggestion, temp_order_id as temp_id');
					$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
					$this->db->where("temp_suggestions.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
					$this->db->group_by('temp_order_id');
					$query3 = $this->db->get();
					$suggestions = $query3->result_array();

					$db_sides_count = 0;
					$db_dessert_count = 0;
					$db_drinks_count = 0;
					$array_addons = [];
					$qty = 0;



					///for addons
					foreach ($addons as $key => $addonValue) {

						$db_sides_count = $addons[$key]['c_sides'];
						$db_dessert_count = $addons[$key]['c_dessert'];
						$db_drinks_count = $addons[$key]['c_drinks'];

						if ($sides_count == $db_sides_count && $dessert_count == $db_dessert_count && $drinks_count == $db_drinks_count) {
							// echo "found\n";
							$temp_id_uom_addons = $addonValue['temp_id'];
							$addonCountIfSame = true;
						} else {
							// echo "not found\n";
							$addonCountIfSame = false;
							$temp_id_uom_addons = $addonValue['temp_id'];
						}
						echo "temp_id_uom_addons $temp_id_uom_addons\n";

						if ($temp_id_uom_addons == $temp_id_uom) {
							$temp_id_uom_all = $addonValue['temp_id'];
						}

						echo "temp_id_uom_all_addons $temp_id_uom_all\n";

						if ($addonCountIfSame == true) {
							// echo "naay same side\n";

							$checkAddonsIfExist = $this->checkAddonsIfExist(
								$temp_id_uom_all,
								$userID,
								$prodId,
								$addon_idd,
								$addon_uom_idd,
								$addon_price,
								$a_id,
								$auom_id
							);

							$addonsResult = $checkAddonsIfExist;
						} else if ($addonCountIfSame == true || $addonCountIfSame == false) {
							// echo "naay same sides\n";

							$checkAddonsIfExist = $this->checkAddonsIfExist(
								$temp_id_uom_all,
								$userID,
								$prodId,
								$addon_idd,
								$addon_uom_idd,
								$addon_price,
								$a_id,
								$auom_id
							);

							$addonsResult = $checkAddonsIfExist;
						}
					}

					// echo print_r($addonsResult);
					// echo "\n";

					///for choices
					$temp_id_choice = [];
					foreach ($choices as $key => $choiceValue) {

						$temp_with_addons = "";
						$temp_without_addons = "";
						$temp_with = "";
						$temp_without = "";
						$temp_idd = "";
						$temp_id = "";
						$temp_id_p = "";
						$db_choice_count = $choices[$key]['c_choice'];
						$db_uom_count = $choices[$key]['c_uom'];
						$price = $tempOrder[$key]['price'];

						echo "price $price\n";

						if ($price == $uomPrice) {
							$temp_id_p = $choices[$key]['temp_id'];
							echo "temp_id_p $temp_id_p\n";
						}

						if ($c_id == $db_choice_count && $cuom_id == $db_uom_count) {
							$temp_id_uom_choices = $choiceValue['temp_id'];
						}

						$this->db->select('*');
						$this->db->from('app_customer_temp_order_addons');
						$this->db->where('temp_order_id', $temp_id_uom_choices);
						$query4 = $this->db->get();
						$res4 = $query4->result_array();

						if (empty($res4)) {
							$temp_without_addons = $choiceValue['temp_id'];
							echo "temp_without_addons $temp_without_addons\n";
						} else {
							$temp_with_addons = $choiceValue['temp_id'];
							echo "temp_with_addons $temp_with_addons\n";
						}

						if (($temp_without_addons == $temp_id_uom) == $temp_id_p) {
							$temp_without = $choiceValue['temp_id'];

							echo "temp_without $temp_without\n";
						}

						if (($temp_with_addons == $temp_id_uom) == $temp_id_p) {
							$temp_with = $choiceValue['temp_id'];

							echo "temp_with $temp_with\n";
						}

						$checkChoicesIfExist = $this->checkChoicesIfExist(
							$temp_with,
							$temp_without,
							$userID,
							$prodId,
							$choices_id,
							$choices_uom_id,
							$c_id,
							$cuom_id,
							$all_addons_count,
							$a_id
						);

						$choicesResult = $checkChoicesIfExist;

						// echo print_r($suggestionsResult);
						echo "\n";

						$temp_id_choices = $choicesResult['temp_id'];
						$temp_id_choice[] = $temp_id_choices;
					}

					// echo print_r($choicesResult);
					// echo "\n";


					///for suggestion
					$temp_id_suggest = [];
					foreach ($suggestions as $key => $suggestionValue) {

						$temp_with_addons = "";
						$temp_without_addons = "";
						$temp_with = "";
						$temp_without = "";
						$temp_idd = "";
						$temp_id = "";
						$temp_id_p = "";
						$db_suggestion_count = $suggestions[$key]['c_suggestion'];
						$price = $tempOrder[$key]['price'];

						echo "price $price\n";

						if ($price == $uomPrice) {
							$temp_id_p = $suggestions[$key]['temp_id'];
							echo "temp_id_p $temp_id_p\n";
						}

						if ($s_id == $db_suggestion_count) {
							// echo "found count for suggestion\n";
							$temp_id_uom_suggestions = $suggestions[$key]['temp_id'];
							echo "temp_id $temp_id\n";
						}

						$this->db->select('*');
						$this->db->from('app_customer_temp_order_addons');
						$this->db->where('temp_order_id', $temp_id_uom_suggestions);
						$query4 = $this->db->get();
						$res4 = $query4->result_array();

						if (empty($res4)) {
							$temp_without_addons = $suggestionValue['temp_id'];
							echo "temp_without_addons $temp_without_addons\n";
						} else {
							$temp_with_addons = $suggestionValue['temp_id'];
							echo "temp_with_addons $temp_with_addons\n";
						}


						if (($temp_without_addons == $temp_id_uom) == $temp_id_p) {
							$temp_without = $suggestionValue['temp_id'];

							echo "temp_without $temp_without\n";
						}

						if (($temp_with_addons == $temp_id_uom) == $temp_id_p) {
							$temp_with = $suggestionValue['temp_id'];

							echo "temp_with $temp_with\n";
						}

						$checkSuggestionsIfExist = $this->checkSuggestionsIfExist(
							$temp_with,
							$temp_without,
							$userID,
							$prodId,
							$suggestions_id,
							$product_suggestions_id,
							$s_id,
							$ps_id,
							$all_addons_count,
							$a_id
						);

						$suggestionsResult = $checkSuggestionsIfExist;

						echo print_r($suggestionsResult);
						echo "\n";

						$temp_id_suggestions = $suggestionsResult['temp_id'];
						$temp_id_suggest[] = $temp_id_suggestions;
					}

					// echo "temp_id_suggestions $temp_id_suggestions\n";
					// echo print_r($temp_id_suggest);

					// echo "temp_id_choices $temp_id_choices\n";
					// echo print_r($temp_id_choice);

					echo "\n";

					$temp_idd_suggest = str_replace($s, $r, $temp_id_suggest);
					$temp_idd_s = implode("", $temp_idd_suggest);
					echo "$temp_idd_s\n";

					$temp_idd_choice = str_replace($s, $r, $temp_id_choice);
					$temp_idd_c = implode("", $temp_idd_choice);
					echo "$temp_idd_c\n";


					$temp_id_addons = $addonsResult['temp_id'];
					echo "$temp_id_addons\n";

					$db_all_addons = [$temp_idd_s, $temp_idd_c, $temp_id_addons];
					$db_all_addons_select = str_replace($ss, $rr, $db_all_addons);
					$db_all_addons_selected = array_values(array_filter($db_all_addons_select));
					$db_all_addons_count = count($db_all_addons_selected);
					$db_all_addons_selected_un = array_unique($db_all_addons_selected);
					$db_all_addons_selected_im = implode("", $db_all_addons_selected_un);
					echo "$db_all_addons_selected_im\n";
					// echo print_r($db_all_addons_selected);
					echo "ang db all addons count kay $db_all_addons_count\n";

					if ($all_addons_count == $db_all_addons_count) {
						echo "update with uom\n";

						$this->updateCartQty_mod2($db_all_addons_selected_im, $_counter);
					} else if ($all_addons_selected != $db_all_addons_selected) {
						echo "add new\n";

						$this->addNew(
							$userID,
							$prodId,
							$uomId,
							$_counter,
							$uomPrice,
							$measurement,

							$choiceUomIdDrinks,
							$choiceIdDrinks,
							$choicePriceDrinks,

							$choiceUomIdFries,
							$choiceIdFries,
							$choicePriceFries,

							$choiceUomIdSides,
							$choiceIdSides,
							$choicePriceSides,

							$suggestionIdFlavor,
							$productSuggestionIdFlavor,
							$suggestionPriceFlavor,

							$suggestionIdWoc,
							$productSuggestionIdWoc,
							$suggestionPriceWoc,

							$suggestionIdTos,
							$productSuggestionIdTos,
							$suggestionPriceTos,

							$suggestionIdTon,
							$productSuggestionIdTon,
							$suggestionPriceTon,

							$suggestionIdTops,
							$productSuggestionIdTops,
							$suggestionPriceTops,

							$suggestionIdCoi,
							$productSuggestionIdCoi,
							$suggestionPriceCoi,

							$suggestionIdCoslfm,
							$productSuggestionIdCoslfm,
							$suggestionPriceCoslfm,

							$suggestionIdSink,
							$productSuggestionIdSink,
							$suggestionPriceSink,

							$suggestionIdBcf,
							$productSuggestionIdBcf,
							$suggestionPriceBcf,

							$suggestionIdCc,
							$productSuggestionIdCc,
							$suggestionPriceCc,

							$suggestionIdCom,
							$productSuggestionIdCom,
							$suggestionPriceCom,

							$suggestionIdCoft,
							$productSuggestionIdCoft,
							$suggestionPriceCoft,

							$suggestionIdCymf,
							$productSuggestionIdCymf,
							$suggestionPriceCymf,

							$suggestionIdTomb,
							$productSuggestionIdTomb,
							$suggestionPriceTomb,

							$suggestionIdCosv,
							$productSuggestionIdCosv,
							$suggestionPriceCosv,

							$suggestionIdTop,
							$productSuggestionIdTop,
							$suggestionPriceTop,

							$suggestionIdTocw,
							$productSuggestionIdTocw,
							$suggestionPriceTocw,

							$suggestionIdNameless,
							$productSuggestionIdNameless,
							$suggestionPriceNameless,

							$selectedSideOnPrice,
							$selectedSideItems,
							$selectedSideItemsUom,

							$selectedSideSides,
							$selectedSideDessert,
							$selectedSideDrinks
						);
					}
				} else if ($uom_id == null) {

					$price = "";

					echo "ug naay mga addons\n";

					$this->db->select('*');
					$this->db->from('app_customer_temp_orders as temp_orders');
					$this->db->where('customerId', $userID);
					$this->db->where('product_id', $prodId);
					$this->db->group_by('id');
					$query = $this->db->get();
					$tempOrder = $query->result_array();

					$this->db->select('*, count(addon_sides) as c_sides, count(addon_dessert) as c_dessert, count(addon_drinks) as c_drinks, temp_order_id as temp_id');
					$this->db->from('app_customer_temp_order_addons as temp_addons');
					$this->db->where("temp_addons.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
					$this->db->group_by('id');
					$query1 = $this->db->get();
					$addons = $query1->result_array();

					$this->db->select('*, count(choice_id) as c_choice, count(uom_id) as c_uom, temp_order_id as temp_id');
					$this->db->from('app_customer_temp_order_choices as temp_choices');
					$this->db->where("temp_choices.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
					$this->db->or_where('uom_id IS NULL', null, false);
					$this->db->group_by('temp_order_id');
					$query2 = $this->db->get();
					$choices = $query2->result_array();

					$this->db->select('*, count(suggestion_id) as c_suggestion, temp_order_id as temp_id');
					$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
					$this->db->where("temp_suggestions.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
					$this->db->group_by('temp_order_id');
					$query3 = $this->db->get();
					$suggestions = $query3->result_array();

					$db_sides_count = 0;
					$db_dessert_count = 0;
					$db_drinks_count = 0;
					$array_addons = [];

					///for addons
					foreach ($addons as $key => $addonValue) {

						$db_sides_count = $addons[$key]['c_sides'];
						$db_dessert_count = $addons[$key]['c_dessert'];
						$db_drinks_count = $addons[$key]['c_drinks'];

						if ($sides_count == $db_sides_count && $dessert_count == $db_dessert_count && $drinks_count == $db_drinks_count) {
							// echo "found\n";
							$temp_id = $addonValue['temp_id'];
							$addonCountIfSame = true;
						} else {
							// echo "not found\n";
							$addonCountIfSame = false;
							$temp_id = $addonValue['temp_id'];
						}
						echo "temp_id_addons $temp_id\n";

						if ($addonCountIfSame == true) {
							// echo "naay same side\n";

							$checkAddonsIfExist = $this->checkAddonsIfExist(
								$temp_id,
								$userID,
								$prodId,
								$addon_idd,
								$addon_uom_idd,
								$addon_price,
								$a_id,
								$auom_id
							);

							$addonsResult = $checkAddonsIfExist;
						} else if ($addonCountIfSame == true || $addonCountIfSame == false) {
							// echo "naay same sides\n";

							$checkAddonsIfExist = $this->checkAddonsIfExist(
								$temp_id,
								$userID,
								$prodId,
								$addon_idd,
								$addon_uom_idd,
								$addon_price,
								$a_id,
								$auom_id
							);

							$addonsResult = $checkAddonsIfExist;
						}
					}

					// echo print_r($addonsResult);
					// echo "\n";

					///for choices
					$temp_id_choice = [];
					foreach ($choices as $key => $choiceValue) {

						$temp_with_addons = "";
						$temp_without_addons = "";
						$temp_with = "";
						$temp_without = "";
						$temp_idd = "";
						$temp_id = "";
						$temp_id_p = "";
						$db_choice_count = $choices[$key]['c_choice'];
						$db_uom_count = $choices[$key]['c_uom'];
						$price = $tempOrder[$key]['price'];

						echo "price $price\n";

						if ($c_id == $db_choice_count && $cuom_id == $db_uom_count) {
							$temp_id = $choiceValue['temp_id'];
						}

						if ($price == $uomPrice) {
							$temp_id_p = $choices[$key]['temp_id'];
							echo "temp_id_p $temp_id_p\n";
						}

						$this->db->select('*');
						$this->db->from('app_customer_temp_order_addons');
						$this->db->where('temp_order_id', $temp_id);
						$query4 = $this->db->get();
						$res4 = $query4->result_array();

						if (empty($res4)) {
							$temp_without_addons = $choiceValue['temp_id'];
							echo "temp_without_addons $temp_without_addons\n";
						} else {
							$temp_with_addons = $choiceValue['temp_id'];
							echo "temp_with_addons $temp_with_addons\n";
						}

						if (($temp_without_addons == $temp_id) == $temp_id_p) {
							$temp_without = $choiceValue['temp_id'];

							echo "temp_without $temp_without\n";
						}

						if (($temp_with_addons == $temp_id) == $temp_id_p) {
							$temp_with = $choiceValue['temp_id'];

							echo "temp_with $temp_with\n";
						}

						$checkChoicesIfExist = $this->checkChoicesIfExist(
							$temp_with,
							$temp_without,
							$userID,
							$prodId,
							$choices_id,
							$choices_uom_id,
							$c_id,
							$cuom_id,
							$all_addons_count,
							$a_id
						);

						$choicesResult = $checkChoicesIfExist;

						// echo print_r($suggestionsResult);
						echo "\n";

						$temp_id_choices = $choicesResult['temp_id'];
						$temp_id_choice[] = $temp_id_choices;
					}

					// echo print_r($choicesResult);
					// echo "\n";


					///for suggestion
					$temp_id_suggest = [];
					foreach ($suggestions as $key => $suggestionValue) {

						$temp_with_addons = "";
						$temp_without_addons = "";
						$temp_with = "";
						$temp_without = "";
						$temp_idd = "";
						$temp_id = "";
						$temp_id_p = "";
						$db_suggestion_count = $suggestions[$key]['c_suggestion'];
						$price = $tempOrder[$key]['price'];

						echo "price $price\n";

						if ($price == $uomPrice) {
							$temp_id_p = $suggestions[$key]['temp_id'];
							echo "temp_id_p $temp_id_p\n";
						}


						if ($s_id == $db_suggestion_count) {
							// echo "found count for suggestion\n";
							$temp_id = $suggestions[$key]['temp_id'];
							echo "temp_id $temp_id\n";
						}

						$this->db->select('*');
						$this->db->from('app_customer_temp_order_addons');
						$this->db->where('temp_order_id', $temp_id);
						$query4 = $this->db->get();
						$res4 = $query4->result_array();

						if (empty($res4)) {
							$temp_without_addons = $suggestionValue['temp_id'];
							echo "temp_without_addons $temp_without_addons\n";
						} else {
							$temp_with_addons = $suggestionValue['temp_id'];
							echo "temp_with_addons $temp_with_addons\n";
						}


						if (($temp_without_addons == $temp_id) == $temp_id_p) {
							$temp_without = $suggestionValue['temp_id'];

							echo "temp_without $temp_without\n";
						}

						if (($temp_with_addons == $temp_id) == $temp_id_p) {
							$temp_with = $suggestionValue['temp_id'];

							echo "temp_with $temp_with\n";
						}

						$checkSuggestionsIfExist = $this->checkSuggestionsIfExist(
							$temp_with,
							$temp_without,
							$userID,
							$prodId,
							$suggestions_id,
							$product_suggestions_id,
							$s_id,
							$ps_id,
							$all_addons_count,
							$a_id
						);

						$suggestionsResult = $checkSuggestionsIfExist;

						echo print_r($suggestionsResult);
						echo "\n";

						$temp_id_suggestions = $suggestionsResult['temp_id'];
						$temp_id_suggest[] = $temp_id_suggestions;
					}

					// echo "temp_id_suggestions $temp_id_suggestions\n";
					// echo print_r($temp_id_suggest);

					// echo "temp_id_choices $temp_id_choices\n";
					// echo print_r($temp_id_choice);

					echo "\n";

					$temp_idd_suggest = str_replace($s, $r, $temp_id_suggest);
					$temp_idd_s = implode("", $temp_idd_suggest);
					echo "$temp_idd_s\n";

					$temp_idd_choice = str_replace($s, $r, $temp_id_choice);
					$temp_idd_c = implode("", $temp_idd_choice);
					echo "$temp_idd_c\n";


					$temp_id_addons = $addonsResult['temp_id'];
					echo "$temp_id_addons\n";

					$db_all_addons = [$temp_idd_s, $temp_idd_c, $temp_id_addons];
					$db_all_addons_select = str_replace($ss, $rr, $db_all_addons);
					$db_all_addons_selected = array_values(array_filter($db_all_addons_select));
					$db_all_addons_count = count($db_all_addons_selected);
					$db_all_addons_selected_un = array_unique($db_all_addons_selected);
					$db_all_addons_selected_im = implode("", $db_all_addons_selected_un);
					echo "$db_all_addons_selected_im\n";
					echo print_r($db_all_addons_selected);
					echo "ang db all addons count kay $db_all_addons_count\n";

					if ($all_addons_count == $db_all_addons_count) {
						echo "update\n";

						$this->updateCartQty_mod2($db_all_addons_selected_im, $_counter);
					} else if ($all_addons_selected != $db_all_addons_selected) {
						echo "add new\n";

						$this->addNew(
							$userID,
							$prodId,
							$uomId,
							$_counter,
							$uomPrice,
							$measurement,

							$choiceUomIdDrinks,
							$choiceIdDrinks,
							$choicePriceDrinks,

							$choiceUomIdFries,
							$choiceIdFries,
							$choicePriceFries,

							$choiceUomIdSides,
							$choiceIdSides,
							$choicePriceSides,

							$suggestionIdFlavor,
							$productSuggestionIdFlavor,
							$suggestionPriceFlavor,

							$suggestionIdWoc,
							$productSuggestionIdWoc,
							$suggestionPriceWoc,

							$suggestionIdTos,
							$productSuggestionIdTos,
							$suggestionPriceTos,

							$suggestionIdTon,
							$productSuggestionIdTon,
							$suggestionPriceTon,

							$suggestionIdTops,
							$productSuggestionIdTops,
							$suggestionPriceTops,

							$suggestionIdCoi,
							$productSuggestionIdCoi,
							$suggestionPriceCoi,

							$suggestionIdCoslfm,
							$productSuggestionIdCoslfm,
							$suggestionPriceCoslfm,

							$suggestionIdSink,
							$productSuggestionIdSink,
							$suggestionPriceSink,

							$suggestionIdBcf,
							$productSuggestionIdBcf,
							$suggestionPriceBcf,

							$suggestionIdCc,
							$productSuggestionIdCc,
							$suggestionPriceCc,

							$suggestionIdCom,
							$productSuggestionIdCom,
							$suggestionPriceCom,

							$suggestionIdCoft,
							$productSuggestionIdCoft,
							$suggestionPriceCoft,

							$suggestionIdCymf,
							$productSuggestionIdCymf,
							$suggestionPriceCymf,

							$suggestionIdTomb,
							$productSuggestionIdTomb,
							$suggestionPriceTomb,

							$suggestionIdCosv,
							$productSuggestionIdCosv,
							$suggestionPriceCosv,

							$suggestionIdTop,
							$productSuggestionIdTop,
							$suggestionPriceTop,

							$suggestionIdTocw,
							$productSuggestionIdTocw,
							$suggestionPriceTocw,

							$suggestionIdNameless,
							$productSuggestionIdNameless,
							$suggestionPriceNameless,

							$selectedSideOnPrice,
							$selectedSideItems,
							$selectedSideItemsUom,

							$selectedSideSides,
							$selectedSideDessert,
							$selectedSideDrinks
						);
					}
				}
			} else {
				echo "false";

				$this->addNew(
					$userID,
					$prodId,
					$uomId,
					$_counter,
					$uomPrice,
					$measurement,

					$choiceUomIdDrinks,
					$choiceIdDrinks,
					$choicePriceDrinks,

					$choiceUomIdFries,
					$choiceIdFries,
					$choicePriceFries,

					$choiceUomIdSides,
					$choiceIdSides,
					$choicePriceSides,

					$suggestionIdFlavor,
					$productSuggestionIdFlavor,
					$suggestionPriceFlavor,

					$suggestionIdWoc,
					$productSuggestionIdWoc,
					$suggestionPriceWoc,

					$suggestionIdTos,
					$productSuggestionIdTos,
					$suggestionPriceTos,

					$suggestionIdTon,
					$productSuggestionIdTon,
					$suggestionPriceTon,

					$suggestionIdTops,
					$productSuggestionIdTops,
					$suggestionPriceTops,

					$suggestionIdCoi,
					$productSuggestionIdCoi,
					$suggestionPriceCoi,

					$suggestionIdCoslfm,
					$productSuggestionIdCoslfm,
					$suggestionPriceCoslfm,

					$suggestionIdSink,
					$productSuggestionIdSink,
					$suggestionPriceSink,

					$suggestionIdBcf,
					$productSuggestionIdBcf,
					$suggestionPriceBcf,

					$suggestionIdCc,
					$productSuggestionIdCc,
					$suggestionPriceCc,

					$suggestionIdCom,
					$productSuggestionIdCom,
					$suggestionPriceCom,

					$suggestionIdCoft,
					$productSuggestionIdCoft,
					$suggestionPriceCoft,

					$suggestionIdCymf,
					$productSuggestionIdCymf,
					$suggestionPriceCymf,

					$suggestionIdTomb,
					$productSuggestionIdTomb,
					$suggestionPriceTomb,

					$suggestionIdCosv,
					$productSuggestionIdCosv,
					$suggestionPriceCosv,

					$suggestionIdTop,
					$productSuggestionIdTop,
					$suggestionPriceTop,

					$suggestionIdTocw,
					$productSuggestionIdTocw,
					$suggestionPriceTocw,

					$suggestionIdNameless,
					$productSuggestionIdNameless,
					$suggestionPriceNameless,

					$selectedSideOnPrice,
					$selectedSideItems,
					$selectedSideItemsUom,

					$selectedSideSides,
					$selectedSideDessert,
					$selectedSideDrinks
				);
			}

			$this->db->trans_complete();
		} catch (\Exception $th) {
			$this->db->trans_rollback();
		}
	}

	private function ifExist($addon_side, $addon_sides)
	{
		if ($addon_side == $addon_sides) {

			return true;
		} else {
			return false;
		}
	}

	private function addNew(
		$userID,
		$prodId,
		$uomId,
		$_counter,
		$uomPrice,
		$measurement,

		$choiceUomIdDrinks,
		$choiceIdDrinks,
		$choicePriceDrinks,

		$choiceUomIdFries,
		$choiceIdFries,
		$choicePriceFries,

		$choiceUomIdSides,
		$choiceIdSides,
		$choicePriceSides,

		$suggestionIdFlavor,
		$productSuggestionIdFlavor,
		$suggestionPriceFlavor,

		$suggestionIdWoc,
		$productSuggestionIdWoc,
		$suggestionPriceWoc,

		$suggestionIdTos,
		$productSuggestionIdTos,
		$suggestionPriceTos,

		$suggestionIdTon,
		$productSuggestionIdTon,
		$suggestionPriceTon,

		$suggestionIdTops,
		$productSuggestionIdTops,
		$suggestionPriceTops,

		$suggestionIdCoi,
		$productSuggestionIdCoi,
		$suggestionPriceCoi,

		$suggestionIdCoslfm,
		$productSuggestionIdCoslfm,
		$suggestionPriceCoslfm,

		$suggestionIdSink,
		$productSuggestionIdSink,
		$suggestionPriceSink,

		$suggestionIdBcf,
		$productSuggestionIdBcf,
		$suggestionPriceBcf,

		$suggestionIdCc,
		$productSuggestionIdCc,
		$suggestionPriceCc,

		$suggestionIdCom,
		$productSuggestionIdCom,
		$suggestionPriceCom,

		$suggestionIdCoft,
		$productSuggestionIdCoft,
		$suggestionPriceCoft,

		$suggestionIdCymf,
		$productSuggestionIdCymf,
		$suggestionPriceCymf,

		$suggestionIdTomb,
		$productSuggestionIdTomb,
		$suggestionPriceTomb,

		$suggestionIdCosv,
		$productSuggestionIdCosv,
		$suggestionPriceCosv,

		$suggestionIdTop,
		$productSuggestionIdTop,
		$suggestionPriceTop,

		$suggestionIdTocw,
		$productSuggestionIdTocw,
		$suggestionPriceTocw,

		$suggestionIdNameless,
		$productSuggestionIdNameless,
		$suggestionPriceNameless,

		$selectedSideOnPrice,
		$selectedSideItems,
		$selectedSideItemsUom,

		$selectedSideSides,
		$selectedSideDessert,
		$selectedSideDrinks
	) {

		if ($uomId == 0) {
			$uomId = null;
		}

		if ($choiceUomIdDrinks == 0) {
			$choiceUomIdDrinks = null;
		}

		if ($choiceUomIdFries == 0) {
			$choiceUomIdFries = null;
		}

		if ($choiceUomIdSides == 0) {
			$choiceUomIdSides = null;
		}

		$datamain = array(
			'customerId'  => $userID,
			'product_id'  => $prodId,
			'uom_id'	  => $uomId,
			'quantity'    => $_counter,
			'price'       => $uomPrice,
			'measurement' => $measurement,
			'total_price' => (float) $uomPrice * (int) $_counter,
			'created_at'   => date('Y-m-d H:i:s'),
			'updated_at'  => date('Y-m-d H:i:s')
		);

		$this->db->insert('app_customer_temp_orders', $datamain);

		$insert_id = $this->db->insert_id();

		$addon_sideItems_array  = explode(',', $selectedSideItems);

		$addon_oums_array = explode(',', $selectedSideItemsUom);

		$addonn_price_array = explode(',', $selectedSideOnPrice);

		$addon_sides_array  = explode(',', $selectedSideSides);

		$addon_dessert_array = explode(',', $selectedSideDessert);

		$addonn_drinks_array = explode(',', $selectedSideDrinks);

		$totalAdChoice = (float) $uomPrice;

		if ($selectedSideItems != 0) {

			for ($x = 0; $x < count($addon_sideItems_array); $x++) {

				$side_id = $addon_sideItems_array[$x];
				$uom_id  =  $addon_oums_array[$x] == 0 ? null : $addon_oums_array[$x];
				$add_price = $addonn_price_array[$x];

				$addon_sides = $addon_sides_array[$x]  == 0 ? null : $addon_sides_array[$x];;
				$addon_dessert = $addon_dessert_array[$x]  == 0 ? null : $addon_dessert_array[$x];;
				$addon_drinks = $addonn_drinks_array[$x]  == 0 ? null : $addonn_drinks_array[$x];;

				// if($side_id == 'null' || $uom_id == 'null' || $add_price == 'null'){
				// 	$side_id = null;
				// 	$uom_id = null;
				// 	$add_price = null;
				// }

				$addons = array(
					'temp_order_id' => $insert_id,
					'addon_id' => $side_id,
					'uom_id' =>  $uom_id,
					'addon_sides' => $addon_sides,
					'addon_dessert' => $addon_dessert,
					'addon_drinks' => $addon_drinks,
					'addon_price' => $add_price,
					'created_at'   => date('Y-m-d H:i:s'),
					'updated_at'  => date('Y-m-d H:i:s')
				);

				$this->db->insert('app_customer_temp_order_addons', $addons);

				$totalAdChoice += $add_price;
			}
		}
		if ($choiceIdDrinks != 0) {
			$choiceD = array(
				'temp_order_id' => $insert_id,
				'choice_id' => $choiceIdDrinks,
				'uom_id' =>  $choiceUomIdDrinks,
				'addon_price' => $choicePriceDrinks,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_choices', $choiceD);

			$totalAdChoice += $choicePriceDrinks;
		}

		if ($choiceIdFries != 0) {
			$choiceF = array(
				'temp_order_id' => $insert_id,
				'choice_id' => $choiceIdFries,
				'uom_id' =>  $choiceUomIdFries,
				'addon_price' => $choicePriceFries,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_choices', $choiceF);

			$totalAdChoice += $choicePriceFries;
		}

		if ($choiceIdSides != 0) {
			$choiceS = array(
				'temp_order_id' => $insert_id,
				'choice_id' => $choiceIdSides,
				'uom_id' =>  $choiceUomIdSides,
				'addon_price' => $choicePriceSides,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_choices', $choiceS);

			$totalAdChoice += $choicePriceSides;
		}

		if ($suggestionIdFlavor != 0) {
			$suggestionF = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdFlavor,
				'product_suggestion_id' =>  $productSuggestionIdFlavor,
				'addon_price' => $suggestionPriceFlavor,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionF);

			$totalAdChoice += $suggestionPriceFlavor;
		}


		if ($suggestionIdWoc != 0) {
			$suggestionWoc = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdWoc,
				'product_suggestion_id' =>  $productSuggestionIdWoc,
				'addon_price' => $suggestionPriceWoc,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionWoc);

			$totalAdChoice += $suggestionPriceWoc;
		}

		if ($suggestionIdTos != 0) {
			$suggestionTos = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdTos,
				'product_suggestion_id' =>  $productSuggestionIdTos,
				'addon_price' => $suggestionPriceTos,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionTos);

			$totalAdChoice += $suggestionPriceTos;
		}

		if ($suggestionIdTon != 0) {
			$suggestionTon = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdTon,
				'product_suggestion_id' =>  $productSuggestionIdTon,
				'addon_price' => $suggestionPriceTon,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionTon);

			$totalAdChoice += $suggestionPriceTon;
		}

		if ($suggestionIdTops != 0) {
			$suggestionTops = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdTops,
				'product_suggestion_id' =>  $productSuggestionIdTops,
				'addon_price' => $suggestionPriceTops,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionTops);

			$totalAdChoice += $suggestionPriceTops;
		}

		if ($suggestionIdCoi != 0) {
			$suggestionCoi = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCoi,
				'product_suggestion_id' =>  $productSuggestionIdCoi,
				'addon_price' => $suggestionPriceCoi,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCoi);

			$totalAdChoice += $suggestionPriceCoi;
		}

		if ($suggestionIdCoslfm != 0) {
			$suggestionCoslfm = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCoslfm,
				'product_suggestion_id' =>  $productSuggestionIdCoslfm,
				'addon_price' => $suggestionPriceCoslfm,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCoslfm);

			$totalAdChoice += $suggestionPriceCoslfm;
		}

		if ($suggestionIdSink != 0) {
			$suggestionSink = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdSink,
				'product_suggestion_id' =>  $productSuggestionIdSink,
				'addon_price' => $suggestionPriceSink,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionSink);

			$totalAdChoice += $suggestionPriceSink;
		}

		if ($suggestionIdBcf != 0) {
			$suggestionBcf = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdBcf,
				'product_suggestion_id' =>  $productSuggestionIdBcf,
				'addon_price' => $suggestionPriceBcf,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionBcf);

			$totalAdChoice += $suggestionPriceBcf;
		}

		if ($suggestionIdCc != 0) {
			$suggestionCc = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCc,
				'product_suggestion_id' =>  $productSuggestionIdCc,
				'addon_price' => $suggestionPriceCc,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCc);

			$totalAdChoice += $suggestionPriceCc;
		}

		if ($suggestionIdCom != 0) {
			$suggestionCom = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCom,
				'product_suggestion_id' =>  $productSuggestionIdCom,
				'addon_price' => $suggestionPriceCom,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCom);

			$totalAdChoice += $suggestionPriceCom;
		}

		if ($suggestionIdCoft != 0) {
			$suggestionCoft = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCoft,
				'product_suggestion_id' =>  $productSuggestionIdCoft,
				'addon_price' => $suggestionPriceCoft,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCoft);

			$totalAdChoice += $suggestionPriceCoft;
		}

		if ($suggestionIdCymf != 0) {
			$suggestionCymf = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCymf,
				'product_suggestion_id' =>  $productSuggestionIdCymf,
				'addon_price' => $suggestionPriceCymf,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCymf);

			$totalAdChoice += $suggestionPriceCymf;
		}

		if ($suggestionIdTomb != 0) {
			$suggestionTomb = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdTomb,
				'product_suggestion_id' =>  $productSuggestionIdTomb,
				'addon_price' => $$suggestionPriceTomb,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionTomb);

			$totalAdChoice += $suggestionPriceTomb;
		}

		if ($suggestionIdCosv != 0) {
			$suggestionCosv = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdCosv,
				'product_suggestion_id' =>  $productSuggestionIdCosv,
				'addon_price' => $suggestionPriceCosv,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionCosv);

			$totalAdChoice += $suggestionPriceCosv;
		}

		if ($suggestionIdTop != 0) {
			$suggestionTop = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdTop,
				'product_suggestion_id' =>  $productSuggestionIdTop,
				'addon_price' => $suggestionPriceTop,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionTop);

			$totalAdChoice += $suggestionPriceTop;
		}

		if ($suggestionIdTocw != 0) {
			$suggestionTocw = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdTocw,
				'product_suggestion_id' =>  $productSuggestionIdTocw,
				'addon_price' => $suggestionPriceTocw,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionTocw);

			$totalAdChoice += $suggestionPriceTocw;
		}

		if ($suggestionIdNameless != 0) {
			$suggestionNameless = array(
				'temp_order_id' => $insert_id,
				'suggestion_id' => $suggestionIdNameless,
				'product_suggestion_id' =>  $productSuggestionIdNameless,
				'addon_price' => $suggestionPriceNameless,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_customer_temp_order_suggestions', $suggestionNameless);

			$totalAdChoice += $suggestionPriceNameless;
		}


		$x = $totalAdChoice * (int) $_counter;

		$this->db->set('total_price', $x);
		$this->db->where('id', $insert_id);
		$this->db->update('app_customer_temp_orders');
	}

	private function checkAddonIfEmpty($product_id)
	{

		$this->db->select('*');
		$this->db->from('fd_product_addons');
		$this->db->where('fd_product_addons.product_id', $product_id);
		$query = $this->db->get();
		$res1 = $query->row_array();

		$this->db->select('*');
		$this->db->from('fd_product_choices');
		$this->db->where('fd_product_choices.product_id', $product_id);
		$query = $this->db->get();
		$res2 = $query->row_array();

		$this->db->select('*');
		$this->db->from('fd_addon_suggestions');
		$this->db->where('fd_addon_suggestions.product_id', $product_id);
		$query = $this->db->get();
		$res3 = $query->row_array();

		if (empty($res1) && empty($res2) && empty($res3)) {
			return true;
		} else {
			return false;
		}
	}

	private function checkSuggestionsIfExist(
		$temp_with,
		$temp_without,
		$userID,
		$prodId,
		$suggestions_id,
		$product_suggestions_id,
		$s_id,
		$ps_id,
		$all_addons_count,
		$a_id
	) {



		$c_suggestion = "";
		$cc_suggestion = "";
		$c_prod_suggestion = "";
		$cc_prod_suggestion = "";

		$tempIdSuggestion = "";
		$tempIdProdSuggestion = "";
		$tempIdSuggestions = "";
		$tempIdSuggestionss = "";

		$ifExist = false;
		$ifSame = false;
		$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdSuggestions];

		if ($a_id == 0) {

			// echo "update without addons\n";

			$this->db->select('*, count(suggestion_id) as c_suggestion, count(product_suggestion_id) as c_prod_suggestion, temp_order_id as temp_id');
			$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_without);
			$query = $this->db->get();
			$res = $query->result_array();

			if (!empty($res)) {
				$ifExist = true;
			}

			$this->db->select('*, count(suggestion_id) as cc_suggestion, temp_order_id as temp_id');
			$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_without);
			$this->db->where_in('suggestion_id', $suggestions_id);
			$query1 = $this->db->get();
			$res1 = $query1->result_array();

			$this->db->select('*, count(product_suggestion_id) as cc_prod_suggestion, temp_order_id as temp_idf');
			$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_without);
			$this->db->where_in('product_suggestion_id', $product_suggestions_id);
			$query2 = $this->db->get();
			$res2 = $query2->result_array();

			foreach ($res as $key => $suggestionValue) {

				$c_suggestion = $res[$key]['c_suggestion'];
				$cc_suggestion = $res1[$key]['cc_suggestion'];
				$cc_product_suggestion = $res2[$key]['cc_prod_suggestion'];

				if (($c_suggestion && $cc_suggestion) == $s_id) {
					$tempIdSuggestion = $res[$key]['temp_id'];
					// echo "tempIdSuggestion $tempIdSuggestion\n";
				}

				if ($cc_product_suggestion == $ps_id) {
					$tempIdProdSuggestion = $res[$key]['temp_id'];
					// echo "tempIdProdSuggestion $tempIdProdSuggestion\n";
				}


				if (($tempIdSuggestion == $tempIdProdSuggestion)) {
					$tempIdSuggestions = $res[$key]['temp_id'];
					// echo "tempIdSuggestions $tempIdSuggestions\n";

					$ifSame = true;
				} else {

					$tempIdSuggestions = null;
					$ifSame = false;
					echo "way same suggestion temp id\n";
				}
			}

			$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdSuggestions];
			return $result;
		} else {

			// echo "update with addons\n";

			$this->db->select('*, count(suggestion_id) as c_suggestion, count(product_suggestion_id) as c_prod_suggestion, temp_order_id as temp_id');
			$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_with);
			$query = $this->db->get();
			$res = $query->result_array();

			if (!empty($res)) {
				$ifExist = true;
			}

			$this->db->select('*, count(suggestion_id) as cc_suggestion, temp_order_id as temp_id');
			$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_with);
			$this->db->where_in('suggestion_id', $suggestions_id);
			$query1 = $this->db->get();
			$res1 = $query1->result_array();

			$this->db->select('*, count(product_suggestion_id) as cc_prod_suggestion, temp_order_id as temp_idf');
			$this->db->from('app_customer_temp_order_suggestions as temp_suggestions');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_with);
			$this->db->where_in('product_suggestion_id', $product_suggestions_id);
			$query2 = $this->db->get();
			$res2 = $query2->result_array();

			foreach ($res as $key => $suggestionValue) {

				$c_suggestion = $res[$key]['c_suggestion'];
				$cc_suggestion = $res1[$key]['cc_suggestion'];
				$cc_product_suggestion = $res2[$key]['cc_prod_suggestion'];

				if ($c_suggestion && $cc_suggestion == $s_id) {
					$tempIdSuggestion = $res[$key]['temp_id'];
					// echo "tempIdSuggestion $tempIdSuggestion\n";
				}

				if ($cc_product_suggestion == $ps_id) {
					$tempIdProdSuggestion = $res[$key]['temp_id'];
					// echo "tempIdProdSuggestion $tempIdProdSuggestion\n";
				}


				if ($tempIdSuggestion == $tempIdProdSuggestion) {
					$tempIdSuggestions = $res[$key]['temp_id'];
					echo "tempIdSuggestions $tempIdSuggestions\n";
					$ifSame = true;
				} else {
					$tempIdSuggestions = null;
					$ifSame = false;
					// echo "way same suggestion temp id\n";


				}
			}

			$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdSuggestions];
			return $result;
		}
	}

	private function checkSuggestionsIfSame($temp_id, $suggestion_id, $product_suggestion_id, $s_id)
	{
		$this->db->select('count(suggestion_id) as suggestion, suggestion_id');
		$this->db->from('app_customer_temp_order_suggestions');
		$this->db->where('temp_order_id', $temp_id);
		$this->db->where_in('product_suggestion_id', $product_suggestion_id);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $key => $value) {
			$ccc_choice = $value['suggestion'];
		}
		// echo "ang choice id count kay $ccc_choice\n";
		// echo "ang choice idd count kay $s_id\n";

		if ($ccc_choice == $s_id) {
			// echo "maka update\n";
			return true;
		} else {
			// echo "dili ka update\n";
			return false;
		}
	}

	private function checkChoicesIfExist(
		$temp_with,
		$temp_without,
		$userID,
		$prodId,
		$choices_id,
		$choices_uom_id,
		$c_id,
		$cuom_id,
		$all_addons_count,
		$a_id
	) {

		// echo "choices_temp_id $choices_temp_id\n";
		echo "temp_withs $temp_with\n";
		echo "temp_withouts $temp_without\n";
		$c_choice = "";
		$cc_choice = "";
		$c_uom = "";
		$cc_uom = "";

		$tempIdChoice = "";
		$tempIdUomChoice = "";
		$tempIdChoices = "";

		$ifExist = false;
		$ifSame = false;
		$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdChoices];

		if ($a_id == 0) {
			echo "update without addons\n";

			$this->db->select('*, temp_order_id as temp_id, count(choice_id) as c_choice, count(uom_id) as c_uom');
			$this->db->from('app_customer_temp_order_choices as temp_choices');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_without);
			$query = $this->db->get();
			$res = $query->result_array();

			if (!empty($res)) {
				$ifExist = true;
			}

			$this->db->select('*, temp_order_id as temp_id, count(choice_id) as cc_choice');
			$this->db->from('app_customer_temp_order_choices as temp_choices');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
			$this->db->where_in('temp_order_id', $temp_without);
			$this->db->where_in('choice_id', $choices_id);
			$query1 = $this->db->get();
			$res1 = $query1->result_array();

			$this->db->select('*, temp_order_id as temp_id, count(uom_id) as cc_uom');
			$this->db->from('app_customer_temp_order_choices as temp_choices');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
			$this->db->where_in('temp_order_id', $temp_without);
			$this->db->where_in('uom_id', $choices_uom_id);
			$this->db->or_where('uom_id IS NULL', null, false);
			$query2 = $this->db->get();
			$res2 = $query2->result_array();

			// echo print_r($res2);
			// echo "\n";

			foreach ($res as $key => $choiceValue) {

				$c_choice = $res[$key]['c_choice'];
				$cc_choice = $res1[$key]['cc_choice'];
				$cc_uom = $res2[$key]['cc_uom'];

				if ($c_choice && $cc_choice == $c_id) {
					$tempIdChoice = $choiceValue['temp_id'];
					// echo "tempIdChoice $tempIdChoice\n"; 
				}

				if ($cc_uom == $cuom_id) {
					$tempIdUomChoice = $res[$key]['temp_id'];
					// echo "tempIdUomChoice $tempIdUomChoice\n"; 
				}

				if (!empty($tempIdChoice)) {
					if ($tempIdChoice == $tempIdUomChoice) {
						$tempIdChoices = $choiceValue['temp_id'];
						echo "tempIdChoices $tempIdChoices\n";

						$ifSame = true;
					} else {
						// echo "way same tempIdChoices\n";
						$ifSame = false;
						$tempIdChoices = null;
					}
				} else {

					$ifSame = false;
					$tempIdChoices = null;
					// echo "way sides_temp_id\n";

				}
			}

			$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdChoices];
			return $result;
		} else {

			echo "update with addons\n";

			$this->db->select('*, temp_order_id as temp_id, count(choice_id) as c_choice, count(uom_id) as c_uom');
			$this->db->from('app_customer_temp_order_choices as temp_choices');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id ='$prodId')");
			$this->db->where_in('temp_order_id', $temp_with);
			$query = $this->db->get();
			$res = $query->result_array();

			if (!empty($res)) {
				$ifExist = true;
			}

			$this->db->select('*, temp_order_id as temp_id, count(choice_id) as cc_choice');
			$this->db->from('app_customer_temp_order_choices as temp_choices');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
			$this->db->where_in('temp_order_id', $temp_with);
			$this->db->where_in('choice_id', $choices_id);
			$query1 = $this->db->get();
			$res1 = $query1->result_array();

			$this->db->select('*, temp_order_id as temp_id, count(uom_id) as cc_uom');
			$this->db->from('app_customer_temp_order_choices as temp_choices');
			$this->db->where("temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
			$this->db->where_in('temp_order_id', $temp_with);
			$this->db->where_in('uom_id', $choices_uom_id);
			$this->db->or_where('uom_id IS NULL', null, false);
			$query2 = $this->db->get();
			$res2 = $query2->result_array();

			// echo print_r($res2);
			// echo "\n";

			foreach ($res as $key => $choiceValue) {

				$c_choice = $res[$key]['c_choice'];
				$cc_choice = $res1[$key]['cc_choice'];
				$cc_uom = $res2[$key]['cc_uom'];

				if ($c_choice && $cc_choice == $c_id) {
					$tempIdChoice = $choiceValue['temp_id'];
					// echo "tempIdChoice $tempIdChoice\n"; 
				}

				if ($cc_uom == $cuom_id) {
					$tempIdUomChoice = $res[$key]['temp_id'];
					// echo "tempIdUomChoice $tempIdUomChoice\n"; 
				}

				if (!empty($tempIdChoice)) {

					if ($tempIdChoice == $tempIdUomChoice) {
						$tempIdChoices = $choiceValue['temp_id'];
						// echo "tempIdChoices $tempIdChoices\n";

						$ifSame = true;
					} else {

						// echo "way same tempIdChoices\n";
						$ifSame = false;
						$tempIdChoices = null;
					}
				} else {

					$ifSame = false;
					$tempIdChoices = null;
					// echo "way sides_temp_id\n";

				}
			}

			$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdChoices];
			return $result;
		}
	}

	private function getTempChoicesId($temp_id, $choice_id, $cc_choice, $choice_uom_idd)
	{
		$this->db->select('count(choice_id) as choice, choice_id');
		$this->db->from('app_customer_temp_order_choices');
		$this->db->where('temp_order_id', $temp_id);
		$this->db->where_in('choice_id', $choice_id);
		$query = $this->db->get();
		$res = $query->result_array();
		// echo "ag temp_id kay $temp_id\n";
		// echo print_r($res);

		foreach ($res as $key => $value) {
			$ccc_choice = $value['choice'];
		}
		echo "ang choice id count kay $ccc_choice\n";
		echo "ang choice idd count kay $cc_choice\n";

		if ($ccc_choice == $cc_choice) {
			echo "maka update\n";
			return true;
		} else {
			echo "dili ka update\n";
			return false;
		}
	}

	private function checkAddonsIfExist(

		$addons_temp_id,
		$userID,
		$prodId,
		$addon_idd,
		$addon_uom_idd,
		$addon_price,
		$a_id,
		$auom_id
	) {

		// echo "addons_temp_id $addons_temp_id\n";
		$c_addon = "";
		$cc_addon = "";
		$c_uom = "";
		$cc_uom = "";

		$tempIdAddon = "";
		$tempIdUom = "";
		$tempIdAddons = "";

		$ifExist = false;
		$ifSame = false;
		$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdAddons];

		$this->db->select('*, temp_order_id as temp_id, count(addon_id) as c_addon, count(uom_id) as c_uom, sum(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_addons as temp_addons');
		$this->db->where("temp_addons.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
		$this->db->where_in('temp_order_id', $addons_temp_id);
		$query = $this->db->get();
		$res = $query->result_array();

		if (!empty($res)) {
			$ifExist = true;
		}

		$this->db->select('*, temp_order_id as temp_id, count(addon_id) as cc_addon');
		$this->db->from('app_customer_temp_order_addons as temp_addons');
		$this->db->where("temp_addons.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
		$this->db->where_in('temp_order_id', $addons_temp_id);
		$this->db->where_in('addon_id', $addon_idd);
		$query1 = $this->db->get();
		$res1 = $query1->result_array();

		$this->db->select('*, temp_order_id as temp_id, count(uom_id) as cc_uom');
		$this->db->from('app_customer_temp_order_addons as temp_addons');
		$this->db->where("temp_addons.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
		$this->db->where_in('temp_order_id', $addons_temp_id);
		$this->db->where_in('uom_id', $addon_uom_idd);
		$this->db->or_where('uom_id IS NULL', null, false);
		$query2 = $this->db->get();
		$res2 = $query2->result_array();

		foreach ($res as $key => $addonValue) {

			$a_price = (float) array_sum($addon_price);
			$db_a_price = $res[$key]['addon_price'];

			$c_addon = $res[$key]['c_addon'];
			$cc_addon = $res1[$key]['cc_addon'];

			$c_uom = $res[$key]['c_uom'];
			$cc_uom = $res2[$key]['cc_uom'];


			if ($c_addon && $cc_addon == $a_id) {
				$tempIdAddon = $res[$key]['temp_id'];
				echo "tempIdAddon $tempIdAddon\n";
			}

			if ($cc_uom == $auom_id) {
				$tempIdUom = $res[$key]['temp_id'];
				echo "tempIdUom $tempIdUom\n";
			}


			if (!empty($tempIdAddon)) {
				if ($tempIdAddon == $tempIdUom && $a_price == $db_a_price) {
					$tempIdAddons = $res[$key]['temp_id'];
					echo "tempIdAddons $tempIdAddons\n";
					$ifSame = true;
					$result = ['exist' => $ifExist, 'same' => $ifSame, 'temp_id' => $tempIdAddons];
					return $result;
				} else {
					$ifSame = false;
					echo "way same item\n";
					$result = array('exist' => $ifExist, 'same' => $ifSame, 'temp_id' => null);
					return $result;
				}
			} else {
				$ifSame = false;
				// echo "way sides_temp_id\n";
				$result = array('exist' => $ifExist, 'same' => $ifSame, 'temp_id' => null);
				return $result;
			}


			// echo "temp_id_addon_sides $temp_id_addon_sides\n";
			// echo "c_addon $c_addon\n";
			// echo "cc_addon $cc_addon\n";
			// echo "c_uom $c_uom\n";
			// echo "cc_uom $cc_uom\n";
			// echo "a_price $a_price\n";
			// echo "db_a_price $db_a_price\n";

		}
	}

	// private function checkAddonsIfExist($userID, $prodId, $c, $u, $side_id, $cc_uom) 
	// {
	// 	$ccc_addon ="";
	// 	$ccc_uom ="";
	// 	$temp_id1 ="";
	// 	$temp_id2 ="";
	// 	$temp_id3 ="";
	// 	$this->db->select('count(addon_id) as addon, count(uom_id) as uom, temp_order_id as temp_id');
	// 	$this->db->from('app_customer_temp_order_addons as temp_addons');
	// 	$this->db->where("temp_addons.temp_order_id in(SELECT id FROM app_customer_temp_orders where customerId = '$userID' and product_id = '$prodId')");
	// 	$this->db->where_in('addon_id', $side_id);
	// 	$this->db->group_by('temp_addons.temp_order_id');
	// 	$query = $this->db->get();
	// 	$res = $query->result_array();


	// 	foreach($res as $key => $value) {
	// 		// echo print_r($res);
	// 		// echo "\n";

	// 		$ccc_addon = $value['addon'];
	// 		$ccc_uom = $value['uom'];

	// 		echo "$ccc_addon\n";
	// 		echo "$cc_uom\n";

	// 		if ($c == $ccc_addon) {
	// 			$temp_id1 = $value['temp_id'];
	// 			echo "ang temp_id1 $temp_id1\n";
	// 		}
	// 		if ($u == $cc_uom) {
	// 			$temp_id2 = $value['temp_id'];
	// 			echo "ang temp_id2 $temp_id2\n";
	// 		}

	// 		if ($temp_id1 == $temp_id2) {
	// 			$temp_id3 = $value['temp_id'];
	// 		}
	// 	}
	// 	echo "ang temp_id3 $temp_id3\n";
	// }

	private function checkIfSameCountAddons($temp_id, $all_addons_count)
	{

		$s = array("null");
		$r = array();

		$this->db->select('*');
		$this->db->from('app_customer_temp_orders');
		$this->db->where_in('id', $temp_id);
		$query = $this->db->get();
		$res = $query->result_array();

		$this->db->select('*');
		$this->db->from('app_customer_temp_order_suggestions');
		$this->db->where_in('temp_order_id', $temp_id);
		$query1 = $this->db->get();
		$res1 = $query1->result_array();

		$this->db->select('*');
		$this->db->from('app_customer_temp_order_choices');
		$this->db->where_in('temp_order_id', $temp_id);
		$query2 = $this->db->get();
		$res2 = $query2->result_array();

		$this->db->select('*');
		$this->db->from('app_customer_temp_order_addons');
		$this->db->where_in('temp_order_id', $temp_id);
		$query3 = $this->db->get();
		$res3 = $query3->result_array();

		foreach ($res as $key => $countValue) {
			$temp_id_suggestions = $res1[$key]['temp_order_id'];
			$temp_id_choices = $res2[$key]['temp_order_id'];
			$temp_id_addons = $res3[$key]['temp_order_id'];

			$all_addons = [$temp_id_suggestions, $temp_id_choices, $temp_id_addons];
			$all_addons_ = str_replace($s, $r, $all_addons);
			$all_addons_a = array_values(array_filter($all_addons_));
			$all_addon_count = count($all_addons_a);

			if ($all_addons_count == $all_addon_count) {
				$temp_id = $res[$key]['id'];
				return $temp_id;
			}
		}
	}

	public function addTempCartPickup_mod(
		$userID,
		$orderID,
		$productID,
		$uomID,
		$quantity,
		$price,
		$measurement,
		$totalPrice,
		$icoos
	) {

		$modeOfOrder = "1";
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($userID, $modeOfOrder);


		$search1 = array("[", "]");
		$replacewith1 = array("", "");

		$orderID 		  	= str_replace($search1, $replacewith1, $orderID);
		$productID 		  	= str_replace($search1, $replacewith1, $productID);
		$uomID 			  	= str_replace($search1, $replacewith1, $uomID);
		$quantity 		  	= str_replace($search1, $replacewith1, $quantity);
		$price			  	= str_replace($search1, $replacewith1, $price);
		$measurement 	  	= str_replace($search1, $replacewith1, $measurement);
		$totalPrice 	  	= str_replace($search1, $replacewith1, $totalPrice);
		$icoos 			  	= str_replace($search1, $replacewith1, $icoos);

		$orderID_array  	= explode(',', $orderID);
		$productID_array  	= explode(',', $productID);
		$uomID_array  		= explode(',', $uomID);
		$quantity_array  	= explode(',', $quantity);
		$price_array 		= explode(',', $price);
		$measurent_array  	= explode(',', $measurement);
		$totalPrice_array  	= explode(',', $totalPrice);
		$icoos_array  		= explode(',', $icoos);

		for ($x = 0; $x < count($productID_array); $x++) {

			$order_id = $orderID_array[$x];
			$prod_id = $productID_array[$x];
			$uom_id  =  $uomID_array[$x] == 0 ? null : $uomID_array[$x];
			$add_qty = $quantity_array[$x];
			$add_price = $price_array[$x];
			$add_measurement = $measurent_array[$x];
			$add_totPrice = $totalPrice_array[$x];
			$add_icoos = $icoos_array[$x];

			$data = array(
				'ticket_id'     => $insert_id,
				'product_id'	=> $prod_id,
				'uom_id'		=> $uom_id,
				'quantity'		=> $add_qty,
				'price'			=> $add_price,
				'measurement'	=> $add_measurement,
				'total_price'	=> $add_totPrice,
				'icoos'			=> $add_icoos,
				'created_at'   	=> date('Y-m-d H:i:s'),
				'updated_at'  	=> date('Y-m-d H:i:s'),
			);

			$this->db->insert('toms_customer_temp_orders', $data);
			$insert_idd = $this->db->insert_id();

			$this->tempFlavors($order_id, $insert_idd);
			$this->tempChoices($order_id, $insert_idd);
			$this->tempAddons($order_id, $insert_idd);
		}
		$this->db->trans_complete();
	}

	public function addTempCartDelivery_mod(
		$userID,
		$orderID,
		$productID,
		$uomID,
		$quantity,
		$price,
		$measurement,
		$totalPrice,
		$icoos
	) {

		$modeOfOrder = "0";
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($userID, $modeOfOrder);


		$search1 = array("[", "]");
		$replacewith1 = array("", "");

		$orderID 		  	= str_replace($search1, $replacewith1, $orderID);
		$productID 		  	= str_replace($search1, $replacewith1, $productID);
		$uomID 			  	= str_replace($search1, $replacewith1, $uomID);
		$quantity 		  	= str_replace($search1, $replacewith1, $quantity);
		$price			  	= str_replace($search1, $replacewith1, $price);
		$measurement 	  	= str_replace($search1, $replacewith1, $measurement);
		$totalPrice 	  	= str_replace($search1, $replacewith1, $totalPrice);
		$icoos 			  	= str_replace($search1, $replacewith1, $icoos);

		$orderID_array  	= explode(',', $orderID);
		$productID_array  	= explode(',', $productID);
		$uomID_array  		= explode(',', $uomID);
		$quantity_array  	= explode(',', $quantity);
		$price_array 		= explode(',', $price);
		$measurent_array  	= explode(',', $measurement);
		$totalPrice_array  	= explode(',', $totalPrice);
		$icoos_array  		= explode(',', $icoos);

		for ($x = 0; $x < count($productID_array); $x++) {

			$order_id = $orderID_array[$x];
			$prod_id = $productID_array[$x];
			$uom_id  =  $uomID_array[$x] == 0 ? null : $uomID_array[$x];
			$add_qty = $quantity_array[$x];
			$add_price = $price_array[$x];
			$add_measurement = $measurent_array[$x];
			$add_totPrice = $totalPrice_array[$x];
			$add_icoos = $icoos_array[$x];


			$data = array(
				'ticket_id'     => $insert_id,
				'product_id'	=> $prod_id,
				'uom_id'		=> $uom_id,
				'quantity'		=> $add_qty,
				'price'			=> $add_price,
				'measurement'	=> $add_measurement,
				'total_price'	=> $add_totPrice,
				'icoos'			=> $add_icoos,
				'created_at'   	=> date('Y-m-d H:i:s'),
				'updated_at'  	=> date('Y-m-d H:i:s'),
			);

			$this->db->insert('toms_customer_temp_orders', $data);
			$insert_idd = $this->db->insert_id();

			$this->tempFlavors($order_id, $insert_idd);
			$this->tempChoices($order_id, $insert_idd);
			$this->tempAddons($order_id, $insert_idd);
		}
		$this->db->trans_complete();
	}

	private function tempFlavors($order_id, $insert_id)
	{

		$this->db->select('*');
		$this->db->from('app_customer_temp_order_flavors');
		$this->db->where('temp_order_id', $order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$flavors = $query->result_array();

			foreach ($flavors as $flavor) {
				$flavor_data = array(
					'temp_order_id' => $insert_id,
					'flavor_id' => $flavor['flavor_id'],
					'addon_price' => $flavor['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_temp_order_flavors', $flavor_data);
			}
		}
	}

	private function tempChoices($order_id, $insert_id)
	{

		$this->db->select('*');
		$this->db->from('app_customer_temp_order_choices');
		$this->db->where('app_customer_temp_order_choices.temp_order_id', $order_id);
		$query = $this->db->get();


		if ($query->num_rows() > 0) {
			$choices = $query->result_array();

			foreach ($choices as $key => $choice) {
				$choice_data = array(
					'temp_order_id' => $insert_id,
					'choice_id' => $choice['choice_id'],
					'uom_id' => $choice['uom_id'],
					'choice_drinks' => '1',
					'choice_fries' => null,
					'choice_sides' => null,
					'addon_price' => $choice['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_temp_order_choices', $choice_data);
			}
		}
	}

	private function tempAddons($order_id, $insert_id)
	{

		$this->db->select('*');
		$this->db->from('app_customer_temp_order_addons');
		$this->db->where('temp_order_id', $order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$addons = $query->result_array();

			foreach ($addons as $key => $addon) {
				$addon_data = array(
					'temp_order_id' => $insert_id,
					'addon_id' => $addon['addon_id'],
					'uom_id' => $addon['uom_id'],
					'addon_sides' => $addon['addon_sides'],
					'addon_dessert' => $addon['addon_dessert'],
					'addon_drinks' => $addon['addon_drinks'],
					'addon_price' => $addon['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_temp_order_addons', $addon_data);
			}
		}
	}

	public function add_to_cart_mod($customerId, $buCode, $tenantCode, $prodId, $productUom, $flavorId, $drinkId, $drinkUom, $friesId, $friesUom, $sideId, $sideUom, $selectedSideItems, $selectedSideItemsUom, $selectedDessertItems, $selectedDessertItemsUom, $_counter)
	{
		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$addon_sideItems = str_replace($search1, $replacewith1, $selectedSideItems);
		$addon_dessertItems = str_replace($search1, $replacewith1, $selectedDessertItems);

		// $selectedDessertItemsUom = '[null,1]';
		$search2 = array("[", "]");
		$replacewith2 = array("", "");
		$addon_sideItems_uom = str_replace($search2, $replacewith2, $selectedSideItemsUom);
		$addon_dessertItems_uom = str_replace($search2, $replacewith2, $selectedDessertItemsUom);

		if ($productUom == 'null') {
			$productUom = null;
		}
		if ($flavorId == 'null') {
			$flavorId = null;
		}
		// if($drinkUom == 'null'){
		// 	$drinkUom = null;
		// }
		if ($friesUom == 'null') {
			$friesUom = null;
		}
		if ($sideUom == 'null') {
			$sideUom = null;
		}
		$this->db->select('*');
		$this->db->from('app_cart_main as appCart');
		$this->db->where('appCart.customerId', $customerId);
		$this->db->where('appCart.productId', $prodId);
		$query = $this->db->get();
		$res = $query->result_array();

		if (empty($res)) {

			$datamain = array(
				'buId' => $buCode,
				'tenantId' => $tenantCode,
				'customerId' => $customerId,
				'productId' => $prodId,
				'uom' => $productUom,
				'flavor' => $flavorId,
				'quantity' => $_counter,
				'create_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_cart_main', $datamain);
			$insert_id = $this->db->insert_id();

			if ($drinkId != 'null') {
				$data1 = array(
					'cart_id' => $insert_id,
					'drink_id' 	=> $drinkId,
					'drink_uom' => $drinkUom,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' =>	date('Y-m-d H:i:s')
				);
				$this->db->insert('app_cart_drink', $data1);
			} else if ($friesId != 'null') {
				$data2 = array(
					'cart_id' => $insert_id,
					'fries_id' 	=> $friesId,
					'fries_uom' => $friesUom,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' =>	date('Y-m-d H:i:s')
				);
				$this->db->insert('app_cart_fries', $data2);
			} else if ($sideId != 'null') {
				$data3 = array(
					'cart_id' => $insert_id,
					'side_id' 	=> $sideId,
					'side_uom' => $sideUom,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' =>	date('Y-m-d H:i:s')
				);
				$this->db->insert('app_cart_sides', $data3);
			}

			if ($addon_sideItems != null) {
				$addon_sideItems_array  = explode(',', $addon_sideItems);
				$addon_sideItems_count  = count($addon_sideItems_array);

				$addon_sideItems_uom_array = explode(',', $addon_sideItems_uom);

				for ($x = 0; $x < $addon_sideItems_count; $x++) {
					$side_id = $addon_sideItems_array[$x];
					$side_uom_id = $addon_sideItems_uom_array[$x];
					if ($side_uom_id == 'null') {
						$side_uom_id = null;
					}
					$data4 = array(
						'cart_id' => $insert_id,
						'side_id' => $side_id,
						'side_uom' => $side_uom_id,
						'type' => "side_addon"
					);
					$this->db->insert('app_cart_addons_side_items', $data4);
				}
			}

			if ($addon_dessertItems != null) {
				$addon_dessertitems_array  = explode(',', $addon_dessertItems);
				$addon_dessertitems_count  = count($addon_dessertitems_array);

				$addon_dessertitems_uom_array  = explode(',', $addon_dessertItems_uom);

				for ($x = 0; $x < $addon_dessertitems_count; $x++) {
					$dessert_id =  $addon_dessertitems_array[$x];
					$dessert_uom_id = $addon_dessertitems_uom_array[$x];
					if ($dessert_uom_id == 'null' or $dessert_uom_id == 0) {
						$dessert_uom_id = null;
					}
					$data5 = array(
						'cart_id' => $insert_id,
						'side_id' => $dessert_id,
						'side_uom' => $dessert_uom_id,
						'type' => "dessert_addon"
					);
					$this->db->insert('app_cart_addons_side_items', $data5);
				}
			}
		} else {
			$this->db->set('quantity', $_counter);
			$this->db->where('customerId', $customerId);
			$this->db->where('productId', $prodId);
			$this->db->update('app_cart_main');
		}
	}

	public function selectSuffix_mod()
	{
		$this->db->select('*');
		$this->db->from('name_suffix as nameSuffix');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'suffix' => $value['suffix']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTowns_mod()
	{
		$this->db->select('*');
		$this->db->from('towns as towns');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'town_id' => $value['town_id'],
				'town_name' =>  $value['town_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getbarrio_mod($town_id)
	{
		$this->db->select('*');
		$this->db->from('barangays as barangays');
		$this->db->where('barangays.town_id', $town_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'brgy_id' => $value['brgy_id'],
				'town_id' => $value['town_id'],
				'brgy_name' => $value['brgy_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function updateCartQty_mod($id, $qty)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->where('appcart.id', $id);
		$query = $this->db->get();
		$res = $query->result();
		$total_price = $res[0]->price;
		// dd($total_price);
		foreach ($query->result() as $temp_order) {
			// $total_price += (float) $temp_order->price;

			$this->db->select("SUM(addon_price) as total_addons");
			$this->db->from("app_customer_temp_order_addons");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasAddons = $this->db->get()->result();

			if (empty($hasAddons) === false) {
				$total_price += (float) $hasAddons[0]->total_addons;
			}


			$this->db->select("SUM(addon_price) as total_choices");
			$this->db->from("app_customer_temp_order_choices");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasChoices = $this->db->get()->result();

			if (empty($hasChoices) === false) {
				$total_price += (float) $hasChoices[0]->total_choices;
			}

			$this->db->select("SUM(addon_price) as total_suggestions");
			$this->db->from("app_customer_temp_order_suggestions");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasSuggestions = $this->db->get()->result();

			if (empty($hasSuggestions) === false) {
				$total_price += (float) $hasSuggestions[0]->total_suggestions;
			}
		}

		$res = $query->row();

		$this->db->set('quantity', $qty);
		$this->db->set('total_price', $qty * $total_price);
		$this->db->where('id', $id);
		$this->db->update('app_customer_temp_orders');
	}

	public function updateCartQty_mod2($id, $_counter)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->where('appcart.id', $id);
		$query = $this->db->get();
		$res = $query->result();
		$total_price = $res[0]->price;
		$quantity = $res[0]->quantity;
		// dd($total_price);
		foreach ($query->result() as $temp_order) {
			// $total_price += (float) $temp_order->price;

			$this->db->select("SUM(addon_price) as total_addons");
			$this->db->from("app_customer_temp_order_addons");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasAddons = $this->db->get()->result();

			if (empty($hasAddons) === false) {
				$total_price += (float) $hasAddons[0]->total_addons;
			}


			$this->db->select("SUM(addon_price) as total_choices");
			$this->db->from("app_customer_temp_order_choices");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasChoices = $this->db->get()->result();

			if (empty($hasChoices) === false) {
				$total_price += (float) $hasChoices[0]->total_choices;
			}

			$this->db->select("SUM(addon_price) as total_suggestions");
			$this->db->from("app_customer_temp_order_suggestions");
			$this->db->where("temp_order_id", $temp_order->id);

			$hasSuggestions = $this->db->get()->result();

			if (empty($hasSuggestions) === false) {
				$total_price += (float) $hasSuggestions[0]->total_suggestions;
			}
		}

		$res = $query->row();

		$this->db->set('quantity', $_counter + $quantity);
		$this->db->set('total_price', ($_counter + $quantity) * $total_price);
		$this->db->where('id', $id);
		$this->db->update('app_customer_temp_orders');
	}

	public function updateCartStk_mod($id, $stk)
	{
		$this->db->set('icoos', $stk);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('product_id', $id);
		$this->db->update('app_customer_temp_orders');
	}

	public function updateCartIcoos_mod($id, $stk)
	{
		$this->db->set('icoos', $stk);
		$this->db->set('date_updated', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$this->db->update('app_cart_gc');
	}

	public function getCounter_mod($cusid)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders as appcart');
		$this->db->where('appcart.customerId', $cusid);
		$query = $this->db->get();
		// echo $query->num_rows();

		$post_data = array();
		$post_data[] = array(

			'num' => $query->num_rows()

		);
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function listenCartSubtotal_mod($cusid)
	{


		$this->db->select('appcart.quantity as cart_qty,IFNULL(SUM(main_prod_price.price),0) + IFNULL(SUM(fd_fries_price.price),0) + IFNULL(SUM(fd_drink_price.price),0) + IFNULL(SUM(fd_side_price.price),0) + IFNULL(SUM(fd_flavors.addon_price),0) as total');
		$this->db->from('app_cart_main as appcart');
		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');
		// $this->db->join('locate_business_units as loc_bu','loc_bu.bunit_code = appcart.buId','inner');
		// $this->db->join('locate_tenants as loc_tenants','loc_tenants.tenant_id = appcart.tenantId','inner');
		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND main_prod_price.uom_id = appcart.uom', 'left');
		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND fd_drink_price.uom_id = drink_id.drink_uom', 'left');
		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND fd_fries_price.uom_id = fries_id.fries_uom', 'left');
		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND fd_side_price.uom_id = side_id.side_uom', 'left');
		$this->db->where('appcart.customerId', $cusid);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'subtotal' => $value['total'] * $value['cart_qty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getMainOrders($customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $insert_id, $productID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$productID 		  	= str_replace($search1, $replacewith1, $productID);
		$productId  	= explode(',', $productID);

		// dd($deliveryDateData, $deliveryTimeData);
		$this->db->select('*,temp_orders.id as temp_order_id, users.id as userID');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = temp_orders.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenant', 'loc_tenant.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('app_users as users', 'users.customer_id = temp_orders.customerId');
		$this->db->order_by('fd_prod.tenant_id', 'DESC');
		$this->db->where('temp_orders.customerId', $customer_id);
		// $this->db->where_in('temp_orders.id', $productId);

		$query = $this->db->get();

		$main_orders = $query->result_array();

		foreach ($main_orders as $main_order) {
			$order_id = $this->insertMainOrders($insert_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $main_order['userID']);

			$this->insertChoices($main_order['temp_order_id'], $order_id);
			$this->insertAddons($main_order['temp_order_id'], $order_id);
			$this->insertFlavors($main_order['temp_order_id'], $order_id);
		}

		$this->clearCustomerCart($main_orders);
	}

	public function getMainOrders2($customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $insert_id, $productID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$productID 		  	= str_replace($search1, $replacewith1, $productID);
		$productId  	= explode(',', $productID);

		// dd($deliveryDateData, $deliveryTimeData);
		$this->db->select('*,temp_orders.id as temp_order_id, users.id as userID');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = temp_orders.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenant', 'loc_tenant.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('app_users as users', 'users.customer_id = temp_orders.customerId');
		$this->db->order_by('fd_prod.tenant_id', 'DESC');
		$this->db->where('temp_orders.customerId', $customer_id);
		$this->db->where_in('temp_orders.id', $productId);

		$query = $this->db->get();

		$main_orders = $query->result_array();

		foreach ($main_orders as $main_order) {
			$order_id = $this->insertMainOrders($insert_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $main_order['userID']);

			$this->insertChoices($main_order['temp_order_id'], $order_id);
			$this->insertAddons($main_order['temp_order_id'], $order_id);
			$this->insertSuggestions($main_order['temp_order_id'], $order_id);
		}

		$this->clearCustomerCart($main_orders);
	}

	public function getPickupOrders($customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $insert_id, $productID)
	{
		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$productID 		  	= str_replace($search1, $replacewith1, $productID);
		$productId  	= explode(',', $productID);

		// $productID = ['166','316','3016','195'];
		$this->db->select('*,temp_orders.id as temp_order_id, users.id as userID');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = temp_orders.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenant', 'loc_tenant.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('app_users as users', 'users.customer_id = temp_orders.customerId');
		$this->db->order_by('fd_prod.tenant_id', 'DESC');
		$this->db->where('temp_orders.customerId', $customer_id);
		// $this->db->where_in('temp_orders.id', $productId);

		$query = $this->db->get();

		$main_orders = $query->result_array();

		foreach ($main_orders as $main_order) {
			$order_id = $this->insertPickupOrders($insert_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $main_order['userID']);

			$this->insertChoices($main_order['temp_order_id'], $order_id);
			$this->insertAddons($main_order['temp_order_id'], $order_id);
			$this->insertFlavors($main_order['temp_order_id'], $order_id);
		}

		$this->clearCustomerCart($main_orders);
	}

	public function getPickupOrders2($customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $insert_id, $productID)
	{
		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$productID 		  	= str_replace($search1, $replacewith1, $productID);
		$productId  	= explode(',', $productID);

		// $productID = ['166','316','3016','195'];
		$this->db->select('*,temp_orders.id as temp_order_id, users.id as userID');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = temp_orders.product_id', 'inner');
		$this->db->join('locate_tenants as loc_tenant', 'loc_tenant.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('app_users as users', 'users.customer_id = temp_orders.customerId');
		$this->db->order_by('fd_prod.tenant_id', 'DESC');
		$this->db->where('temp_orders.customerId', $customer_id);
		$this->db->where_in('temp_orders.id', $productId);

		$query = $this->db->get();

		$main_orders = $query->result_array();

		foreach ($main_orders as $main_order) {
			$order_id = $this->insertPickupOrders($insert_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $main_order['userID']);

			$this->insertChoices($main_order['temp_order_id'], $order_id);
			$this->insertAddons($main_order['temp_order_id'], $order_id);
			$this->insertSuggestions($main_order['temp_order_id'], $order_id);
		}

		$this->clearCustomerCart($main_orders);
	}

	public function clearCustomerCart($main_orders)
	{
		foreach ($main_orders as $main_order) {

			$temp_order_id = $main_order['temp_order_id'];

			$this->clearChoices($temp_order_id);
			$this->clearAddons($temp_order_id);
			$this->clearFlavors($temp_order_id);
			$this->clearSuggestions($temp_order_id);
			$this->clearMainOrders($temp_order_id);
		}
	}

	public function clearCustomerCartPerItem($cartID)
	{
		// foreach ($main_orders as $main_order) {
		$temp_order_id = $cartID;

		$this->clearChoices($temp_order_id);
		$this->clearAddons($temp_order_id);
		$this->clearFlavors($temp_order_id);
		$this->clearSuggestions($temp_order_id);
		$this->clearMainOrders($temp_order_id);
		// }
	}

	private function clearChoices($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_choices', array('temp_order_id' => $temp_order_id));
	}

	private function clearAddons($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_addons', array('temp_order_id' => $temp_order_id));
	}

	private function clearFlavors($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_flavors', array('temp_order_id' => $temp_order_id));
	}

	private function clearSuggestions($temp_order_id)
	{
		$this->db->delete('app_customer_temp_order_suggestions', array('temp_order_id' => $temp_order_id));
	}

	private function clearMainOrders($temp_order_id)
	{
		$this->db->delete('app_customer_temp_orders', array('id' => $temp_order_id));
	}

	private function insertMainOrders($ticket_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $userID)
	{
		// dd(date('Y-m-d H:i:s', strtotime($deliveryDateData . " " . $deliveryTimeData)));

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$deliveryDate = str_replace($search1, $replacewith1, $deliveryDateData);
		$deliveryTime = str_replace($search1, $replacewith1, $deliveryTimeData);
		$tenantID = str_replace($search1, $replacewith1, $getTenantData);
		$special = str_replace($search1, $replacewith1, $specialInstruction);

		$date_array = explode(',', $deliveryDate);
		$time_array = explode(',', $deliveryTime);
		$tenant_array = explode(',', $tenantID);
		$instruction = explode("', '", $special);

		$key = array_search((int) $main_order['tenant_id'], $tenant_array);

		$date 		  = $date_array[$key];
		$time		  = $time_array[$key];
		$instructions = $instruction[$key];

		$s = array("'");
		$r = array("");
		$instruct = str_replace($s, $r, $instructions);

		$order2 = array(
			'ticket_id' 		=> $ticket_id,
			'tenant_id' 		=> $main_order['tenant_id'],
			'instructions'		=> $instruct,
			'created_at' 		=> date('Y-m-d H:i:s'),
			'updated_at' 		=> date('Y-m-d H:i:s'),
		);

		$this->db->insert('customer_special_instructions', $order2);


		$dateTimePickup = date("Y-m-d H:i:s", strtotime("$date $time"));

		$order1 = array(
			'ticket_id' 		=> $ticket_id,
			'product_id' 		=> $main_order['product_id'],
			'uom_id' 			=> $main_order['uom_id'],
			'quantity'			=> $main_order['quantity'],
			'product_price'		=> $main_order['price'],
			'measurement' 		=> $main_order['measurement'],
			'total_price' 		=> $main_order['total_price'],
			'mop' 				=> '0',
			'icoos' 			=>  $main_order['icoos'],
			'submitted_at' 		=> date('Y-m-d H:i:s'),
			'user_id' 			=> $userID,
			'created_at' 		=> date('Y-m-d H:i:s'),
			'updated_at' 		=> date('Y-m-d H:i:s'),
			'pickup_at' 		=> date('Y-m-d H:i:s', strtotime($deliveryDateData . " " . $deliveryTimeData)),
		);

		$this->db->insert('toms_customer_orders', $order1);

		return $this->db->insert_id();
	}

	private function insertPickupOrders($ticket_id, $main_order, $customer_id, $deliveryDateData, $deliveryTimeData, $getTenantData, $specialInstruction, $modeOfOrder, $userID)
	{
		// dd(date('Y-m-d H:i:s', strtotime($deliveryDateData . " " . $deliveryTimeData)));

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$deliveryDate = str_replace($search1, $replacewith1, $deliveryDateData);
		$deliveryTime = str_replace($search1, $replacewith1, $deliveryTimeData);
		$tenantID = str_replace($search1, $replacewith1, $getTenantData);
		$special = str_replace($search1, $replacewith1, $specialInstruction);

		$date_array = explode(',', $deliveryDate);
		$time_array = explode(',', $deliveryTime);
		$tenant_array = explode(',', $tenantID);
		$instruction = explode("', '", $special);

		$temp = count($date_array);

		$key = array_search((int) $main_order['tenant_id'], $tenant_array);

		$date 		  = $date_array[$key];
		$time		  = $time_array[$key];
		$instructions = $instruction[$key];

		$s = array("'");
		$r = array("");
		$instruct = str_replace($s, $r, $instructions);

		$order2 = array(
			'ticket_id' 		=> $ticket_id,
			'tenant_id' 		=> $main_order['tenant_id'],
			'instructions'		=> $instruct,
			'created_at' 		=> date('Y-m-d H:i:s'),
			'updated_at' 		=> date('Y-m-d H:i:s'),
		);

		$this->db->insert('customer_special_instructions', $order2);

		$dateTimePickup = date("Y-m-d H:i:s", strtotime("$date $time"));

		$order1  = array(
			'ticket_id' 		=> $ticket_id,
			'product_id' 		=> $main_order['product_id'],
			'uom_id' 			=> $main_order['uom_id'],
			'quantity'			=> $main_order['quantity'],
			'product_price'		=> $main_order['price'],
			'measurement' 		=> $main_order['measurement'],
			'total_price' 		=> $main_order['total_price'],
			'mop' 				=> '1',
			'icoos' 			=> $main_order['icoos'],
			'submitted_at' 		=> date('Y-m-d H:i:s'),
			'user_id' 			=> $userID,
			'created_at' 		=> date('Y-m-d H:i:s'),
			'updated_at' 		=> date('Y-m-d H:i:s'),
			'pickup_at' 		=> $dateTimePickup,
		);

		$this->db->insert('toms_customer_orders', $order1);

		return $this->db->insert_id();
	}

	private function insertChoices($temp_order_id, $order_id)
	{

		// SELECT * FROM app_customer_temp_orders as temp_orders

		// join app_customer_temp_order_choices as temp_choices
		// on temp_choices.temp_order_id = temp_orders.id

		// join fd_product_choices as fd_choices
		// on fd_choices.choice_id = temp_choices.choice_id and fd_choices.product_id = temp_orders.product_id

		// where temp_orders.id ='438'
		$this->db->select('*, temp_choices.choice_id as choice_id, fd_choices.uom_id as uom_id, temp_choices.addon_price as addon_price');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('app_customer_temp_order_choices as temp_choices', 'temp_choices.temp_order_id = temp_orders.id');
		$this->db->join('fd_product_choices as fd_choices', 'fd_choices.choice_id = temp_choices.choice_id AND fd_choices.product_id = temp_orders.product_id AND temp_choices.addon_price = fd_choices.addon_price');
		$this->db->where('temp_orders.id', $temp_order_id);
		$this->db->order_by('temp_choices.id', 'ASC');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$choices = $query->result_array();

			foreach ($choices as $key => $choice) {

				if ($choice['choice_drinks'] == '0') {
					$choice_drinks = null;
				} else {
					$choice_drinks = $choice['choice_drinks'];
				}
				if ($choice['choice_fries'] == '0') {
					$choice_fries = null;
				} else {
					$choice_fries = $choice['choice_fries'];
				}
				if ($choice['choice_sides'] == '0') {
					$choice_sides = null;
				} else {
					$choice_sides = $choice['choice_sides'];
				}
				if ($choice['uom_id'] == '0') {
					$uom_id = null;
				} else {
					$uom_id = $choice['uom_id'];
				}
				$choice_data = array(
					'order_id' => $order_id,
					'choice_id' => $choice['choice_id'],
					'uom_id' => $uom_id,
					'choice_drinks' => $choice_drinks,
					'choice_fries' => $choice_fries,
					'choice_sides' => $choice_sides,
					'addon_price' => $choice['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_choices', $choice_data);
			}
		}
	}

	private function insertAddons($temp_order_id, $order_id)
	{

		// SELECT * FROM app_customer_temp_orders as temp_orders

		// join app_customer_temp_order_addons as temp_addons
		// on temp_addons.temp_order_id = temp_orders.id

		// join fd_product_addons as fd_addons
		// on fd_addons.addon_id = temp_addons.addon_id AND fd_addons.product_id = temp_orders.product_id AND temp_addons.addon_price = fd_addons.addon_price

		// where temp_orders.id ='444'

		$this->db->select('*, temp_addons.addon_id as addon_id, fd_addons.uom_id as uom_id, temp_addons.addon_price as addon_price');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('app_customer_temp_order_addons as temp_addons', ' temp_addons.temp_order_id = temp_orders.id');
		$this->db->join('fd_product_addons as fd_addons', 'fd_addons.addon_id = temp_addons.addon_id AND fd_addons.product_id = temp_orders.product_id AND temp_addons.addon_price = fd_addons.addon_price');
		$this->db->where('temp_order_id', $temp_order_id);
		$this->db->order_by('temp_addons.id', 'ASC');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$addons = $query->result_array();

			foreach ($addons as $key => $addon) {
				if ($addon['addon_sides'] == '0') {
					$addon_sides = null;
				} else {
					$addon_sides = $addon['addon_sides'];
				}
				if ($addon['addon_dessert'] == '0') {
					$addon_dessert = null;
				} else {
					$addon_dessert = $addon['addon_dessert'];
				}
				if ($addon['addon_drinks'] == '0') {
					$addon_drinks = null;
				} else {
					$addon_drinks = $addon['addon_drinks'];
				}
				if ($addon['upgradable_item'] == '0') {
					$upgradable_item = null;
				} else {
					$upgradable_item = $addon['upgradable_item'];
				}
				if ($addon['uom_id'] == '0') {
					$uom_id = null;
				} else {
					$uom_id = $addon['uom_id'];
				}
				$addon_data = array(
					'order_id' => $order_id,
					'addon_id' => $addon['addon_id'],
					'uom_id' => $uom_id,
					'addon_sides' => $addon_sides,
					'addon_dessert' => $addon_dessert,
					'addon_drinks' => $addon_drinks,
					'upgradable_item' => $upgradable_item,
					'addon_price' => $addon['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_addons', $addon_data);
			}
		}
	}

	private function insertFlavors($temp_order_id, $order_id)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_order_flavors');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$flavors = $query->result_array();

			foreach ($flavors as $flavor) {
				$flavor_data = array(
					'order_id' => $order_id,
					'flavor_id' => $flavor['flavor_id'],
					'addon_price' => $flavor['addon_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_flavors', $flavor_data);
			}
		}
	}

	private function insertSuggestions($temp_order_id, $order_id)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_order_suggestions');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$suggestions = $query->result_array();

			foreach ($suggestions as $suggestion) {
				$suggestion_data = array(
					'order_id' 				=> $order_id,
					'suggestion_id' 		=> $suggestion['suggestion_id'],
					'product_suggestion_id' => $suggestion['product_suggestion_id'],
					'addon_price' 			=> $suggestion['addon_price'],
					'created_at' 			=> date('Y-m-d H:i:s'),
					'updated_at' 			=> date('Y-m-d H:i:s')
				);

				$this->db->insert('toms_customer_order_suggestions', $suggestion_data);
			}
		}
	}

	private function getTenantIDs($ticket_id)
	{
		$this->db->select('DISTINCT(tenant_id) as tenant_id');
		$this->db->from('toms_customer_orders');
		$this->db->join('fd_products', 'fd_products.product_id = toms_customer_orders.product_id', 'inner');
		$this->db->where('ticket_id', $ticket_id);

		$query = $this->db->get();

		$result = $query->result_array();

		return $result;
	}


	public function placeOrder_delivery_mod(
		$cusId,
		$deliveryDate,
		$deliveryTime,
		$selectedDiscountType,
		$deliveryCharge,
		$amountTender,
		$specialInstruction,
		$getTenantData,
		$productID
	) {
		$modeOfOrder = "0";
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($cusId, $modeOfOrder);
		$totalPayablePrice = $this->loadSubTotalnew_mod($cusId, true) + $deliveryCharge;
		$this->getMainOrders(
			$cusId,
			$deliveryDate,
			$deliveryTime,
			$getTenantData,
			$specialInstruction,
			$modeOfOrder,
			$insert_id,
			$productID
		);

		// dd($totalPayablePrice);

		$tenant_ids = $this->getTenantIDs($insert_id);
		// save delivery infos
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('customer_id', $cusId);
		$this->db->where('shipping', '1');
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}


		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$selectedDiscountType = str_replace($search1, $replacewith1, $selectedDiscountType);

		if (!empty($selectedDiscountType)) {
			$selectedDiscountType_array  = explode(',', $selectedDiscountType);
			$addon_sideItems_count  = count($selectedDiscountType_array);

			for ($x = 0; $x < $addon_sideItems_count; $x++) {
				$cust_disc = array(
					'ticket_id' => $insert_id,
					'customer_discount_storage_id' => $selectedDiscountType_array[$x],
					'status' => '0',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('customer_discounts', $cust_disc);
			}
		}

		$customer_bills = array(
			'ticket_id' => $insert_id,
			'amount' => $amountTender,
			'delivery_charge' => $deliveryCharge,
			'change' => $amountTender - $totalPayablePrice,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('customer_bills', $customer_bills);

		foreach ($tenant_ids as $tenant_id) {

			// dump($tenant_id);

			$tId = $tenant_id['tenant_id'];

			$fd_vtype = array(
				'ticket_id' => $insert_id,
				'transpo_id' => '1',
				'tenant_id' => $tId,
				'delivery_charge' => $deliveryCharge,
				'status' => '1',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('fd_vtype_suggestions', $fd_vtype);

			$this->pusher()->trigger("order-submitted.{$tId}", 'App\Events\OrderSubmitted', array('message' => ''));
		}

		$payment_methods = array(
			'ticket_id' => $insert_id,
			'payment_method_id' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('ticket_payment_methods', $payment_methods);

		$this->db->where('customerId', $cusId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($cusId);

		$this->db->trans_complete();
	}

	public function placeOrder_delivery_mod2(
		$cusId,
		$deliveryDate,
		$deliveryTime,
		$selectedDiscountType,
		$deliveryCharge,
		$amountTender,
		$specialInstruction,
		$getTenantData,
		$productID
	) {
		$modeOfOrder = "0";
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($cusId, $modeOfOrder);
		$totalPayablePrice = $this->loadSubTotalnew_mod2($cusId, $productID, true) + $deliveryCharge;
		$this->getMainOrders2(
			$cusId,
			$deliveryDate,
			$deliveryTime,
			$getTenantData,
			$specialInstruction,
			$modeOfOrder,
			$insert_id,
			$productID
		);

		// dd($totalPayablePrice);

		$tenant_ids = $this->getTenantIDs($insert_id);
		// save delivery infos
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('customer_id', $cusId);
		$this->db->where('shipping', '1');
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}


		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$selectedDiscountType = str_replace($search1, $replacewith1, $selectedDiscountType);

		if (!empty($selectedDiscountType)) {
			$selectedDiscountType_array  = explode(',', $selectedDiscountType);
			$addon_sideItems_count  = count($selectedDiscountType_array);

			for ($x = 0; $x < $addon_sideItems_count; $x++) {
				$cust_disc = array(
					'ticket_id' => $insert_id,
					'customer_discount_storage_id' => $selectedDiscountType_array[$x],
					'status' => '0',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('customer_discounts', $cust_disc);
			}
		}

		$customer_bills = array(
			'ticket_id' => $insert_id,
			'amount' => $amountTender,
			'delivery_charge' => $deliveryCharge,
			'change' => $amountTender - $totalPayablePrice,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('customer_bills', $customer_bills);

		foreach ($tenant_ids as $tenant_id) {

			// dump($tenant_id);

			$tId = $tenant_id['tenant_id'];

			$fd_vtype = array(
				'ticket_id' => $insert_id,
				'transpo_id' => '1',
				'tenant_id' => $tId,
				'delivery_charge' => $deliveryCharge,
				'status' => '1',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('fd_vtype_suggestions', $fd_vtype);

			$this->pusher()->trigger("order-submitted.{$tId}", 'App\Events\OrderSubmitted', array('message' => ''));
		}

		$payment_methods = array(
			'ticket_id' => $insert_id,
			'payment_method_id' => '1',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('ticket_payment_methods', $payment_methods);

		$this->db->where('customerId', $cusId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($cusId);

		$this->db->trans_complete();
	}

	public function placeOrder_pickup_mod(
		$cusId,
		$deliveryDateData,
		$deliveryTimeData,
		$getTenantData,
		$specialInstruction,
		$subtotal,
		$selectedDiscountType,
		$productID
	) {

		$modeOfOrder = '1';
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($cusId, $modeOfOrder);
		$totalPayablePrice = $this->loadSubTotalnew_mod($cusId, true);
		$this->getPickupOrders(
			$cusId,
			$deliveryDateData,
			$deliveryTimeData,
			$getTenantData,
			$specialInstruction,
			$modeOfOrder,
			$insert_id,
			$productID
		);

		// dd($totalPayablePrice);

		$tenant_ids = $this->getTenantIDs($insert_id);
		// save delivery infos
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}

		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$selectedDiscountType = str_replace($search1, $replacewith1, $selectedDiscountType);


		if (!empty($selectedDiscountType)) {
			$selectedDiscountType_array  = explode(',', $selectedDiscountType);
			$addon_sideItems_count  = count($selectedDiscountType_array);

			for ($x = 0; $x < $addon_sideItems_count; $x++) {
				$cust_disc = array(
					'ticket_id' => $insert_id,
					'customer_discount_storage_id' => $selectedDiscountType_array[$x],
					'status' => '0',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('customer_discounts', $cust_disc);
			}
		}

		$customer_bills = array(
			'ticket_id' => $insert_id,
			'amount' => $totalPayablePrice,
			'delivery_charge' => 0,
			'change' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('customer_bills', $customer_bills);

		$this->db->where('customerId', $cusId);
		$this->db->delete('app_cart_main');


		$this->delete_on_checkout_mod($cusId);

		$this->db->trans_complete();
	}

	public function placeOrder_pickup_mod2(
		$cusId,
		$deliveryDateData,
		$deliveryTimeData,
		$getTenantData,
		$specialInstruction,
		$subtotal,
		$selectedDiscountType,
		$productID
	) {

		$modeOfOrder = '1';
		$this->db->trans_start();
		$insert_id = $this->app_cart_today_order($cusId, $modeOfOrder);
		$totalPayablePrice = $this->loadSubTotalnew_mod2($cusId, $productID, true);
		$this->getPickupOrders2(
			$cusId,
			$deliveryDateData,
			$deliveryTimeData,
			$getTenantData,
			$specialInstruction,
			$modeOfOrder,
			$insert_id,
			$productID
		);

		// dd($totalPayablePrice);

		$tenant_ids = $this->getTenantIDs($insert_id);
		// save delivery infos
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
			// $this->db->insert('customer_billing_infos', $infos);
		}

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$selectedDiscountType = str_replace($search1, $replacewith1, $selectedDiscountType);


		if (!empty($selectedDiscountType)) {
			$selectedDiscountType_array  = explode(',', $selectedDiscountType);
			$addon_sideItems_count  = count($selectedDiscountType_array);

			for ($x = 0; $x < $addon_sideItems_count; $x++) {
				$cust_disc = array(
					'ticket_id' => $insert_id,
					'customer_discount_storage_id' => $selectedDiscountType_array[$x],
					'status' => '0',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$this->db->insert('customer_discounts', $cust_disc);
			}
		}

		$customer_bills = array(
			'ticket_id' => $insert_id,
			'amount' => $totalPayablePrice,
			'delivery_charge' => 0,
			'change' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);

		$this->db->insert('customer_bills', $customer_bills);

		$this->db->where('customerId', $cusId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($cusId);

		$this->db->trans_complete();
	}

	public function savePickup_mod($customerId, $groupValue, $deliveryDateData, $deliveryTimeData, $getTenantData, $subtotal, $tender)
	{
		// $jsonStr = json_decode($deliveryDateData,true);
		// var_dump($this->app_cart(12));

		$deliveryDateData = str_replace(array("[", "]"), array("", ""), $deliveryDateData);
		$deliveryDateData  = explode(',', $deliveryDateData);

		$deliveryTimeData = str_replace(array("[", "]"), array("", ""), $deliveryTimeData);
		$deliveryTimeData  = explode(',', $deliveryTimeData);

		$getTenantData = str_replace(array("[", "]"), array("", ""), $getTenantData);
		$getTenantData  = explode(',', $getTenantData);

		$count = 1;
		$tody_order = $this->app_cart_today_order($customerId, 5);
		if (!empty($tody_order)) {
			$count = $tody_order + 1;
		}

		$data = array(
			'ticket' => date('ymd') . '-2-00' . $count,
			'customer_id' => $customerId,
			'type' => '2',
			'mop' => 'Pick-up',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('tickets', $data);
		$insert_id = $this->db->insert_id();

		$this->db->select('*,appcart.productId as appcartproductId,side_id.side_id as side_id,side_id.side_uom as side_uom,fries_id.fries_id as fries_id,fries_id.fries_uom as fries_uom,appcart.uom as productUom,appcart.flavor as flavor_id,appcart.productId as productId,drink_id.drink_id as drink_id,drink_id.drink_uom as drink_uom,appcart.quantity as cart_qty,appcart.id as d_id,fd_flavors.addon_price as flavor_price,loc_tenants.tenant_id as tenantId,loc_tenants.tenant as loc_tenant_name,loc_bu.business_unit as loc_bu_name,main_prod_price.price as prod_price,main_prod.product_name as prod_name,fd_side_price.price as side_price,fd_side_name.product_name as side_name,fd_fries_price.price as fries_price,fd_fries_name.product_name as fries_name ,fd_drink_name.product_name as drink_name, fd_drink_price.price as drink_price');

		$this->db->from('app_cart_main as appcart');

		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = appcart.buId', 'left');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = appcart.tenantId', 'left');
		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_uoms as fd_uom', "fd_uom.id = appcart.uom AND IFNULL(fd_uom.id, 0) = IFNULL(appcart.uom, 0)", 'left');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND IFNULL(main_prod_price.uom_id,0) = IFNULL(appcart.uom,0)', 'left');
		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND IFNULL(fd_drink_price.uom_id,0) = IFNULL(drink_id.drink_uom,0)', 'left');
		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND IFNULL(fd_fries_price.uom_id,0) = IFNULL(fries_id.fries_uom,0)', 'left');
		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND IFNULL(fd_side_price.uom_id,0) = IFNULL(side_id.side_uom,0)', 'left');

		$this->db->where('appcart.customerId', $customerId);

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$totalitemprice = 0;

		foreach ($res as $value) {
			$totalitemprice = $value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'];

			$key = array_search((int) $value['tenantId'], $getTenantData);

			$date = $deliveryDateData[$key];
			$time = $deliveryTimeData[$key];

			$dateTimePickup = date("Y-m-d h:i:s", strtotime("$date $time"));

			// $post_data[] = array(
			// 	'appcartproductId' => $value['appcartproductId'],
			// 	'd_productId' =>$value['productId'],
			// 	'd_productUom' => $value['productUom'],
			// 	'd_flavor_id' => $value['flavor_id'],
			// 	'flavor_price' => $value['flavor_price'],
			// 	'd_drink_id' =>$value['drink_id'],
			// 'd_drink_uom' =>$value['drink_uom'],
			// 'd_fries_id' => $value['fries_id'],
			// 'd_fries_uom' => $value['fries_uom'],
			// 'd_side_id' => $value['side_id'],
			// 'd_side_uom'=> $value['side_uom'],
			// 	'd_id' => $value['d_id'],
			// 	'prod_name' => $value['prod_name'],
			// 	'cart_qty' => $value['cart_qty'],
			// 	'loc_bu_name' => $value['loc_bu_name'],
			// 	'loc_tenant_name' => $value['loc_tenant_name'],
			// 	'flavor_price' => $value['flavor_price'],
			// 'prod_price' => $value['prod_price'],
			// 	'drink_name' => $value['drink_name'],
			// 	'drink_price' => $value['drink_price'],
			// 	'fries_name' => $value['fries_name'],
			// 	'fries_price' => $value['fries_price'],
			// 	'side_name' => $value['side_name'],
			// 	'side_price' => $value['side_price'],
			// 	'total_price' => ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty'],);


			$data1 = array(
				'ticket_id' => $insert_id,
				'product_id' =>  $value['productId'],
				'uom_id' => $value['productUom'],
				'quantity' =>  $value['cart_qty'],
				'product_price' => $value['prod_price'],
				'total_price' => ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty'],
				'mop' => '1',
				'pickup_at' => $dateTimePickup,
				'icoos' => 0,
				'submitted_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('toms_customer_orders', $data1);

			if ($value['flavor_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'flavor_id' => $value['flavor_id'],
					'addon_price' => $value['flavor_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_flavors', $data11);
			}
			if ($value['drink_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'choice_id' => $value['productId'],
					'uom_id' => $value['productUom'],
					'choice_drinks' => $value['drink_id'],
					'choice_fries' => null,
					'choice_sides' => null,
					'choice_sizes' => null,
					'addon_price' => $value['drink_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_choices', $data11);
			}
			if ($value['fries_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'choice_id' => $value['productId'],
					'uom_id' => $value['productUom'],
					'choice_drinks' => null,
					'choice_fries' => $value['fries_id'],
					'choice_sides' => null,
					'choice_sizes' => null,
					'addon_price' => $value['fries_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_choices', $data11);
			}
			if ($value['side_id'] != null) {
				$data11 = array(
					'order_id' => $insert_id,
					'choice_id' => $value['productId'],
					'uom_id' => $value['productUom'],
					'choice_drinks' => null,
					'choice_fries' => null,
					'choice_sides' => $value['side_id'],
					'choice_sizes' => null,
					'addon_price' => $value['side_price'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('toms_customer_order_choices', $data11);
			}
		}

		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $customerId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$this->db->insert('customer_delivery_infos', $infos);
		}

		//save customer_bills
		$bills = array(
			'ticket_id' => $insert_id,
			'amount' => $subtotal,
			'change' => $tender,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_bills', $bills);

		$this->db->where('customerId', $customerId);
		$this->db->delete('app_cart_main');
		$this->delete_on_checkout_mod($customerId);
	}

	public function app_cart($customerId)
	{
		$this->db->select('*');
		$this->db->from('app_cart as appcart');
		$this->db->where('appcart.customerId', $customerId);
		return $this->db->get()->result_array();
	}

	public function app_cart_today_order($customerId, $modeOfOrder)
	{
		$this->db->select('*, appcart.ticket, DATE_FORMAT(created_at, "%y%m%d") as prevDate');
		$this->db->from('tickets as appcart');
		$this->db->limit(1);
		$this->db->where('appcart.customer_id', $customerId);
		// $this->db->where('appcart.type', '2');
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		$result = $query->result_array();

		$count = 1;
		$today = date("ymd");
		$now = 'prevDate';
		$date = date_format($now, 'ymd');

		if (empty($result)) {

			$data = array(
				'ticket' => date('ymd') . '-2-00' . $count,
				'customer_id' => $customerId,
				'type' => '2',
				'order_type_stat' => $modeOfOrder == '1' ? "1" : "0",
				'mop' => $modeOfOrder == '1' ? "Pick-up" : "Delivery",
				'source_id' => '3',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('tickets', $data);
			return $this->db->insert_id();
		}

		foreach ($result as $value) {
			$prev = $value['prevDate'];

			if ($today == $prev) {
				$result = $query->row();
				$grmNumber = $result->ticket;
				$suffix = substr($grmNumber, -3);
				$newsuffix = intval($suffix) + 1;
				$count = str_pad($newsuffix, 3, '0', STR_PAD_LEFT);

				$data = array(

					'ticket' => date('ymd') . '-2-' . $count,
					'customer_id' => $customerId,
					'type' => '2',
					'order_type_stat' => $modeOfOrder == '1' ? "1" : "0",
					'mop' => $modeOfOrder == '1' ? "Pick-up" : "Delivery",
					'source_id' => '3',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('tickets', $data);
				return $this->db->insert_id();
			}

			$data = array(

				'ticket' => date('ymd') . '-2-00' . $count,
				'customer_id' => $customerId,
				'type' => '2',
				'order_type_stat' => $modeOfOrder == '1' ? "1" : "0",
				'mop' => $modeOfOrder == '1' ? "Pick-up" : "Delivery",
				'source_id' => '3',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('tickets', $data);
			return $this->db->insert_id();
		}
	}

	public function loadSubTotal_mod($cusId)
	{
		// sum(appcart.quantity * appcart.price) as subtotal
		// $this->db->select('*, product_prices.price as product_price , product_name.product_name as prod_name, product_name');
		// $this->db->select('*,appcart.quantity as cart_qty,appcart.id as d_id,main_prod.image as prod_image,fd_flavors.addon_price as flavor_price,loc_tenants.tenant as loc_tenant_name,loc_bu.business_unit as loc_bu_name,main_prod_price.price as prod_price,main_prod.product_name as prod_name,fd_side_price.price as side_price,fd_side_name.product_name as side_name,fd_fries_price.price as fries_price,fd_fries_name.product_name as fries_name ,fd_drink_name.product_name as drink_name, fd_drink_price.price as drink_price');
		$this->db->select('appcart.quantity as cart_qty, 
		IFNULL(SUM(main_prod_price.price),0) 
		+ IFNULL(SUM(fd_fries_price.price),0) 
		+ IFNULL(SUM(fd_drink_price.price),0) 
		+ IFNULL(SUM(fd_side_price.price),0) 
		+ IFNULL(SUM(fd_flavors.addon_price),0) as total');
		$this->db->from('app_cart_main as appcart');
		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');
		// $this->db->join('locate_business_units as loc_bu','loc_bu.bunit_code = appcart.buId','inner');
		// $this->db->join('locate_tenants as loc_tenants','loc_tenants.tenant_id = appcart.tenantId','inner');
		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND main_prod_price.uom_id = appcart.uom', 'left');
		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND fd_drink_price.uom_id = drink_id.drink_uom', 'left');
		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND fd_fries_price.uom_id = fries_id.fries_uom', 'left');
		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND fd_side_price.uom_id = side_id.side_uom', 'left');
		$this->db->where('appcart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'subtotal' => $value['total'] * $value['cart_qty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadSubTotalnew_mod($userId, $from_controller = false)
	{
		$this->db->select('*');
		$this->db->from('app_customer_temp_orders');
		$this->db->where('customerId', $userId);
		$query = $this->db->get();
		$res = $query->result_array();
		$grand_total = 0.00;

		foreach ($res as $temp_order) {
			$grand_total += $temp_order['total_price'];
		}

		if ($from_controller == false) {
			$item = [];
			$item['grand_total'] = $grand_total;

			echo json_encode(['user_details' => [$item]]);
			exit();
		}

		return (float) $grand_total;
	}

	public function loadSubTotalnew_mod2($userId, $productID, $from_controller = false)
	{

		$search1 		= array("[", "]");
		$replacewith1 	= array("", "");
		$productID 		= str_replace($search1, $replacewith1, $productID);
		$productId  	= explode(',', $productID);

		$this->db->select('*');
		$this->db->from('app_customer_temp_orders');
		$this->db->where('customerId', $userId);
		$this->db->where_in('id', $productId);
		$query = $this->db->get();
		$res = $query->result_array();
		$grand_total = 0.00;

		foreach ($res as $temp_order) {
			$grand_total += $temp_order['total_price'];
		}

		if ($from_controller == false) {
			$item = [];
			$item['grand_total'] = $grand_total;

			echo json_encode(['user_details' => [$item]]);
			exit();
		}

		return (float) $grand_total;
	}

	private function totalAddons($temp_order_id)
	{
		$this->db->select('SUM(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_addons');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$total_addons = $query->result_array();

			foreach ($total_addons as $key => $total_addon) {
				return $total_addon['addon_price'];
			}
		}
		return 0;
	}


	private function totalChoices($temp_order_id)
	{
		$this->db->select('SUM(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_choices');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$total_addons = $query->result_array();
			foreach ($total_addons as $key => $total_addon) {
				return $total_addon['addon_price'];
			}
		}
		return 0;
	}

	private function totalFlavors($temp_order_id)
	{
		$this->db->select('SUM(addon_price) as addon_price');
		$this->db->from('app_customer_temp_order_flavors');
		$this->db->where('temp_order_id', $temp_order_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$total_addons = $query->result_array();
			foreach ($total_addons as $key => $total_addon) {
				return $total_addon['addon_price'];
			}
		}
		return 0;
	}

	// public function loadRiderDetails_mod($ticket_id){
	// 		$this->db->select('*');
	// 		$this->db->from('tickets as ticket_id ');
	// 		$this->db->join('toms_tag_riders as tagrider', 'tagrider.ticket_id = ticket_id.id','inner');
	// 		$this->db->join('toms_riders_data as riderdata', 'riderdata.id = tagrider.rider_id','inner');
	// 		$this->db->where('ticket_id.ticket',$ticket_id);
	// 		$query = $this->db->get();
	// 		$res = $query->result_array();
	//     	$post_data = array();
	// 	 	foreach($res as $value){
	// 		 			$post_data[] = array(
	// 		 				'firstname' => $value['r_firstname'],
	// 		 				'lastname' => $value['r_lastname'],
	// 		 				'photo' => $this->buImage.$value['r_picture'],
	// 		 				'mobile' => $value['r_mobile'],
	// 		 				'rm_brand' => $value['rm_brand'],
	// 		 				'rm_model' => $value['rm_model'],
	// 		 				'rm_picture' => $this->buImage.$value['rm_picture']

	// 		 		);
	// 			}
	// 		$item = array('user_details' => $post_data);
	// 		echo json_encode($item);
	// }

	public function getTrueTime_mod()
	{
		$t = time();
		$post_data[] = array(
			'date_today' 	=> date("Y-m-d", $t),
			'hour_today' 	=> date("H", $t),
			'minute_today' 	=> date("i", $t),
			'next_day'		=> date("Y-m-d", strtotime(' +1 day'))

		);
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadFlavor_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_addon_flavors as fd_addon_flavors ');
		$this->db->join('fd_flavors as fd_flavors', 'fd_flavors.id = fd_addon_flavors.flavor_id', 'inner');
		$this->db->where('fd_addon_flavors.product_id', $productId);
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'flavor_id' => $value['flavor_id'],
				'add_on_flavors' => $value['flavor'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadDrinks_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_choices as fd_product_choices ');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
		$this->db->join('fd_uoms as fd_uoms', 'fd_uoms.id = fd_product_choices.uom_id', 'inner');
		$this->db->where('fd_product_choices.product_id', $productId);
		$this->db->where('fd_product_choices.choice_drinks', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'uom_id' => $value['uom_id'],
				'drink_id' => $value['choice_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadFries_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_choices as fd_product_choices');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
		$this->db->join('fd_uoms as fd_uoms', 'fd_uoms.id = fd_product_choices.uom_id', 'inner');
		$this->db->where('fd_product_choices.product_id', $productId);
		$this->db->where('fd_product_choices.choice_fries', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'uom_id' => $value['uom_id'],
				'fries_id' => $value['choice_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}



	public function loadSide_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_choices as fd_product_choices ');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_choices.choice_id', 'inner');
		$this->db->join('fd_uoms as fd_uoms', 'fd_uoms.id = fd_product_choices.uom_id', 'inner');
		$this->db->where('fd_product_choices.product_id', $productId);
		$this->db->where('fd_product_choices.choice_sides', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'uom_id' => $value['uom_id'],
				'side_id' => $value['choice_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function checkAddon_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_addons as fd_product_addons');
		$this->db->where('fd_product_addons.product_id', $productId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'addon_sides' => $value['addon_sides'],
				'addon_dessert' => $value['addon_dessert'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadAddonSide_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_addons as fd_product_addons');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
		$this->db->where('fd_product_addons.product_id', $productId);
		$this->db->where('fd_product_addons.addon_sides', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' => $value['product_id'],
				'addon_id' => $value['addon_id'],
				'uom_id' => $value['uom_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}
	public function loadAddonDessert_mod($productId)
	{
		$this->db->select('*');
		$this->db->from('fd_product_addons as fd_product_addons');
		$this->db->join('fd_products as fd_products', 'fd_products.product_id = fd_product_addons.addon_id', 'inner');
		$this->db->where('fd_product_addons.product_id', $productId);
		$this->db->where('fd_product_addons.addon_dessert', '1');
		$this->db->order_by('addon_price', 'asc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' => $value['product_id'],
				'addon_id' => $value['addon_id'],
				'uom_id' => $value['uom_id'],
				'product_name' => $value['product_name'],
				'addon_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function cancelOrderTenant_mod($tenantID, $ticketID)
	{
		echo $tenantID;
		echo "\n";
		echo $ticketID;

		$sql = "
		UPDATE toms_customer_orders toms_order
		INNER JOIN fd_products fd_prod ON  toms_order.product_id = fd_prod.product_id
		SET toms_order.canceled_status = 1,
		toms_order.pending_status = 0
		WHERE fd_prod.tenant_id = $tenantID AND toms_order.ticket_id = $ticketID";
		$this->db->query($sql);

		$this->db->select('*');
		$this->db->from('toms_customer_orders as toms_order');
		$this->db->where('toms_order.ticket_id', $ticketID);
		$this->db->where('toms_order.canceled_status', '0');
		$query = $this->db->get();
		$res = $query->num_rows();
		if ($res == 0) {
			$this->db->set('cancel_status', '1');
			$this->db->set('updated_at', date('Y-m-d H:i:s'));
			$this->db->where('id', $ticketID);
			$this->db->update('tickets');
		}
	}

	public function cancelOrderGoods_mod($buId, $ticketID)
	{
		echo $buId;
		echo "\n";
		echo $ticketID;

		$this->db->set('cancelled_status', '1');
		$this->db->set('pending_status', '0');
		$this->db->set('cancelled_at', date('Y-m-d H:i:s'));
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('ticket_id', $ticketID);
		$this->db->where('bu_id', $buId);
		$this->db->update('gc_order_statuses');

		$this->db->set('canceled_status', '1');
		$this->db->set('pending_status', '0');
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('ticket_id', $ticketID);
		$this->db->where('bu_id', $buId);
		$this->db->update('gc_final_order');

		// $this->db->set('cancel_status', '1');
		// $this->db->set('updated_at', date('Y-m-d H:i:s'));
		// $this->db->where('id', $ticketID);
		// $this->db->update('tickets');

	}

	public function cancelOrderSingleFood_mod($tomsId, $ticketId)
	{
		$this->db->set('canceled_status', '1');
		$this->db->where('id', $tomsId);
		$this->db->update('toms_customer_orders');

		$this->db->select('*');
		$this->db->from('toms_customer_orders as toms_order');
		$this->db->where('toms_order.ticket_id', $ticketId);
		$this->db->where('toms_order.canceled_status', '0');
		$query = $this->db->get();
		$res = $query->num_rows();
		if ($res == 0) {
			$this->db->set('cancel_status', '1');
			$this->db->set('updated_at', date('Y-m-d H:i:s'));
			$this->db->where('id', $ticketId);
			$this->db->update('tickets');
		}
	}

	public function cancelOrderSingleGood_mod($tomsId, $ticketId)
	{
		$this->db->set('canceled_status', '1');
		$this->db->where('id', $tomsId);
		$this->db->update('gc_final_order');

		$this->db->set('pending_status', '0');
		$this->db->where('id', $tomsId);
		$this->db->update('gc_final_order');


		$this->db->select('*');
		$this->db->from('gc_final_order as gcfinal');
		$this->db->where('gcfinal.ticket_id', $ticketId);
		$this->db->where('gcfinal.canceled_status', '0');
		$query = $this->db->get();
		$res = $query->num_rows();
		if ($res == 0) {
			$this->db->set('cancel_status', '1');
			$this->db->where('id', $ticketId);
			$this->db->update('tickets');

			$this->db->set('cancelled_status', '1');
			$this->db->set('cancelled_at', date('Y-m-d H:i:s'));
			$this->db->where('ticket_id', $ticketId);
			$this->db->update('gc_order_statuses');


			//          $this->db->select('*');
			// 			$this->db->from('gc_order_statuses as gc_stat');
			// 			$this->db->where('gc_stat.ticket_id',$ticketId);
			// 			$query = $this->db->get();
			// 			$res = $query->num_rows();	

			$this->pusher()->trigger("private-grocery-order-submitted", 'App\Events\GroceryOrderSubmitted', array('message' => ''));
		}
	}

	public function loadLocation_mod($locid)
	{
		$this->db->select('*');
		$this->db->from('towns');
		$this->db->where('town_id', $locid);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'town_id' => $value['town_id'],
				'town_name' => $value['town_name'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	function delete_on_checkout_mod($cusId)
	{

		$this->db->select('*');
		$this->db->from('app_cart_main as appcart');
		$this->db->where('customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			// $post_data[] = array(
			// 	'cart_id' => $value['id']
			// );

			$this->db->where('app_cart_main.id', $value['id']);
			$this->db->delete('app_cart_main');

			$this->db->where('app_cart_fries.cart_id', $value['id']);
			$this->db->delete('app_cart_fries');

			$this->db->where('app_cart_addons_side_items.cart_id', $value['id']);
			$this->db->delete('app_cart_addons_side_items');

			$this->db->where('app_cart_drink.cart_id', $value['id']);
			$this->db->delete('app_cart_drink');

			$this->db->where('app_cart_sides.cart_id', $value['id']);
			$this->db->delete('app_cart_sides');
		}
	}

	function displayAddOns_mod($cartId)
	{
		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_drink", "drink_id = product_id AND IFNULL(uom_id, 0) = IFNULL(drink_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = drink_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_fries", "fries_id = product_id AND IFNULL(uom_id, 0) = IFNULL(fries_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = fries_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res1 = $query->result_array();
		$post_data1 = array();
		foreach ($res1 as $value) {
			$post_data1[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_sides", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = side_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res2 = $query->result_array();
		$post_data2 = array();
		foreach ($res2 as $value) {
			$post_data2[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$this->db->select("*,fd_prod.product_name");
		$this->db->from("fd_product_prices as prod");
		$this->db->join("app_cart_addons_side_items", "side_id = product_id AND IFNULL(uom_id, 0) = IFNULL(side_uom, 0)", "left");
		$this->db->join("fd_products as fd_prod", "fd_prod.product_id = side_id ", "inner");
		$this->db->where("cart_id", $cartId);
		$query = $this->db->get();
		$res3 = $query->result_array();
		$post_data3 = array();
		foreach ($res3 as $value) {
			$post_data3[] = array(
				'price' => $value['price'],
				'drink_name' => $value['product_name']
			);

			// 'drink_name'  => $value[''],

		}

		$item = array($post_data);
		$a = json_encode($item);

		$item1 = array($post_data1);
		$b = json_encode($item1);

		$item2 = array($post_data2);
		$c = json_encode($item2);

		$item3 = array($post_data3);
		$d = json_encode($item3);


		echo $merger = json_encode(array_merge(json_decode($a, true), json_decode($b, true), json_decode($c, true), json_decode($d, true)));
	}


	public function upLoadImage_sr_mod($customerId)
	{
		$this->db->select("*");
		$this->db->from("app_users");
		$this->db->where("customer_id", $customerId);
		$query = $this->db->get();
		$res3 = $query->result_array();
		$post_data = array();
		foreach ($res3 as $value) {
			$post_data[] = array(
				'user_id' => $value['id'],

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function showFlavor_mod($cartId)
	{
		$this->db->select("*");
		$this->db->from('app_cart_main as appcartmain');
		$this->db->join("fd_flavors as fd_flavors", "fd_flavors.id = appcartmain.flavor", "left");
		$this->db->join("fd_addon_flavors as fd_addon_flavors", "fd_addon_flavors.flavor_id = appcartmain.flavor AND appcartmain.productId=fd_addon_flavors.product_id", "left");
		$this->db->where("appcartmain.id", $cartId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'flavor_d' => $value['flavor'],
				'flavor_price' => $value['addon_price']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getDiscount_mod($ticketID)
	{
		$this->db->select('*, sum(discounts.discount) as total_discount');
		$this->db->from('customer_discounted_amounts as discounts');
		$this->db->where('discounts.ticket_id', $ticketID);
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'total_discount'   => $value['total_discount']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTotal_mod($ticket)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,SUM(IF(toms_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = ticket.id');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = ticket.id');
		$this->db->join('ticket_payment_methods as tik_methods', 'tik_methods.ticket_id = toms_order.ticket_id');
		$this->db->join('payment_methods as p_methods', 'p_methods.id = tik_methods.payment_method_id');
		$this->db->where('ticket.id', $ticket);
		// $this->db->where('ticket.cancel_status', '0');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'sub_total'		  => $value['total_price'],
				'delivery_charge' => $value['delivery_charge'],
				'total_price' 	  => sprintf("%.2f", ($value['total_price'] + $value['delivery_charge'])),
				'amount_tender'	  => $value['amount'],
				'change'		  => $value['change'],
				'cancel_status'   => $value['cancel_status'],
				'payment_method'  => $value['payment_gateway']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTotal_mod2($ticket)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,SUM(IF(toms_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = ticket.id');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = ticket.id');
		$this->db->where('ticket.id', $ticket);
		// $this->db->where('ticket.cancel_status', '0');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'sub_total'		  => $value['total_price'],
				'delivery_charge' => $value['delivery_charge'],
				'total_price' 	  => sprintf("%.2f", ($value['total_price'] + $value['delivery_charge'])),
				'amount_tender'	  => $value['amount'],
				'change'		  => $value['change'],
				'cancel_status'   => $value['cancel_status'],

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getTotalGoods_mod($ticket)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,SUM(IF(gc_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as ticket');
		$this->db->join('gc_final_order as gc_order', 'gc_order.ticket_id = ticket.id');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = ticket.id');
		$this->db->where('ticket.id', $ticket);
		// $this->db->where('ticket.cancel_status', '0');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'sub_total'		  => $value['total_price'],
				'picking_charge' => $value['picking_charge'],
				'total_price' 	  => sprintf("%.2f", ($value['total_price'] + $value['picking_charge'])),
				'amount_tender'	  => $value['amount'],
				'change'		  => $value['change'],
				'cancel_status'   => $value['cancel_status'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getPickupScheduleFoods_mod($ticket)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, SUM(IF(toms_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('pick_up_schedules as pick_sched', 'pick_sched.tenant_id = loc_tenants.tenant_id');
		$this->db->where('toms_order.ticket_id', $ticket);
		// $this->db->where('toms_order.canceled_status', '0'); 20834
		$this->db->order_by('toms_order.id', 'desc');
		$this->db->group_by('loc_tenants.tenant_id');

		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'price'		   		=> $value['total_price'],
				'tenant_name'  		=> $value['tenant'],
				'time_start'   		=> $value['time_start'],
				'time_end'	   		=> $value['time_end'],
				'time_pickup'  		=> $value['pickup_at'],
				'cancel_status'		=> $value['cancel_status']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getPickupScheduleGoods_mod($ticket)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, SUM(IF(gc_order.canceled_status = 0, total_price, 0)) total_price');
		$this->db->from('tickets as tickets');
		$this->db->join('gc_final_order as gc_order', 'gc_order.ticket_id = tickets.id', 'inner');
		$this->db->join('gc_order_statuses as order_status', 'order_status.ticket_id = tickets.id and order_status.bu_id = gc_order.bu_id',);
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = gc_order.bu_id');
		$this->db->where('gc_order.ticket_id', $ticket);
		// $this->db->order_by('gc_order.id', 'desc');
		$this->db->group_by('gc_order.bu_id');

		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'price'		   		=> $value['total_price'],
				'bu_name'  			=> $value['business_unit'],
				'time_pickup'  		=> $value['order_pickup'],
				'cancel_status'		=> $value['cancel_status']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getVehicleType_mod($ticketId)
	{

		$this->db->select('*, partial_riders.delivery_charge as riders_fee');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id', 'inner');
		$this->db->join('toms_tag_riders as tag_riders', 'tag_riders.tenant_id = loc_tenants.tenant_id AND tag_riders.ticket_id = toms_order.ticket_id', 'inner');
		$this->db->join('partial_tag_riders as partial_riders', 'partial_riders.ticket_id = toms_order.ticket_id');
		$this->db->join('toms_riders_data as riders_data', 'riders_data.id = partial_riders.rider_id', 'inner');
		$this->db->join('vehicle_types as vtypes', 'vtypes.id = riders_data.vehicle_type', 'inner');
		// $this->db->join('customer_bills as bills', 'bills.ticket_id = tag_riders.ticket_id', 'inner');
		$this->db->where('toms_order.ticket_id', $ticketId);
		$this->db->where('tag_riders.cancelled_status', '0');
		$this->db->where('toms_order.canceled_status', '0');
		// $this->db->where('tickets.cancel_status', '0');
		$this->db->group_by('partial_riders.rider_id');
		$query = $this->db->get();
		$res = $query->result_array();

		if (count($res) == 0) {

			$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
			$this->db->select('*');
			$this->db->from('tickets as tickets');
			$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
			$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id');
			$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
			$this->db->join('fd_vtype_suggestions as vtype', 'vtype.tenant_id = loc_tenants.tenant_id AND vtype.ticket_id = toms_order.ticket_id');
			$this->db->join('vehicle_types as vtypes', 'vtypes.id = vtype.transpo_id');
			$this->db->where('toms_order.ticket_id', $ticketId);
			// $this->db->where('tickets.cancel_status', '0');
			$this->db->group_by('vtype.ticket_id');
			$query = $this->db->get();
			$res = $query->result_array();

			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'vehicle_type'   => $value['vehicle_type'],
					'riders_fee'     => $value['delivery_charge'],
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		} else {

			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'vehicle_type'   => $value['vehicle_type'],
					'riders_fee'     => $value['riders_fee'],
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function getInstruction_mod($ticket_id)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('customer_special_instructions as instruction');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = instruction.ticket_id');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = toms_order.product_id');
		$this->db->where('instruction.ticket_id', $ticket_id);
		$this->db->group_by('instruction.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'tenant_id'		 => $value['tenant_id'],
				'instructions' 	 => $value['instructions'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getOrderSummary_mod($ticket_id)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('tickets as tickets');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = tickets.id', 'inner');
		$this->db->join('customer_delivery_infos as delivery_info', 'delivery_info.ticket_id = toms_order.ticket_id');
		$this->db->join('barangays as brgy', 'brgy.brgy_id = delivery_info.barangay_id');
		$this->db->join('towns as towns', 'towns.town_id = brgy.town_id');
		$this->db->join('province as prov', 'prov.prov_id = towns.prov_id');
		$this->db->where('toms_order.ticket_id', $ticket_id);
		$this->db->group_by('toms_order.ticket_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'pickup_at'		=> $value['pickup_at'],
				'submitted'     => $value['submitted_at'],
				'firstname' 	=> $value['firstname'],
				'lastname'		=> $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'house_no'		=> $value['complete_address'],
				'street'		=> $value['street_purok'],
				'barangay'		=> $value['brgy_name'],
				'town'			=> $value['town_name'],
				'zipcode'		=> $value['zipcode'],
				'province'		=> $value['prov_name'],
				'landmark'		=> $value['land_mark'],
				'cancel_status' => $value['cancel_status'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getPickupSummaryFoods_mod($ticket_id)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = ticket.id');
		$this->db->join('customer_delivery_infos as delivery_info', 'delivery_info.ticket_id = toms_order.ticket_id');
		$this->db->join('barangays as brgy', 'brgy.brgy_id = delivery_info.barangay_id');
		$this->db->join('towns as towns', 'towns.town_id = brgy.town_id');
		$this->db->join('province as prov', 'prov.prov_id = towns.prov_id');
		$this->db->where('toms_order.ticket_id', $ticket_id);
		$this->db->group_by('toms_order.ticket_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'submitted'     	=> $value['submitted_at'],
				'firstname' 		=> $value['firstname'],
				'lastname'			=> $value['lastname'],
				'mobile_number' 	=> $value['mobile_number'],
				'house_no'			=> $value['complete_address'],
				'street'			=> $value['street_purok'],
				'barangay'			=> $value['brgy_name'],
				'town'				=> $value['town_name'],
				'zipcode'			=> $value['zipcode'],
				'province'			=> $value['prov_name'],
				'cancel_status' 	=> $value['cancel_status']

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getPickupSummaryGoods_mod($ticket_id)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*, order_status.created_at as submitted_at');
		$this->db->from('tickets as ticket');
		$this->db->join('gc_order_statuses as order_status', 'order_status.ticket_id = ticket.id');
		$this->db->join('customer_delivery_infos as delivery_info', 'delivery_info.ticket_id = order_status.ticket_id');
		$this->db->join('barangays as brgy', 'brgy.brgy_id = delivery_info.barangay_id');
		$this->db->join('towns as towns', 'towns.town_id = brgy.town_id');
		$this->db->join('province as prov', 'prov.prov_id = towns.prov_id');
		$this->db->where('order_status.ticket_id', $ticket_id);
		$this->db->group_by('order_status.ticket_id');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'submitted'     	=> $value['submitted_at'],
				'firstname' 		=> $value['firstname'],
				'lastname'			=> $value['lastname'],
				'mobile_number' 	=> $value['mobile_number'],
				'house_no'			=> $value['complete_address'],
				'street'			=> $value['street_purok'],
				'barangay'			=> $value['brgy_name'],
				'town'				=> $value['town_name'],
				'zipcode'			=> $value['zipcode'],
				'province'			=> $value['prov_name'],
				'cancel_status' 	=> $value['cancel_status'],
				'cancelled_status' 	=> $value['cancelled_status']


			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getPickupTime_mod()
	{
		$this->db->select('*');
		$this->db->from('gc_setup_business_rules');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'time_start' => $value['ordering_cutoff_time_start'],
				'time_end'   => $value['ordering_cutoff_time_end'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function subTotal_mod($ticket_id)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(toms_order.total_price) as total_price');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_customer_orders as toms_order', 'toms_order.ticket_id = ticket.id');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = ticket.id');
		$this->db->where('ticket.ticket', $ticket_id);
		$this->db->where('toms_order.canceled_status', '0');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'sub_total' => $value['total_price'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function checkifongoing_mod($ticket_id)
	{

		$this->db->select('*');
		$this->db->from('tickets as ticket');
		$this->db->join('toms_tag_riders as tagriders', 'tagriders.ticket_id = ticket.id');
		$this->db->where("ticket.id", $ticket_id);
		$this->db->where('tagriders.trans_status', '1');
		$query = $this->db->get();
		$res = $query->result_array();

		if (empty($res)) {
			echo "false";
		}
		if (!empty($res)) {
			echo "true";
		}
	}

	public function viewCategories_mod($categoryID)
	{
		$this->db->select('*');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_categories as fd_cat', 'fd_cat.category_id = fd_prod_cat.category_id');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->where("fd_prod_cat.category_id", $categoryID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'total_price' => $value['product_name'],
			);
		}
		$item = array('user_details' => $value);
		echo json_encode($item);
	}

	public function checkifemptystore_mod($tenant_id)
	{
		$this->db->select('*');
		$this->db->from('fd_products as fd_prod');
		$this->db->where("fd_prod.tenant_id", $tenant_id);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function getCategories_mod($tenant_id)
	{
		$this->db->select('*');
		$this->db->from('fd_categories as fd_ccat');
		$this->db->where("fd_ccat.tenant_id", $tenant_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'tenant_id' => $value['tenant_id'],
				'category_id' => $value['category_id'],
				'category' => $value['category'],
				'image' =>  $this->productImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getItemsBycategories_mod($category_id)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->where('fd_prod_cat.category_id', $category_id);
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$this->db->order_by('fd_prod.product_name');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$now = strtotime(date('H:i:s'));

		foreach ($res as $value) {
			$bf_start = strtotime($value['breakfast_start']);
			$bf_end = strtotime($value['breakfast_end']);
			if (!$bf_start && !$bf_end) {
				$avail = true;
			} else {
				$avail = $bf_start >= $now && $now <= $bf_end;
			}
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'isAvailnow' => $avail
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getItemsByCategoriesAll_mod($tenant_id)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$this->db->where('fd_prod.tenant_id', $tenant_id);
		$this->db->order_by('fd_prod.product_name');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		$now = strtotime(date('H:i:s'));
		foreach ($res as $value) {
			$bf_start = strtotime($value['breakfast_start']);
			$bf_end = strtotime($value['breakfast_end']);
			if (!$bf_start && !$bf_end) {
				$avail = true;
			} else {
				$avail = $bf_start >= $now && $now <= $bf_end;
			}
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'isAvailnow' => $avail
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function searchGc_item_mod($query, $unitGroupId, $bunitCode, $groupCode)
	{

		$this->db->select('*, gc_prod.product_id as prod_id');
		$this->db->from('gc_product_items as gc_prod');
		$this->db->join('gc_product_prices as gc_price', 'gc_price.itemcode =  gc_prod.itemcode', 'inner');
		$this->db->where('gc_price.price_group', $groupCode);
		$this->db->where("gc_prod.product_name LIKE '%$query%' 
		and NOT EXISTS (select 1 from gc_item_log_availables where gc_item_log_availables.itemcode = gc_prod.itemcode and store = '$bunitCode')");
		$this->db->limit(30);
		$this->db->group_by('gc_prod.itemcode');
		// $this->db->join('gc_item_log_availables as gc_available','gc_available.itemcode = gc_prod.itemcode', 'left');
		// $this->db->like('gc_prod.product_name', $query);

		// $this->db->where('NOT EXISTS (select 1 from gc_item_log_availables where gc_item_log_availables.itemcode = gc_prod.itemcode and store = 1)');
		// ->offset($offset);


		$products = $this->db->where('gc_prod.status', 'active')
			// ->where('image !=', 'null')
			->get()
			->result();

		$user_details = array_map(function (object $product) {
			$with = $this->productLeastUOMPrice($product);
			if (!empty($with)) {
				$product->prod_id = $product->product_id;
				$product->image = $this->gcproductImage . $product->image;
				$product->uom = $with->UOM;
				$product->price = number_format($with->price_with_vat, 2);
				$product->uom_id = $with->price_id;
			}
			return $product;
		}, $products);
		echo json_encode(compact('user_details'));
	}

	public function getGcItems_mod($offset, $categoryNo, $groupCode, $itemSearch)
	{
		$this->db->select('*, gc_prod.product_id as prod_id')
			->from('gc_product_items as gc_prod')
			->join('gc_product_prices as gc_price', 'gc_price.itemcode =  gc_prod.itemcode', 'inner')
			->where('gc_price.price_group', $groupCode)
			->limit(10)
			->offset($offset)
			->group_by('gc_prod.itemcode');

		// if (!empty($itemSearch)) {
		// 	$this->db->like('gc_prod.product_name', $itemSearch);
		// }

		$products = $this->db->where('gc_prod.status', 'active')
			->where('gc_prod.category_no', $categoryNo)
			// ->where('image!=', 'null')
			->get()
			->result();

		$user_details = array_map(function (object $product) {

			$with = $this->productLeastUOMPrice($product);

			if (!empty($with)) {

				$product->prod_id = $product->product_id;
				$product->image = $this->gcproductImage . $product->image;
				$product->uom = $with->UOM;
				$product->price = number_format($with->price_with_vat, 2);
				$product->uom_id = $with->price_id;
			}

			return $product;
		}, $products);

		echo json_encode(compact('user_details'));
	}

	private function productLeastUOMPrice(object $product): object
	{
		$uomPrice = $this->db->select('*')
			->from('gc_product_prices as price')
			// ->join('gc_product_prices as price', 'uom.itemcode = price.itemcode AND uom.UOM = price.UOM')
			->where('price.itemcode', $product->itemcode)
			->where('price.price_group', $product->price_group)
			->order_by('price_with_vat', 'asc')
			->limit(1)
			->get()
			->result();

		if (!empty($uomPrice)) {
			return $uomPrice[0];
		}
	}

	private function productWithoutUOMPrice(object $product): object
	{
		$uomPrice = $this->db->select('*')
			->from('gc_product_prices as price')
			->where('price.itemcode', $product->itemcode)
			->order_by('price_with_vat', 'asc')
			->limit(1)
			->get()
			->result();

		if (!empty($uomPrice)) {
			return $uomPrice[0];
		}
	}

	public function gc_loadPriceGroup_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'price_group'   => $value['price_group_code']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getStore_mod($cusId)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(gc_prod_price.price_with_vat * cart_gc.quantity) as sumperstore, cart_gc.quantity as cartQty, count(*) as num');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->group_by('loc_bu.bunit_code');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'total' => number_format($value['sumperstore'], 2),
				'buName' => $value['business_unit'],
				'count' => $value['num'],
				'buCode' => $value['bunit_code'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getStore2_mod($cusId, $tempID)
	{

		$search1 	  = array("[", "]");
		$replacewith1 = array("", "");
		$tempID 	  = str_replace($search1, $replacewith1, $tempID);
		$tempId  	  = explode(',', $tempID);

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,sum(gc_prod_price.price_with_vat * cart_gc.quantity) as sumperstore, cart_gc.quantity as cartQty, count(*) as num');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->where_in('cart_gc.id', $tempId);
		$this->db->group_by('loc_bu.bunit_code');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'total' => number_format($value['sumperstore'], 2),
				'buName' => $value['business_unit'],
				'count' => $value['num'],
				'buCode' => $value['bunit_code'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function gc_cart_mod($cusId)
	{
		$this->db->select('*,cart_gc.item_code as itemCode , cart_gc.quantity as cartQty');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->where('cart_gc.customer_id', $cusId);
		// $this->db->where('gc_prod_price.price_group', $priceGroup);
		$this->db->order_by('cart_gc.date_created', 'DESC');
		// $this->db->group_by('loc_bu.bunit_code');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'cart_id' => $value['id'],
				'cart_qty' => $value['cartQty'],
				'product_id' => $value['itemCode'],
				'product_name' => $value['product_name'],
				'product_image' => $this->gcproductImage . $value['image'],
				'product_uom' => $value['UOM'],
				'price_group' => $value['price_group'],
				'bu' => $value['business_unit'],
				'buCode'  => $value['bunit_code'],
				'price_price' => $value['price_with_vat'],
				'total_price' => number_format($value['price_with_vat'] * $value['cartQty'], 2),

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_cart2_mod($cusId, $tempID)
	{

		$search1 	  = array("[", "]");
		$replacewith1 = array("", "");
		$tempID 	  = str_replace($search1, $replacewith1, $tempID);
		$tempId  	  = explode(',', $tempID);

		$this->db->select('*,cart_gc.item_code as itemCode , cart_gc.quantity as cartQty');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->where_in('cart_gc.id', $tempId);
		$this->db->order_by('cart_gc.date_created', 'DESC');
		// $this->db->group_by('loc_bu.bunit_code');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'cart_id' 		=> $value['id'],
				'cart_qty' 		=> $value['cartQty'],
				'product_id' 	=> $value['itemCode'],
				'product_name' 	=> $value['product_name'],
				'product_image' => $this->gcproductImage . $value['image'],
				'product_uom' 	=> $value['UOM'],
				'price_group' 	=> $value['price_group'],
				'bu' 			=> $value['business_unit'],
				'buCode'  		=> $value['bunit_code'],
				'price_price' 	=> $value['price_with_vat'],
				'total_price' 	=> number_format($value['price_with_vat'] * $value['cartQty'], 2),
				'icoos'			=> $value['icoos']

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function addToCartGc_mod($userID, $buCode, $prodId, $itemCode, $uomSymbol, $uom, $_counter)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as appCart');
		$this->db->where('appCart.customer_id', $userID);
		$this->db->where('appCart.item_code', $itemCode);
		$this->db->where('appCart.uom_symbol', $uomSymbol);
		$query = $this->db->get();
		$res = $query->result_array();

		if (empty($res)) {

			$data = array(
				'customer_id' => $userID,
				'buId' => $buCode,
				'product_id' => $prodId,
				'item_code' => $itemCode,
				'uom' => $uom,
				'uom_symbol' => $uomSymbol,
				'quantity' => $_counter,
				'date_created' => date('Y-m-d H:i:s'),
				'date_updated' => date('Y-m-d H:i:s')
			);
			$this->db->insert('app_cart_gc', $data);
		} else {
			foreach ($res as $value) {
				$quantity = $value['quantity'];

				$this->db->set('quantity', $_counter + $quantity);
				$this->db->where('customer_id', $userID);
				$this->db->where('item_code', $itemCode);
				$this->db->where('uom_symbol', $uomSymbol);
				$this->db->update('app_cart_gc');
			}
		}
	}

	public function updateGcCartQty_mod($id, $qty)
	{
		$this->db->set('quantity', $qty);
		$this->db->where('id', $id);
		$this->db->update('app_cart_gc');
	}

	public function loadGcSubTotal_mod($userID)
	{
		$this->db->select('*,SUM(price_with_vat * cart_gc.quantity) as price_vat');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->where('cart_gc.customer_id', $userID);
		$this->db->where('gc_prod_price.status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => number_format($value['price_vat'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadGcSubTotal2_mod($userID, $tempID)
	{

		$search1 	  = array("[", "]");
		$replacewith1 = array("", "");
		$tempID 	  = str_replace($search1, $replacewith1, $tempID);
		$tempId  	  = explode(',', $tempID);

		$this->db->select('*,SUM(price_with_vat * cart_gc.quantity) as price_vat');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->where('cart_gc.customer_id', $userID);
		$this->db->where('gc_prod_price.status', '1');
		$this->db->where_in('cart_gc.id', $tempId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => number_format($value['price_vat'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getGcCounter_mod($cusid)
	{

		$this->db->select('*');
		$this->db->from('app_cart_gc as appgccart');
		$this->db->where('appgccart.customer_id', $cusid);
		$query = $this->db->get();
		// echo $query->num_rows();

		$post_data = array();
		$post_data[] = array(
			'num' => $query->num_rows()
		);
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getGcCategories_mod()
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('gc_product_items as gc_cate');
		$this->db->group_by('category_no');
		$this->db->order_by('category_name', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_id' => $value['product_id'],
				'category_name' => $value['category_name'],
				'category_no' => $value['category_no'],
				'itemcode' => $value['itemcode'],
				'image' => $this->gcproductImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getItemsByGcCategories_mod($category_id, $offset, $groupCode, $bunitCode)
	{

		$this->db->select('*,min(price_with_vat) as minprice');
		$this->db->from('gc_product_items as gc_cate');
		$this->db->join('gc_product_uoms as gc_product_uom', 'gc_product_uom.itemcode = gc_cate.itemcode');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_cate.itemcode AND gc_product_uom.UOM = gc_prod_price.UOM');
		$this->db->limit(10);
		$this->db->offset($offset);
		$this->db->group_by('gc_cate.itemcode');
		$this->db->where('gc_cate.category_no', $category_id);
		$this->db->where('gc_prod_price.price_group', $groupCode);
		$this->db->where('gc_prod_price.status', '1');
		$this->db->where('gc_cate.image!=', 'null');
		$this->db->where("NOT EXISTS (select 1 from gc_item_log_availables where gc_item_log_availables.itemcode = gc_cate.itemcode and store = '$bunitCode')");
		$this->db->order_by('gc_cate.product_name', 'ASC');
		$query = $this->db->get();
		$res = $query->result_array();

		if (count($res) == 0) {
			// echo "way uom";

			$this->db->select('*,min(price_with_vat) as minprice, gc_cate.itemcode as itemcode');
			$this->db->from('gc_product_items as gc_cate');
			$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_cate.itemcode');
			$this->db->limit(10);
			$this->db->offset($offset);
			$this->db->where('gc_cate.category_no', $category_id);
			$this->db->where('gc_prod_price.price_group', $groupCode);
			$this->db->where('gc_prod_price.status', '1');
			$this->db->where("NOT EXISTS (select 1 from gc_item_log_availables where gc_item_log_availables.itemcode = gc_cate.itemcode and store = '$bunitCode')");
			$this->db->group_by('gc_cate.itemcode');
			// $this->db->order_by('gc_cate.product_name', 'ASC');
			$query = $this->db->get();
			$res = $query->result_array();

			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'product_name' 	=> $value['product_name'],
					'itemcode' 		=> $value['itemcode'],
					'image'			=> $this->gcproductImage . $value['image'],
					'price'			=> number_format($value['minprice'], 2),
					'uom' 			=> $value['UOM'],
					'product_id' 	=> $value['product_id'],
					'category_name' => $value['category_name'],
					'category_no' 	=> $value['category_no'],
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		} else {

			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'product_name' 		=> $value['product_name'],
					'itemcode' 			=> $value['itemcode'],
					'image'				=> $this->gcproductImage . $value['image'],
					'price'				=> number_format($value['minprice'], 2),
					'uom' 				=> $value['UOM'],
					'uom_id' 			=> $value['uom_id'],
					'product_id' 		=> $value['product_id'],
					'category_name' 	=> $value['category_name'],
					'category_no' 		=> $value['category_no'],
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function removeGcItemFromCart_mod($cartId)
	{
		$this->db->where('app_cart_gc.id', $cartId);
		$this->db->delete('app_cart_gc');
	}

	//testing this if usefull
	public function getGcPickUpItems_mod($cusId)
	{
		$this->db->select('*,cart_gc.product_id as itemCode , cart_gc.quantity as cartQty');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.product_id');
		$this->db->join('gc_product_uoms as gc_product_uom', 'gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.product_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'cart_id' => $value['id'],
				'cart_qty' => $value['cartQty'],
				'product_id' => $value['itemCode'],
				'product_name' => $value['product_name'],
				'product_image' => $this->gcproductImage . $value['image'],
				'product_uom' => $value['UOM'],
				'bu' => $value['business_unit'],
				'price_price' => $value['price_with_vat'],
				'total_price' => number_format($value['price_with_vat'] * $value['cartQty'], 2)
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBill_mod($cusId, $priceG)
	{
		$this->db->select('*,SUM(price_with_vat * cart_gc.quantity) as price_vat');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->where('gc_prod_price.price_group', $priceG);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => $value['price_vat']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_getbillperbu_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.product_id AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->where('cart_gc.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'd_subtotal' => $value['price_vat']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gcgroupbyBu_mod($cusId, $priceGroup)
	{
		// $this->db->distinct();
		$this->db->select('cart_gc.customer_id, cart_gc.buId, loc_bu.business_unit, SUM(gc_prod_price.price_with_vat * cart_gc.quantity) as gcsum, loc_bu.price_group_code as price_group');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		// $this->db->join('gc_product_uoms as gc_product_uom','gc_product_uom.itemcode = cart_gc.product_id AND cart_gc.uom = gc_product_uom.uom_id AND gc_prod_price.UOM = gc_product_uom.UOM');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->where('gc_prod_price.price_group', $priceGroup);
		$this->db->group_by('cart_gc.buId');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'buId' => $value['buId'],
				'buName' => $value['business_unit'],
				'total' => number_format($value['gcsum'], 2),

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gcgroupbyBu2_mod($cusId, $priceGroup, $tempID)
	{

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$tempID 		  	= str_replace($search1, $replacewith1, $tempID);
		$tempId  	= explode(',', $tempID);
		// $this->db->distinct();
		$this->db->select('cart_gc.customer_id, cart_gc.buId, loc_bu.business_unit, SUM(gc_prod_price.price_with_vat * cart_gc.quantity) as gcsum, loc_bu.price_group_code as price_group');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		$this->db->where('cart_gc.customer_id', $cusId);
		$this->db->where('gc_prod_price.price_group', $priceGroup);
		$this->db->where_in('cart_gc.id', $tempId);
		$this->db->group_by('cart_gc.buId');
		$query = $this->db->get();
		$res = $query->result_array();

		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'buId' => $value['buId'],
				'buName' => $value['business_unit'],
				'total' => number_format($value['gcsum'], 2),
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	public function getConFee_mod()
	{
		$this->db->select('*');
		$this->db->from('gc_setup_business_rules as gc_setupRules');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'minimum_order_amount' => $value['minimum_order_amount'],
				'pickup_charge' => $value['pickup_charge']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gcDeliveryFee_mod($townID)
	{
		$this->db->select('*');
		$this->db->from('good_delivery_charges as gc_delivery');
		$this->db->where('vtype', '1');
		$this->db->where('status', '1');
		$this->db->where('town_id', $townID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		foreach ($res as $value) {
			$post_data[] = array(
				'con_fee'		=> $value['con_fee'],
				'delivery_fee'	=> $value['charge_amt'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_submitOrder_mod(
		$customerId,
		$groupValue,
		$deliveryDateData,
		$deliveryTimeData,
		$buData,
		$totalData,
		$convenienceData,
		$placeRemarks,
		$pickUpOrDelivery,
		$priceGroup
	) {
		$this->db->trans_start();
		// dd($customerId, $groupValue, $deliveryDateData, $deliveryTimeData, $buData, $totalData, $convenienceData, $placeRemarks);
		$deliveryDateData_arr = str_replace(array("[", "]"), array("", ""), $deliveryDateData);
		$dates  = explode(',', $deliveryDateData_arr);

		$deliveryTime = str_replace(array("[", "]"), array("", ""), $deliveryTimeData);
		$times  = explode(',', $deliveryTime);

		$bus = str_replace(array("[", "]"), array("", ""), $buData);
		$stores  = explode(',', $bus);

		$totalData = str_replace(array("[", "]"), array("", ""), $totalData);
		$totalPerStores  = explode(',', $totalData);

		$convenienceData = str_replace(array("[", "]"), array("", ""), $convenienceData);
		$pickingChargePerStores  = explode(',', $convenienceData);

		$placeRemarks = str_replace(array("[", "]"), array("", ""), $placeRemarks);
		$placeRemarks = explode(',', $placeRemarks);

		$insert_id = $this->app_cart_today_order($customerId, $pickUpOrDelivery);

		foreach ($stores as $key => $buId) {

			$date = $dates[$key];
			$time = $times[$key];
			$store = $stores[$key];
			$total = $totalPerStores[$key];
			$pickingChargePerStore = $pickingChargePerStores[$key];
			$placeRemark = $placeRemarks[$key];


			$this->db->select('*,cart_gc.product_id as gcarProdId  , cart_gc.quantity as cartQty, users.id as userID');
			$this->db->from('app_cart_gc as cart_gc');
			$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
			$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
			$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
			$this->db->join('app_users as users', 'users.customer_id = cart_gc.customer_id');
			$this->db->where('cart_gc.customer_id', $customerId);
			$this->db->where('cart_gc.buId', $buId);
			// $this->db->where('gc_prod_price.price_group', $priceGroup);

			$query2 = $this->db->get();
			$res2 = $query2->result_array();


			foreach ($res2 as $value) {
				$data = array(
					'ticket_id' => $insert_id,
					'bu_id' => $value['buId'],
					'product_id' => $value['gcarProdId'],
					'uom_id' => $value['uom'],
					'quantity' => $value['cartQty'],
					'price' => $value['price_with_vat'],
					'total_price' => $value['price_with_vat'] * $value['cartQty'],
					'icoos' => '1',
					'pending_status' => '1',
					'canceled_status' => '0',
					'user_id' => $value['userID'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('gc_final_order', $data);

				$this->pusher()->trigger("private-grocery-order-submitted.{$buId}", 'App\Events\GroceryOrderSubmitted', array('message' => ''));


				$dat = [
					'ticket_id' => $insert_id,
					'bu_id' => $buId,
					'mode_of_order' => 0,
					'order_pickup' => date('Y-m-d H:i:s', strtotime("$date $time")),
					'user_id' => $value['userID'],
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				];

				$this->db->insert('gc_order_statuses', $dat);
			}

			$dat1 = [
				'ticket_id' => $insert_id,
				'bu_id' => $buId,
				'remarks' => $placeRemark,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$this->db->insert('gc_special_instructions', $dat1);

			$dat2 = [
				'ticket_id' => $insert_id,
				'amount' => $total,
				'picking_charge' => $pickingChargePerStore,
				'change' => '0.00',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];
			$this->db->insert('customer_bills', $dat2);
		}

		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $customerId);
		$query = $this->db->get();
		$res = $query->result_array();
		// $post_data = array();
		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}

		// $this->db->select('*');
		// $this->db->from('customer_addresses');
		// $this->db->where('shipping', '1');
		// $this->db->where('customer_id', $customerId);
		// $query = $this->db->get();
		// $res = $query->result_array();
		// // $post_data = array();
		// foreach ($res as $value) {
		// 	$infos = array(
		// 		'ticket_id' => $insert_id,
		// 		'firstname' => $value['firstname'],
		// 		'lastname' => $value['lastname'],
		// 		'mobile_number' => $value['mobile_number'],
		// 		'barangay_id' => $value['barangay_id'],
		// 		'street_purok' => $value['street_purok'],
		// 		'complete_address' => $value['complete_address'],
		// 		'land_mark' => $value['land_mark'],
		// 		'created_at' => date('Y-m-d H:i:s'),
		// 		'updated_at' => date('Y-m-d H:i:s')
		// 	);
		// 	$this->db->insert('customer_delivery_infos', $infos);
		// }

		$this->db->where('customer_id', $customerId);
		$this->db->delete('app_cart_gc');

		$this->db->trans_complete();
	}

	public function gc_submitOrderPickup_mod(
		$customerId,
		$groupValue,
		$deliveryDateData,
		$deliveryTimeData,
		$buData,
		$totalAmount,
		$pickingCharge,
		$placeRemarks,
		$pickUpOrDelivery,
		$priceGroup,
		$tempID
	) {

		// dd($customerId, $groupValue, $deliveryDateData, $deliveryTimeData, $buData, $totalData, $convenienceData, $placeRemarks);
		$deliveryDateData_arr = str_replace(array("[", "]"), array("", ""), $deliveryDateData);
		$dates  = explode(',', $deliveryDateData_arr);

		$deliveryTime = str_replace(array("[", "]"), array("", ""), $deliveryTimeData);
		$times  = explode(',', $deliveryTime);

		$bus = str_replace(array("[", "]"), array("", ""), $buData);
		$stores  = explode(',', $bus);

		$placeRemarks = str_replace(array("[", "]"), array("", ""), $placeRemarks);
		$placeRemarks = explode("', '", $placeRemarks);

		$search1 			= array("[", "]");
		$replacewith1 		= array("", "");
		$tempID 		  	= str_replace($search1, $replacewith1, $tempID);
		$tempId  			= explode(',', $tempID);

		$insert_id = $this->app_cart_today_order($customerId, $pickUpOrDelivery);

		$userID = "";
		$this->db->select('*,cart_gc.product_id as gcarProdId  , cart_gc.quantity as cartQty, users.id as userID');
		$this->db->from('app_cart_gc as cart_gc');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = cart_gc.item_code AND gc_prod_price.UOM = cart_gc.uom_symbol');
		$this->db->join('gc_product_items as gc_prod_items', 'gc_prod_items.itemcode = cart_gc.item_code');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = cart_gc.buId AND loc_bu.price_group_code = gc_prod_price.price_group');
		$this->db->join('app_users as users', 'users.customer_id = cart_gc.customer_id');
		$this->db->where('cart_gc.customer_id', $customerId);
		$this->db->where_in('cart_gc.id', $tempId);
		// $this->db->where('cart_gc.buId', $buId);
		// $this->db->where('gc_prod_price.price_group', $priceGroup);
		$query2 = $this->db->get();
		$res2 = $query2->result_array();
		foreach ($res2 as $value) {
			$userID = $value['userID'];
			$data = array(
				'ticket_id' 		=> $insert_id,
				'bu_id' 			=> $value['buId'],
				'product_id' 		=> $value['gcarProdId'],
				'uom_id' 			=> $value['uom'],
				'quantity' 			=> $value['cartQty'],
				'price' 			=> $value['price_with_vat'],
				'total_price' 		=> $value['price_with_vat'] * $value['cartQty'],
				'icoos' 			=> $value['icoos'],
				'pending_status' 	=> '1',
				'user_id' 			=> $value['userID'],
				'created_at' 		=> date('Y-m-d H:i:s'),
				'updated_at' 		=> date('Y-m-d H:i:s')
			);
			$this->db->insert('gc_final_order', $data);
			$this->pusher()->trigger("private-grocery-order-submitted.{$buId}", 'App\Events\GroceryOrderSubmitted', array('message' => ''));
		}

		foreach ($stores as $key => $buId) {

			$date 					= $dates[$key];
			$time 					= $times[$key];
			$placeRemark 			= $placeRemarks[$key];

			$dat = [
				'ticket_id' 	=> $insert_id,
				'bu_id' 		=> $buId,
				'mode_of_order' => 1,
				'order_pickup' 	=> date('Y-m-d H:i:s', strtotime("$date $time")),
				'user_id' 		=> $userID,
				'submitted_at' 	=> date('Y-m-d H:i:s'),
				'created_at' 	=> date('Y-m-d H:i:s'),
				'updated_at' 	=> date('Y-m-d H:i:s')

			];

			$this->db->insert('gc_order_statuses', $dat);
			$s = array("'");
			$r = array("");
			$instruct = str_replace($s, $r, $placeRemark);

			$dat1 = [
				'ticket_id' 	=> $insert_id,
				'bu_id' 		=> $buId,
				'remarks' 		=> $instruct,
				'created_at' 	=> date('Y-m-d H:i:s'),
				'updated_at' 	=> date('Y-m-d H:i:s')
			];
			$this->db->insert('gc_special_instructions', $dat1);
		}

		$dat2 = [
			'ticket_id' => $insert_id,
			'amount' => $totalAmount,
			'picking_charge' => $pickingCharge,
			'change' => '0.00',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		];
		$this->db->insert('customer_bills', $dat2);

		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $customerId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$infos = array(
				'ticket_id' => $insert_id,
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'mobile_number' => $value['mobile_number'],
				'barangay_id' => $value['barangay_id'],
				'street_purok' => $value['street_purok'],
				'complete_address' => $value['complete_address'],
				'land_mark' => $value['land_mark'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_delivery_infos', $infos);
		}
		$this->db->where_in('id', $tempId);
		$this->db->delete('app_cart_gc');
	}


	public function gc_searchProd_mod($search_prod)
	{
		$this->db->select('*');
		$this->db->from('gc_product_items as gc_prod');
		$this->db->join('gc_product_uoms as gc_product_uom', 'gc_product_uom.itemcode = gc_prod.itemcode');
		$this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_prod.itemcode AND gc_product_uom.UOM = gc_prod_price.UOM');

		// $this->db->offset($offset);
		$this->db->where('gc_prod.status', 'active');
		$this->db->where('image!=', 'null');
		$this->db->like('product_name', $search_prod, 'both');
		$this->db->order_by('gc_prod_price.price_with_vat', 'asc');
		// $this->db->where('gc_prod.itemcode','131798');	
		// $this->db->order_by('gc_prod_price.price_id','asc');
		$this->db->limit(100);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'product_name' => $value['product_name'],
				'itemcode' => $value['itemcode'],
				'image'	=> $this->gcproductImage . $value['image'],
				'price'	=> number_format($value['price_with_vat'], 2),
				'uom' => $value['UOM'],
				'uom_id' => $value['uom_id']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function gc_select_uom_mod($itemCode, $groupCode)
	{
		$this->db->select('*');
		$this->db->from('gc_product_prices as gc_prod_price');
		// $this->db->join('gc_product_prices as gc_prod_price', 'gc_prod_price.itemcode = gc_prod_uom.itemcode AND gc_prod_uom.uom_id = gc_prod_price.price_id');
		$this->db->where('gc_prod_price.itemcode', $itemCode);
		$this->db->where('gc_prod_price.status', '1');
		$this->db->where('price_group', $groupCode);
		$this->db->order_by('gc_prod_price.price', 'ASC');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'UOM' => $value['UOM'],
				'price_with_vat' => $value['price_with_vat'],
				'uom_id'  => $value['price_id'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function showDiscount_mod()
	{
		$this->db->select('*');
		$this->db->from('discount_lists as ds');
		$this->db->where('ds.status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'id' =>  $value['id'],
				'discount_name' =>  $value['discount_name'],
				'discount_percent' => $value['discount_percent'] / 100 * 100 . "%"
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function uploadId_mod($userID, $discountId, $name, $idNumber, $imageName)
	{
		$data = array(
			'customer_id' => $userID,
			'discount_id' => $discountId,
			'name'   	 => $name,
			'id_number'	 => $idNumber,
			'image_path' => 'storage/uploads/discount_ids/' . $imageName . '.jpeg',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_discount_storages', $data);
	}

	public function uploadNumber_mod($userID, $number)
	{
		$data = array(
			'customer_id' => $userID,
			'mobile_number' => '+63' . $number,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customer_numbers', $data);
	}

	public function uploadProfilePic_mod($userID, $picName)
	{
		$this->db->select('*');
		$this->db->from('app_users');
		$this->db->where('app_users.customer_id', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {

			$this->db->where('customer_id', $userID);
			$this->db->update('app_users', array('picture' => 'storage/uploads/profile_pics/' . $picName . '.jpeg'));
		}
	}

	// public function uploadId1_mod($userID,$discountId,$name,$idNumber,$imageName,$imageBookletName){
	// 	$data = array(
	//    		'customer_id'=> $userID,
	//    		'discount_id'=> $discountId,
	//    		'name'   	 => $name,
	//    		'id_number'	 => $idNumber,
	//    		'image_path' => 'storage/uploads/discount_ids/'.$imageName.'.jpeg',
	//    		'image_booklet_path' => 'storage/uploads/discount_ids/'.$imageBookletName.'.jpeg',
	//         'created_at' => date('Y-m-d H:i:s'),
	//         'updated_at' => date('Y-m-d H:i:s')
	// 	);
	// 	$this->db->insert('customer_discount_storages', $data);
	// }

	public function loadIdList_mod($userID)
	{
		$this->db->select('*,cs_ds.id as cs_id');
		$this->db->from('customer_discount_storages as cs_ds');
		$this->db->join('discount_lists as ds', 'ds.id = cs_ds.discount_id', 'inner');
		$this->db->where('cs_ds.customer_id', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			if ($value['image_path'] == null) {
				$picture  = "https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg";
			} else {
				$picture = $this->profileImage . $value['image_path'];
			}
			$post_data[] = array(
				'id' => $value['cs_id'],
				'name' =>  $value['name'],
				'discount_name' =>  $value['discount_name'],
				'dicount_id' => $value['id'],
				'discount_no' => $value['id_number'],
				'discount_percent' => $value['discount_percent'] / 100 * 100 . "%",
				'd_photo' =>  $picture
			);
		}
		// foreach ($res as $value) {
		// 	$post_data[] = array(
		// 		'id' => $value['cs_id'],
		// 		'name' =>  $value['name'],
		// 		'discount_name' =>  $value['discount_name'],
		// 		'dicount_id' => $value['id'],
		// 		'discount_no' => $value['id_number'],
		// 		'discount_percent' => $value['discount_percent'] / 100 * 100 . "%"
		// 	);
		// }
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function delete_id_mod($id)
	{
		$this->db->where('customer_discount_storages.id', $id);
		$this->db->delete('customer_discount_storages');
	}

	public function checkidcheckout_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('customer_discount_storages as cs_ds');
		$this->db->where('cs_ds.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkIfHasAddresses_mod($cusId)
	{
		$this->db->select('*');
		$this->db->from('customer_addresses as cs_ds');
		$this->db->where('cs_ds.customer_id', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function changeAccountStat_mod($username)
	{
		$this->db->set('status', '0');
		$this->db->where('username', $username);
		$this->db->update('app_users');
	}

	public function getUserDetails_mod($usernameLogIn)
	{

		// $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));") ;
		$this->db->select('*');
		$this->db->from('app_users as app_us');
		$this->db->where("(app_us.username = '$usernameLogIn' OR app_us.email = '$usernameLogIn')");
		// $this->db->where('app_us.username', $usernameLogIn);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'mobile_number' => strval($value['mobile_number']),
				'user_id' 		=> $value['customer_id'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getusernameusingnumber_mod($mobileNumber)
	{
		$this->db->select('*');
		$this->db->from('app_users as app_us');
		$this->db->where('app_us.mobile_number', $mobileNumber);
		$query = $this->db->get();
		$res = $query->row_array();
		return $res['customer_id'];
	}

	public function verifyOTP_mod($userID, $my_number, $otp_num)
	{


		$this->db->set('status', '1');
		$this->db->where('contact_num', $my_number);
		$this->db->update('user_verification_codes');

		$data = array(
			'user_id' => $userID,
			'contact_num' => $my_number,
			'otp_code' => $otp_num,
			'status' => '0',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('user_verification_codes', $data);
	}

	public function recoverOTP_mod($userID, $my_number, $otp_num)
	{

		$this->db->set('status', '1');
		$this->db->where('contact_num', $my_number);
		$this->db->update('user_recovery_codes');

		$data = array(
			'user_id' => $userID,
			'contact_num' => $my_number,
			'otp_code' => $otp_num,
			'status' => '0',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('user_recovery_codes', $data);
	}

	public function updateProfileOTP_mod($userID, $my_number, $otp_num)
	{

		$this->db->set('status', '1');
		$this->db->where('contact_num', $my_number);
		$this->db->update('user_recovery_codes');

		$data = array(
			'user_id' => $userID,
			'contact_num' => $my_number,
			'otp_code' => $otp_num,
			'status' => '0',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('user_recovery_codes', $data);
	}

	public function itexmo($number, $message, $apicode, $passwd)
	{

		$ch = curl_init();
		$itexmo = array(
			'Email' => 'itsysdev@alturasbohol.com',
			'Password' => $passwd,
			'Recipients' => [$number],
			'Message' => $message,
			'ApiCode' => $apicode,
			'SenderId' => 'ASC',
		);
		curl_setopt($ch, CURLOPT_URL, "https://api.itexmo.com/api/broadcast");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($itexmo));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec($ch);
		curl_close($ch);

		echo 'okay raka?';
	}

	public function verifyOtpCode_mod($otpCode, $mobileNumber, $userID)
	{
		$this->db->select('*');
		$this->db->from('user_verification_codes as usrvrcode');
		$this->db->where('usrvrcode.contact_num', $mobileNumber);
		$this->db->where('usrvrcode.otp_code', $otpCode);
		$this->db->where('usrvrcode.status', '0');
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			$this->changeVerifyOtpStatus_mod($otpCode, $mobileNumber, $userID);
			echo "true";
		} else {
			echo "false";
		}
	}

	public function changeVerifyOtpStatus_mod($otpCode, $mobileNumber, $userID)
	{
		$this->db->set('active_status', '1');
		$this->db->where('customer_id', $userID);
		$this->db->update('app_users');

		$this->db->set('status', '1');
		$this->db->where('contact_num', $mobileNumber);
		$this->db->where('otp_code', $otpCode);
		$this->db->update('user_verification_codes');
	}

	public function checkOtpCode_mod($otpCode, $mobileNumber)
	{
		$this->db->select('*');
		$this->db->from('user_recovery_codes as usrvrcode');
		$this->db->where('usrvrcode.contact_num', $mobileNumber);
		$this->db->where('usrvrcode.otp_code', $otpCode);
		$this->db->where('usrvrcode.status', '0');
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			$this->changeOtpStatus_mod($otpCode, $mobileNumber);
			echo "true";
		} else {
			echo "false";
		}
	}

	public function changeOtpStatus_mod($otpCode, $mobileNumber)
	{
		$this->db->set('status', '1');
		$this->db->where('contact_num', $mobileNumber);
		$this->db->where('otp_code', $otpCode);
		$this->db->update('user_verification_codes');
	}

	public function changePassword_mod($password2, $realMobileNumber)
	{
		$this->db->set('password2', md5($password2));
		$this->db->set('status', '1');
		$this->db->where('mobile_number', $realMobileNumber);
		$this->db->update('app_users');
	}

	public function checkUsernameIfExist_mod($username)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where("(appsu.username = '$username' OR appsu.email = '$username')");
		// $this->db->where('appsu.username', $username);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function getOrderTicket_mod($ticketID)
	{
		$this->db->select('*');
		$this->db->from('toms_customer_orders as toms_orders');
		$this->db->where('toms_orders.ticket_id', $ticketID);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkEmailIfExist_mod($email)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.email', $email);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}

	public function checkPhoneIfExist_mod($phonenumber)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.mobile_number', '0' . $phonenumber);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			echo "true";
		} else {
			echo "false";
		}
	}


	public function displayCartAddOns_mod($cartId)
	{
		echo "string";
		// 		$this->db->select('*');
		// 		$this->db->from('app_cart_main as app_c_main');
		// 		$this->db->join('app_cart_drink as appdrink', 'appdrink.cart_id = app_c_main.id','left');
		// 		$this->db->join('app_cart_fries as appfries', 'appfries.cart_id = app_c_main.id','left');
		// 		$this->db->join('app_cart_sides as appside', 'appside.cart_id = app_c_main.id','left');
		// 		$this->db->join('app_cart_addons_side_items as appaddon', 'appaddon.cart_id = app_c_main.id','left');

		// 		$this->db->where('app_c_main.id',$cartId);
		// 		$query = $this->db->get();
		// 		$res = $query->result_array();
		// 		// echo json_encode($res);
		// 		// exit();
		// 		$post_data = array();
		// 		  foreach($res as $value){
		// 			$post_data[] = array(
		// 				'mobile_number' => strval($value['mobile_number'])  
		// 			);
		// 		  }
		// 		$item = array('user_details' => $post_data);
		// 		echo json_encode($item);
	}


	public function getProvince_ctrl()
	{
		$this->db->select('*');
		$this->db->from('province');
		$this->db->where('status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'prov_id' => $value['prov_id'],
				'prov_name' => $value['prov_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}


	// public function getTown_mod($provId)
	// {
	// 	$this->db->select(' *, tblcharges.town_id as town_id');
	// 	$this->db->from('tbl_delivery_charges as tblcharges');
	// 	$this->db->join('towns as twn', 'twn.town_id = tblcharges.town_id', 'inner');
	// 	$this->db->where('twn.prov_id', $provId);
	// 	$this->db->where('twn.status', '1');
	// 		$this->db->group_by('tblcharges.town_id');
	// 	//$this->db->group_by('twn.town_id');	
	// 	$query = $this->db->get();
	// 	$res = $query->result_array();
	// 	$post_data = array();
	// 	foreach ($res as $value) {
	// 		$post_data[] = array(
	// 			'town_id' => $value['town_id'],
	// 			'town_name' => $value['town_name'],
	// 			'bunit_group_id' => $value['bunit_group_id']
	// 		);
	// 	}
	// 	$item = array('user_details' => $post_data);
	// 	echo json_encode($item);
	// }

	public function getTown_mod($provId)
	{

		// $query = $this->db->query("select * from tbl_delivery_charges
		// 							inner join towns
		// 							on towns.town_id = tbl_delivery_charges.town_id
		// 							where towns.prov_id = '$provId'
		// 							and tbl_delivery_charges.status = '1'
		// 							group by tbl_delivery_charges.town_id ");

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*');
		$this->db->from('tbl_delivery_charges as tblcharges');
		$this->db->join('towns as twn', 'twn.town_id = tblcharges.town_id', 'inner');
		$this->db->where('twn.prov_id', $provId);
		$this->db->where('tblcharges.status', '1');
		$this->db->group_by('tblcharges.town_id');

		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'town_id' => $value['town_id'],
				'town_name' => $value['town_name'],
				'bunit_group_id' => $value['bunit_group_id'],
				'zipcode' => $value['zipcode'],
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function getBarangay_mod($townID)
	{
		$this->db->select('*');
		$this->db->from('barangays');
		$this->db->where('town_id', $townID);
		$this->db->where('status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'brgy_id' => $value['brgy_id'],
				'brgy_name' => $value['brgy_name']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function selectBuildingType_mod()
	{

		$this->db->select('*');
		$this->db->from('building_type');
		$this->db->where('status', '1');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'buildingID' => $value['buildingID'],
				'buildingName' => $value['buildingName']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function updateProfile_mod($userID, $firstName, $lastName, $email, $mobileNumber)
	{
		$this->db->set('firstname',  $firstName);
		$this->db->where('customer_id', $userID);
		$this->db->update('app_users');

		$this->db->set('lastname',  $lastName);
		$this->db->where('customer_id', $userID);
		$this->db->update('app_users');

		$this->db->set('email',  $email);
		$this->db->where('customer_id', $userID);
		$this->db->update('app_users');

		$this->db->set('mobile_number',  '0' . $mobileNumber);
		$this->db->where('customer_id', $userID);
		$this->db->update('app_users');

		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('customer_id', $userID);
		$this->db->update('app_users');
	}

	public function updateNewAddress_mod($userID, $id, $firstName, $lastName, $mobileNum, $houseUnit, $streetPurok, $landMark, $otherNotes, $barangayID, $addressType)
	{

		$this->db->set('firstname',  $firstName);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('lastname',  $lastName);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('mobile_number',  $mobileNum);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('complete_address',  $houseUnit);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('street_purok',  $streetPurok);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('land_mark',  $landMark);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('other_notes',  $otherNotes);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('barangay_id',  $barangayID);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('address_type', $addressType);
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');

		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$this->db->where('customer_id', $userID);
		$this->db->update('customer_addresses');
	}

	public function submitNewAddress_mod(
		$userID,
		$firstName,
		$lastName,
		$mobileNum,
		$houseUnit,
		$streetPurok,
		$landMark,
		$otherNotes,
		$barangayID,
		$addressType
	) {
		$this->db->select('*');
		$this->db->from('customer_addresses');
		$this->db->where('shipping', '1');
		$this->db->where('customer_id', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		if (!empty($res)) {
			$this->db->set('shipping', '0');
			$this->db->where('customer_id', $userID);
			$this->db->update('customer_addresses');

			$data = array(
				'customer_id' => $userID,
				'firstname' => $firstName,
				'lastname' => $lastName,
				'mobile_number' => $mobileNum,
				'barangay_id' => $barangayID,
				'complete_address' => $houseUnit,
				'land_mark' => $landMark,
				'shipping' => '1',
				'other_notes' => $otherNotes,
				'address_type' => $addressType,
				'street_purok' => $streetPurok,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_addresses', $data);
		} else {
			$data = array(
				'customer_id' => $userID,
				'firstname' => $firstName,
				'lastname' => $lastName,
				'mobile_number' => $mobileNum,
				'barangay_id' => $barangayID,
				'complete_address' => $houseUnit,
				'land_mark' => $landMark,
				'shipping' => '1',
				'other_notes' => $otherNotes,
				'address_type' => $addressType,
				'street_purok' => $streetPurok,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			$this->db->insert('customer_addresses', $data);
		}
	}

	public function loadAdresses_mod($idd, $userID)
	{
		$this->db->select('*,cust_add.id as csid, cust_add.mobile_number, twn.town_id as town_ids');
		$this->db->from('customer_addresses as cust_add');
		$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
		$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
		$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
		$this->db->where('cust_add.customer_id', $userID);
		$this->db->where('cust_add.id', $idd);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(

				'd_customerId' => $value['customer_id'],
				'id' => $value['csid'],
				'd_townId' => $value['town_ids'],
				'd_brgId' => $value['barangay_id'],
				'd_townName' => $value['town_name'],
				'd_brgName' => $value['brgy_name'],
				'd_contact' => $value['mobile_number'],
				'd_province_id' => $value['prov_id'],
				'd_province' => $value['prov_name'],
				'add_type' => $value['address_type'],
				'street_purok' => $value['street_purok'],
				'land_mark' => $value['land_mark'],
				'complete_add' => $value['complete_address'],
				'firstname' => $value['firstname'],
				'lastname' => $value['lastname'],
				'zipcode'  => $value['zipcode'],
				'other_notes' => $value['other_notes']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function loadAddress_mod($cusId)
	{

		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,cust_add.id as csid, twn.town_id as town_ids,cust_add.firstname,cust_add.lastname, cust_add.mobile_number');
		$this->db->from('customer_addresses as cust_add');
		$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
		$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
		$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
		$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.town_id = twn.town_id', 'left');
		$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
		$this->db->where('cust_add.customer_id', $cusId);
		$this->db->group_by('cust_add.id');
		$query = $this->db->get();
		$res = $query->result_array();
		if (count($res) == 0) {
			$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
			$this->db->select('*,cust_add.id as csid,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname, cust_add.mobile_number');
			$this->db->from('customer_addresses as cust_add');
			$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
			$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
			$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
			$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.brgy_id = cust_add.barangay_id', 'left');
			$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
			$this->db->where('cust_add.customer_id', $cusId);
			$this->db->group_by('cust_add.id');
			$query2 = $this->db->get();
			$res2 = $query2->result_array();
			$post_data = array();
			foreach ($res2 as $value) {
				$post_data[] = array(
					'd_groupID'  => $value['bunit_group_id'],
					'option' => 'brgy',
					'd_customerId' => $value['customer_id'],
					'id' => $value['csid'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname'],
					'zipcode'  => $value['zipcode'],
					'shipping' => $value['shipping'],
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		} else {
			$post_data = array();
			foreach ($res as $value) {
				$post_data[] = array(
					'option' => 'town',
					'd_groupID'  => $value['bunit_group_id'],
					'd_customerId' => $value['customer_id'],
					'id' => $value['csid'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname'],
					'zipcode'  => $value['zipcode'],
					'shipping'    => $value['shipping'],
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function submitLoadAddress_mod($cusId, $groupID)
	{
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		$this->db->select('*,cust_add.id as csid, twn.town_id as town_ids,cust_add.firstname,cust_add.lastname, cust_add.mobile_number');
		$this->db->from('customer_addresses as cust_add');
		$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
		$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
		$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
		$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.town_id = twn.town_id', 'left');
		$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
		$this->db->where('cust_add.customer_id', $cusId);
		// $this->db->where('tblcharges.bunit_group_id', $groupID);
		$this->db->group_by('cust_add.id');
		$query = $this->db->get();
		$res = $query->result_array();
		if (count($res) == 0) {
			$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
			$this->db->select('*,cust_add.id as csid,twn.town_id as town_ids,cust_add.firstname,cust_add.lastname, cust_add.mobile_number');
			$this->db->from('customer_addresses as cust_add');
			$this->db->join('barangays as brg', 'brg.brgy_id = cust_add.barangay_id', 'inner');
			$this->db->join('towns as twn', 'twn.town_id = brg.town_id', 'inner');
			$this->db->join('province as prov', 'prov.prov_id = twn.prov_id', 'inner');
			$this->db->join('tbl_delivery_charges as tblcharges', 'tblcharges.brgy_id = cust_add.barangay_id', 'left');
			$this->db->join('app_users as uppsu', 'uppsu.customer_id = cust_add.customer_id', 'inner');
			$this->db->where('cust_add.customer_id', $cusId);
			// $this->db->where('tblcharges.bunit_group_id', $groupID);
			$this->db->group_by('cust_add.id');
			$query2 = $this->db->get();
			$res2 = $query2->result_array();
			$post_data = array();
			foreach ($res2 as $value) {
				$townID = $this->checkTownID($value['town_ids']);
				$post_data[] = array(
					'd_groupID'  => $value['bunit_group_id'],
					'option' => 'brgy',
					'd_customerId' => $value['customer_id'],
					'id' => $value['csid'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname'],
					'zipcode'  => $value['zipcode'],
					'shipping' => $value['shipping'],
					'town_id'		=> $townID,
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		} else {
			$post_data = array();
			foreach ($res as $value) {
				$townID = $this->checkTownID($value['town_ids']);
				$post_data[] = array(
					'option' => 'town',
					'd_groupID'  => $value['bunit_group_id'],
					'd_customerId' => $value['customer_id'],
					'id' => $value['csid'],
					'd_townId' => $value['town_ids'],
					'd_brgId' => $value['barangay_id'],
					'd_townName' => $value['town_name'],
					'd_brgName' => $value['brgy_name'],
					'd_contact' => $value['mobile_number'],
					'd_province_id' => $value['prov_id'],
					'd_province' => $value['prov_name'],
					'street_purok' => $value['street_purok'],
					'land_mark' => $value['land_mark'],
					'd_charge_amt' => $value['charge_amt'],
					'minimum_order_amount' => number_format($value['customer_to_pay'] - $value['charge_amt'], 2),
					'firstname' => $value['firstname'],
					'lastname' => $value['lastname'],
					'zipcode'  => $value['zipcode'],
					'shipping'    => $value['shipping'],
					'town_id'		=> $townID,
				);
			}
			$item = array('user_details' => $post_data);
			echo json_encode($item);
		}
	}

	public function checkTownID($townID)
	{
		$this->db->select('town_id');
		$this->db->from('good_delivery_charges');
		$this->db->where('vtype', '1');
		$this->db->where('status', '1');
		$this->db->where_in('town_id', $townID);
		return $this->db->get()->result();
	}

	public function deleteAddress_mod($id)
	{
		$this->db->where('customer_addresses.id', $id);
		$this->db->delete('customer_addresses');
	}

	public function deleteDiscountID_mod($id)
	{
		$this->db->where('customer_discount_storages.id', $id);
		$this->db->delete('customer_discount_storages');
	}

	public function deleteCartGc_mod($cusID, $buCode)
	{
		$this->db->where('app_cart_gc.customer_id', $cusID);
		// $this->db->where('app_cart_gc.buId', $buCode);
		$this->db->delete('app_cart_gc');

		echo $cusID;
		echo "\n";
		echo $buCode;
	}

	public function showRiderDetails_mod($ticketId)
	{
		$this->db->select('*,rider_data.id as rider_id, tik.id as ticket_id');
		$this->db->from('tickets as tik');
		$this->db->join('toms_tag_riders as tag_rider', 'tag_rider.ticket_id = tik.id', 'inner');
		$this->db->join('partial_tag_riders as partial_riders', 'partial_riders.ticket_id = tik.id');
		$this->db->join('toms_riders_data as rider_data', 'rider_data.id = partial_riders.rider_id', 'inner');
		$this->db->where('tik.id', $ticketId);
		$this->db->group_by('partial_riders.rider_id');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'r_picture' =>  $this->cssadmin . $value['r_picture'],
				'r_firstname' =>  $value['r_firstname'],
				'r_lastname' =>  $value['r_lastname'],
				'rm_picture' =>  $this->cssadmin . $value['rm_picture'],
				'rm_brand' =>  $value['rm_brand'],
				'rm_color' => $value['rm_color'],
				'rm_plate_no' => $value['rm_plate_num'],
				'rm_mobile_no' => $value['r_mobile'],
				'rm_id' => $value['rider_id'],
				'rider_stat' => $value['main_rider_stat'],
				'ticket_id'  => $value['ticket_id'],
				'delivered_status' => $value['delevered_status']

			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function updateDefaultShipping_mod($id, $customerId)
	{
		$this->db->set('shipping', '0');
		$this->db->where('customer_id', $customerId);
		$this->db->update('customer_addresses');

		$this->db->set('shipping', '1');
		$this->db->where('id', $id);
		$this->db->update('customer_addresses');
	}

	public function updateDefaultNumber_mod($id, $customerId)
	{
		$this->db->set('in_use', '0');
		$this->db->where('customer_id', $customerId);
		$this->db->update('customer_numbers');

		$this->db->set('in_use', '1');
		$this->db->where('id', $id);
		$this->db->update('customer_numbers');

		$this->db->select('*');
		$this->db->from('customer_numbers');
		$this->db->where('id', $id);
		$this->db->where('customer_id', $customerId);
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $value) {

			$phone_no = $value['mobile_number'];
			$number = preg_replace('/^\+?63|\|1|\D/', '', ($phone_no));

			$this->db->set('mobile_number', '0' . $number);
			$this->db->where('customer_id', $customerId);
			$this->db->update('app_users');
		}
	}

	public function updateNumber_mod($id, $updateNumber)
	{
		$this->db->set('mobile_number', '+63' . $updateNumber);
		$this->db->where('id', $id);
		$this->db->update('customer_numbers');
	}

	public function updatePickupAt_mod($deliveryDateData, $deliveryTimeData, $userID)
	{

		$modeOfOrder = '1';
		$insert_id = $this->app_cart_today_order($userID, $modeOfOrder);

		$search1 = array("[", "]");
		$replacewith1 = array("", "");
		$deliveryDate = str_replace($search1, $replacewith1, $deliveryDateData);
		$deliveryTime = str_replace($search1, $replacewith1, $deliveryTimeData);

		$date_array = explode(',', $deliveryDate);
		$time_array = explode(',', $deliveryTime);

		$this->db->select('*');
		$this->db->from('toms_customer_orders');
		$this->db->join('fd_products', 'fd_products.product_id = toms_customer_orders.product_id', 'inner');
		$this->db->join('locate_tenants', 'locate_tenants.tenant_id = fd_products.tenant_id', 'inner');
		$this->db->where('ticket_id', $insert_id);
		$this->db->group_by('fd_products.tenant_id');
		$query = $this->db->get();
		$res = $query->result_array();

		foreach ($res as $key) {
			$date = $date_array[$key];
			$time = $time_array[$key];

			$this->db->set('pickup_at', date('Y-m-d H:i:s', strtotime("$date $time")));
			$this->db->where('ticket_id', $insert_id);
			$this->db->update('toms_customer_orders');
		}
	}

	public function pusher()
	{
		$app_id = '1106021';
		$app_key = '41ffaa2dad5288031ed1';
		$app_secret = 'ffe4eb654395eb6325c0';
		$app_cluster = 'ap1';
		$pusher = new Pusher\Pusher($app_key, $app_secret, $app_id, array('cluster' => $app_cluster));
		return $pusher;
	}

	public function viewTenantCategories_mod($tenant_id)
	{
		$this->db->select('*');
		$this->db->from('fd_categories as fd_cat');
		$this->db->where('fd_cat.tenant_id', $tenant_id);
		$this->db->where('fd_cat.active', '1');
		// $this->db->limit(10);
		// $this->db->offset($offset);
		// $this->db->group_by('fd_cat.category');
		$this->db->order_by('fd_cat.category');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'category_id' => $value['category_id'],
				'category' => $value['category'],
				'image' => $this->productImage . $value['image']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function viewAddon_mod($cusId)
	{

		$this->db->select('*,appcart.productId as appcartproductId,side_id.side_id as side_id,side_id.side_uom as side_uom,fries_id.fries_id as fries_id,fries_id.fries_uom as fries_uom,appcart.uom as productUom,appcart.tenantId as tenant_id,appcart.flavor as flavor_id,appcart.productId as productId,drink_id.drink_id as drink_id,drink_id.drink_uom as drink_uom,appcart.quantity as cart_qty,appcart.id as d_id,fd_flavors.addon_price as flavor_price,loc_tenants.tenant as loc_tenant_name,loc_bu.business_unit as loc_bu_name,main_prod_price.price as prod_price,main_prod.product_name as prod_name,fd_side_price.price as side_price,fd_side_name.product_name as side_name,fd_fries_price.price as fries_price,fd_fries_name.product_name as fries_name ,fd_drink_name.product_name as drink_name, fd_drink_price.price as drink_price');
		$this->db->from('app_cart_main as appcart');

		$this->db->join('fd_addon_flavors as fd_flavors', 'fd_flavors.flavor_id = appcart.flavor AND fd_flavors.product_id = appcart.productId', 'left');

		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = appcart.buId', 'left');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = appcart.tenantId', 'left');

		$this->db->join('fd_products as main_prod', 'main_prod.product_id = appcart.productId', 'inner');
		$this->db->join('fd_product_prices as main_prod_price', 'main_prod_price.product_id = appcart.productId AND IFNULL(main_prod_price.uom_id,0) = IFNULL(appcart.uom,0)', 'left');

		$this->db->join('app_cart_drink as drink_id', 'drink_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_drink_name', 'fd_drink_name.product_id = drink_id.drink_id', 'left');
		$this->db->join('fd_product_prices as fd_drink_price', 'fd_drink_price.product_id = drink_id.drink_id AND IFNULL(fd_drink_price.uom_id,0) = IFNULL(drink_id.drink_uom,0)', 'left');

		$this->db->join('app_cart_fries as fries_id', 'fries_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_fries_name', 'fd_fries_name.product_id = fries_id.fries_id', 'left');
		$this->db->join('fd_product_prices as fd_fries_price', 'fd_fries_price.product_id = fries_id.fries_id AND IFNULL(fd_fries_price.uom_id,0) = IFNULL(fries_id.fries_uom,0)', 'left');

		$this->db->join('app_cart_sides as side_id', 'side_id.cart_id = appcart.id', 'left');
		$this->db->join('fd_products as fd_side_name', 'fd_side_name.product_id = side_id.side_id', 'left');
		$this->db->join('fd_product_prices as fd_side_price', 'fd_side_price.product_id = side_id.side_id AND IFNULL(fd_side_price.uom_id,0) = IFNULL(side_id.side_uom,0)', 'left');

		$this->db->where('appcart.customerId', $cusId);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$totalitemprice = 0;
		$totalPayablePrice = 0;

		$tenant_ids = [];
		$post_data = array();
		foreach ($res as $value) {
			$totalitemprice = $value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'];
			$totalPayablePrice = ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty'];
			$tenant_ids[] = $value['tenant_id'];
			$post_data[] = array(
				'appcartproductId' => $value['appcartproductId'],
				'd_productId' => $value['productId'],
				'd_productUom' => $value['productUom'],
				'd_flavor_id' => $value['flavor_id'],
				'flavor_price' => $value['flavor_price'],
				'd_drink_id' => $value['drink_id'],
				'd_drink_uom' => $value['drink_uom'],
				'd_fries_id' => $value['fries_id'],
				'd_fries_uom' => $value['fries_uom'],
				'd_side_id' => $value['side_id'],
				'd_side_uom' => $value['side_uom'],
				'd_id' => $value['d_id'],
				'prod_name' => $value['prod_name'],
				'cart_qty' => $value['cart_qty'],
				'loc_bu_name' => $value['loc_bu_name'],
				'loc_tenant_name' => $value['loc_tenant_name'],
				'flavor_price' => $value['flavor_price'],
				'prod_price' => $value['prod_price'],
				'drink_name' => $value['drink_name'],
				'drink_price' => $value['drink_price'],
				'fries_name' => $value['fries_name'],
				'fries_price' => $value['fries_price'],
				'side_name' => $value['side_name'],
				'side_price' => $value['side_price'],
				'total_price' => ($value['prod_price'] + $value['drink_price'] + $value['fries_price'] + $value['side_price'] + $value['flavor_price']) * $value['cart_qty']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	function checkIfBf_mod($userID)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('app_customer_temp_orders as temp_orders');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = temp_orders.product_id');
		$this->db->where('temp_orders.customerId', $userID);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		$now = strtotime(date('H:i:s'));
		foreach ($res as $value) {
			$bf_start = strtotime($value['breakfast_start']);
			$bf_end = strtotime($value['breakfast_end']);
			if (!$bf_start && !$bf_end) {
				$avail = true;
			} else {
				$avail = $bf_start >= $now && $now <= $bf_end;
			}
			$post_data[] = $avail;
		}

		$xb = [];

		$xb[] = ['isavail' => !in_array(false, $post_data)];

		$item = array('user_details' => $xb);
		echo json_encode($item);
	}

	public function getTotalFee_mod($ticket_id)
	{
		$this->db->select('*');
		$this->db->from('tickets as tik');
		$this->db->join('customer_bills as cust_bill', 'cust_bill.ticket_id = tik.id', 'inner');
		$this->db->where('tik.ticket', $ticket_id);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				"amount" => $value['amount'],
				"delivery_charge" => $value['delivery_charge']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function checkBuId_mod($cusId, $bunitCode)
	{
		$this->db->select('*');
		$this->db->from('app_cart_gc as cart_temp');
		$this->db->where('cart_temp.customer_id', $cusId);
		$this->db->where('cart_temp.buId', $bunitCode);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'bunitCode' => $value['buId']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}



	public function search_item_mod($query, $unitGroupId)
	{
		$this->db->select('*,fd_prod.tenant_id as tId');
		$this->db->from('fd_product_categories as fd_prod_cat');
		$this->db->join('fd_products as fd_prod', 'fd_prod.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_product_prices as fd_prod_price', 'fd_prod_price.product_id = fd_prod_cat.product_id');
		$this->db->join('fd_uoms as fd_uom', 'fd_uom.id = fd_prod_price.uom_id', 'left');
		$this->db->join('locate_tenants as loc_tenants', 'loc_tenants.tenant_id = fd_prod.tenant_id');
		$this->db->join('locate_business_units as loc_bu', 'loc_bu.bunit_code = loc_tenants.bunit_code');
		$this->db->where('fd_prod.active', '1');
		$this->db->where('fd_prod_price.primary_uom', '1');
		$this->db->where('fd_prod_price.price!=', '0.00');
		$this->db->like('fd_prod.product_name', $query, 'both');
		$this->db->where('loc_bu.group_id', $unitGroupId);
		$this->db->limit(30);
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();
		foreach ($res as $value) {
			$post_data[] = array(
				'unit_measure' => $value['unit_measure'],
				'product_id' => $value['product_id'],
				'product_uom' => $value['uom_id'],
				'tenant_id' => $value['tId'],
				'tenant_name' => $value['tenant'],
				'product_name' => $value['product_name'],
				'price' => $value['price'],
				'image' => $this->productImage . $value['image'],
				'prod_bu' => $value['business_unit'],
				'bu_id' => $value['bunit_code']
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function updatePassword_mod($cusId, $currentpass, $newpass)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.customer_id', $cusId);
		$this->db->where('appsu.password2', md5($currentpass));
		$query = $this->db->get();
		$res = $query->row_array();

		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.customer_id', $cusId);
		$this->db->where('appsu.password2', md5($newpass));
		$query = $this->db->get();
		$res2 = $query->row_array();

		if (empty($res)) {
			echo "wrongPass";
		} else if (!empty($res2)) {
			echo "samePass";
		}
		$this->db->set('password2', md5($newpass));
		$this->db->where('customer_id', $cusId);
		$this->db->update('app_users');
	}

	public function updateUsername_mod($cusId, $currentpass, $newUsername)
	{
		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.customer_id', $cusId);
		$this->db->where('appsu.password2', md5($currentpass));
		$query = $this->db->get();
		$res = $query->row_array();

		$this->db->select('*');
		$this->db->from('app_users as appsu');
		$this->db->where('appsu.customer_id', $cusId);
		$this->db->where('appsu.username', $newUsername);
		$query = $this->db->get();
		$res2 = $query->row_array();

		if (empty($res)) {
			echo "wrongPass";
		} else if (!empty($res2)) {
			echo "userTaken";
		} else {
			$this->db->set('username', $newUsername);
			$this->db->where('customer_id', $cusId);
			$this->db->update('app_users');
		}
	}

	public function chat_mod($from, $to, $ticketId)
	{

		$this->db->select();
		$this->db->from('messages as mes');

		$this->db->where('contact_type_from', 'CUSTOMER');
		$this->db->where('mes.from_id', $from);
		$this->db->where('mes.contact_type_to', 'RIDER');
		$this->db->where('mes.to_id', $to);
		$this->db->where('mes.ticket_id', $ticketId);

		$this->db->or_where('contact_type_from', 'RIDER');
		$this->db->where('mes.from_id', $to);
		$this->db->where('mes.contact_type_to', 'CUSTOMER');
		$this->db->where('mes.to_id', $from);
		$this->db->where('mes.ticket_id', $ticketId);


		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		$res = $query->result_array();
		$post_data = array();

		foreach ($res as $value) {
			$f = 'false';
			if ($value['from_id'] == $from && $value['contact_type_from'] == 'CUSTOMER') {
				$f = 'true';
			}

			$post_data[] = array(
				'body'	=> $value['body'],
				'isSender'  => $f,
			);
		}
		$item = array('user_details' => $post_data);
		echo json_encode($item);
	}

	public function send_chat_mod($from, $to, $message, $ticketId)
	{
		$data = array(
			'contact_type_from' => 'CUSTOMER',
			'from_id' 			=> $from,
			'contact_type_to' 	=> 'RIDER',
			'to_id' 			=> $to,
			'body' 				=> $message,
			'ticket_id'			=> $ticketId,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('messages', $data);
	}
}
