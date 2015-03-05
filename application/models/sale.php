<?php
class Sale extends CI_Model
{
	var $con;
	var $model = null;

    function __construct()
    {
        parent::__construct();
        //Seleccion de DB
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }

	public function get_info($sale_id)
	{
		$this->con->from('sales');
		$this->con->where('sale_id',$sale_id);
		return $this->con->get();
	}

	function exists($sale_id)
	{
		$this->con->from('sales');
		$this->con->where('sale_id',$sale_id);
		$query = $this->con->get();

		return ($query->num_rows()==1);
	}

	function update($sale_data, $sale_id)
	{
		$this->con->where('sale_id', $sale_id);
		$success = $this->con->update('sales',$sale_data);

		return $success;
	}

	function save($items,$customer_id,$employee_id,$comment,$payments,$sale_id=false,$mode=0)
	{
		if ($mode == 2) {
			// $this->con = $this->load->database('shippings', true);
		}
		if(count($items)==0)
			return -1;

		//Alain Multiple payments
		//Build payment types string
		$payment_types='';
		foreach($payments as $payment_id=>$payment)
		{
			$payment_types=$payment_types.$payment['payment_type'].': '.to_currency($payment['payment_amount']).'<br />';
		}

		$sales_data = array(
			'sale_time' => date('Y-m-d H:i:s'),
			'customer_id'=> $this->Customer->exists($customer_id) ? $customer_id : null,
			'employee_id'=>$employee_id,
			'payment_type'=>$payment_types,
			'comment'=>$comment,
			'mode'=>$mode
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->con->trans_start();

		$this->con->insert('sales',$sales_data);
		$sale_id = $this->con->insert_id();

		foreach($payments as $payment_id=>$payment)
		{
			if ( substr( $payment['payment_type'], 0, strlen( $this->lang->line('sales_giftcard') ) ) == $this->lang->line('sales_giftcard') )
			{
				/* We have a gift card and we have to deduct the used value from the total value of the card. */
				$splitpayment = explode( ':', $payment['payment_type'] );
				$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $splitpayment[1] );
				$this->Giftcard->update_giftcard_value( $splitpayment[1], $cur_giftcard_value - $payment['payment_amount'] );
			}

			$sales_payments_data = array
			(
				'sale_id'=>$sale_id,
				'payment_type'=>$payment['payment_type'],
				'payment_amount'=>$payment['payment_amount']
			);
			$this->con->insert('sales_payments',$sales_payments_data);
		}

		foreach($items as $line=>$item)
		{
			if(isset($item['is_service'])&& $item['is_service']>0 && isset($item['service_id'])){
				$this->Service->save(array('status'=>100, 'date_delivered'=> date('Y-m-d H:i:s')),$item['service_id']);
			}

			$cur_item_info = $this->Item->get_info($item['item_id']);
			$sales_items_data = array
			(
				'sale_id'=>$sale_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'discount_percent'=>$item['discount'],
				'item_cost_price' => $cur_item_info->cost_price,
				'item_unit_price'=>$item['price']
			);

			$this->con->insert('sales_items',$sales_items_data);

			$final_quantity = $cur_item_info->is_service?0:($cur_item_info->quantity - $item['quantity']);
			$item_data = array('quantity'=>$final_quantity);
			$this->Item->save($item_data,$item['item_id']);

			//Ramel Inventory Tracking
			//Inventory Count Details
			$qty_buy = -$item['quantity'];
			$sale_remarks ='POS '.$sale_id;
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$sale_remarks,
				'trans_inventory'=>$qty_buy
			);
			$this->Inventory->insert($inv_data);
			//------------------------------------Ramel

			$customer = $this->Customer->get_info($customer_id);
 			if ($customer_id == -1 or $customer->taxable)
 			{
				foreach($this->Item_taxes->get_info($item['item_id']) as $row)
				{
					$this->con->insert('sales_items_taxes', array(
						'sale_id' 	=>$sale_id,
						'item_id' 	=>$item['item_id'],
						'line'      =>$item['line'],
						'name'		=>$row['name'],
						'percent' 	=>$row['percent']
					));
				}
			}
		}
		$this->con->trans_complete();

