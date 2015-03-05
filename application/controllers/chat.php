<?php
class Chat extends CI_Controller{
	//Global variable
	public $outputData;//Holds the output data for each view
	public $user=array();
	private $con=false;

	function __construct(){
		parent::__construct();
		//Load the chat users model
		$this->load->model('chat_model');
		$_logged=$this->chat_model->is_logged();
		$this->con=$this->chat_model->getCon();
		$this->user=$this->chat_model->getUser();
		//Load the session library
		$this->load->library('session');
	}

	public function print_json($json){
		if(is_array($json)) $json['online']=$this->is_online();
		$this->load->view('partial/json',array('json'=>$json));
	}
	private function is_online(){
		return $this->chat_model->is_logged();
	}
	public function index(){
		if($this->is_online())
			$this->load->view('chat/chat',$this->getUsersList());
		else
			redirect('..');
	}
	function islogged(){
		$this->print_json(array(
			'logged'=>$this->chat_model->logged,
			'status'=>$this->chat_model->getUser()->status_name,
		));
	}
	function startchatsession(){
		$this->chat_model->updateLoggedUser();
		$items=array();
		$session=$this->getSession();
		if(!empty($_SESSION['chat'][$this->user->chat_id]['openBoxes'])){
			foreach($_SESSION['chat'][$this->user->chat_id]['openBoxes'] as $chatbox=>$void){
				foreach($this->chatBoxSession($chatbox) as $item){
					$items[]=$item;
				}
			}
		}
		$data=array(
			'userid'=>$this->user->chat_id,
			'username'=>$this->user->user,
			'items'=>$items,
			'chat'=>$_SESSION['chat'][$this->user->chat_id],
		);
		$this->putSession($session);
		$this->print_json($data);
	}
	function closechat(){
		$chatbox = $this->input->post('chatbox');
		// $this->session->unset_userdata('chat');
		$id=$this->chat_model->getUser()->chat_id;
		$this->chat_model->setStatus($id,0);
		$this->chat_model->cleanTyping();
		$session=$this->getSession();
		unset($_SESSION['chat'][$this->user->chat_id]['openBoxes'][$chatbox]);
		$_SESSION['chat'][$this->user->chat_id]['status']='offline';
		$this->putSession($session);
		$this->print_json(1);
	}

