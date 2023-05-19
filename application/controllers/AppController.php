<?php
defined('BASEPATH') or exit('No direct script access allowed');

define('SECRET_KEY', 'SoAxVBnw8PYHzHHTFBQdG0MFCLNdmGFf');
define('SECRET_IV', 'T1g994xo2UAqG81M');
define('ENCRYPT_METHOD', 'AES-256-CBC');

class AppController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Manila');
		$this->load->model('AppModel');
	}

	public function decrypt($string)
	{
		return openssl_decrypt($string, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
	}

	public function appCreateAccountCtrl()
	{
		// if(isset($_POST['townId']) && isset($_POST['barrioId']) && isset($_POST['username']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['suffix']) && isset($_POST['password']) && isset($_POST['birthday']) && isset($_POST['contactNumber'])){

		$this->AppModel->appCreateAccountMod(
			$this->decrypt($_POST['firstName']), 
			$this->decrypt($_POST['lastName']), 
			$this->decrypt($_POST['email']), 
			$this->decrypt($_POST['birthday']), 
			$this->decrypt($_POST['contactNumber']), 
			$this->decrypt($_POST['username']), 
			$this->decrypt($_POST['password']));
		// }
	}

	public function checkLoginCtrl()
	{
		if (isset($_POST['_usernameLogIn']) && isset($_POST['_passwordLogIn'])) {
			$this->AppModel->signInUserMod($this->decrypt($_POST['_usernameLogIn']), $this->decrypt($_POST['_passwordLogIn']));
		// $this->AppModel->signInUserMod('jen', '12345');
		}
	}

	public function getUserDataCtrl()
	{
		if (isset($_POST['id'])) {
		$this->AppModel->getUserDataMod($this->decrypt($_POST['id']));
		// $this->AppModel->getUserDataMod('368');
		}

	}

	public function getPlaceOrderDataCtrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getPlaceOrderDataMod($this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->getPlaceOrderDataMod('368');
	}

	public function checkAllowedPlace_ctrl()
	{
		if (isset($_POST['townId'])) {
			$this->AppModel->checkAllowedPlaceMod(
				$this->decrypt($_POST['townId']));
		}
	}

	public function checkFee_ctrl()
	{
		if (isset($_POST['townId'])) {
			$this->AppModel->checkFeeMod(
				$this->decrypt($_POST['townId']));
			// $this->AppModel->checkFeeMod('6');
		}
	}

	public function getOrderDataCtrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getOrderDataMod(
				$this->decrypt($_POST['cusId']));
			// $this->AppModel->getOrderDataMod('2282');
		}
	}

	public function getMobileNumber_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getMobileNumber_mod(
				$this->decrypt($_POST['cusId']));
			// $this->AppModel->getOrderDataMod('2282');
		}
	}

	public function getAppUser_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getAppUser_mod(
				$this->decrypt($_POST['cusId']));
			// $this->AppModel->getOrderDataMod('2282');
		}
	}

	public function getSubtotalCtrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getSubtotalMod(
				$this->decrypt($_POST['customerId']));
			// $this->AppModel->getSubtotalMod('1628');
		}
	}


	public function getLastItems_ctrl()
	{
		if (isset($_POST['orderNo'])) {
			$this->AppModel->getLastItems_mod(
				$this->decrypt($_POST['orderNo']));
		}
	}

	public function getAllowedLoc_ctrl()
	{
		if (isset($_POST['d'])) {
			$this->AppModel->getAllowedLoc_mod();
		}
	}

	public function getBuGroupID_ctrl()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->getBuGroupID_mod(
				$this->decrypt($_POST['cusId']));
		}
	}

	public function gcGetAddress_ctrl()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->gcGetAddress_mod(
				$this->decrypt($_POST['cusId']));
		}
	}

	public function gcLoadBu_ctrl()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->gcLoadBu_mod(
				$this->decrypt($_POST['cusId']));
		}
	}

	public function gcLoadBu2_ctrl()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->gcLoadBu2_mod(
				$this->decrypt($_POST['cusId']),
				$this->decrypt($_POST['tempID']));
		}
	}

	public function getBu_ctrl()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->getBu_mod(
				$this->decrypt($_POST['cusId']));
		}
	}

	public function getBu_ctrl1()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->getBu_mod1(
				$this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->getBu_mod1('2423');
	}

	public function getBu_ctrl2()
	{ // para mailhan ug nag double ang malls ang iyang gipang palitan
		if (isset($_POST['cusId'])) {
			$this->AppModel->getBu_mod2(
				$this->decrypt($_POST['cusId']), 
				$this->decrypt($_POST['productID']));
		}
		// $this->AppModel->getBu_mod1('2423');
	}

	public function getTenant_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getTenant_mod(
				$this->decrypt($_POST['cusId']));
			// 	// $this->AppModel->getTenant_mod('163');
		}
		// $this->AppModel->getTenant_mod('2423');
	}

	public function getTenant_ctrl2()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getTenant_mod2(
				$this->decrypt($_POST['cusId']), 
				$this->decrypt($_POST['productID']));
			// 	// $this->AppModel->getTenant_mod('163');
		}
		// $this->AppModel->getTenant_mod('2423');
	}


	public function getTicketNoOnFoods_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getTicketNoOnFoods_mod(
				$this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->getTicketNoFood_mod('1815');
	}

	public function getTicketNoOnGoods_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getTicketNoOnGoods_mod(
				$this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->getTicketNoFood_mod('1815');
	}

	public function getTicketNoFood_ontrans_ctrl()
	{
		if(isset($_POST['cusId'])){
			$this->AppModel->getTicketNoFood_ontrans_mod(
				$this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->getTicketNoFood_ontrans_mod('2423');
	}


	public function getTicketNoFood_delivered_ctrl()
	{
		if(isset($_POST['cusId'])){
			$this->AppModel->getTicketNoFood_delivered_mod(
				$this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->getTicketNoFood_delivered_mod('2423');
	}

	public function getTicket_cancelled_ctrl()
	{
		// if(isset($_POST['cusId'])){
		// 	$this->AppModel->getTicketNoFood_delivered_mod($_POST['cusId']);
		// }
		$this->AppModel->getTicket_cancelled_mod(
			$this->decrypt($_POST['cusId']));
	}




	// public function getTicketNoGood_ctrl(){
	// 		$this->AppModel->getTicketNoGood_mod($_POST['cusId']);
	// 		// $this->AppModel->getTicketNoGood_mod('465');
	// }

	
	public function lookItems_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookItems_mod(
				$this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_mod('210705-2-001');
	}

	public function getContainer_ctrl()
	{
		$this->AppModel->getContainer_mod(
			$this->decrypt($_POST['ticketId']), 
			$this->decrypt($_POST['tenantId']));
	}

	public function orderTimeFrameDelivery_ctrl()
	{
		$this->AppModel->orderTimeFrameDelivery_mod(
			$this->decrypt($_POST['ticketNo']), 
			$this->decrypt($_POST['tenantId']));
	}

	public function orderTimeFramePickUp_ctrl()
	{
		$this->AppModel->orderTimeFramePickUp_mod(
			$this->decrypt($_POST['ticketNo']), 
			$this->decrypt($_POST['tenantId']));
	}

	public function orderTimeFramePickUpGoods_ctrl()
	{
		$this->AppModel->orderTimeFramePickUpGoods_mod(
			$this->decrypt($_POST['ticketId']), 
			$this->decrypt($_POST['buId']));
	}

	public function getCancelStatus_ctrl()
	{
		// if (isset($_POST['ticketNo'])) {
			// $this->AppModel->orderTimeFrame_mod($this->decrypt($_POST['ticketNo']));
		// }
		// $this->AppModel->getCancelStatusMod('ticketNo');
		$this->AppModel->getCancelStatusMod(
			$this->decrypt($_POST['ticketNo']));
	}


	public function lookItems_segregate_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookItems_segregate_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_segregatemod('210104-2-001');
	}

	public function lookItems_segregate2_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookItems_segregate2_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_segregatemod('210104-2-001');
	}

	public function getTotalAmount_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getTotalAmount_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_segregatemod('210104-2-001');
	}

	public function getAmountPerTenantmod_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getAmountPerTenantmod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->lookItems_segregatemod('210104-2-001');
	}

	public function lookitems_good_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->lookitems_good_mod($this->decrypt($_POST['ticketNo']));
			// $this->AppModel->lookitems_good_mod('210420-2-001');
		}
	}

	public function loadCartData_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadCartData_mod($_POST['cusId']);
			// $this->AppModel->loadCartData_mod('1628');
		}
	}


	public function loadCartDataNew_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadCartDataNew_mod(
				$this->decrypt($_POST['cusId']));
			// $this->AppModel->loadCartDataNew_mod(3822);
		}
	}

	public function loadCartDataNew2_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadCartDataNew2_mod(
				$this->decrypt($_POST['cusId']), 
				$this->decrypt($_POST['productID']));
			// $this->AppModel->loadCartDataNew_mod(3822);
		}
	}


	public function loadCartData_sides_ctrl()
	{
		$this->AppModel->loadCartData_sides_mod('163'); //tiwason pd
	}

	public function clearCustomerCartPerItem_ctrl()
	{
		if (isset($_POST['cartId'])) {
			$this->AppModel->clearCustomerCartPerItem($_POST['cartId']);
			// $this->AppModel->removeItemFromCart_mod('179','163');
		}
	}

	public function displayOrder_ctrl()
	{
		if (isset($_POST['cusId']) && isset($_POST['tenantId'])) {
			$this->AppModel->displayOrder_mod(
				$this->decrypt($_POST['cusId']), 
				$this->decrypt($_POST['tenantId']));
			// $this->AppModel->displayOrder_mod('163','9');
		}
	}

	public function getDiscountID_ctrl()
	{
		if (isset($_POST['cusId']) && isset($_POST['discountName'])) {
			$this->AppModel->getDiscountID_mod(
				$this->decrypt($_POST['cusId']), 
				$this->decrypt($_POST['discountName']));
			// $this->AppModel->displayOrder_mod('163','9');
		}
	}

	public function trapTenantLimit_ctrl()
	{
		if (isset($_POST['cusId']) && isset($_POST['townId'])) {
			$this->AppModel->trapTenantLimit_mod(
				$this->decrypt($_POST['townId']), 
				$this->decrypt($_POST['cusId']));
			// $this->AppModel->trapTenantLimit_mod('1','1628');
		}
	}


	public function getAmountPertenant_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->getAmountPertenant_mod($_POST['cusId']);
			// $this->AppModel->getAmountPertenant_mod('163');
		}
	}

	public function getTenant_perbu_ctrl()
	{
		if (isset($_POST['buId'])) {
			$this->AppModel->getTenant_perbu_mod($_POST['buId']);
		}
	}

	//node

	public function display_tenant_ctrl()
	{
		$this->AppModel->display_tenant_mod(
			$this->decrypt($_POST['buCode']), 
			$this->decrypt($_POST['globalID']));
	}

	public function display_restaurant_ctrl()
	{
		if (isset($_POST['categoryId'])) {
			$this->AppModel->display_restaurant_mod(
				$this->decrypt($_POST['categoryId']));
			// $this->AppModel->display_restaurant_mod('27');
		}
	}

	public function display_item_data_ctrl()
	{
		if (isset($_POST['prodId']) && isset($_POST['productUom'])) {
			$this->AppModel->display_item_data_mod(
				$this->decrypt($_POST['prodId']), 
				$this->decrypt($_POST['productUom']));
		}
		// $this->AppModel->display_item_data_mod('97', null);
	}

	public function getSuggestion_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->getSuggestion_mod(
				$this->decrypt($_POST['prodId']));
		}
		// $this->AppModel->display_item_data_mod('97', null);
	}

	public function add_to_cart_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->add_to_cart_mod(
				$_POST['customerId'],
				$_POST['buCode'],
				$_POST['tenantCode'],
				$_POST['prodId'],
				$_POST['productUom'],
				$_POST['flavorId'],
				$_POST['drinkId'],
				$_POST['drinkUom'],
				$_POST['friesId'],
				$_POST['friesUom'],
				$_POST['sideId'],
				$_POST['sideUom'],
				$_POST['selectedSideItems'],
				$_POST['selectedSideItemsUom'],
				$_POST['selectedDessertItems'],
				$_POST['selectedDessertItemsUom'],
				$_POST['_counter']
			);
		}
		// $this->AppModel->add_to_cart_mod(1,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
	}

	public function addTempCartPickup_ctrl()
	{
	
		$this->AppModel->addTempCartPickup_mod(
			$this->decrypt($_POST['userID']),
			$this->decrypt($_POST['orderID']),
			$this->decrypt($_POST['productID']),
			$this->decrypt($_POST['uomID']),
			$this->decrypt($_POST['quantity']),
			$this->decrypt($_POST['price']),
			$this->decrypt($_POST['measurement']),
			$this->decrypt($_POST['totalPrice']),
			$this->decrypt($_POST['icoos']),

		);
	
	}

	public function addTempCartDelivery_ctrl()
	{
	
		$this->AppModel->addTempCartDelivery_mod(
			$this->decrypt($_POST['userID']),
			$this->decrypt($_POST['orderID']),
			$this->decrypt($_POST['productID']),
			$this->decrypt($_POST['uomID']),
			$this->decrypt($_POST['quantity']),
			$this->decrypt($_POST['price']),
			$this->decrypt($_POST['measurement']),
			$this->decrypt($_POST['totalPrice']),
			$this->decrypt($_POST['icoos']),
			
		);
	
	}

	public function addToCartNew_ctrl()
	{
		if(isset($_POST['userID'])){
			$this->AppModel->addToCartNew_mod(
				$this->decrypt($_POST['userID']),
				$this->decrypt($_POST['prodId']),
				$this->decrypt($_POST['uomId']),
				$this->decrypt($_POST['_counter']),
				$this->decrypt($_POST['uomPrice']),
				$this->decrypt($_POST['measurement']),
	
				$this->decrypt($_POST['choiceUomIdDrinks']),
				$this->decrypt($_POST['choiceIdDrinks']),
				$this->decrypt($_POST['choicePriceDrinks']),
	
				$this->decrypt($_POST['choiceUomIdFries']),
				$this->decrypt($_POST['choiceIdFries']),
				$this->decrypt($_POST['choicePriceFries']),
	
				$this->decrypt($_POST['choiceUomIdSides']),
				$this->decrypt($_POST['choiceIdSides']),
				$this->decrypt($_POST['choicePriceSides']),

				$this->decrypt($_POST['suggestionIdFlavor']),
				$this->decrypt($_POST['productSuggestionIdFlavor']),
				$this->decrypt($_POST['suggestionPriceFlavor']),

				$this->decrypt($_POST['suggestionIdWoc']),
				$this->decrypt($_POST['productSuggestionIdWoc']),
				$this->decrypt($_POST['suggestionPriceWoc']),

				$this->decrypt($_POST['suggestionIdTos']),
				$this->decrypt($_POST['productSuggestionIdTos']),
				$this->decrypt($_POST['suggestionPriceTos']),

				$this->decrypt($_POST['suggestionIdTon']),
				$this->decrypt($_POST['productSuggestionIdTon']),
				$this->decrypt($_POST['suggestionPriceTon']),

				$this->decrypt($_POST['suggestionIdTops']),
				$this->decrypt($_POST['productSuggestionIdTops']),
				$this->decrypt($_POST['suggestionPriceTops']),

				$this->decrypt($_POST['suggestionIdCoi']),
				$this->decrypt($_POST['productSuggestionIdCoi']),
				$this->decrypt($_POST['suggestionPriceCoi']),

				$this->decrypt($_POST['suggestionIdCoslfm']),
				$this->decrypt($_POST['productSuggestionIdCoslfm']),
				$this->decrypt($_POST['suggestionPriceCoslfm']),

				$this->decrypt($_POST['suggestionIdSink']),
				$this->decrypt($_POST['productSuggestionIdSink']),
				$this->decrypt($_POST['suggestionPriceSink']),

				$this->decrypt($_POST['suggestionIdBcf']),
				$this->decrypt($_POST['productSuggestionIdBcf']),
				$this->decrypt($_POST['suggestionPriceBcf']),

				$this->decrypt($_POST['suggestionIdCc']),
				$this->decrypt($_POST['productSuggestionIdCc']),
				$this->decrypt($_POST['suggestionPriceCc']),

				$this->decrypt($_POST['suggestionIdCom']),
				$this->decrypt($_POST['productSuggestionIdCom']),
				$this->decrypt($_POST['suggestionPriceCom']),

				$this->decrypt($_POST['suggestionIdCoft']),
				$this->decrypt($_POST['productSuggestionIdCoft']),
				$this->decrypt($_POST['suggestionPriceCoft']),

				$this->decrypt($_POST['suggestionIdCymf']),
				$this->decrypt($_POST['productSuggestionIdCymf']),
				$this->decrypt($_POST['suggestionPriceCymf']),

				$this->decrypt($_POST['suggestionIdTomb']),
				$this->decrypt($_POST['productSuggestionIdTomb']),
				$this->decrypt($_POST['suggestionPriceTomb']),

				$this->decrypt($_POST['suggestionIdCosv']),
				$this->decrypt($_POST['productSuggestionIdCosv']),
				$this->decrypt($_POST['suggestionPriceCosv']),

				$this->decrypt($_POST['suggestionIdTop']),
				$this->decrypt($_POST['productSuggestionIdTop']),
				$this->decrypt($_POST['suggestionPriceTop']),

				$this->decrypt($_POST['suggestionIdTocw']),
				$this->decrypt($_POST['productSuggestionIdTocw']),
				$this->decrypt($_POST['suggestionPriceTocw']),

				$this->decrypt($_POST['suggestionIdNameless']),
				$this->decrypt($_POST['productSuggestionIdNameless']),
				$this->decrypt($_POST['suggestionPriceNameless']),

				$this->decrypt($_POST['selectedSideOnPrice']),
				$this->decrypt($_POST['selectedSideItems']),
				$this->decrypt($_POST['selectedSideItemsUom']),

				$this->decrypt($_POST['selectedSideSides']),
				$this->decrypt($_POST['selectedSideDessert']),
				$this->decrypt($_POST['selectedSideDrinks'])
			);
		}
	}

	public function selectSuffix_ctrl()
	{
		$this->AppModel->selectSuffix_mod();
	}

	public function getTowns_ctrl()
	{
		$this->AppModel->getTowns_mod();
	}

	public function getbarrio_ctrl()
	{
		if (isset($_POST['townId'])) {
			$this->AppModel->getbarrio_mod(
				$this->decrypt($_POST['townId']));
		}
	}


	//

	public function updateCartQty_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->updateCartQty_mod(
				$this->decrypt($_POST['id']), 
				$this->decrypt($_POST['qty']));
			// $this->AppModel->updateCartQty_mod(36, 12);
		}
	}

	public function updateCartStk_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->updateCartStk_mod(
				$this->decrypt($_POST['id']), 
				$this->decrypt($_POST['stk']));
			// $this->AppModel->updateCartQty_mod(36, 12);
		}
	}

	public function updateCartIcoos_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->updateCartIcoos_mod(
				$this->decrypt($_POST['id']), 
				$this->decrypt($_POST['stk']));
			// $this->AppModel->updateCartQty_mod(36, 12);
		}
	}

	public function getCounter_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getCounter_mod(
				$this->decrypt($_POST['customerId']));
		}
	}

	public function placeOrder_ctrl()
	{
		$this->AppModel->placeOrder_delivery_mod(
			$this->decrypt($_POST['cusId']),
			$this->decrypt($_POST['deliveryDateData']),
			$this->decrypt($_POST['deliveryTimeData']),
			$this->decrypt($_POST['selectedDiscountType']),
			$this->decrypt($_POST['deliveryCharge']),
			$this->decrypt($_POST['amountTender']),
			$this->decrypt($_POST['specialInstruction']),
			$this->decrypt($_POST['getTenantData']),
			$this->decrypt($_POST['productID'])
			// '368',
			// '38',
			// '38448',
			// '4',
			// '5',
			// '6'
			// '7',
			// '8',
			// '9',
			// '10',
			// '11',
			// '1',
			// '1'
		);
	}

	public function placeOrder_ctrl2()
	{
		$this->AppModel->placeOrder_delivery_mod2(
			$this->decrypt($_POST['cusId']),
			$this->decrypt($_POST['deliveryDateData']),
			$this->decrypt($_POST['deliveryTimeData']),
			$this->decrypt($_POST['selectedDiscountType']),
			$this->decrypt($_POST['deliveryCharge']),
			$this->decrypt($_POST['amountTender']),
			$this->decrypt($_POST['specialInstruction']),
			$this->decrypt($_POST['getTenantData']),
			$this->decrypt($_POST['productID'])
			// '368',
			// '38',
			// '38448',
			// '4',
			// '5',
			// '6'
			// '7',
			// '8',
			// '9',
			// '10',
			// '11',
			// '1',
			// '1'
		);
	}

	public function placeOrderGoodsDelivery_ctrl()
	{
		$this->AppModel->placeOrderGoodsDelivery_mod(
			$this->decrypt($_POST['cusId']),
			$this->decrypt($_POST['deliveryDateData']),
			$this->decrypt($_POST['deliveryTimeData']),
			$this->decrypt($_POST['selectedDiscountType']),
			$this->decrypt($_POST['deliveryCharge']),
			$this->decrypt($_POST['amountTender']),
			$this->decrypt($_POST['specialInstruction']),
			$this->decrypt($_POST['getTenantData']),
			$this->decrypt($_POST['productID'])
			// '368',
			// '38',
			// '38448',
			// '4',
			// '5',
			// '6'
			// '7',
			// '8',
			// '9',
			// '10',
			// '11',
			// '1',
			// '1'
		);
	}

	public function savePickup_ctrl()
	{
		if(isset($_POST['customerId'])) {

		$this->AppModel->placeOrder_pickup_mod(
			$this->decrypt($_POST['customerId']),
			$this->decrypt($_POST['deliveryDateData']),
			$this->decrypt($_POST['deliveryTimeData']),
			$this->decrypt($_POST['getTenantData']),
			$this->decrypt($_POST['specialInstruction']),
			$this->decrypt($_POST['subtotal']),
			$this->decrypt($_POST['selectedDiscountType']),
			$this->decrypt($_POST['productID']),
			);
		}
	}

	public function savePickup_ctrl2()
	{
		if(isset($_POST['customerId'])) {

		$this->AppModel->placeOrder_pickup_mod2(
			$this->decrypt($_POST['customerId']),
			$this->decrypt($_POST['deliveryDateData']),
			$this->decrypt($_POST['deliveryTimeData']),
			$this->decrypt($_POST['getTenantData']),
			$this->decrypt($_POST['specialInstruction']),
			$this->decrypt($_POST['subtotal']),
			$this->decrypt($_POST['selectedDiscountType']),
			$this->decrypt($_POST['productID']),
			);
		}
	}

	public function loadSubTotal_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadSubTotal_mod($_POST['userID']);
		}
	}

	public function loadSubTotalnew_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->loadSubTotalnew_mod(
				$this->decrypt($_POST['customerId']));
		}
		// $this->AppModel->loadSubTotalnew_mod('2423');
	}

	public function loadSubTotalnew_ctrl2()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->loadSubTotalnew_mod2(
				$this->decrypt($_POST['customerId']), 
				$this->decrypt($_POST['productID']));
		}
		// $this->AppModel->loadSubTotalnew_mod('2423');
	}

	public function xsample()
	{
		$this->AppModel->getMainOrders('2282');
	}

	// public function loadRiderDetails_ctrl(){
	// 	$this->AppModel->loadRiderDetails_mod($this->input->post('ticketNo'));
	// 	// $this->AppModel->loadRiderDetails_mod('201125-2-003');
	// }


	public function getTrueTime_ctrl()
	{
		$this->AppModel->getTrueTime_mod();
	}

	public function listenCartSubtotal_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->listenCartSubtotal_mod($this->input->post('customerId'));
		}
	}

	public function loadFlavor_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadFlavor_mod(
				$this->decrypt($_POST['prodId']));
		}
	}

	public function loadDrinks_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadDrinks_mod(
				$this->decrypt($_POST['prodId']));
		}
		// $this->AppModel->loadDrinks_mod('111');
	}

	public function loadFries_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadFries_mod(
				$this->decrypt($_POST['prodId']));
		}
	}

	public function loadSide_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadSide_mod(
				$this->decrypt($_POST['prodId']));
		}
	}

	public function checkAddon_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->checkAddon_mod(
				$this->decrypt($_POST['prodId']));
		}
	}

	public function loadAddonSide_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadAddonSide_mod(
				$this->decrypt($_POST['prodId']));
		}
	}

	public function loadAddonDessert_ctrl()
	{
		if (isset($_POST['prodId'])) {
			$this->AppModel->loadAddonDessert_mod(
				$this->decrypt($_POST['prodId']));
		}
	}

	public function cancelOrderTenant_ctrl()
	{
		if (isset($_POST['tenantID']) && isset($_POST['ticketID'])) {
			$this->AppModel->cancelOrderTenant_mod(
				$this->decrypt($_POST['tenantID']), 
				$this->decrypt($_POST['ticketID']));
		}

		// $this->AppModel->cancelOrderSingleFood_mod('6693','4185');
	}

	public function cancelOrderGoods_ctrl()
	{
		if (isset($_POST['buId']) && isset($_POST['ticketID'])) {
			$this->AppModel->cancelOrderGoods_mod(
				$this->decrypt($_POST['buId']), 
				$this->decrypt($_POST['ticketID']));
		}

		// $this->AppModel->cancelOrderSingleFood_mod('6693','4185');
	}


	public function cancelOrderSingleFood_ctrl()
	{
		if (isset($_POST['tomsId']) && isset($_POST['ticketId'])) {
			$this->AppModel->cancelOrderSingleFood_mod(
				$this->decrypt($_POST['tomsId']), 
				$this->decrypt($_POST['ticketId']));
		}

		// $this->AppModel->cancelOrderSingleFood_mod('6693','4185');
	}

	public function cancelOrderSingleGood_ctrl()
	{
		if (isset($_POST['tomsId']) && isset($_POST['ticketId'])) {
			$this->AppModel->cancelOrderSingleGood_mod(
				$this->decrypt($_POST['tomsId']), 
				$this->decrypt($_POST['ticketId']));
		}
	}

	public function loadLocation_ctrl()
	{
		$this->AppModel->loadLocation_mod('1');
	}

	public function displayAddOns_ctrl()
	{
		if (isset($_POST['prodId'])) {
			// $this->AppModel->displayAddOns_mod('90');
			$this->AppModel->displayAddOns_mod($this->input->post('cartId'));
		}
	}

	public function showFlavor_ctrl()
	{
		$this->AppModel->showFlavor_mod('103');
	}

	public function showDrinks_ctrl()
	{
		$this->AppModel->showDrinks_ctrl('103');
	}

	public function getDiscount_ctrl()
	{
		if (isset($_POST['ticketID'])) {
			$this->AppModel->getDiscount_mod(
				$this->decrypt($_POST['ticketID']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getTotal_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getTotal_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getTotal_ctrl2()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getTotal_mod2($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getTotalGoods_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getTotalGoods_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getPickupScheduleFoods_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getPickupScheduleFoods_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getPickupScheduleGoods_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->getPickupScheduleGoods_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getVehicleType_ctrl()
	{
		if (isset($_POST['ticketId'])) {
			$this->AppModel->getVehicleType_mod($this->decrypt($_POST['ticketId']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getInstruction_ctrl()
	{
		if (isset($_POST['ticketId'])) {
			$this->AppModel->getInstruction_mod($this->decrypt($_POST['ticketId']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getOrderSummary_ctrl()
	{
		if (isset($_POST['ticketId'])) {
			$this->AppModel->getOrderSummary_mod($this->decrypt($_POST['ticketId']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getPickupSummaryFoods_ctrl()
	{
		if (isset($_POST['ticketId'])) {
			$this->AppModel->getPickupSummaryFoods_mod($this->decrypt($_POST['ticketId']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getPickupSummaryGoods_ctrl()
	{
		if (isset($_POST['ticketId'])) {
			$this->AppModel->getPickupSummaryGoods_mod($this->decrypt($_POST['ticketId']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function getSubTotal_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->subTotal_mod($this->decrypt($_POST['ticketNo']));
		}
		// $this->AppModel->getTotal_mod('210714-2-002');
	}

	public function checkifongoing_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->checkifongoing_mod(
				$this->decrypt($_POST['ticketNo']));
			// $this->AppModel->checkifongoing_mod('210415-2-003');
		}
	}

	public function viewCategories_ctrl()
	{
		$this->AppModel->viewCategories_mod('1');
	}

	public function checkifemptystore_ctrl()
	{

		if (isset($_POST['tenantCode'])) {

			// $this->AppModel->checkifemptystore_mod('14');
			$this->AppModel->checkifemptystore_mod(
				$this->decrypt($_POST['tenantCode']));
		}
	}

	public function getCategories_ctrl()
	{
		if (isset($_POST['tenantCode'])) {
			$this->AppModel->getCategories_mod(
				$this->decrypt($_POST['tenantCode']));
		}
	}


	public function getItemsBycategories_ctrl()
	{
		if (isset($_POST['categoryId'])) {
			$this->AppModel->getItemsBycategories_mod(
				$this->decrypt($_POST['categoryId']));
			// $this->AppModel->getItemsBycategories_mod('27');
		}
	}

	public function getItemsByCategoriesAll_ctrl()
	{
		if (isset($_POST['tenantCode'])) {
			$this->AppModel->getItemsByCategoriesAll_mod(
				$this->decrypt($_POST['tenantCode']));
			// $this->AppModel->getItemsBycategories_mod('32');
		}
	}

	public function getGcItems_ctrl()
	{
		if (isset($_POST['offset']) &&  isset($_POST['categoryNo']) && isset($_POST['itemSearch'])) {
			$this->AppModel->getGcItems_mod(
				$_POST['offset'], 
				$_POST['categoryNo'], 
				$_POST['groupCode'],
				$_POST['itemSearch']
				);
			// $this->AppModel->getGcItems_mod('10', '130', '');
		}
	}


	public function addToCartGc_ctrl()
	{
		if (isset($_POST['userID']) && 
		isset($_POST['userID']) && 
		isset($_POST['buCode']) && 
		isset($_POST['prodId']) && 
		isset($_POST['itemCode']) && 
		isset($_POST['uomSymbol']) && 
		isset($_POST['uom'])  && 
		isset($_POST['_counter'])) {
			$this->AppModel->addToCartGc_mod(
				$this->decrypt($_POST['userID']), 
				$this->decrypt($_POST['buCode']), 
				$this->decrypt($_POST['prodId']), 
				$this->decrypt($_POST['itemCode']), 
				$this->decrypt($_POST['uomSymbol']), 
				$this->decrypt($_POST['uom']), 
				$this->decrypt($_POST['_counter']));
			// $this->AppModel->addToCartGc_mod('12121','12','12','12','1');
		}
	}

	

	public function gc_loadPriceGroup_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->gc_loadPriceGroup_mod(
				$this->decrypt($_POST['userID']));
			// $this->AppModel->gc_cart_mod('1628');
		}
	}

	public function getStore_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->getStore_mod(
				$this->decrypt($_POST['userID']));
			// 	// $this->AppModel->getTenant_mod('163');
		}
		// $this->AppModel->getTenant_mod('2423');
	}

	public function getStore2_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->getStore2_mod(
				$this->decrypt($_POST['userID']),
				$this->decrypt($_POST['tempID']));
				
		}
		// $this->AppModel->getTenant_mod('2423');
	}

	public function gc_cart_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->gc_cart_mod(
				$this->decrypt($_POST['userID']));
			// $this->AppModel->gc_cart_mod('1628');
		}
	}

	public function gc_cart2_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->gc_cart2_mod(
				$this->decrypt($_POST['userID']),
				$this->decrypt($_POST['tempID']));
				
			// $this->AppModel->gc_cart_mod('1628');
		}
	}

	public function updateGcCartQty_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->updateGcCartQty_mod(
				$this->decrypt($_POST['id']), 
				$this->decrypt($_POST['qty']));
		}
	}


	public function loadGcSubTotal_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->loadGcSubTotal_mod(
				$this->decrypt($_POST['customerId']));
			// $this->AppModel->loadGcSubTotal_mod('2423');
		}
	}

	public function loadGcSubTotal2_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->loadGcSubTotal2_mod(
				$this->decrypt($_POST['customerId']),
				$this->decrypt($_POST['tempID']));
			// $this->AppModel->loadGcSubTotal_mod('2423');
		}
	}

	public function getGcCounter_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getGcCounter_mod(
				$this->decrypt($_POST['customerId']));
			// $this->AppModel->getGcCounter_mod('378');
		}
	}


	public function getGcCategories_ctrl()
	{
		$this->AppModel->getGcCategories_mod();
	}

	public function getItemsByGcCategories_ctrl()
	{
		if (isset($_POST['categoryId']) && isset($_POST['offset'])) {
			$this->AppModel->getItemsByGcCategories_mod(
				$this->decrypt($_POST['categoryId']), 
				$this->decrypt($_POST['offset']), 
				$this->decrypt($_POST['groupCode']),
				$this->decrypt($_POST['bunitCode']));
		}
	}

	public function removeGcItemFromCart_ctrl()
	{
		if (isset($_POST['cartId'])) {
			$this->AppModel->removeGcItemFromCart_mod(
				$this->decrypt($_POST['cartId']));
		}
	}


	public function getBill_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getBill_mod(
				$this->decrypt($_POST['customerId']),
				$this->decrypt($_POST['priceG'])
		);
			// $this->AppModel->getBill_mod('1628');
		}
	}

	public function gc_getbillperbu_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->getBill_mod($_POST['customerId']);
			// $this->AppModel->gc_getbillperbu_mod('1628');
		}
	}

	public function gcgroupbyBu()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->gcgroupbyBu_mod(
				$this->decrypt($_POST['customerId']),
				$this->decrypt($_POST['priceGroup'])
			);
		}
	}

	public function gcgroupbyBu2()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->gcgroupbyBu2_mod(
				$this->decrypt($_POST['customerId']),
				$this->decrypt($_POST['priceGroup']),
				$this->decrypt($_POST['tempID'])
			);
		}
	}


	public function getConFee_ctrl()
	{
		$this->AppModel->getConFee_mod();
	}

	public function gcDeliveryFee_ctrl()
	{
		if (isset($_POST['townID'])) {
			$this->AppModel->gcDeliveryFee_mod(
				$this->decrypt($_POST['townID']),
			);
		}
	}

	public function gc_submitOrder_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->gc_submitOrder_mod(
				$this->decrypt($_POST['customerId']),
				$this->decrypt($_POST['groupValue']),
				$this->decrypt($_POST['deliveryDateData']),
				$this->decrypt($_POST['deliveryTimeData']),
				$this->decrypt($_POST['buData']),
				$this->decrypt($_POST['totalData']),
				$this->decrypt($_POST['convenienceData']),
				$this->decrypt($_POST['placeRemarks']),
				$this->decrypt($_POST['pickUpOrDelivery']),
				$this->decrypt($_POST['priceGroup'])
			);
		}
	}

	public function gc_submitOrderPickup_ctrl()
	{
		if (isset($_POST['customerId'])) {
			$this->AppModel->gc_submitOrderPickup_mod(
				$this->decrypt($_POST['customerId']),
				$this->decrypt($_POST['groupValue']),
				$this->decrypt($_POST['deliveryDateData']),
				$this->decrypt($_POST['deliveryTimeData']),
				$this->decrypt($_POST['buData']),
				$this->decrypt($_POST['totalAmount']),
				$this->decrypt($_POST['pickingCharge']),
				$this->decrypt($_POST['placeRemarks']),
				$this->decrypt($_POST['pickUpOrDelivery']),
				$this->decrypt($_POST['priceGroup']),
				$this->decrypt($_POST['tempID'])
			);
		}
	}

	public function gc_searchProd_ctrl()
	{
		if (isset($_POST['search_prod'])) {
			$this->AppModel->gc_searchProd_mod($_POST['search_prod']);
			// $this->AppModel->gc_searchProd_mod('ORANGE');
		}
	}

	public function gc_select_uom_ctrl()
	{
		if (isset($_POST['itemCode'])) {
			// $this->AppModel->gc_select_uom_mod('100462');
			$this->AppModel->gc_select_uom_mod(
				$this->decrypt($_POST['itemCode']),
				$this->decrypt($_POST['groupCode']));
		}
	}

	public function showDiscount_ctrl()
	{
		$this->AppModel->showDiscount_mod();
	}

	// public function uploadId1_ctrl(){
	// 	if(isset($_POST['userID']) && isset($_POST['discountId']) && isset($_POST['name']) && isset($_POST['idNumber']) && isset($_POST['imageName']) && isset($_POST['imageBookletName'])){
	// 		$this->AppModel->uploadId1_mod($this->decrypt($_POST['userID']),$this->decrypt($_POST['discountId']),$this->decrypt($_POST['name']),$this->decrypt($_POST['idNumber']),$this->decrypt($_POST['imageName']),$this->decrypt($_POST['imageBookletName']));
	// 	}
	// }

	public function loadIdList_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadIdList_mod(
				$this->decrypt($_POST['userID']));
		}
	}

	public function delete_id_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->delete_id_mod(
				$this->decrypt($_POST['id']));
		}
	}

	public function checkidcheckout_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->checkidcheckout_mod(
				$this->decrypt($_POST['userID']));
			// $this->AppModel->checkidcheckout_mod("465");
		}
	}

	public function checkIfHasAddresses_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->checkIfHasAddresses_mod($this->decrypt($_POST['userID']));
		}
	}


	public function changeAccountStat_ctrl()
	{
		if (isset($_POST['usernameLogIn'])) {
			$this->AppModel->changeAccountStat_mod(
				$this->decrypt($_POST['usernameLogIn']));
		}
	}

	public function getUserDetails_ctrl()
	{
		if (isset($_POST['usernameLogIn'])) {
			$this->AppModel->getUserDetails_mod(
				$this->decrypt($_POST['usernameLogIn']));
			// $this->AppModel->getUserDetails_mod('pj');
		}
	}

	public function getusernameusingnumber_ctrl($mobileNumber)
	{
		$userid = $this->AppModel->getusernameusingnumber_mod($mobileNumber);
		return $userid;
	}

	public function verifyOTP_ctrl()
	{
		if (isset($_POST['mobileNumber'])) {
			$data = array();
			$data_result = array();
			$otp_num = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
			$apicode = 'PR-ALTUR152758_ITHWZ ';
			$passwd = '!t5y5d3v@';
			$my_number = $this->decrypt($_POST['mobileNumber']);
			$message =  "Alturush Delivery: TO VERIFY YOUR ACCOUNT, use OTP " . $otp_num . ".";
			$result = $this->AppModel->itexmo($my_number, $message, $apicode, $passwd);
			//Save data to user_verification_codes table...
			$userID = $this->getusernameusingnumber_ctrl($my_number);
			$this->AppModel->verifyOTP_mod($userID, $my_number, $otp_num);

			if ($result == false) {
				echo "iTexMo: No response from server!!!
			Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.	
			Please CONTACT US for help. ";	
			} else if ($result == false){
				echo 'Message Sent!';
			} else {	
				echo "Error Num ". $result . " was encountered!";
			}

			
		}
	}

	public function updateProfileOTP_ctrl()
	{
		if (isset($_POST['mobileNumber'])) {
			$data = array();
			$data_result = array();
			$otp_num = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
			$apicode = 'PR-ALTUR152758_ITHWZ';
			$passwd = '!t5y5d3v@';
			$my_number = $this->decrypt($_POST['mobileNumber']);
			$message =  "Alturush Delivery: TO EDIT YOUR PROFILE ACCOUNT, use OTP " . $otp_num . ".";
			$result = $this->AppModel->itexmo($my_number, $message, $apicode, $passwd);
			//Save data to user_verification_codes table...
			$userID = $this->getusernameusingnumber_ctrl($my_number);
			$this->AppModel->updateProfileOTP_mod($userID, $my_number, $otp_num);

			if ($result == false) {
				echo "iTexMo: No response from server!!!
			Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.	
			Please CONTACT US for help. ";	
			} else if ($result == false){
				echo 'Message Sent!';
			} else {	
				echo "Error Num ". $result . " was encountered!";
			}
		}
	}

	public function recoverOTP_ctrl()
	{
		
		if (isset($_POST['mobileNumber'])) {
			$data = array();
			$data_result = array();
			$otp_num = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
			$apicode = 'PR-ALTUR152758_ITHWZ';
			$passwd = '!t5y5d3v@';
			$my_number = $this->decrypt($_POST['mobileNumber']);
			$message =  "Alturush Delivery: TO RECOVER YOUR ACCOUNT, use OTP " . $otp_num . ".";
			$result = $this->AppModel->itexmo($my_number, $message, $apicode, $passwd);
			//Save data to user_verification_codes table...
			$userID = $this->getusernameusingnumber_ctrl($my_number);
			$this->AppModel->recoverOTP_mod($userID, $my_number, $otp_num);

			if ($result == false) {
				echo "iTexMo: No response from server!!!
			Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.	
			Please CONTACT US for help. ";	
			} else if ($result == false){
				echo 'Message Sent!';
			} else {	
				echo "Error Num ". $result . " was encountered!";
			}
		}
	}

	public function verifyOtpCode_ctrl()
	{
		if (isset($_POST['otpCode']) && isset($_POST['mobileNumber']) && isset($_POST['userID'])) {
			$this->AppModel->verifyOtpCode_mod($this->decrypt($_POST['otpCode']), $this->decrypt($_POST['mobileNumber']), $this->decrypt($_POST['userID']));
		}
		// $this->AppModel->checkOtpCode_mod('316626','09107961118');
	}

	public function checkOtpCode_ctrl()
	{
		if (isset($_POST['otpCode']) && isset($_POST['mobileNumber'])) {
			$this->AppModel->checkOtpCode_mod($this->decrypt($_POST['otpCode']), $this->decrypt($_POST['mobileNumber']));
		}
		// $this->AppModel->checkOtpCode_mod('316626','09107961118');
	}

	public function changePassword_ctrl()
	{
		if (isset($_POST['newPassWord']) && isset($_POST['realMobileNumber'])) {
			// $this->AppModel->changePassword_mod($this->decrypt('1212121212'),'09107961118');
			$this->AppModel->changePassword_mod($this->decrypt($_POST['newPassWord']), $this->decrypt($_POST['realMobileNumber']));
		}
	}

	public function checkUsernameIfExist_ctrl()
	{
		if (isset($_POST['username'])) {
			$this->AppModel->checkUsernameIfExist_mod($this->decrypt($_POST['username']));
		}
	}

	public function getOrderTicket_ctrl()
	{
		if (isset($_POST['ticketID'])) {
			$this->AppModel->getOrderTicket_mod(
				$this->decrypt($_POST['ticketID']));
		}
		// $this->AppModel->getTicketNoFood_mod('1815');
	}

	public function checkEmailIfExist_ctrl()
	{
		if (isset($_POST['email'])) {
			$this->AppModel->checkEmailIfExist_mod($this->decrypt($_POST['email']));
		}
	}

	public function checkPhoneIfExist_ctrl()
	{
		if (isset($_POST['phoneNumber'])) {
			$this->AppModel->checkPhoneIfExist_mod($this->decrypt($_POST['phoneNumber']));
		}
		// $this->AppModel->checkPhoneIfExist_mod('9107961118');
	}

	public function displayCartAddOns_ctrl()
	{
		$this->AppModel->displayCartAddOns_mod('51');
	}

	public function getProvince_ctrl()
	{
		$this->AppModel->getProvince_ctrl();
	}

	public function getTown_ctrl()
	{
		//if (isset($_POST['provinceId'])) {
		$this->AppModel->getTown_mod($this->decrypt($_POST['provinceId']));
		//}
		// $this->AppModel->getTown_mod('1');		
	}


	public function getBarangay_ctrl()
	{
		if (isset($_POST['townID'])) {
			$this->AppModel->getBarangay_mod($this->decrypt($_POST['townID']));
		}
	}

	public function selectBuildingType_ctrl()
	{
		$this->AppModel->selectBuildingType_mod();
	}

	public function updateProfile_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->updateProfile_mod(
			$this->decrypt($_POST['userID']), 
			$this->decrypt($_POST['firstName']),
			$this->decrypt($_POST['lastName']), 
			$this->decrypt($_POST['email']), 
			$this->decrypt($_POST['mobileNumber']));
		}
	}

	public function updateNewAddress_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->updateNewAddress_mod(
			$this->decrypt($_POST['userID']), 
			$this->decrypt($_POST['id']),
			$this->decrypt($_POST['firstName']), 
			$this->decrypt($_POST['lastName']), 
			$this->decrypt($_POST['mobileNum']), 
			$_POST['houseUnit'], 
			$this->decrypt($_POST['streetPurok']), 
			$this->decrypt($_POST['landMark']), 
			$_POST['otherNotes'],
			$this->decrypt($_POST['barangayID']), 
			$this->decrypt($_POST['addressType']));
		}
	}

	public function submitNewAddress_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->submitNewAddress_mod(
			$this->decrypt($_POST['userID']), 
			$this->decrypt($_POST['firstName']), 
			$this->decrypt($_POST['lastName']), 
			$this->decrypt($_POST['mobileNum']), 
			$_POST['houseUnit'], 
			$this->decrypt($_POST['streetPurok']), 
			$this->decrypt($_POST['landMark']), 
			$_POST['otherNotes'], 
			$this->decrypt($_POST['barangayID']), 
			$this->decrypt($_POST['addressType']));
		}
	}

	public function loadAdresses_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadAdresses_mod($this->decrypt($_POST['idd']), $this->decrypt($_POST['userID']));
		}
		// $this->AppModel->loadAddress_mod('2279');
	}

	public function loadAddress_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->loadAddress_mod($this->decrypt($_POST['userID']));
		}
		// $this->AppModel->loadAddress_mod('2279');
	}
	
	public function submitLoadAddress_ctrl()
	{
		if (isset($_POST['userID']) && isset($_POST['groupID'])) {
			$this->AppModel->submitLoadAddress_mod($this->decrypt($_POST['userID']), $this->decrypt($_POST['groupID']));
		}
		// $this->AppModel->loadAddress_mod('2279');
	}

	public function deleteAddress_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->deleteAddress_mod($this->decrypt($_POST['id']));
		}
	}

	public function deleteDiscountID_ctrl()
	{
		if (isset($_POST['id'])) {
			$this->AppModel->deleteDiscountID_mod($this->decrypt($_POST['id']));
		}
	}

	public function deleteCartGc_ctrl()
	{
		if (isset($_POST['cusID'])) {
			$this->AppModel->deleteCartGc_mod(
				$this->decrypt($_POST['cusID']),
				$this->decrypt($_POST['buCode'])
			);
		}
	}

	public function showRiderDetails_ctrl()
	{
		if (isset($_POST['ticketNo'])) {
			$this->AppModel->showRiderDetails_mod($this->decrypt($_POST['ticketNo']));
		}
	}

	public function updateDefaultShipping_ctrl()
	{
		if (isset($_POST['id']) && isset($_POST['customerId'])) {
			$this->AppModel->updateDefaultShipping_mod($this->decrypt($_POST['id']), $this->decrypt($_POST['customerId']));
		}
	}

	public function updateDefaultNumber_ctrl()
	{
		if (isset($_POST['id']) && isset($_POST['customerId'])) {
			$this->AppModel->updateDefaultNumber_mod($this->decrypt($_POST['id']), $this->decrypt($_POST['customerId']));
		}
	}

	public function updateNumber_ctrl()
	{
		if (isset($_POST['id']) && isset($_POST['updateNumber'])) {
			$this->AppModel->updateNumber_mod($this->decrypt($_POST['id']), $this->decrypt($_POST['updateNumber']));
		}
	}

	public function updatePickupAt_ctrl()
	{
		if (isset($_POST['date']) && isset($_POST['time']) && (isset($_POST['userID']))) {
			$this->AppModel->updatePickupAt_mod(
				$this->decrypt($_POST['date']),
				$this->decrypt($_POST['time']),
				$this->decrypt($_POST['userID']));
		}
	}

	public function viewTenantCategories_ctrl()
	{
		if (isset($_POST['tenantId'])) {
			$this->AppModel->viewTenantCategories_mod(
				$this->decrypt($_POST['tenantId']));
		}
		// $this->AppModel->viewTenantCategories_mod(2);
	}


	public function checkIfBf_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->checkIfBf_mod($this->decrypt($_POST['userID']));
		}
	}

	public function viewAddon_ctrl()
	{
		echo $this->decrypt('123');
		// $this->AppModel->viewAddon_mod('2282');
	}

	public function getTotalFee_ctrl()
	{
		if (isset($_POST['ticketID'])) {
			$this->AppModel->getTotalFee_mod(
				$this->decrypt($_POST['ticketID']));
		}
	}

	public function checkBuId_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->checkBuId_mod(
				$this->decrypt($_POST['cusId']),
				$this->decrypt($_POST['bunitCode']));
		}
	}

	public function display_store_ctrl()
	{
		//	if (isset($_POST['unitGroupId'])) {
		$this->AppModel->display_store_mod();
		//	}
		// $this->AppModel->display_store_mod('1','1');
	}

	public function load_store_ctrl()
	{
		//	if (isset($_POST['unitGroupId'])) {
		$this->AppModel->load_store_mod(
			$this->decrypt($_POST['groupID']));
		//	}
		// $this->AppModel->display_store_mod('1','1');
	}

	public function getglobalcat_ctrl()
	{
		$this->AppModel->getglobalcat_mod();
	}
	public function getPickupTime_ctrl() 
	{
		$this->AppModel->getPickupTime_mod();
	}

	public function search_item_ctrl()
	{
		if (isset($_POST['search'])) {
			$this->AppModel->search_item_mod($_POST['search'], $_POST['unitGroupId']);
		}
		// $this->AppModel->search_item_mod("hotdog","1");
	}

	public function searchGc_item_ctrl()
	{
		if (isset($_POST['search'])) {
			$this->AppModel->searchGc_item_mod($_POST['search'], 
			$_POST['unitGroupId'],
			$_POST['bunitCode'],
			$_POST['groupCode']
		);
		}
	}

	public function updatePassword_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->updatePassword_mod($this->decrypt($_POST['userID']), $this->decrypt($_POST['currentPass']), $this->decrypt($_POST['oldPassword']));
		}
		// $this->AppModel->updatePassword_mod('2423','12345','123451');
	}

	public function updateUsername_ctrl()
	{
		if (isset($_POST['userID'])) {
			$this->AppModel->updateUsername_mod($this->decrypt($_POST['userID']), $this->decrypt($_POST['currentPass']), $this->decrypt($_POST['newUsername']));
		}
		// $this->AppModel->updatePassword_mod('2423','12345','123451');
	}


	public function chat_ctrl()
	{
		$this->AppModel->chat_mod(
			$this->decrypt($_POST['userID']), 
			$this->decrypt($_POST['riderId']), 
			$this->decrypt($_POST['ticketId']));
		// $this->AppModel->chat_mod('344','35');
	}

	public function send_chat_ctrl()
	{
		$this->AppModel->send_chat_mod(
			$this->decrypt($_POST['userID']), 
			$this->decrypt($_POST['riderId']), 
			$_POST['chat'], 
			$this->decrypt($_POST['ticketId']));
	}

	public function check_version_ctrl()
	{
		if (isset($_POST['appName'])) {
			$this->AppModel->check_version_mod(
				$this->decrypt($_POST['appName']));
		}
		// $this->AppModel->loadProfile_mod('2423');
	}

	public function loadProfile_ctrl()
	{
		if (isset($_POST['cusId'])) {
			$this->AppModel->loadProfile_mod(
				$this->decrypt($_POST['cusId']));
		}
		// $this->AppModel->loadProfile_mod('2423');
	}

	public function get_status_ctrl()
	{
		if (isset($_POST['tenantID'])) {
			$this->AppModel->get_status_mod(
				$this->decrypt($_POST['tenantID']));
		}
		// $this->AppModel->loadProfile_mod('2423');
	}

	public function get_status_ctrl2()
	{
		if (isset($_POST['bunitCode'])) {
			$this->AppModel->get_status_mod2(
				$this->decrypt($_POST['bunitCode']));
		}
		// $this->AppModel->loadProfile_mod('2423');
	}


	public function uploadProfilePic_ctrl()
	{
		// file_put_contents('storage/uploads/profilePhotos/' . $_POST['userID'] . '.jpeg', base64_decode($_POST['base64Image']));
		// return $output_file;
		if (isset($_POST['userID']) && isset($_POST['picName'])){
			$this->AppModel->uploadProfilePic_mod(
				$this->decrypt($_POST['userID']), 
				$this->decrypt($_POST['picName']));
		}
	}

	public function uploadId_ctrl()
	{
		if (isset($_POST['userID']) && 
		isset($_POST['discountId']) && 
		isset($_POST['name']) && 
		isset($_POST['idNumber']) && 
		isset($_POST['imageName'])) {
			$this->AppModel->uploadId_mod(
				$this->decrypt($_POST['userID']), 
				$this->decrypt($_POST['discountId']), 
				$this->decrypt($_POST['name']), 
				$this->decrypt($_POST['idNumber']), 
				$this->decrypt($_POST['imageName']));
		}
	}

	public function uploadNumber_ctrl()
	{
		if (isset($_POST['userID']) && 
		isset($_POST['number'])) {
			$this->AppModel->uploadNumber_mod(
				$this->decrypt($_POST['userID']), 
				$this->decrypt($_POST['number']));
		}
	}

	public function upLoadImage_ctrl()
	{
		$imageName = $_POST['_imageName'];
		$image = $_POST['_image'];
		$this->base64_to_jpeg($image, $imageName);
	}

	public function upLoadPic_ctrl()
	{
		$imageName = $_POST['_imageName'];
		$image = $_POST['_image'];
		$this->base64_to_jpg($image, $imageName);
	}

	public function base64_to_jpg($base64_string, $output_file)
	{
		file_put_contents('storage/uploads/profile_pics/' . $output_file . '.jpeg', base64_decode($base64_string));
		return $output_file;
	}

	public function base64_to_jpeg($base64_string, $output_file)
	{
		file_put_contents('storage/uploads/discount_ids/' . $output_file . '.jpeg', base64_decode($base64_string));
		return $output_file;
	}

}
