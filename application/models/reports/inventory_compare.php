<?php
class Inventory_compare extends CI_Model
{
    var $con;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        //Seleccion de DB
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }

    public function getDataColumns()
    {
        return array($lang['reports_item_id'] = 'Id', $this->lang->line('reports_item'), $lang['reports_item_in_stock'] = 'In Stock', $lang['reports_comment_item'] = 'Comment', $lang['reports_checked'] = 'Checked');
    }

    public function getData($where='')
    {
        $this->con->select('item_id, name, sum(quantity) as quantity');
        $this->con->from('items');
        // $this->con->join('items', 'sales_items_temp.item_id = items.item_id');
        $this->con->where('quantity > 0');
        $this->con->where('deleted = 0');
        if ($where!='') $this->con->where('item_id '.$where);
        $this->con->group_by('item_id');
        $this->con->order_by('quantity DESC');

        return $this->con->get()->result_array();
    }

    public function save(&$compare_data){
        $b = false;
        if($this->con->insert('items_report',$compare_data)) $b = true;
        return $b;
    }

    public function save_inventory($obs){
        $b = false;
        if(!$this->exist_inventory()){
            $employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
            $data=array('date_register'=>date('Y-m-d H:i:s'),'observation'=>$obs,'person_id'=>$employee_id);
            if($this->con->insert('observation_inventories',$data)) $b = true;
        }
        return $b;
    }
    public function exist_inventory(){
        $b = true;
        $this->con->select('id');
        $this->con->from('observation_inventories');
        $this->con->where('DATE(date_register)=DATE(NOW())');
        //$this->con->where('DATE(date_register) = "'.date('Y-m-d').'"');
        if(!$this->con->get()->row_array()){ $b = false; }
        return $b;
    }

    function count_all()
    {
        $this->con->from('items')->where('deleted',0);
        return $this->con->count_all_results();
    }
}
?>
