<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
Author: Stojan Kukrika
Version: 1.0
*/

// ------------------------------------------------------------------------
class Users extends Website_Controller{
	
	private $page;
	private $pagecontent;
	function __construct(){
		parent::__construct();		
		$this->load->model(array('Model_user', "Model_message","Model_password_reminder"));	
	
	}
	function login(){
		$this->page = $this->db->limit(1)->get('pages')->first_row();
		$this->pagecontent = Website_Controller::createSiderContent($this->page->id);
		if($this->_is_logged_in()){
			redirect('');
		}
		
		if($_POST){
			  if ($u = $this->Model_user->login($this->input->post('email'),$this->input->post('password')))
		        {		        													
		        	$user = $this->Model_user->selectuser($this->input->post('email'));
		        	$data = array(
		        	'user_id' => $user->id,
					'username' => $user->username,
					'name' => $user->name,
					'surname' => $user->surname,
					'logged_in' => true,
					'avatar' =>($this->session->userdata('usegravatar')=="No")?($user->avatar!='NULL')?$user->avatar:"":$this->gravatar->get_gravatar($user->email),
					'admin_logged_in' => $this->_is_admin($user->id),
					);
					$this->session->set_userdata($data);
					$result = $this->db->from('assigned_roles')
										->join('permission_role','assigned_roles.role_id=permission_role.role_id')
										->join('permissions','permissions.id=permission_role.permission_id')
										->where('assigned_roles.user_id',$this->session->userdata("user_id"))
										->select('name')
										->get()->result();
					foreach ($result as $row)
					{
						$this->session->set_userdata($row->name,$row->name);
					}					
					redirect('');
		        }
		        else
		        {
		        	echo '<br><p class="text-danger">Wrong username or password!</p>';
		        }
		}		
		$this->_member_area();		
		$data['content'] = array(
            'right_content' => $this->pagecontent['sidebar_right'],
            'left_content' => $this->pagecontent['sidebar_left'],
        );
		$this->load->view('login',$data);
	}
	function login_partial(){			
		
		$this->load->view('login_partial');
	}
	
	function logout(){
		$this->session->sess_destroy();
		redirect('');
	}
	
	function register(){
		$this->page = $this->db->limit(1)->get('pages')->first_row();
		$this->pagecontent = Website_Controller::createSiderContent($this->page->id);
		$this->_member_area();
		
		$data['content'] = array(
            'right_content' => $this->pagecontent['sidebar_right'],
            'left_content' => $this->pagecontent['sidebar_left'],
        );
		
		$this->load->view('register', $data);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password', "Password", 'required');
		$this->form_validation->set_rules('confirm_password', "Confirm password", 'required');
		$this->form_validation->set_rules('username', "Username", 'required|is_unique[users.username]');
		$this->form_validation->set_rules('email', "Email", 'required|valid_email|is_unique[users.email]');
		if ($this->form_validation->run() == TRUE)
        {
        	$passwordOk = "";
        	if($this->session->userdata('passwordpolicy')=="Yes"){	        		
	        	$varname = array('minpasswordlength','minpassworddigits','minpasswordlower',
	        					'minpasswordupper','minpasswordnonalphanum');
				$this->db->where_in('varname',$varname);
				$query = $this->db->from('settings')->get();
				foreach ($query->result() as $row)
				{
					switch ($row->varname) {
						case 'minpasswordlength':
								if(!strlen($this->input->post('password'))>=$row->value) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
						case 'minpassworddigits':
								if(!preg_match('/[0-9]{'.$row->value.'}+/', $this->input->post('password'))) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
						case 'minpasswordlower':
								if(!preg_match('/[a-z]{'.$row->value.'}+/', $this->input->post('password'))) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
						case 'minpasswordupper':
								if(!preg_match('/[A-Z]+/', $this->input->post('password'))) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
					}
				}
        	}			
			if($passwordOk==""){
				
			$this->load->library('Hash');
			$hash = new Hash();
				if($this->input->post('password')!="" && $this->input->post('password')==$this->input->post('confirm_password'))
					{
						$code = md5(microtime() . $this->input->post('password'));
						$this->Model_user->insert(array('name'=>$this->input->post('name'),
													'surname'=>$this->input->post('surname'),
													'username'=>$this->input->post('username'),
													'password'=>$hash->make($this->input->post('password')),
													'email'=>$this->input->post('email'),
													'confirmation_code'=> $code,
													'confirmed'=>0,
													'active'=>1,
													'created_at' => date("Y-m-d H:i:s"),
													'updated_at' => date("Y-m-d H:i:s")));
			        	echo '<div class="container"><div class="col-xs-12 col-sm-6 col-lg-8"><br>
								<div class="row">You have successfully registered</p></div></div></div>';
	
							//Send validation mail 
							
							$this->load->library('email');
		
							$this->email->from($this->Model_user->getsiteemail(), $this->Model_user->getsitetitle());
							$this->email->to($this->input->post('email')); 
							
							$this->email->subject('Confirmation');
							$this->email->message("Confirm your subscription <a href=''>Confirmar</a>".$code);	
							$this->email->send();
			        }
			        else
			        {
			            echo '<br><p class="text-danger">Password not equal</p>';
			        }
	        	}
			else {
				echo '<br><p>'.$passwordOk.'</p>';		            
			}
		}		
	}	