	public function disable(){
		$this->chat_model->disableUser(true);
		$this->print_json(1);
	}
	public function enable(){
		$this->chat_model->disableUser(false);
		$this->print_json(1);
	}
	public function chatheartbeat(){
		$items = array();
		$typing = array();
		$chatBoxes = array();
		$session=$this->getSession();
		foreach($this->chat_model->getChats() as $chat){
			if(!isset($_SESSION['chat'][$this->user->chat_id]['openBoxes'][$chat->from_id]) && isset($_SESSION['chat'][$this->user->chat_id]['history'][$chat->from_id])){
				$items = $_SESSION['chat'][$this->user->chat_id]['history'][$chat->from_id];
			}
			$items[]=array(
				's'=>0,
				'f'=>$chat->from_id,
				'u'=>$chat->from,
				'm'=>$this->sanitize($chat->message),
			);
			if(!isset($_SESSION['chat'][$this->user->chat_id]['history'][$chat->from_id])){
				$_SESSION['chat'][$this->user->chat_id]['history'][$chat->from_id] = '';
			}
			$_SESSION['chat'][$this->user->chat_id]['history'][$chat->from_id][]=array(
				's'=>0,
				'f'=>$chat->from_id,
				'u'=>$chat->from,
				'm'=>$chat->message,
			);
			unset($_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$chat->from_id]);
			$_SESSION['chat'][$this->user->chat_id]['openBoxes'][$chat->from_id] = $chat->sent;
		}
		if(!empty($_SESSION['chat'][$this->user->chat_id]['openBoxes'])){
			foreach ($_SESSION['chat'][$this->user->chat_id]['openBoxes'] as $chatbox => $time){
				if(!isset($_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$chatbox])){
					$now = time()-strtotime($time);
					$time = date('g:iA M dS',strtotime($time));
					$message = "Sent at $time";
					if($now > 180){
						$items[]=array(
							's'=>2,
							'f'=>$chatbox,
							'm'=>$message,
						);
						if(!isset($_SESSION['chat'][$this->user->chat_id]['history'][$chatbox])){
							$_SESSION['chat'][$this->user->chat_id]['history'][$chatbox] = '';
						}
						$_SESSION['chat'][$this->user->chat_id]['history'][$chatbox][]=array(
							's'=>2,
							'f'=>$chatbox,
							'm'=>$message,
						);
						$_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$chatbox] = 1;
					}
				}
			}
		}
		$this->con->where('recd',0);
		$this->con->where('to_id',$this->user->chat_id);
		$query=$this->con->update('chat',array('recd'=>1));
		foreach($this->chat_model->isTyping() as $chat){
			$typing[]=array(
				's'=>$chat->typing,
				'f'=>$chat->to_id,
				'u'=>$chat->from_id,
				'm'=>'',
			);
		}
		$data=array(
			'ty'=>$typing,
			'items'=>$items,
			'query'=>isset($_SESSION['chat'][$this->user->chat_id]['history'])?$_SESSION['chat'][$this->user->chat_id]['history']:array()
		);
		$this->putSession($session);
		$this->print_json($data);
	}
	public function friendslist(){
		$update=$this->input->post('update');
		if(!$this->chat_model->logged) $this->print_json(array());
		$list=$this->chat_model->getChatUsers($update);
		$data=array(
			'a'=>1,
			'f'=>$list?$list->result_array():array(),
			'u'=>$update,
		);
		$this->print_json($data);
	}
	public function starttyping(){
		$to = $this->input->post('to');
		$this->print_json(
			$this->chat_model->setTyping($to,1)
		);
	}
	public function stoptyping(){
		$to = $this->input->post('to');
		$this->print_json(
			$this->chat_model->setTyping($to,0)
		);
	}
	function sendchat(){
		$from = $this->user->chat_id;
		$to = $this->input->post('to');
		$usr = $this->chat_model->getUser($to);
		$session=$this->getSession();
		$tmp = array(
			'from'=>$from,
			'to'=>$to,
			'usr'=>$usr,
			'tsbox'=>isset($_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$to])?$_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$to]:NULL,
		);
		$name_to = $usr->user;
		$message = $this->input->post('message');

		$_SESSION['chat'][$this->user->chat_id]['openBoxes'][$to] = date('Y-m-d H:i:s');
		$tmp['openbox']=$_SESSION['chat'][$this->user->chat_id]['openBoxes'][$to];
		$messagesan = $this->sanitize($message);

		if(!isset($_SESSION['chat'][$this->user->chat_id]['history'][$to])){
			$_SESSION['chat'][$this->user->chat_id]['history'][$to] = array();
		}
		$data=array(
			's'=>1,
			'f'=>$to,
			'u'=>$name_to,
			'm'=>$message,
		);
		$tmp[$to]=$data;
		$_SESSION['chat'][$this->user->chat_id]['history'][$to][]=$data;
		$tmp['history']=$_SESSION['chat'][$this->user->chat_id]['history'];

		$tmp['tsbox']=isset($_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$to])?$_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$to]:NULL;
		unset($_SESSION['chat'][$this->user->chat_id]['tsBoxes'][$to]);

		$this->con->set(array(
			'from_id'=>$from,
			'to_id'=>$to,
			'message'=>$message,
		));
		$this->con->insert('chat');
		$this->putSession($session);
		$this->print_json($tmp);
	}

	private function chatBoxSession($chatbox){
		$items='';
		$session=$this->getSession();
		if (isset($_SESSION['chat'][$this->user->chat_id]['history'][$chatbox])) {
			$items = $_SESSION['chat'][$this->user->chat_id]['history'][$chatbox];
			// var_dump($items);
		}
		return $items;
	}
	private function getUsersList(){
		$users=$this->chat_model->getUsers();
		return array(
			'listOfUsers'=>$users,
			'user'=>$this->chat_model->getUser(),
		);
	}
	private function get_db(){
		include('application/config/database.php');
		return $db;
	}
	private function getCon(){
		$this->load->model('chat_model');
		return $this->chat_model->con;
	}
	private function sanitize($text){
		$text = htmlspecialchars($text,ENT_QUOTES);
		$text = str_replace("\r",'',$text);
		$text = str_replace("\n",'<br/>',$text);
		return $text;
	}
	private function getSession(){
		$session=$this->session->userdata('chat');
		if(!$session) $session=array();
		return $session;

	}
	private function putSession($data){
		$this->session->set_userdata('chat',$data);		
	}
}
