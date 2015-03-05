<?php
require_once ("secure_area.php");
class Backup extends Secure_area {
	function __construct(){ parent::__construct('config'); }
	
	function index()
	{	
		if(!$this->Employee->is_logged_in()){ redirect('login'); }
		else {	
			if (!$this->Employee->isAdmin()) redirect('config');
			$data['fastUser'] = $this->Employee->get_logged_in_employee_info()->username;
			$data['referen']=false;
			$this->load->view('config/backup', $data);
		}
	}
	function confirm($cover='-1',$file='',$getS=''){
		$username = $this->Employee->get_logged_in_employee_info()->username;
		$password = $this->input->post("password");
		if(!$this->Employee->login($username,$password) && $cover!='-5'){
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('config_backup_n')));
		}else{
			if (!$this->Employee->isAdmin()) redirect('home');
			if ($cover=='-1'){
				if (!$this->ejecuteBackup()){
					echo json_encode(array('success'=>false,'message'=>'file exist'));
				}else{
					echo json_encode(array('success'=>true,'message'=>$this->lang->line('config_backup_s')));
				}
			}elseif($cover=='-5'){
				$auto=(isset($getS) && $getS!='')?'['.$this->lang->line('config_backup_auto_file').']':'';
				$bd=$this->session->userdata('dblocation');
				$this->load->helper('download');
				$data=&file_get_contents("file-bakups/".$bd.' '.$file.$auto.".sql"); 
				force_download('backup.sql',$data);
				redirect('config');
			}elseif($cover=='-7'){
				$this->ejecuteBackup(true);
				$ched=$this->input->post('recover-backup');
				$mod='';
				if (!$ched){
					$bd=$this->session->userdata('dblocation');
					$file=$this->input->post('list-back');
					$data=&file_get_contents("file-bakups/".$bd.' '.$file.".sql");
					$mod='backup';
				}else{ 
					$data=&file_get_contents($_FILES['datab']['tmp_name']); 
					$mod='file';
				}
				$data=$this->cleanstring($data);
				$res=explode('#;;;',$data);
				if ($this->Appconfig->recoverAll($res))	
					echo json_encode(array('success'=>true,'message'=>$this->lang->line('config_recover_exit_file'),'mod'=>$mod));
				else{
					$data['files']=get_filenames ('file-bakups/');
					$bd=$this->session->userdata('dblocation');
					foreach ($data['files'] as $key) {
						if (strpos($key,$bd)!==false) $file=$key;
					}
					$data=&file_get_contents("file-bakups/".$file);
					$data=$this->cleanstring($data);
					$res=explode('#;;;',$data);
					$this->Appconfig->recoverAll($res);
					echo json_encode(array('success'=>false,'message'=>$this->lang->line('config_recover_error_file'),'mod'=>$mod));
				} 

			}


		}
	}
	function recover(){
		if(!$this->Employee->is_logged_in()){ redirect('login'); }
		else {	
			if (!$this->Employee->isAdmin()) redirect('home');
			$this->load->helper('file');
			$data['files']=get_filenames ('file-bakups/');
			$bd=$this->session->userdata('dblocation');
			$data['numF']=0;
			foreach ($data['files'] as $key) {
				if (strpos($key,$bd)!==false) $data['numF']++;
			}
			krsort($data['files']);
			$data['display']=$data['numF']>0?'display:none;':'';
			$data['fastUser'] = $this->Employee->get_logged_in_employee_info()->username;
			$data['referen']=true;
			$this->load->view('config/backup', $data);
		}
	}
	function cleanstring($data){
		$data=preg_replace("/;\s*$/","", $data);
		$data=preg_replace("/;\r?\n/", ";#;;;", $data);
		return $data;
	}
	function ejecuteBackup($x=false){
		// Carga la clase de utilidades de base de datos
		$this->load->dbutil();
		// Crea una copia de seguridad de toda la base de datos y la asigna a una variable
		$file =& $this->dbutil->backup(array('format'=>'sql')); 
		$date=date("Y-m-d-H-i-s");
		$bd=$this->session->userdata('dblocation');
		// Carga el asistente de archivos y escribe el archivo en su servidor
		$this->load->helper('file');
		// echo $_SERVER['SERVER_NAME'];
		if (file_exists('file-bakups/'.$bd.' '.$date.'.sql')){ return false; }
		else{
			$auto=$x?'['.$this->lang->line('config_backup_auto_file').']':'';
			write_file('file-bakups/'.$bd.' '.$date.$auto.'.sql', $file);	
			return true;
		}
	}
		
}
?>