	function _member_area(){
		if($this->_is_logged_in()){
			redirect('');
		}
	}
	
	function _is_logged_in(){
		if($this->session->userdata('logged_in')){
			return true;
		}else{
			return false;
		}
	}
	
	function userdata(){
		if($this->_is_logged_in()){
			$user = $this->Model_user->select($this->session->userdata('user_id'));
			$user->avatar = ($this->session->userdata('usegravatar')=="No")?($user->avatar!='NULL')?$user->avatar:"":$this->gravatar->get_gravatar($user->email);
			return $user;
		}else{
			return false;
		}
	}
	
	function _is_admin($user_id=0)
	{
		$roles = $this->Model_user->isadmin($user_id);
		$is_admin = false;
		if(!empty($roles)){
			foreach($roles as $item)
			{
				if($item->is_admin=='1')
				{
					$is_admin = true;
				}
			}	
		}	 
		return $is_admin;
	}

	function account ()
	{
		$this->page = $this->db->limit(1)->get('pages')->first_row();
		$this->pagecontent = Website_Controller::createSiderContent($this->page->id);
		
		$data['content'] = array(
            'right_content' => $this->pagecontent['sidebar_right'],
            'left_content' => $this->pagecontent['sidebar_left'],
        );
		
		$this->load->view('account', $data);
			   	
	   if($_POST)
        {
        	$this->load->library('Hash');
			$hash = new Hash();
			if($_FILES['avatar']['name']!=""){
					$filename = $_FILES['avatar']['name'];
					$sha = sha1($filename.time());
					$file = $sha.'.'.pathinfo($filename, PATHINFO_EXTENSION);
					$config['file_name'] = $file;
					$config['upload_path'] = DATA_PATH.'/avatar/';
					$config['allowed_types'] = 'gif|jpg|png';
					$this->load->library('upload', $config);
					$this->upload->do_upload('avatar');
					
					$config_manip['source_image'] = $config['upload_path'].$file;
					$config_manip['new_image'] = DATA_PATH.'/avatar/';
		            $config_manip['maintain_ratio'] = TRUE;
				    $config_manip['create_thumb'] = TRUE;
				    $config_manip['width'] = $this->session->userdata("useravatwidth");
				    $config_manip['quality'] = 100;
					$config_manip['height'] = $this->session->userdata("useravatheight");
		            $this->load->library('image_lib', $config_manip);
		            $this->image_lib->resize();
					
					unlink($config['upload_path'].$file);
					rename($config['upload_path'].$sha.'_thumb.'.pathinfo($filename, PATHINFO_EXTENSION), $config['upload_path'].$file);
					
					$data = array('avatar'=>$file);
					$this->Model_user->update($data,$this->session->userdata('user_id'));				
				}

			$passwordOk = "";
        	if($this->session->userdata('passwordpolicy')=="Yes"){	        		
	        	$varname = array('minpasswordlength','minpassworddigits','minpasswordlower',
	        					'minpasswordupper','minpasswordnonalphanum');
				$this->db->where_in('varname',$varname);
				$query = $this->db->from('settings')->get();
				foreach ($query->result() as $row)
				{
					switch ($row->varname) {
						case 'minpasswordlength':
								if(!strlen($this->input->post('password'))>=$row->value) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
						case 'minpassworddigits':
								if(!preg_match('/[0-9]{'.$row->value.'}+/', $this->input->post('password'))) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
						case 'minpasswordlower':
								if(!preg_match('/[a-z]{'.$row->value.'}+/', $this->input->post('password'))) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
						case 'minpasswordupper':
								if(!preg_match('/[A-Z]+/', $this->input->post('password'))) 
								$passwordOk = "Password do not corresponding to Password policy.";
							break;
					}
				}
        	}
			if($passwordOk==""){	
				if($this->input->post('old_password')!="" && $this->input->post('password')!="" && $this->input->post('password')==$this->input->post('confirm_password'))
				{
					$user = $this->Model_user->selectuser($this->session->userdata('username'));
					if($user->password==$hash->make($this->input->post('old_password')))
					{
						$data = array('password'=>$hash->make($this->input->post('password')));
						$this->Model_user->update($data,$this->session->userdata('user_id'));
					}
					else {
						echo '<br><p class="text-danger">Old password is not valid!</p>';
					}
				}
			}
			else {
				echo '<br><p>'.$passwordOk.'</p>';		            
			}
					
			$this->logout();	
		}	
	}
	
