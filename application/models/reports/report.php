<?php
abstract class Report extends CI_Model
{	var $con;
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->stabledb('default');
		//Make sure the report is not cached by the browser
		$this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");

		//$db = $this->session->set_userdata('dblocation','other');
		//Create our temp tables to work with the data in our report
		// $this->Sale->create_sales_items_temp_table();
		// $this->Receiving->create_receivings_items_temp_table();

	}

	public function stabledb($db,$retu=false){
		//$db = $this->session->userdata('items_location');
        if($db) $this->con = $this->load->database($db, true);
        else $this->con = $this->db;
        if ($retu) return $this->con;
	}

	//Returns the column names used for the report
	public abstract function getDataColumns();

	//Returns all the data to be populated into the report
	public abstract function getData(array $inputs);

	//Returns key=>value pairing of summary data for the report
	public abstract function getSummaryData(array $inputs);
}
?>
