<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Model {

	public $con = false;
    private $dbgroup = 'centralized';

	public function __construct()
	{
		parent::__construct();

		include('application/config/database.php');
        if (isset( $db[$this->dbgroup] )){
            $this->con = $this->load->database($this->dbgroup, true); //Unica base de dato centralizada
        }else{
            show_error('Please set the Connection group and load database "ospos_centralized"');
        }
	}

	public function save($order_data, $order_items_data){
		if ($this->con->insert('orders', $order_data)) {
			$order_id = $this->con->insert_id();
			foreach ($order_items_data as $item) {
				$this->con->insert('order_items', array_merge( $item, array('id_order'=>$order_id) ));
			}
			return true;
		}

		return false;
	}

	public function cancel($order_id){
    	$this->con->where('id', $order_id);
    	return $this->con->update('orders', array('status'=>2));
    }

	public function complete($order_id = 0, $sale_id = 0){
		$order_data = array('status'=>1, 'sale_id'=>$sale_id);
		$this->con->where('id', $order_id);
		$this->session->unset_userdata('from_order');
		return $this->con->update('orders', $order_data);
	}

	public function get_info($order_id = false){
		$order = array('info'=>false, 'items'=>false);
		$this->con->from('orders');
		$this->con->where('id', $order_id);
		$this->con->limit(1);
		$order['info'] = $this->con->get();

		if ($order['info']->num_rows() > 0) {
			$this->con->from('order_items');
			$this->con->where('id_order', $order_id);
			$order['items'] = $this->con->get();

			return $order;
		}

		return false;
	}

	function count_all($data=array()){
		return count($this->get_all($data));
	}

	function get_all($data, $limit=5000,$offset=0){
		$this->con->from('orders');
		if (isset($data['filters'])){
			switch ($data['filters']) {
				case 1: $this->con->where('DATE(date) = CURDATE()');	break;
				case 2: $this->con->where('DATE(date) = DATE(CURDATE()-1)');	break;
				case 3: $this->con->where('date between date_sub(now(),INTERVAL 1 WEEK) and now()');	break;
                case 4: $this->con->where('date between date_sub(now(),INTERVAL 1 MONTH) and now()');    break;
			}
		}
		if (isset($data['filter_status'])){
			switch ($data['filter_status']) {
				case 1:	$this->con->where('status', 0); break;
				case 2:	$this->con->where('status', 1); break;
			}
		}
		if (isset($data['filter_location']) && $data['filter_location']!='') $this->con->where('location', $data['filter_location']); 
		else $this->con->where('location !=', $this->session->userdata('dblocation'));

		$this->con->where('status !=', 2)->order_by('date', 'desc');
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get()->result_array();
	}

	 public function get_detail($order_id = 0){
        $this->con->from('orders');
        $this->con->join('order_items', 'orders.id = order_items.id_order');
        
        //Si no hay ID devulve todos las transacciones
        if ($order_id > 0) {
            $this->con->where('orders.id', $order_id);
        }

        //$this->con->where('orders.status', 0);
        //$this->con->where('order.location', $this->session->userdata('dblocation'));
        return $this->con->get();
    }
	public function available(){
        $this->load->dbutil();

        return $this->dbutil->database_exists('possp_'.$this->dbgroup) && $this->con;
    }
    function check_availability($order_id){
    	$con=0;$ctotal=0;$string='';
		$items = $this->get_detail($order_id)->result();
		foreach ($items as $key) { $ctotal++;
			$stock=$this->Item->get_info($key->id_item,'quantity');
			if ($stock->quantity<$key->quantity){
				$con++;$string.=($string==''?'':'+').$key->id_item;
			} 
		}
		return $con==$ctotal?'all':($string==''?1:$string);
	}
}

/* End of file order.php */
/* Location: ./application/models/order.php */