	function messages ()
	{
		$this->page = $this->db->limit(1)->get('pages')->first_row();
		$this->pagecontent = Website_Controller::createSiderContent($this->page->id);
		
		$sent = $this->Model_message->selectSend($this->session->userdata('user_id'));
		foreach($sent as $item){
			if($this->session->userdata('timeago')=='Yes'){
				$item->created_at =timespan(strtotime($item->created_at), time() ) . ' ago' ;
			}
			else{				
				$item->created_at = date($this->session->userdata("datetimeformat"),strtotime($item->created_at));
			}
		}
		$received = $this->Model_message->selectReceived($this->session->userdata('user_id'));
		foreach($received as $item){
			if($this->session->userdata('timeago')=='Yes'){
				$item->created_at =timespan(strtotime($item->created_at), time() ) . ' ago' ;
			}
			else{				
				$item->created_at = date($this->session->userdata("datetimeformat"),strtotime($item->created_at));
			}
		}
		$data['send'] = $sent;
		$data['received'] = $received;
		$data['allUsers'] = $this->Model_user->selectAll($this->session->userdata('user_id'));
		
		$data['content'] = array(
            'right_content' => $this->pagecontent['sidebar_right'],
            'left_content' => $this->pagecontent['sidebar_left'],
        );
		
		$this->load->view('messages', $data);
	}
	
	function readmessage($id_message)
	{
		$data = array('read'=>1, 'updated_at'=>date("Y-m-d H:i:s"));
		$this->Model_message->update($data,$id_message);
	}
	
	function sendmessage()
	{
		if($this->input->post('subject')!="" && $this->input->post('recipients')!="")
		{
			foreach($this->input->post('recipients') as $to){
				$this->Model_message->insert(array('subject'=>$this->input->post('subject'),
											'user_id_from'=>$this->session->userdata('user_id'),
											'user_id_to'=>$to,
											'content'=>$this->input->post('message'),
											'read'=>'0',
											'created_at' => date("Y-m-d H:i:s"),
											'updated_at' => date("Y-m-d H:i:s")));
			}
		}
		redirect(base_url('users/messages'));
	}
	function sendreplay($id_message)
	{
		if($id_message>0)
		{
			$message = $this->Model_message->select($id_message);
			$this->Model_message->insert(array('subject'=>"RE: ".$message->subject,
											'user_id_from'=>$this->session->userdata('user_id'),
											'user_id_to'=>$message->user_id_from,
											'content'=>$this->input->post('message'),
											'read'=>'0',
											'created_at' => date("Y-m-d H:i:s"),
											'updated_at' => date("Y-m-d H:i:s")));
		}
	}
	function deletereceiver($id_message)
	{
		$this->Model_message->deletereceiver($id_message);
	}
	function deletesender($id_message)
	{
		$this->Model_message->deletesender($id_message);
	}
	
	function forgot()
	{
		$this->page = $this->db->limit(1)->get('pages')->first_row();
		$this->pagecontent = Website_Controller::createSiderContent($this->page->id);
		
		$data['content'] = array(
            'right_content' => $this->pagecontent['sidebar_right'],
            'left_content' => $this->pagecontent['sidebar_left'],
        );
		
		$this->load->view('forgot', $data);		
		
		if($_POST){
			  if ($u = $this->Model_user->checkuser($this->input->post('username'),$this->input->post('email')))
		        {
		        	$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
				    $randstring = '';
				    for ($i = 0; $i < 50; $i++) {
				        $randstring .= $characters[rand(0, strlen($characters))];
				    }
					$this->load->library('Hash');	
					$hash = new Hash();
					$data = array('password'=>$hash->make($randstring));
					$this->Model_user->update($data,$u->id);
					
					$data = array('email'=>$this->input->post('email'), 
									'token' => $randstring,
									'created_at' => date("Y-m-d H:i:s"));
					$this->Model_password_reminder->insert($data);
					
					//Send validation mail 					
					$this->load->library('email');

					$this->email->from($this->Model_user->getsiteemail(), $this->Model_user->getsitetitle());
					$this->email->to($this->input->post('email')); 
					
					$this->email->subject('Change password');
					$this->email->message("Your password is changed to: ".$randstring);	
					$this->email->send();
					
				}
			  else {
			  	echo '<br><p class="text-danger">Username and email is not match in users of this site!</p>';
			  }
		}
	
	}	
	
}

?>