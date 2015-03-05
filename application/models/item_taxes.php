<?php
class Item_taxes extends CI_Model
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
	/*
	Gets tax info for a particular item
	*/
	function get_info($item_id)
	{
		$this->con->from('items_taxes');
		$this->con->where('item_id',$item_id);
		//return an array of taxes for an item
		return $this->con->get()->result_array();
	}

	/*
	Inserts or updates an item's taxes
	*/
	function save(&$items_taxes_data, $item_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->con->trans_start();

		$this->delete($item_id);

		foreach ($items_taxes_data as $row)
		{
			$row['item_id'] = $item_id;
			$this->con->insert('items_taxes',$row);
		}

		$this->con->trans_complete();
		return true;
	}

	function save_multiple(&$items_taxes_data, $item_ids)
	{
		foreach($item_ids as $item_id)
		{
			$this->save($items_taxes_data, $item_id);
		}
	}

	/*
	Deletes taxes given an item
	*/
	function delete($item_id)
	{
		return $this->con->delete('items_taxes', array('item_id' => $item_id));
	}
}
?>
