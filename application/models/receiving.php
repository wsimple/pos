<?php
class Receiving extends CI_Model
{
	var $con;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        //Seleccion de DB
        // $this->session->set_userdata(array('dblocation'=>'other'));
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }

	public function get_info($receiving_id)
	{
		$this->con->from('receivings');
		$this->con->where('receiving_id',$receiving_id);
		return $this->con->get();
	}

	function exists($receiving_id)
	{
		$this->con->from('receivings');
		$this->con->where('receiving_id',$receiving_id);
		$query = $this->con->get();

		return ($query->num_rows()==1);
	}

	function save($items,$supplier_id,$employee_id,$comment,$payment_type,$amount_tendered,$receiving_id=false)
	{
		if(count($items)==0)
			return -1;

		$receivings_data = array(
		'supplier_id'=> $this->Supplier->exists($supplier_id) ? $supplier_id : null,
		'employee_id'=>$employee_id,
		'payment_type'=>$payment_type,
		'comment'=>$comment,
		'payment'=>$amount_tendered
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->con->trans_start();

		$this->con->insert('receivings',$receivings_data);
		$receiving_id = $this->con->insert_id();


		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'discount_percent'=>$item['discount'],
				'item_cost_price' => $cur_item_info->cost_price,
				'item_unit_price'=>$item['price']
			);

			$this->con->insert('receivings_items',$receivings_items_data);

			//Update stock quantity
			$item_data = array('quantity'=>$cur_item_info->quantity + $item['quantity']);
			$this->Item->save($item_data,$item['item_id']);

			$qty_recv = $item['quantity'];
			$recv_remarks ='RECV '.$receiving_id;
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$recv_remarks,
				'trans_inventory'=>$qty_recv
			);
			$this->Inventory->insert($inv_data);

			$supplier = $this->Supplier->get_info($supplier_id);
		}
		$this->con->trans_complete();

		if ($this->con->trans_status() === FALSE)
		{
			return -1;
		}

		return $receiving_id;
	}

	function get_receiving_items($receiving_id)
	{
		$this->con->from('receivings_items');
		$this->con->where('receiving_id',$receiving_id);
		return $this->con->get();
	}

	function get_supplier($receiving_id)
	{
		$this->con->from('receivings');
		$this->con->where('receiving_id',$receiving_id);
		return $this->Supplier->get_info($this->con->get()->row()->supplier_id);
	}

	//We create a temp table that allows us to do easy report/receiving queries
	public function create_receivings_items_temp_table($con=false)
	{
  		if ($con) {
  			$this->con = $con;
  		}elseif(!$this->con){
        	$this->con = $this->db;
       	}
		$this->con->query( 'DROP TABLE IF EXISTS '.$this->con->dbprefix('receivings_items_temp') );
		$this->con->query("CREATE TEMPORARY TABLE ".$this->con->dbprefix('receivings_items_temp')."
		(SELECT date(receiving_time) as receiving_date, ".$this->con->dbprefix('receivings_items').".receiving_id, comment,payment_type, employee_id,payment,
		".$this->con->dbprefix('items').".item_id, ".$this->con->dbprefix('receivings').".supplier_id, quantity_purchased, item_cost_price, item_unit_price,
		discount_percent, (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100) as subtotal,
		".$this->con->dbprefix('receivings_items').".line as line, serialnumber, ".$this->con->dbprefix('receivings_items').".description as description,
		ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),2) as total,
		(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100) - (item_cost_price*quantity_purchased) as profit
		FROM ".$this->con->dbprefix('receivings_items')."
		INNER JOIN ".$this->con->dbprefix('receivings')." ON  ".$this->con->dbprefix('receivings_items').'.receiving_id='.$this->con->dbprefix('receivings').'.receiving_id'."
		INNER JOIN ".$this->con->dbprefix('items')." ON  ".$this->con->dbprefix('receivings_items').'.item_id='.$this->con->dbprefix('items').'.item_id'."
		GROUP BY receiving_id, item_id, line)");
	}
}
?>
