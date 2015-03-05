<?php
class Caja extends CI_Model{

    var $con; //Objeto de conexsion con la bd

    function __construct()
    {
        parent::__construct();
        $this->Sale->create_sales_items_temp_table();
        $this->Receiving->create_receivings_items_temp_table();

        //Seleccion de DB
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }

    public function getDataColumns()
    {
        return array($this->lang->line('reports_payment_type'), $this->lang->line('reports_total'));
    }

    function getCierreDetails(){
        $this->con->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit');
        $this->con->from('sales_items_temp');
        $this->con->where('quantity_purchased > 0');
        $this->con->where("sale_date = DATE(NOW())");
        $this->con->order_by('sale_date');
        return $this->con->get()->row();
    }

    function getDetailsPayments(){
        $this->con->select('sales_payments.payment_type, SUM(payment_amount) as payment_amount', false);
        $this->con->from('sales_payments');
        $this->con->join('sales', 'sales.sale_id=sales_payments.sale_id');
        $this->con->where('DATE(sale_time) = DATE(NOW())');
        $this->con->where('payment_amount > 0');
        $this->con->group_by("payment_type");
        return $this->con->get()->result_array();
    }
}
?>
