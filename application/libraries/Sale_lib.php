<?php
class Sale_lib
{
	var $CI,$nameS;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->nameS='sale_';
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata($this->nameS.'cart'))
			$this->set_cart(array());

		return $this->CI->session->userdata($this->nameS.'cart');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata($this->nameS.'cart',$cart_data);
	}

	//Alain Multiple Payments
	function get_payments($format=false)
	{
		if( !$this->CI->session->userdata($this->nameS.'payments') )
			$this->set_payments( array( ) );
		if ($format && count($this->CI->session->userdata($this->nameS.'payments'))>0)
			foreach ($this->CI->session->userdata($this->nameS.'payments') as $key) {
				$payments[$key['payment_type']]=array('payment_type'=>str_replace('_',' ',$key['payment_type']),
													  'payment_amount'=>$key['payment_amount']);	
			}
		else $payments=$this->CI->session->userdata($this->nameS.'payments');
		return $this->CI->session->userdata($this->nameS.'payments');
	}
	//Alain Multiple Payments
	function set_payments($payments_data)
	{
		$this->CI->session->set_userdata($this->nameS.'payments',$payments_data);
	}

	function get_comment()
	{
		return $this->CI->session->userdata($this->nameS.'comment');
	}

	function set_comment($comment)
	{
		$this->CI->session->set_userdata($this->nameS.'comment', $comment);
	}

	function clear_comment()
	{
		$this->CI->session->unset_userdata($this->nameS.'comment');
	}

	function get_email_receipt()
	{
		return $this->CI->session->userdata($this->nameS.'email_receipt');
	}

	function set_email_receipt($email_receipt)
	{
		$this->CI->session->set_userdata($this->nameS.'email_receipt', $email_receipt);
	}

	function clear_email_receipt()
	{
		$this->CI->session->unset_userdata($this->nameS.'email_receipt');
	}

	function add_payment($payment_id,$payment_amount)
	{
		$payment_id=str_replace('_',' ',$payment_id);
		$payments=$this->get_payments();
		$payment = array($payment_id=>
		array(
			'payment_type'=>$payment_id,
			'payment_amount'=>$payment_amount
			)
		);

		//payment_method already exists, add to payment_amount
		if(isset($payments[$payment_id]))
		{
			$payments[$payment_id]['payment_amount']+=$payment_amount;
		}
		else
		{
			//add to existing array
			$payments+=$payment;
		}

		$this->set_payments($payments);
		return true;
	}

	//Alain Multiple Payments
	function edit_payment($payment_id,$payment_amount){
		$payment_id=str_replace('_',' ',$payment_id);
		$payments = $this->get_payments();
		if(isset($payments[$payment_id]))
		{
			$payments[$payment_id]['payment_type'] = $payment_id;
			$payments[$payment_id]['payment_amount'] = $payment_amount;
			$this->set_payments($payment_id);
		}
		return false;
	}

	//Alain Multiple Payments
	function delete_payment( $payment_id ){
		$payment_id=str_replace('_',' ',$payment_id);
		$payments = $this->get_payments();
		unset( $payments[urldecode( $payment_id )] );
		$this->set_payments( $payments );
	}

	//Alain Multiple Payments
	function empty_payments()
	{
		$this->CI->session->unset_userdata($this->nameS.'payments');
	}

	//Alain Multiple Payments
	function get_payments_total()
	{
		$subtotal = 0;
		foreach($this->get_payments() as $payments)
		{
			$subtotal+=$payments['payment_amount'];
		}
		return to_currency_no_money($subtotal);
	}

	//Alain Multiple Payments
	function get_amount_due()
	{
		$amount_due=0;
		$payment_total = $this->get_payments_total();
		$sales_total=$this->get_total();
		$amount_due=to_currency_no_money($sales_total - $payment_total);
		return $amount_due;
	}

	function get_customer()
	{
		if(!$this->CI->session->userdata($this->nameS.'customer'))
			$this->set_customer(-1);

		return $this->CI->session->userdata($this->nameS.'customer');
	}

	function set_customer($customer_id)
	{
		$this->CI->session->set_userdata($this->nameS.'customer',$customer_id);
		$this->CI->session->set_userdata('discounting','checked');
		$customer_discount=$this->CI->Customer->get_info($customer_id)->discounts;
		$this->set_discount($customer_discount);
	}

	public function get_discount() {
		if(!$this->CI->session->userdata($this->nameS.'discount'))
			$this->set_discount(0);

		return $this->CI->session->userdata($this->nameS.'discount');
	}
	
	public function set_discount($discount) {
		$this->CI->session->set_userdata($this->nameS.'discount',$discount);
	}

	function get_employee()
	{
		if(!$this->CI->session->userdata($this->nameS.'employee'))
			$this->set_employee(-1);

		return $this->CI->session->userdata($this->nameS.'employee');
	}

	function set_employee($employee_id)
	{
		$this->CI->session->set_userdata($this->nameS.'employee',$employee_id);
	}

	function get_mode()
	{
		if(!$this->CI->session->userdata('sale_mode'))
			$this->set_mode('sale');

		return $this->CI->session->userdata('sale_mode');
	}

	function set_mode($mode)
	{
		$this->CI->session->set_userdata('sale_mode',$mode);
	}

	function add_item($item_id,$quantity=1,$discount=0,$price=null,$description=null,$serialnumber=null,$service_id=null)
	{
		//make sure item exists
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}
		//Alain Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();

		//We need to loop through all items in the cart.
		//If the item is already there, get it's key($updatekey).
		//We also need to get the next key that we are going to use in case we need to add the
		//item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

		$maxkey=0;					//Highest key so far
		$itemalreadyinsale=FALSE;	//We did not find the item yet.
		$insertkey=0;				//Key to use for new entry.
		$updatekey=0;				//Key to use to update(quantity)

		foreach ($items as $item)
		{
			//We primed the loop so maxkey is 0 the first time.
			//Also, we have stored the key in the element itself so we can compare.

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id']==$item_id)
			{
				if(!$item['is_service']){
					$itemalreadyinsale=TRUE;
					$updatekey=$item['line'];
				}elseif($item_id>0&&$item['service_id']==$service_id){
					return true;
				}
			}
		}

		$insertkey=$maxkey+1;

		$item_info=$this->CI->Item->get_info($item_id);
		if($item_id==-1&&$serialnumber){
			$item_number=$serialnumber;
			$serialnumber=null;
			$quantity=1;
		}elseif($item_id>0){
			if($item_info->is_service){
				$item_number=$service_id;
				$serialnumber=null;
				$quantity=1;
				// foreach($this->CI->Service->get_id_items($service_id) as $item){
				// 	$this->add_item($item,1);
				// }
			}else{
				$item_number=$item_info->item_number;
			}
		}
		//array/cart records are identified by $insertkey and item_id is just another field.
		if ($this->get_mode() == 'shipping') $price = $item_info->cost_price; //si es shipping se cobra el item a su precio de costo
		$array=array(
			'item_id'				=>$item_id,
			'line'					=>$insertkey,
			'is_service'			=>$item_info->is_service,
			'name'					=>$item_info->name,
			'item_number'			=>$item_number,
			'description'			=>$description!=null ? $description: $item_info->description,
			'serialnumber'			=>$item_info->is_service?$service_id:($serialnumber!=null?$serialnumber:''),
			'allow_alt_description'	=>$item_info->allow_alt_description,
			'is_serialized'			=>$item_info->is_service?1:$item_info->is_serialized,
			'quantity_total'		=>$item_info->quantity,
			'quantity'				=>$quantity,
			'reorder'				=>$item_info->reorder_level,
			'discount'				=>$discount,
			'price'					=>$price!=null ? $price: $item_info->unit_price
		);
		if($service_id) $array['service_id']=$service_id;
		$item = array(($insertkey)=>$array);

		//Item already exists and is not serialized, add to quantity
		if($itemalreadyinsale && ($item_info->is_serialized ==0) )
		{
			$items[$updatekey]['quantity']+=$quantity;
		}
		else
		{
			//add to existing array
			$items+=$item;
		}

		$this->set_cart($items);
		return true;

	}

	function out_of_stock($item_id)
	{
		//make sure item exists
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

		$item = $this->CI->Item->get_info($item_id);
		$quanity_added = $this->get_quantity_already_added($item_id);

		if ($item->quantity - $quanity_added < 0)
		{
			return true;
		}

		return false;
	}

	function get_quantity_already_added($item_id)
	{
		$items = $this->get_cart();
		$quantity_already_added = 0;
		foreach ($items as $item)
		{
			if($item['item_id']==$item_id)
			{
				$quantity_already_added+=$item['quantity'];
			}
		}

		return $quantity_already_added;
	}

	function get_item_id($line_to_get)
	{
		$items = $this->get_cart();

		foreach ($items as $line=>$item)
		{
			if($line==$line_to_get)
			{
				return $item['item_id'];
			}
		}

		return -1;
	}

	function edit_item($line,$description,$serialnumber,$quantity,$discount,$price)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$items[$line]['description'] = $description;
			$items[$line]['serialnumber'] = $serialnumber;
			$items[$line]['quantity'] = $quantity;
			$items[$line]['discount'] = $discount;
			$items[$line]['price'] = $price;
			$this->set_cart($items);
		}

		return false;
	}
	function _edit_item($line,$campo,$valor)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$items[$line][$campo] = $valor;
			$this->set_cart($items);
		}
		return false;
	}
	function is_valid_receipt($receipt_sale_id)
	{
		//POS #
		$pieces = explode(' ',$receipt_sale_id);

		if(count($pieces)==2)
		{
			return $this->CI->Sale->exists($pieces[1]);
		}

		return false;
	}

	function is_valid_item_kit($item_kit_id)
	{
		//KIT #
		$pieces = explode(' ',$item_kit_id);

		if(count($pieces)==2)
		{
			return $this->CI->Item_kit->exists($pieces[1]);
		}

		return false;
	}

	function return_entire_sale($receipt_sale_id)
	{
		//POS #
		$pieces = explode(' ',$receipt_sale_id);
		$sale_id = $pieces[1];

		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id,-$row->quantity_purchased,$row->discount_percent,$row->item_unit_price,$row->description,$row->serialnumber);
		}
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
	}

	function add_item_kit($external_item_kit_id)
	{
		//KIT #
		$pieces = explode(' ',$external_item_kit_id);
		$item_kit_id = $pieces[1];

		foreach ($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			$this->add_item($item_kit_item['item_id'], $item_kit_item['quantity']);
		}
	}

	function copy_entire_sale($sale_id)
	{
		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_purchased,$row->discount_percent,$row->item_unit_price,$row->description,$row->serialnumber);
		}
		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type,$row->payment_amount);
		}
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);

	}

	function copy_entire_suspended_sale($sale_id)
	{
		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale_suspended->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_purchased,$row->discount_percent,$row->item_unit_price,$row->description,$row->serialnumber);
		}
		foreach($this->CI->Sale_suspended->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type,$row->payment_amount);
		}
		$this->set_customer($this->CI->Sale_suspended->get_customer($sale_id)->person_id);
		$this->set_comment($this->CI->Sale_suspended->get_comment($sale_id));
	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata($this->nameS.'cart');
	}

	function remove_customer()
	{
		$this->delete_payment('account');
		$this->CI->session->unset_userdata($this->nameS.'customer');
		$this->set_discount(0);
		$this->CI->session->unset_userdata('discounting');
	}

	function remove_employee()
	{
		$this->CI->session->unset_userdata($this->nameS.'employee');
	}

	function clear_mode()
	{
		$this->CI->session->unset_userdata('sale_mode');
	}

	function clear_all()
	{
		//$this->clear_mode();
		$this->empty_cart();
		$this->clear_comment();
		$this->clear_email_receipt();
		$this->empty_payments();
		$this->remove_customer();
		$this->remove_employee();
		$this->CI->session->unset_userdata('from_order'); //Solo si es orden
	}

	function get_taxes()
	{
		$customer_id = $this->get_customer();
		$customer = $this->CI->Customer->get_info($customer_id);

		//Do not charge sales tax if we have a customer that is not taxable
		if (!$customer->taxable and $customer_id!=-1)
		{
			return array();
		}

		$taxes = array();
		foreach($this->get_cart() as $line=>$item)
		{
			$tax_info = $this->CI->Item_taxes->get_info($item['item_id']);

			foreach($tax_info as $tax)
			{
				$name = $tax['percent'].'% ' . $tax['name'];
				$tax_amount=($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)*(($tax['percent'])/100);


				if (!isset($taxes[$name]))
				{
					$taxes[$name] = 0;
				}
				$taxes[$name] += $tax_amount;
			}
		}

		return $taxes;
	}

	function get_subtotal()
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
			$subtotal+=($item['price']*$item['quantity']-($item['price']*$item['quantity']*$item['discount']/100));
		}
		return to_currency_no_money($subtotal);
	}

	function get_total()
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
			$total+=($item['price']*$item['quantity']-($item['price']*$item['quantity']*$item['discount']/100));
		}

		$discount = 0;
		if ($this->CI->session->userdata('discounting') == 'checked') {
			$discount = $this->get_discount();
			if ($discount > 0) {		
				$discount = ($total*$discount)/100;
			}
		}

		if ( $this->get_taxing() ) {
			foreach($this->get_taxes() as $tax)
			{
				$total+=$tax;
			}
		}

		return to_currency_no_money($total-$discount);
	}

	function get_taxing(){
		if ( $this->CI->session->userdata( $this->nameS.'taxing' ) == null ) {
			$this->CI->session->set_userdata($this->nameS.'taxing',1);
		}
		return $this->CI->session->userdata($this->nameS.'taxing');
	}

	function set_taxing($val)
	{
		$this->CI->session->set_userdata($this->nameS.'taxing',$val);
	}
}
?>