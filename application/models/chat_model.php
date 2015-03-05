<?php
class Chat_model extends CI_Model{
	/**
	 * Constructor
	 *
	 */
	public $logged=false;
	private $user=false;
	private $dbusr=false;
	private $loggedUsr=false;
	private $con=false;
	private $dbgroup='centralized';
	private $table='chat_users';
	private $view='chat_users_view';
	private $chat_view='chat_view';
	private $typing='chat_user_typing';
	private $prefix='ospos_';
	private $chat_id=false;

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		//Redirect if not logged
		$this->logged=(!!$this->session->userdata('person_id'));
		$this->getCon();
		if($this->logged) $this->getUser();
		// var_dump($this->user);
		@session_start();
		if(!isset($_SESSION['chat'])) $_SESSION['chat']=array();
		if(!isset($_SESSION['chat'][$this->user->chat_id])) $_SESSION['chat'][$this->user->chat_id]=array();
	}

	// --------------------------------------------------------------------

	function cleanTyping($id=false){
		if(!$id) $id=$this->user->chat_id;
		$this->con->where('from_id',$id);
		$query=$this->con->delete($this->typing);
		return $query?true:false;
	}
	function isTyping(){
		$this->con->from($this->typing);
		//$this->con->where('typing',1);
		$this->con->where('to_id',$this->user->chat_id);
		$this->con->order_by('date','asc');
		$query=$this->con->get();
		return $query->result();
	}
	function disableUser($disabled=false){
		$id=$this->user->chat_id;
		$this->con->set('disabled',$disabled?1:0);
		$this->con->where('chat_id',$id);
		$this->con->update($this->table);
	}
	function isDisabledUser($id=false,$disabled=false){
		if(!$id) return false;
		$this->con->set('disabled',$disabled?0:1);
		$this->con->where('chat_id',$id);
		$this->con->update($this->table);
	}
	function getChats()
	{
		$this->con->where('recd',0);
		$this->con->where('to_id',$this->user->chat_id);
		$this->con->order_by('id','asc');
		$query=$this->con->get($this->chat_view);
		return $query->result();
	}
	function getChatUsers($update=false){
		$this->updateUsers();
		if($update) $this->updateLoggedUser();
		$this->con->select('chat_id AS c,user AS u,username AS n,location AS l,status_name AS t');
		$this->con->where('disabled',0);
		$this->con->where('timediff(now(),last_action) < "02:00"');
		$this->con->order_by('user_id','asc');
		$query=$this->con->get($this->view);
		if($query->num_rows()>0) return $query;
		return false;
	}
	function getCon($conditions=array(),$fields='')
	{
		if($this->con) return $this->con;
		include('application/config/database.php');
		if (isset( $db[$this->dbgroup] )){
			$this->con = $this->load->database($this->dbgroup,true);
			//a typing se le agrega el prefijo debido a que se realiza una consulta especial
			$this->prefix=$db[$this->dbgroup]['dbprefix'];
		}else{
			show_error('Please set the Connection group and database transactions');
		}
		return $this->con;
	}
	function getUser($id=false)
	{
		if(!$id){
			if($this->user) return $this->user;
			else{
				$this->dbusr=$this->Employee->get_logged_in_employee_info();
				// var_dump($this->dbusr);
				$id=$this->setChatId($this->dbusr->person_id,$this->dbusr->location);
			}
		}
		if(!$id) return false;
		// echo $id;
		$this->con->where('chat_id',$id);
		$query=$this->con->get($this->view);
		// var_dump($query->row());
		if($query) $this->user=$query->row();
		return $this->user;
	}
	function getUsers($conditions=array(),$fields='')
	{
		$this->updateLoggedUser();
		$this->updateUsers();

		if(count($conditions)>0){
			$this->con->where($conditions);
		}

		$this->con->order_by('user_id','asc');

		if($fields!='')
			$this->con->select($fields);

		$result = $this->con->get($this->view);

		return $result;
	}
	function is_logged()
	{
		if(!$this->logged){
			return false;
		}else{
			return $this->user->status_id!=0;
		}
	}
	function setChatId($id,$location)
	{
		if(!$id||!$location) return false;
		// var_dump(array($id,$location));
		$query=$this->con->query("
			INSERT INTO $this->prefix$this->table SET
				user_id=$id,
				location='$location',
				status=1,
				disabled=0
			ON DUPLICATE KEY UPDATE
				status=1,
				disabled=0
		");
		// echo $this->con->last_query();
		$this->con->where('user_id',$id);
		$this->con->where('location',$location);
		$query=$this->con->get($this->view);
		// var_dump($query->row());
		// echo $this->con->last_query();
		if($query->num_rows() > 0)
			return $query->row()->chat_id;
		return false;
	}
	function setStatus($chat_id=false,$status=1){
		if(!$chat_id) return false;
		$this->con->set('status',$status);
		$this->con->where('chat_id',$chat_id);
		$this->con->update($this->table);
	}
	function setTyping($to=false,$status=true){
		if(!$to) return false;
		$this->updateLoggedUser();
		$status=$status?1:0;
		$from=$this->user->chat_id;
		$query=$this->con->query("
			INSERT INTO $this->prefix$this->typing SET
				from_id=$from,
				to_id=$to,
				typing=$status
			ON DUPLICATE KEY UPDATE typing=$status
		");
		return $query?true:false;
	}
	function updateUsers()
	{
		$this->con->set('status',0);
		$this->con->set('last_action','last_action',FALSE);
		$this->con->where('status in (1,2)');
		$this->con->where('timediff(now(),last_action) > "00:30"');
		$this->con->update($this->table);

		$this->con->set('status',2);
		$this->con->set('last_action','last_action',FALSE);
		$this->con->where('status',1);
		$this->con->where('timediff(now(),last_action) > "00:05"');
		$this->con->update($this->table);

		$this->con->set('typing',0);
		$this->con->where('timediff(now(),date) > "00:03:00"');
		$this->con->update($this->typing);
	}
	function updateLoggedUser()
	{
		if(!$this->user) return false;
		$id=$this->user->user_id;
		$location=$this->user->location;
		$data=array(
			'username'=>$this->dbusr->username,
			'status'=>1,
		);
		$this->con->where('user_id',$id);
		$this->con->where('location',$location);
		$this->con->update($this->table,$data);
	}
	function clear_session(){
		unset($_SESSION['chat']);
	}
}