		if ($this->con->trans_status() === FALSE)
		{
			return -1;
		}
		//si finalizo la venta y hay giftcards se les agrega el monto pagado
		$first_gc=true;
		foreach($items as $line=>$row){
			if($row['item_id']<0){
				if($first_gc){
					$this->load->model('giftcard');
					$first_gc=false;
				}
				$gift_card=array('giftcard_number'=>$row['item_number'],'value'=>$row['price']);
				$this->Giftcard->save($gift_card,false,true);
			}
		}
		return $sale_id;
	}

	function delete($sale_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->con->trans_start();

		$this->con->delete('sales_payments', array('sale_id' => $sale_id));
		$this->con->delete('sales_items_taxes', array('sale_id' => $sale_id));
		$this->con->delete('sales_items', array('sale_id' => $sale_id));
		$this->con->delete('sales', array('sale_id' => $sale_id));

		$this->con->trans_complete();

		return $this->con->trans_status();
	}

	function get_sale_items($sale_id)
	{
		$this->con->from('sales_items');
		$this->con->where('sale_id',$sale_id);
		return $this->con->get();
	}

	function get_sale_payments($sale_id)
	{
		$this->con->from('sales_payments');
		$this->con->where('sale_id',$sale_id);
		return $this->con->get();
	}

	function get_customer($sale_id)
	{
		$this->con->from('sales');
		$this->con->where('sale_id',$sale_id);
		return $this->Customer->get_info($this->con->get()->row()->customer_id);
	}

	//We create a temp table that allows us to do easy report/sales queries
	public function create_sales_items_temp_table($con = false)
	{

		if ($con) {
  			$this->con = $con;
  		}elseif(!$this->con){
        	$this->con = $this->db;
       	}
		$this->con->query( 'DROP TABLE IF EXISTS '.$this->con->dbprefix('sales_items_temp') );
		$this->con->query("CREATE TEMPORARY TABLE ".$this->con->dbprefix('sales_items_temp')."
		(SELECT date(sale_time) as sale_date, ".$this->con->dbprefix('sales_items').".sale_id, comment, mode,payment_type, customer_id, employee_id,
		".$this->con->dbprefix('items').".item_id, supplier_id, quantity_purchased, item_cost_price, item_unit_price, SUM(percent) as item_tax_percent,
		discount_percent, (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100) as subtotal,
		".$this->con->dbprefix('sales_items').".line as line, serialnumber, ".$this->con->dbprefix('sales_items').".description as description,
		ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(1+(SUM(percent)/100)),2) as total,
		ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(percent)/100),2) as tax,
		(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100) - (item_cost_price*quantity_purchased) as profit
		FROM ".$this->con->dbprefix('sales_items')."
		INNER JOIN ".$this->con->dbprefix('sales')." ON  ".$this->con->dbprefix('sales_items').'.sale_id='.$this->con->dbprefix('sales').'.sale_id'."
		INNER JOIN ".$this->con->dbprefix('items')." ON  ".$this->con->dbprefix('sales_items').'.item_id='.$this->con->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->con->dbprefix('suppliers')." ON  ".$this->con->dbprefix('items').'.supplier_id='.$this->con->dbprefix('suppliers').'.person_id'."
		LEFT OUTER JOIN ".$this->con->dbprefix('sales_items_taxes')." ON  "
		.$this->con->dbprefix('sales_items').'.sale_id='.$this->con->dbprefix('sales_items_taxes').'.sale_id'." and "
		.$this->con->dbprefix('sales_items').'.item_id='.$this->con->dbprefix('sales_items_taxes').'.item_id'." and "
		.$this->con->dbprefix('sales_items').'.line='.$this->con->dbprefix('sales_items_taxes').'.line'."
		GROUP BY sale_id, item_id, line)");

		//Update null item_tax_percents to be 0 instead of null
		$this->con->where('item_tax_percent IS NULL');
		$this->con->update('sales_items_temp', array('item_tax_percent' => 0));

		//Update null tax to be 0 instead of null
		$this->con->where('tax IS NULL');
		$this->con->update('sales_items_temp', array('tax' => 0));

		//Update null subtotals to be equal to the total as these don't have tax
		$this->con->query('UPDATE '.$this->con->dbprefix('sales_items_temp'). ' SET total=subtotal WHERE total IS NULL');
	}

	public function get_giftcard_value( $giftcardNumber )
	{
		if ( !$this->Giftcard->exists( $this->Giftcard->get_giftcard_id($giftcardNumber)))
			return 0;

		$this->con->from('giftcards');
		$this->con->where('giftcard_number',$giftcardNumber);
		return $this->con->get()->row()->value;
	}
}
?>
