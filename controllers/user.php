<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends App_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		// $this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('user_agent');
		$this->load->library('form_validation');
		$this->load->helper('url','form');
        $this->load->helper('download');
        // $this->load->helper('home');

		// Load MongoDB library instead of native db driver if required
		// $this->config->item('use_mongodb', 'ion_auth') ?
		// $this->load->library('mongo_db') :
		// $this->load->model('user_model');
		// $this->load->database();
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		// $this->lang->load('auth');
        $this->load->helper('file');

		$this->load->helper('language');
		if (!$this->session->userdata('biggle_id')){
			redirect($this->config->item("base_url"));
		}
		$lang= $this->session->userdata['lang'];
		if ($lang!="" && !empty($lang)){
			if ($lang == 'ch'){
				$this->lang->load('ch','chinese');
			}
			else{
				$this->lang->load('auth','english');
			}
		}
		
		// $my_id = $this->ion_auth->user()->row()->id; //parent id

	}

	public function index()
	{
		redirect('user/account');
	}
	//deactivate the user
	function deactivate($id = NULL)
	{
		$id = $this->config->item('use_mongodb', 'ion_auth') ? (string) $id : (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE)
		{
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();
			$this->render_page('user/deactivate_user', $this->data);
		}
		else
		{
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
				{
					$this->ion_auth->deactivate($id);
				}
			}

			//redirect them back to the auth page
			redirect('user', 'refresh');
		}
	}

	function activate($id, $val=false)
	{
		$registration_fee_value= lang('registration_fee_value');
		$registration_fee_value= $val;
		// if ($val !== false)	
		// {
		// 	$activation = $this->ion_auth->activate($id, $code);
		// }
		if ($this->ion_auth->is_admin())
		{
			$add_registration_value = $this->ion_auth->add_registration_value($id,$registration_fee_value);
			$activation = $this->ion_auth->activate($id);
		}
		if ($activation && $add_registration_value)
		{
			//redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("user/all_agents", 'refresh');
		}
		else
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}
	function top_up()
	{
		// redirect('user/topup');
		$this->form_validation->set_rules('package', $this->lang->line('create_user_validation_package_label'), 'required|xss_clean');
		$this->data['packages'] = $this->ion_auth->get_packages(); // get packages
		$this->data['package'] = array(
			'name'     => 'package',
			'id'       => 'package',
			'type'     => 'text',
			'readonly' => 'true',
			'title'    =>   'choose a package',
			'value'    => $this->form_validation->set_value('package'),
		);
		$this->render_page('user/top_up', $this->data);
	}
	//create a new user
	function create_user()
	{
		$this->data['title'] = "Create User";

			if (!$this->ion_auth->logged_in())
		{
			redirect('auth', 'refresh');
		}

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('package', $this->lang->line('create_user_validation_package_label'), 'required|xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
		$this->form_validation->set_rules('phone1', $this->lang->line('create_user_validation_phone1_label'), 'required|xss_clean|min_length[3]|max_length[3]');
		$this->form_validation->set_rules('phone2', $this->lang->line('create_user_validation_phone2_label'), 'required|xss_clean|min_length[3]|max_length[3]');
		$this->form_validation->set_rules('phone3', $this->lang->line('create_user_validation_phone3_label'), 'required|xss_clean|min_length[4]|max_length[4]');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true)
		{
			$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
			$email    = $this->input->post('email');
			$assoc_id = $this->ion_auth->user()->row()->id; //parent id
			$level_id = intval($this->ion_auth->user()->row()->level_id); //level id 
			$level_id++;
			$email    = $this->input->post('email');
			$ic    = $this->input->post('ic');
			$password = $this->input->post('password');

			$additional_data = array(
				'assoc_id' 	 => $assoc_id,
				'level_id' 	 => $level_id,
				'package' 	 => $package,
				'first_name' => $this->input->post('first_name'),
				'last_name'  => $this->input->post('last_name'),
				'ic'  	 => $this->input->post('ic'),
				'company'    => $this->input->post('company'),
				'phone'      => $this->input->post('phone1') . '-' . $this->input->post('phone2') . '-' . $this->input->post('phone3'),
			);
		}
		if ($this->form_validation->run() == true && $this->ion_auth->register($username,$password, $email, $additional_data))
		{
			//check to see if we are creating the user
			//redirect them back to the admin page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("/", 'refresh');
		}
		else
		{
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name'  => 'last_name',
				'id'    => 'last_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('last_name'),
			);
	    	// return ;
			// $this->data['packages'] = $this->ion_auth->get_packages(); // get packages
			$this->data['package'] = array(
				'name'     => 'package',
				'id'       => 'package',
				'type'     => 'text',
				'readonly' => 'true',
				'title'    =>   'choose a package',
				'value'    => $this->form_validation->set_value('package'),
			);
			$this->data['email'] = array(
				'name'  => 'email',
				'id'    => 'email',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('email'),
			);
			$this->data['company'] = array(
				'name'  => 'company',
				'id'    => 'company',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('company'),
			);
			$this->data['phone1'] = array(
				'name'  => 'phone1',
				'id'    => 'phone1',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('phone1'),
			);
			$this->data['phone2'] = array(
				'name'  => 'phone2',
				'id'    => 'phone2',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('phone2'),
			);
			$this->data['phone3'] = array(
				'name'  => 'phone3',
				'id'    => 'phone3',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('phone3'),
			);
			$this->data['password'] = array(
				'name'  => 'password',
				'id'    => 'password',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name'  => 'password_confirm',
				'id'    => 'password_confirm',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);

			$this->render_page('auth/create_user', $this->data);
		}
	}

	public function login()
	{
		
		$this->body_class[] = 'login';

		$this->page_title = 'Please sign in';

    $this->current_section = 'login';

		// validate form input
		$this->form_validation->set_rules('identity', 'Email', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{ 
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{ 
				$this->session->set_flashdata('app_success', $this->ion_auth->messages());
				redirect('home');
			}
			else
			{ 
				$this->session->set_flashdata('app_error', $this->ion_auth->errors());
				redirect('login');
			}
		}
		else
		{  
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
				'class' => 'input-block-level',
				'placeholder' => 'Your email'
			);
			$data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'class' => 'input-block-level',
				'placeholder' => 'Your password'
			);

			$this->render_page('user/login', $data);
		}
	}

	public function logout()
	{
		// log the user out
		$logout = $this->ion_auth->logout();
		// redirect them back to the login page
		redirect($this->config->item("base_url"));
	}

	public function forgot_password()
	{
		if ($this->form_validation->run('user_forgot_password'))
		{
			$forgotten = $this->ion_auth->forgotten_password($this->input->post('email', TRUE));

			if ($forgotten)
			{ 
				// if there were no errors
				$this->session->set_flashdata('app_success', $this->ion_auth->messages());
				redirect('login');
			}
			else
			{
				$this->session->set_flashdata('app_error', $this->ion_auth->errors());
				redirect('login');
			}
		}

		$this->body_class[] = 'forgot_password';

		$this->page_title = 'Forgot password';

    $this->current_section = 'forgot_password';

		$this->render_page('user/forgot_password');
	}

	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	 function account()
	{
		
		$this->data['merchants']= $this->get_merchants();
	    $this->data['cats']= $this->get_categories();
			// $this->render_page('user/account', $this->data);
		$this->body_class[] = 'my_account';
		$this->page_title = 'Biggle Account';
	    $this->current_section = 'my_account';
	    // $this->data['user'] = $user;

		$this->render_page('user/account', $this->data);
	}
	
	function payment_claim(){
		$today = date("Y-m-d h:i:s");
		$my_id = $this->ion_auth->user()->row()->id; //parent id
        $this->data['message'] = "";
        $pic_name= 'slip_' . date("Ymdhis");
		if ($this->input->post("claim_payment")){
		  $config['upload_path'] = "./assets/img/";
          $config['allowed_types'] = "gif|jpg|png|jpeg|pdf|doc";
          $config['file_name']= $pic_name;
          $config['max_size'] = "1000000";      	
		  $this->load->library("upload",$config);
		  // $type= pathinfo($_FILES['slip']['type']);
		  $path_parts = pathinfo($_FILES["slip"]["name"]);
		  $extension = $path_parts['extension'];
			$data= array(
						'user_id' => $my_id,
						'amount' => $this->input->post("p_val"),
						'desc' => $this->input->post("p_desc"),
						'payment_date' => $today,
						'src' => $pic_name,
						'src_type' => $extension,
						'date' => $this->input->post("p_date"),
						'approved' => 0,
						 );

			if ($this->upload->do_upload('slip')) {
				$insert_claim = $this->ion_auth->insert_claim($data);
				if ($insert_claim){
	          		$this->data['message'] = 'Successfully submitted the <a href="" style="color:red"> payment receipt </a>, Montlife admin would be evaluating it shortly..';   
				}
				else{
	          		$this->data['message'] = 'Unfortunately something has gove wrong, pelase try again';   
				}
	      	}
	      	else{
	          $this->data['message'] = $this->upload->display_errors();   
	      	}
		}
		$this->data['claims'] = $this->ion_auth->Get_My_Claims(); //parent id
		$this->data['credits']= $this->ion_auth->get_user_credit($my_id);

		$this->render_page('user/payment_claim',$this->data);
	}

	public function test()
	{
		$this->body_class[] = 'test';

		$this->page_title = 'My Account';

    $this->current_section = 'test';
    $user = $this->ion_auth->user()->row_array();


		$this->render_page('user/test', array('user' => $user));
	}
	public function all_agents($id="")
	{
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		elseif (!$this->ion_auth->is_admin())
		{
			//redirect them to the home page because they must be an administrator to view this
			redirect('/', 'refresh');
		}
		else
		{
			if ($id!=""){
				$this->data['agent_datas'] = $this->ion_auth->get_agents_properties($id,'');  
				$this->data['agent_info'] = $this->ion_auth->get_agentinfo($id);  
				if (isset($_POST['filter'])){
					$this->data['chosen_date']= $_POST['date'];
				$this->data['agent_datas'] = $this->ion_auth->get_agents_properties($id,$_POST['date']);  

				}
			}
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			$this->data['groups'] = $this->ion_auth->get_allagents();
			$this->data['inactive_users'] = $this->ion_auth->get_inactive_agents();

			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}

			$this->render_page('user/all_agents', $this->data);
		}
	}
	 public function my_agents($id="")
	{
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		elseif ($this->ion_auth->is_admin())
		{
			//redirect them to the home page because they must be an administrator to view this
			redirect('/', 'refresh');
		}
		else
		{
			if ($id!=""){
				$this->data['user'] = $this->ion_auth->get_patientinfo($id);  
				$this->data['user_old'] = $this->ion_auth->get_patientinfo_old($id);  
				$this->data['username'] = $this->ion_auth->get_patientname($id);  
			}
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			// echo 'yay';
			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();  
			$this->data['sub_agents'] = $this->ion_auth->get_mymembers(); // underlying members
			
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}
			$this->render_page('user/my_agents', $this->data);
		}
	}
	public function my_commission(){
		$my_id = $this->ion_auth->user()->row()->id; //parent id
		$this->data['members_info'] = $this->ion_auth->get_patient_properties($my_id);
		$this->data['members_comms'] = $this->ion_auth->get_agents_commissions($my_id);
		$this->data['members'] = $this->ion_auth->get_mymembers($my_id);
		$this->render_page('user/my_commission',$this->data);
	}
	public function drgeorges(){
		if ($this->ion_auth->is_admin()){ //if admin
			if (isset($_POST['filter'])){
			$date= $_POST['date'];
			$this->data['georges'] = $this->ion_auth->get_drgeorges_properties('dr Georges',$date);
			$this->data['georges_alps'] = $this->ion_auth->get_drgeorges_alps_properties('Dr George Junior',$date); // the name is his accunt name
			$this->data['georges_comm'] = $this->ion_auth->get_drgeorges_paid_comms('dr Georges');
			$this->data['chosen_date'] = $date;
			}
			else{
			$this->data['georges'] = $this->ion_auth->get_drgeorges_properties('dr Georges',''); //no filter
			$this->data['georges_alps'] = $this->ion_auth->get_drgeorges_alps_properties('Dr George Junior',''); // the name is his accunt name
			$this->data['georges_comm'] = $this->ion_auth->get_drgeorges_paid_comms('dr Georges','');
			}
			$this->render_page('user/drgeorges',$this->data);
		}
		else{
			redirect('/', 'refresh');	
		}
		
	}
	public function all_commission(){
		if (!$this->ion_auth->is_admin()){
			redirect('/', 'refresh');	
		}
		else{
			$my_id = $this->ion_auth->user()->row()->id; //parent id
			$this->data['members_info'] = $this->ion_auth->get_all_agents_properties();
			$this->data['members_comms'] = $this->ion_auth->get_all_agents_commissions();
			$this->data['members'] = $this->ion_auth->get_allagents();
			$this->render_page('user/all_commission',$this->data);
		}
	}
	public function pay_commission(){
		if (!$this->ion_auth->is_admin()){
			redirect('/n', 'refresh');	
		}
		else{
			$pay= $this->ion_auth->pay_commission($_POST['id'],$_POST['amount'],$_POST['note'],$_POST['month']);
			if ($pay){
				
			redirect('/', 'refresh');	
			}
			else{
			redirect('/', 'refresh');	
			}
		}
	}
	public function sale(){
		$this->form_validation->set_rules('first_name', $this->lang->line('member_sale_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('member_sale_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('ic_no', $this->lang->line('member_sale_validation_ic_label'), 'required|xss_clean');
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));	
		if ($this->form_validation->run() === TRUE)
		{
			$ic = $this->input->post('ic');
			$first = $this->input->post('first_name');
			$last = $this->input->post('last_name');
			// echo $ic;
			// header('location:index.php?ic=');
			$this->render_page('user/sales',$this->data);
			// redirect("user/sales");

		}
		else{
			$this->data['sale_values'] = $this->ion_auth->get_card_sale_val();
			$this->data['my_credit'] = $this->ion_auth->get_user_credit();
			$this->data['first_name'] = array(
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'placeholder'  => 'First name',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'placeholder'  => 'Last name',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['ic_no'] = array(
				'name'  => 'ic',
				'id'    => 'ic',
				'type'  => 'text',
				'placeholder'  => ' IC Number',
				'value' => $this->form_validation->set_value('ic'),
			);
		}
			$this->render_page('user/sale', $this->data);
	}
	public function contact_us(){
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->render_page('user/contact_us', $this->data);
	}
	public function get_categories(){
		$api_point=lang('api_point_elastic'); //api point
	
	    		$url = $api_point.'categoy/menu-list';

				try{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HTTPGET, 1);
	        // curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'GET');
					curl_setopt($ch, CURLOPT_TIMEOUT, 100);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$categories= curl_exec($ch);
					return $categories;
					curl_close($ch);
				} catch (Exception $ex) {
				}
			}
	public function get_merchants(){
			$api_point=lang('api_point_elastic'); //api point

	    		$url = $api_point.'categoy/mmerchant-list';
	
				try{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HTTPGET, 1);
	        // curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'GET');
					curl_setopt($ch, CURLOPT_TIMEOUT, 100);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$merchants= curl_exec($ch);
					return $merchants;

					curl_close($ch);
				} catch (Exception $ex) {
				}
			}
}