<?php
class Order_lib
{
	var $CI;

	function __construct()
	{
		$this->CI =& get_instance();
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata('cart_order'))
			$this->set_cart(array());

		return $this->CI->session->userdata('cart_order');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('cart_order',$cart_data);
	}

	function add_item($item_id,$quantity=1,$serialnumber=null,$service_id=null)
	{
		//make sure item exists
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			// $item_id = $this->CI->Item->get_item_id($item_id);
			// if(!$item_id) return false;
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
		if (count($items)>0)
		foreach ($items as $item)
		{
			//We primed the loop so maxkey is 0 the first time.
			//Also, we have stored the key in the element itself so we can compare.

			if($maxkey <= $item['line']){ $maxkey = $item['line']; }
			if($item['item_id']==$item_id){
				if($item_id>0 && $service_id){
					return true;
				}
			}
		}

		$insertkey=$maxkey+1;

		$item_info=$this->CI->Item->get_info($item_id);
		if($item_id>0){
			$item_number=$item_info->item_number;
		}
		//array/cart records are identified by $insertkey and item_id is just another field.
		$array=array(
			'item_id'				=>$item_id,
			'line'					=>$insertkey,
			'name'					=>$item_info->name,
			'quantity'				=>floor($quantity),
			'reorder'				=>floor($item_info->reorder_level)
		);
		$item = array(($item_id)=>$array);

		//Item already exists and is not serialized, add to quantity
		if($itemalreadyinsale)
		{
			$items[$updatekey]['quantity']+=floor($quantity);
		}
		else
		{
			//add to existing array
			$items+=$item;
		}

		$this->set_cart($items);
		return true;

	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cart_order');
	}

	function clear_all()
	{
		$this->empty_cart();
	}
}
?>