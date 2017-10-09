<?php
namespace HarmJan1990\nocksPHP;

class Client {
	private $baseUrl;
	private $apiVersion = 'v2';
	private $bearer;
	
	public function __construct ($bearer) {
		$this->bearer    = $bearer;
		$this->baseUrl   = 'https://api.nocks.com/api/'.$this->apiVersion.'/';
	}
	
	private function call ($method, $verb, $params = array(), $bearer = false, $pagination = false, $page = 1) {
		if($page > 1) {
			$uri  = $this->baseUrl.$method.'?page='.$page;
		} else {
			$uri  = $this->baseUrl.$method;
		}

		$ch = curl_init ($uri);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		if ($bearer == true) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			'Authorization: Bearer '.$this->bearer.''
			));
		}
		if (!empty($params)) {
			$params = json_encode($params);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		switch ($verb) {
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
			break;
			
			case 'PUT':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			break;
			
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			break;
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		$answer = json_decode($result, true);
		$obj = json_decode(json_encode($answer));
		if ($pagination == true) {
			return $obj->meta->pagination;
		} else {
			return $obj->data;
		}
	}
	
	// Get rate from exchange
	//
	// $code can be : NLG-EUR. Is required and must be a string.
	//
	// Response is an Object with the following data:
	//
	// ->code				Returns value of code.
	// ->last 				Object of price from last transation.
	//		->amount  		Price of last transaction.
	//		->currency 		Currency of price.
	// ->volume				Object of volume.
	//		->amount  		Volume.
	//		->currency 		Currency of volume.
	// ->low				Object of 24H low.
	//		->amount  		24H low.
	//		->currency 		Currency of 24H low.
	// ->high				Object of 24H high.
	//		->amount  		24H high.
	//		->currency 		Currency of 24H high.
	// ->buy				Object of highest buy order.
	//		->amount  		highest buy order.
	//		->currency 		Currency of highest buy order.
	// ->sell				Object of lowest sell order.
	//		->amount  		lowest sell order.
	//		->currency 		Currency of lowest sell order.
	// ->is_active			True or False.
	// ->resource			Returns resource of call.
	public function GetRate($code) {
		return $this->call('trade-market/'.$code.'', 'GET');
	}
	
	// Get balance from account.
	//
	// $code can be : EUR or NLG. Is required and must be a string.
	//
	// Response is an Object with the following data:
	//
	// ->uuid				uuid of balance.
	// ->available			Object of available balance.
	//		->amount  		Available balance.
	//		->currency 		Currency of available balance.
	// ->reserved			Object of reserved balance.
	//		->amount  		Reserved balance.
	//		->currency 		Currency of reserved balance.
	// ->total				Object of total balance.
	//		->amount  		Total balance.
	//		->currency 		Currency of total balance.
	// ->resource			Returns resource of call.
	public function GetBalance($code) {
		return $this->call('balance/'.$code.'', 'GET', array(), true);
	}
	
	// Get deposit Address
	//
	// $currency can be : EUR or NLG. Is required and must be a string.
	// $method can be : sepa or gulden. Is required, must be a string and match $curreny code.
	//
	// Response is an Object with the folowing data:
	//
	// ->method_type		Method type.
	// ->metadata			Object of method specific metadata.
	// 		->address		Deposit address.
	public function GetDepositAddress($currency, $method) {
		$params = array(
			'currency' => $currency,
			'payment_method' => array('method' => $method)
		);
		
		return $this->call('deposit', 'POST', $params, true);
	}
	
	// Create withdrawal of funds.
	//
	// $currency can be : EUR or NLG. Is required and must be a string.
	// $amount is the amount you want to withdraw. Is required and must be a string, may not exceed current balance.
	// $address is a Gulden address or verfied IBAN address. Is required and must be a string.
	//
	// Response is an Object with the folowing data:
	//
	// ->uuid				uuid of withdrawal.
	// ->status				Status of withdrawal.
	// ->type				Type of withdrawal.
	// ->method_type		Method type.
	// ->description		Description of withdrawal.
	// ->created_at			Object of created_at.
	// 		->datetime		Date and time of created_at.
	//		->timestamp		Timestamp of created_at.
	// ->updated_at			Object of updated_at.
	// 		->datetime		Date and time of updated_at.
	//		->timestamp		Timestamp of updated_at.
	// ->resource			Returns resource of call.
	// ->amount				Object of withdrawal amount.
	//		->amount  		Withdrawal amount.
	//		->currency 		Currency of withdrawal amount.
	// ->metadata			Object of method specific metadata.
	// 		->address		Withdrawa address.
	public function CreateWithdrawal($currency, $amount, $address) {
		$params = array(
			'currency' => $currency,
			'amount' => $amount,
			'address' => $address
		);
		
		return $this->call('withdrawal', 'POST', $params, true);
	}
	
	// Create trade order
	//
	// $market can be : NLG-EUR. Is required and must be a string.
	// $amount is the amount you want to trade. Is required and must be a string, may not exceed current balance.
	// $side can be : buy or sell. Is required and must be a string.
	// $rate is the price per unit and is not required, if not given the ammount specified will be bought or sold from the orderbook.
	//
	// Response is an Object with the following data:
	//
	// ->uuid				uuid of balance.
	// ->amount				Object of amount.
	//		->amount  		Amount.
	//		->currency 		Currency of amount.
	// ->amount_filled		Object of amount_filled.
	//		->amount  		Amount filled.
	//		->currency 		Currency of amount filled.
	// ->amount_cost		Object of amount cost.
	//		->amount  		Amount cost.
	//		->currency 		Currency of amount cost.
	// ->amount_fee			Object of amount fee.
	//		->amount  		Amount fee.
	//		->currency 		Currency of amount fee.
	// ->amount_fillable	Object of amount fillable.
	//		->amount  		Amount fillable.
	//		->currency 		Currency of amount fillable.
	// ->rate				Returns rate of order.
	// ->rate_actual		Returns actual rate of order.
	// ->side				Returns side of order.
	// ->type				Returns type of order.
	// ->status				Returns status of order.
	// ->created_at			Object of created_at.
	// 		->datetime		Date and time of created_at.
	//		->timestamp		Timestamp of created_at.
	// ->updated_at			Object of updated_at.
	// 		->datetime		Date and time of updated_at.
	//		->timestamp		Timestamp of updated_at.
	// ->cancelled_at		Object of cancelled_at.
	// 		->datetime		Date and time of cancelled_at.
	//		->timestamp		Timestamp of cancelled_at.
	// ->filled_at			Object of filled_at.
	// 		->datetime		Date and time of filled_at.
	//		->timestamp		Timestamp of filled_at.
	// ->trade_market		Returns trade market.
	// ->resource			Returns resource of call.
	public function CreateOrder($market, $amount, $side, $rate = 0) {
		$params = array(
			'trade-market' => $market,
			'amount' => $amount,
			'side' => $side
		);
		if($rate > 0) {
			$params['rate'] = $rate;
		}
		
		return $this->call('trade-order', 'POST', $params, true);
	}
	
	// Cancel trade order
	//
	// $uuid is the uuid of the trade order you want to cancel. Is required and must be a string.
	public function CancelOrder($uuid) {
		return $this->call('trade-order/'.$uuid.'', 'DELETE', array(), true);
	}
	
	// Get a single order
	//
	// $uuid is the uuid of the trade order you want to read. Is required and must be a string.
	//
	// Response is an Object with the following data:
	//
	// ->uuid				uuid of balance.
	// ->amount				Object of amount.
	//		->amount  		Amount.
	//		->currency 		Currency of amount.
	// ->amount_filled		Object of amount_filled.
	//		->amount  		Amount filled.
	//		->currency 		Currency of amount filled.
	// ->amount_cost		Object of amount cost.
	//		->amount  		Amount cost.
	//		->currency 		Currency of amount cost.
	// ->amount_fee			Object of amount fee.
	//		->amount  		Amount fee.
	//		->currency 		Currency of amount fee.
	// ->amount_fillable	Object of amount fillable.
	//		->amount  		Amount fillable.
	//		->currency 		Currency of amount fillable.
	// ->rate				Returns rate of order.
	// ->rate_actual		Returns actual rate of order.
	// ->side				Returns side of order.
	// ->type				Returns type of order.
	// ->status				Returns status of order.
	// ->created_at			Object of created_at.
	// 		->datetime		Date and time of created_at.
	//		->timestamp		Timestamp of created_at.
	// ->updated_at			Object of updated_at.
	// 		->datetime		Date and time of updated_at.
	//		->timestamp		Timestamp of updated_at.
	// ->cancelled_at		Object of cancelled_at.
	// 		->datetime		Date and time of cancelled_at.
	//		->timestamp		Timestamp of cancelled_at.
	// ->filled_at			Object of filled_at.
	// 		->datetime		Date and time of filled_at.
	//		->timestamp		Timestamp of filled_at.
	// ->trade_market		Returns trade market.
	// ->resource			Returns resource of call.
	public function GetOrder($uuid) {
		return $this->call('trade-order/'.$uuid.'', 'GET', array(), true);
	}
	
	// Get the orderbook
	//
	// $code can be : NLG-EUR. Is required and must be a string.
	//
	// Response is an Object with the following data:
	//
	// ->buy
	// An array of 50 orders in objects at the buy side of the market book. You can scroll trough them using ->buy[0] and further.
	// ->sell
	// An maximum of 50 orders in objects at the sell side of the market book. You can scroll trough them using ->sell[0] and further.
	public function GetOrderBook($code) {
		return $this->call('trade-market/'.$code.'/book', 'GET', array(), true);
	}
	
	// Get all orders
	//
	// $pagination can be : true or false. if true you get the following data:
	//
	// ->total				The number of orders in this account.
	// ->count				The number of orders on this page.
	// ->per_page			The number of orders per page.
	// ->current_page		The number of the currenct page.
	// ->total_pages		The number op pages.
	// ->links				Object links.
	// 		->next			Link to the next page.
	//
	// Response is array of all open orders. You can scroll trough them using GetOrders[0] and further.
	public function GetOrders($pagination = false, $page = 1) {
		return $this->call('trade-order', 'GET', array(), true, $pagination, $page);
	}
	
	// Get all deposits
	//
	// $pagination can be : true or false. if true you get the following data:
	//
	// ->total				The number of orders in this account.
	// ->count				The number of orders on this page.
	// ->per_page			The number of orders per page.
	// ->current_page		The number of the currenct page.
	// ->total_pages		The number op pages.
	// ->links				Object links.
	// 		->next			Link to the next page.
	//
	// Response is array of all deposits. You can scroll trough them using GetDeposits[0] and further.
	public function GetDeposits($pagination = false, $page = 1) {
		return $this->call('deposit', 'GET', array(), true, $pagination, $page);
	}
	
	// Get all withdrawals
	//
	// $pagination can be : true or false. if true you get the following data:
	//
	// ->total				The number of orders in this account.
	// ->count				The number of orders on this page.
	// ->per_page			The number of orders per page.
	// ->current_page		The number of the currenct page.
	// ->total_pages		The number op pages.
	// ->links				Object links.
	// 		->next			Link to the next page.
	//
	// Response is array of all withdrawals. You can scroll trough them using GetDeposits[0] and further.
	public function GetWithdrawals($pagination = false, $page = 1) {
		return $this->call('withdrawal', 'GET', array(), true, $pagination, $page);
	}
}
?>
