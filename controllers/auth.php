
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		// $this->load->library('user');

		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->library('email');

		// Load MongoDB library instead of native db driver if required
		// $this->config->item('use_mongodb', 'ion_auth') ?
		// $this->load->library('mongo_db') :
		$this->load->database();
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
		$this->load->helper('language');
		
		if (isset($this->session->userdata['lang'])){
			if ($this->session->userdata['lang'] == 'ch'){
				$this->lang->load('ch','chinese');
			}
			else{
				$this->lang->load('auth','english');
			}
		}
		else{
			$this->session->set_userdata(array(
				'lang'       => 'en'));
		}
		
		// $this->config->item('g_comm_adduser');
	}

		/**
	 * Misc functions
	 *User auth in general and auth-needed calls
	 *
	 * @author Sadra Shokouhi
	 */

		function index()
		{
			$url=lang('api_point_elastic'); //api point;
			
			if (!$this->ion_auth->logged_in())
			{
			//redirect them to the login page
			// redirect($this->config->item('base_url').'index.php/auth/login', 'refresh');
				redirect('my_home');
			}
		// elseif (!$this->ion_auth->is_admin())
		// {
		// 	//redirect them to the home page because they must be an administrator to view this
		// 	redirect('/', 'refresh');
		// }
			else
			{
			//set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
		
			//list the users
				$this->data['users'] = $this->ion_auth->users()->result();
				foreach ($this->data['users'] as $k => $user)
				{
					$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
				}
			// echo $this->email->print_debugger();

				$this->_render_page('auth/index', $this->data);
			}
		}

		public function get_promo()
		{
			$url=lang('api_point_elastic'); //api point;

			if (!$this->ion_auth->logged_in()){
				echo 'false';
			}
			else{
				$productId=$this->input->get('productId');
				$merchantId=$this->input->get('merchantId');
				$promoBase=$this->input->get('promoBase');
				$promoType=$this->input->get('promoType');

				$biggle_id= $this->session->userdata['biggle_id'];
				$username= $this->session->userdata['email'];
				$url = $url."product/promo/get-promo";
				$data = array("merchantId" => $merchantId,
					"productId"=> $productId,
					"promoBased" => $promoBase,
					"promoType" => $promoType);
				try{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
		        // curl_setopt($ch, CURLOPT_HTTPGET, 1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 100);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('username:'.$username,'Authorization:'.$biggle_id,'Content-Type:application/json'));                                                                    

					$response= curl_exec($ch);
					$response= json_decode($response,true);
					$response['note']=$this->get_promo_note($merchantId);
					echo json_encode($response);
					curl_close($ch);
				} catch (Exception $ex) {
					echo $ex;
				}
			}
		}
		public function get_promo_note($merchantId)
		{
			$url=lang('api_point_elastic'); //api point;

			$biggle_id= $this->session->userdata['biggle_id'];
			$username= $this->session->userdata['email'];
			$url = $url."/product/promo/promo-note";
			$data = array("merchantId" => $merchantId);
			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
		        // curl_setopt($ch, CURLOPT_HTTPGET, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('username:'.$username,'Authorization:'.$biggle_id,'Content-Type:application/json'));                                                                    

				$response= curl_exec($ch);
				return json_decode($response);
				curl_close($ch);
			} catch (Exception $ex) {
				echo $ex;
			}
		}
		

		function update_promo_status()
		{
			$url=lang('api_point_elastic'); //api point;

			if (!$this->ion_auth->logged_in()){
				echo 'false';
			}
			else{
			$status_txt="no"; // userd,ot,notworking
			$biggle_id= $this->session->userdata['biggle_id'];
			$username= $this->session->userdata['email'];
			$url = $url."product/promo/promo-status";
			$status= 0; // used (yes)
			if($status_txt=="no"){
				$status= 1;
			}
			if($status_txt="not_working"){
				$status=2;
			}

			$data = array("status" => 1); // 
			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
		        // curl_setopt($ch, CURLOPT_HTTPGET, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('username:'.$username,'Authorization:'.$biggle_id,'Content-Type:application/json'));                                                                    
				$response= curl_exec($ch);
				echo $response;
				curl_close($ch);
			} catch (Exception $ex) {
				echo $ex;
			}
		}
		// }
	}

	//log the user in
	function login_web()
	{
		$url=lang('api_point_elastic'); //api point;

		// if ($this->ion_auth->logged_in()){
		// 	redirect('/', 'refresh');
		// }
		// else{
		$name=$this->input->post('name');
		$type="web";
		$email=$this->input->post('email');
		$id=$this->input->post('id');
		$picture=$this->input->post('imageUrl');
		$referralCode=$this->input->post('referralCode');
		// $picture=false;
		// $referralCode='1234';
		$url = $url."user/fb-signin";
		$data = array("name"=>$name, "countryCode" => "MY", "picture"=>$picture, "email"=>$email, "referralCode"=>$referralCode,"signupType"=>$type);
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
	        // curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

			$response= curl_exec($ch);
				// var_dump($response);
			$d=json_decode($response);
    		   $biggle_id= $d->data->idToken; //user biggle id
    		   
    		   $this->session->set_userdata(array("referralCode"=>$d->data->referralCode));
	       		// echo $bigg
				// var_dump($d);

    		   if ($this->ion_auth->login_web($name,$email,$biggle_id, $picture ,true))
    		   {
    		   	echo json_encode($d);
				// echo $this->session->userdata['biggle_id'];
    		   }

    		   curl_close($ch);
    		} catch (Exception $ex) {
    			echo $ex;
    		}
		// }
    	}
		//log the user in
    	function login()
    	{

    		if ($this->ion_auth->logged_in()){
    			redirect('/', 'refresh');
    		}
    		else{
    			if (isset($_GET['id']) && $_GET['id']==lang('token')){
    				$this->data['title'] = "Login";
				//validate form input
    				$this->form_validation->set_rules('identity', 'Identity', 'required');
    				$this->form_validation->set_rules('password', 'Password', 'required');
    				if ($this->form_validation->run() == true)
    				{
					//check to see if the user is logging in
					//check for "remember me"
    					$remember = (bool) $this->input->post('remember');
    					if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
    					{
						//if the login is successful
						// $this->session->set_userdata(array(
      //                       'lang'       => 'en'));
						//redirect them back to the home page
    						$this->session->set_flashdata('message', $this->ion_auth->messages());
    						redirect('/', 'refresh');
    					}
    					else
    					{
						//if the login was un-successful
						//redirect them back to the login page
    						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/login?id='.lang('token'), 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
					}
				}
				else
				{
					//the user is not logging in so display the login page
					//set the flash data error message if there is one
					$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

					$this->data['identity'] = array('name' => 'identity',
						'id' => 'identity',
						'class' => 'form-control',
						'type' => 'text',
						'value' => $this->form_validation->set_value('identity'),
					);
					$this->data['password'] = array('name' => 'password',
						'id' => 'password',
						'class' => 'form-control',
						'type' => 'password',
					);

					$this->_render_page('auth/login', $this->data);
				}
			}//if right token
			else{
				redirect('my_home');
			}
		} //else
	}

	function like_it()
	{
		$url=lang('api_point_elastic'); //api point;

		if (!$this->ion_auth->logged_in())
		{
			echo 'false';
		}
		else{
			$productId=$this->input->get('id');
			$biggle_id= $this->session->userdata['biggle_id'];
			$url =$url."user/addProduct";
				// $query= 'select * from table as a where a.name like '%abc%' and (a.merchant_id=1 or a.merchant_id=2)';
			$data = array("productId"=>$productId);

			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('userKey: '.$biggle_id));
			        // curl_setopt($ch, CURLOPT_HTTPGET, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response= curl_exec($ch);
				echo $response;
				curl_close($ch);
			} catch (Exception $ex) {
				$response="false";
			}
		}

		 // $url = "http://35.186.151.116/biggle-controller/mobile/v1/product?productId=".$id;
		 //$url='http://13.228.222.55/biggle-controller/mobile/v1/product?productId='.$id;



	}

	function dislike_it()
	{

		$url=lang('api_point_elastic'); //api point;

		if (!$this->ion_auth->logged_in())
		{
			echo 'false';
		}
		else{
			$productId=$this->input->get('id');

			$biggle_id= $this->session->userdata['biggle_id'];

			$url = $url."user/removeProduct";
				// $query= 'select * from table as a where a.name like '%abc%' and (a.merchant_id=1 or a.merchant_id=2)';
			$data = array("productId"=>$productId);

			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('userKey: '.$biggle_id));
			        // curl_setopt($ch, CURLOPT_HTTPGET, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response= curl_exec($ch);
				echo $response;
				curl_close($ch);
			} catch (Exception $ex) {
				$response="false";
			}
			// echo 'true';
		}

		 // $url = "http://35.186.151.116/biggle-controller/mobile/v1/product?productId=".$id;
		 //$url='http://13.228.222.55/biggle-controller/mobile/v1/product?productId='.$id;



	}
	function load_more(){
		$url=lang('api_point_elastic'); //api point;
		

		if ($this->ion_auth->logged_in())
		{
			// redirect('my_home');
			$this->data['logged_in']="";
		}
		$this->data['merchants']= $this->get_merchants();
		$this->data['cats']= $this->get_categories();

		$query = $this->input->post('query');
		$page=0;
		$pager=0;
		$size=80;
		if ($this->input->post('page')){  //if not 1st page
			$page=$this->input->post('page');
			//get page out of url
			$parsed = parse_url($query);
			$query2 = $parsed['query'];
			parse_str($query2, $params);
			unset($params['page']);
			$string = http_build_query($params);
			$this->data['pagination_link'] = '?'.$string; // display the logo menu
			// $page
			// $page=$page*50;
			$pager=$page*80;
		}
		
		$this->data['link'] = $query.'?'; // display the logo menu
		$this->data['page'] = $page; // page no
		$this->data['menu_display'] = 1; // display the logo menu
		$this->body_class[] = 'home';
		$this->page_title = 'Compare & Get Discount Codes Malaysia | Online Shopping';
		$this->data['page_status'] = 0; // for index displays
		if($this->uri->segment(1)){ //category search

			$this->data['pagination_link'] = $this->uri->segment(1).'?';
			// $this->data['pagination_link'] = $_SERVER['REQUEST_URI']; // display the logo menu

			$search_key= explode("&",strtolower($this->uri->segment(1))); // categories clicked
			$items= explode("-",strtolower($this->uri->segment(1))); // categories clicked
			$this->data['page_status'] = $items; // for index displays
			$this->page_title = $this->uri->segment(1).' | Compare and get free Discount Malaysia';
			$this->data['search_key']= str_replace("-", " ",$search_key[0]);
			$this->data['page_status'] = 1; // for index displays
			$this->data['menu_display'] = 0; // display the logo menu
			$this->data['search_category'] = str_replace("-", " ", $this->uri->segment(1)); // for index displays
			// $search_key = str_replace(" ", "-", $items[0]); 

			$must=[];
			if ($this->input->post('priceFrom')){
				$priceFrom=$this->input->post('priceFrom');
	    			$this->data['priceFrom']= $priceFrom; //send to frontend
	    		}
	    		if ($this->input->post('priceTo')){
	    			$priceTo=$this->input->post('priceTo');
	    			$this->data['priceTo']= $priceTo; //send to frontend
	    			$must=["range" => array(
	    				"biggleDiscount" => array(
	    					"gte" =>$priceFrom,
	    					"lt" => $priceTo
	    				)
	    			)];
	    		}


	    		$myArr= array( "from" => $pager,
	    			"size" => $size, 
 								   // "sort" => [array("price" =>["order" => "asc"])],
 								   		// "range"=> array("gte"=>1,"lte"=>11),
	    			"query"=> array(
	    				"bool"=> array(
	    					"should"=> array( 

									      		["match"=> ['name'=>$this->uri->segment(1)]], // all typed
									      		["terms"=> array(
									      			"merchantCategoryB"=> $items,
									      			"boost"=> 15.0 
									      		)],
									      		["terms"=> array(
									      			"merchantCategoryA"=> $items,
									      			"boost"=> 14.0 
									      		)],
									      		["terms"=> array(
									      			"merchantCategory"=> $items,
									      			"boost"=> 13.0 
									      		)],

									      		["terms"=> array(
									      			"brand"=> $items,
									      			"boost"=> 20.0 
									      		)],
									      		["terms"=> array(
									      			"name"=> $items,
									      			"boost"=> 25.0 
									      		)]
									      	),

										     // "must_not" =>

								       //         ["match"=>array(  
								       //            "merchantId"=>"BGL_MY_LAZADA"
								       //         )],

	    					"minimum_should_match" => 1, "boost" => 1.0,
	    					"must" => $must
	    				)
	    			)
	    		);
	    		try{
	    			$ch = curl_init();
	    			curl_setopt($ch, CURLOPT_URL, $url);
	    			curl_setopt($ch, CURLOPT_TIMEOUT, 100);

	    			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($myArr));
	        	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 'false');   
	    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    			curl_setopt($ch, CURLOPT_HTTPHEADER, array(       
	    				'X-API-KEY:key',                                                                   
	    				'Content-Type: application/json')                                                                       
	    		);           
	    			$response= curl_exec($ch);
	    			if(curl_error($ch))
	    			{
	    				$this->data['results']= curl_error($ch);
	    			}
	    			else{

	    				$this->data['results']= $response;
	    			}

				// $this->data['results2']= $merchant_filters;
				// $this->data['results3']= $myArr;
	    			curl_close($ch);
	    		} catch (Exception $ex) {
	    		}
	    	}

	    	$this->current_section = 'home';
	    	$this->data['sort'] = array();
	    	$this->data['referral'] = "";
		// when searching
	    	if ($this->input->post('referral') ){
	    		$this->data['referral'] = $this->input->post('referral');
	    	}

			if ($this->input->post('search') ){  //when searching

				$this->page_title = 'Compare and Get Free Promotion malaysia';

				$this->data['menu_display'] = 0; // display the logo menu
				$this->data['page_status'] = 1; // for index displays
				$this->data['search_key']= $this->input->post('search');
	   		 	$search_key = explode(" ", strtolower($this->input->post('search'))); //split the space into array each
	   		 	$aQuery = explode("&", $query);
	   		 	$aQueryOutput = array();

	   		 	$merchant_filters=[];
				// $merchant_filters->;
				$merchant_filter=""; //for web page auto checkbox of merchants
				$vars=array();
				$priceFrom="";
				$priceTo="";
				$all_merchants= json_decode($this->data['merchants'],true);

			    $this->data['sort_all'] = 'true'; // all shops only
			    $this->data['aQuery'] = $aQuery; // all shops only
			    if (!$this->input->post('sort_all')){
			     	$this->data['sort_all'] = 'false'; // all shops only
			     	foreach ($aQuery as $key=>$param) {
			     		if(!empty($param)){
			     			$aTemp = explode('=', $param, 2);
			     			if(isset($aTemp[1]) && $aTemp[1] != ""){
			     				list($name, $value) = explode('=', $param, 2);
			     				$aQueryOutput[ strtolower(urldecode($name)) ][] = $value;
			     				if ($merchant_filter==""){
			     					$merchant_filter=$value;
			     				}
			     				else{
			     					$merchant_filter.=','.$value;
					            	// array_push($vars, $value);
			     				}
			     				if ($name=="sort_merchant"){
			     					array_push($vars, $value);

			     				}

			     			}
			     		}
			     	}

			     }
			     foreach($all_merchants['data'] as $key=>$val){ 
			    	// if($vars[$key]){}
			     	if (!in_array($val['merchantId'],$vars)){
			     		$filter = new stdClass;
			     		$filter->match->merchantId=$val['merchantId'];
			     		$merchant_filters[]= $filter;
		            	// array_push($vars, $val);
			     	}
			     }
			     $must=[];
			     if ($this->input->post('priceFrom')!=0){
			     	$priceFrom=$this->input->post('priceFrom');
	    			$this->data['priceFrom']= $priceFrom; //send to frontend
	    		}
	    		if ($this->input->post('priceTo')!=0){
	    			$priceTo=$this->input->post('priceTo');
	    			$this->data['priceTo']= $priceTo; //send to frontend
	    			$must=["range" => array(
	    				"biggleDiscount" => array(
	    					"gte" =>$priceFrom,
	    					"lt" => $priceTo
	    				)
	    			)];
	    		}
			    $this->data['sort'] = $vars; // all merchants checked
 								   	// "sort"=> array("gte"=>1,"lte"=1000),

			    $myArr= array( "from" => $page,
			    	"size" => $size, 

			    	"query"=> array(
			    		"bool"=> array(

			    			"should"=> array( 
									      		// ["match"=> ["name"=>$this->data['search_key']]], // all typed
			    				["terms"=> array(
			    					"merchantCategoryB"=> $search_key,
			    					"boost"=> 15.0 
			    				)],
			    				["terms"=> array(
			    					"merchantCategoryA"=> $search_key,
			    					"boost"=> 14.0 
			    				)],
			    				["terms"=> array(
			    					"merchantCategory"=> $search_key,
			    					"boost"=> 13.0 
			    				)],

			    				["terms"=> array(
			    					"brand"=> $search_key,
			    					"boost"=> 20.0 
			    				)],
			    				["terms"=> array(
			    					"name"=> $search_key,
			    					"boost"=> 25.0 
			    				)]
			    			),
			    			"minimum_should_match" => 1, "boost" => 1.0,
			    			"must" => $must,	

			    		)

			    	)
			    );
					if (!empty($vars)){ //add merhcnat folter to query
						$myArr['query']['bool']['must_not']=$merchant_filters;
					}

					try{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_TIMEOUT, 100);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($myArr));
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 'false');   
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
							'X-API-KEY:key',                                                                   
							'Content-Type: application/json','Accept-Language: en-us')                                                                       
					);           
						$response= curl_exec($ch);
						if(curl_error($ch))
						{
							$this->data['results']= curl_error($ch);
						}
						else{

							$this->data['results']= $response;
						}

				// $this->data['results2']= $merchant_filters;
				// $this->data['results3']= $myArr;
						curl_close($ch);
					} catch (Exception $ex) {
					}

				}
			if(!$search_key){ // homepage data
				$input= array('shirt','pant','tshirt','jeans','sunglasses','wedding','bag','tudung','skirt');
				// $input= array('bag','tudung');
				$array = array_rand($input, 3); //randomize
				$array = array($input[$array[0]],$input[$array[1]],$input[$array[2]]);
				

				$myArr= array( "from" => 0,
					"size" => 120, 

					"query"=> array(
						"bool"=> array(

							"should"=> array( 
									      		// ["match"=> ["name"=>$this->data['search_key']]], // all typed
								["terms"=> array(
									"merchantCategoryB"=> $array,
									"boost"=> 15.0 
								)],
								["terms"=> array(
									"merchantCategoryA"=> $array,
									"boost"=> 14.0 
								)],
								["terms"=> array(
									"merchantCategory"=> $array,
									"boost"=> 13.0 
								)],

								["terms"=> array(
									"brand"=> $array,
									"boost"=> 20.0 
								)],
								["terms"=> array(
									"name"=> $array,
									"boost"=> 25.0 
								)]
							),
							"minimum_should_match" => 1, "boost" => 1.0,
										    	    // "must" => $must,	

						)

					)
				);


			     // try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($myArr));
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 'false');   
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
					'X-API-KEY:key',                                                                   
					'Content-Type: application/json','Accept-Language: en-us')                                                                       
			);           
				$response= curl_exec($ch);
				if(curl_error($ch))
				{
					$this->data['results_home']= curl_error($ch);
				}
				else{

					$this->data['results_home']= $response;
				}

			// }
			}
			
			echo $this->data['results'];

		}

		function index_tabs(){
			$no= $_POST['no'];
		// $array= array('shirt','shoe','skirt','phone','jeans','shorts');
			$input= array('shirt','pant','tshirt','jeans','sunglasses','wedding','bag','tudung','skirt');
				// $input= array('bag','tudung');
				$array = array_rand($input, 3); //randomize
				$array = array($input[$array[0]],$input[$array[1]],$input[$array[2]]);
				$url= $url.'product/search';

 								   	// "sort"=> array("gte"=>1,"lte"=1000),
 								   	// "sort" : [{"price" : {"order" : "asc"}}]
 								   		// "range"=> array("gte"=>1,"lte"=1000),
				$myArr= array( "from" => $no,
					"size" => 32, 
 								   // "sort" => [array("price" =>["order" => "asc"])],
 								   		// "range"=> array("gte"=>1,"lte"=>11),
					"query"=> array(
						"bool"=> array(
							"should"=> array( 

									      		// ["match"=> ['name'=>$this->uri->segment(1)]], // all typed
								["terms"=> array(
									"merchantCategoryB"=> $array,
									"boost"=> 15.0 
								)],
								["terms"=> array(
									"merchantCategoryA"=> $array,
									"boost"=> 14.0 
								)],
								["terms"=> array(
									"merchantCategory"=> $array,
									"boost"=> 13.0 
								)],

								["terms"=> array(
									"brand"=> $array,
									"boost"=> 20.0 
								)],
								["terms"=> array(
									"name"=> $array,
									"boost"=> 25.0 
								)]
							),

										     // "must_not" =>

								       //         ["match"=>array(  
								       //            "merchantId"=>"BGL_MY_LAZADA"
								       //         )],

							"minimum_should_match" => 1, "boost" => 1.0
										    // "must" => $must
						)
					)
				);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);

				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($myArr));
	        	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 'false');   
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(       
					'X-API-KEY:key',                                                                   
					'Content-Type: application/json'));

				$response= curl_exec($ch);
				if(curl_error($ch))
				{
					$this->data['results']= curl_error($ch);
				}
				else{
					echo $response;

				}

			}

	//log the user out
			function logout()
			{
				$this->data['title'] = "Logout";
		//log the user out
				$logout = $this->ion_auth->logout();
				$this->session->set_userdata(array('force_logout'=> 'true'));

		//redirect them to the login page
				$this->session->set_flashdata('message', $this->ion_auth->messages());

		// redirect($this->config->item('base_url').'index.php/auth/login', 'refresh');
			}

		//log the user out
			function logout_web()
			{
				$this->data['title'] = "Logout";
		//log the user out
				$logout = $this->ion_auth->logout();
				$this->session->set_userdata(array('force_logout'=> 'true'));

		//redirect them to the login page
				$this->session->set_flashdata('message', $this->ion_auth->messages());

		// redirect($this->config->item('base_url').'index.php/auth/login', 'refresh');
			}


	//change password
			function change_password()
			{


				$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
				$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
				$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

				if (!$this->ion_auth->logged_in())
				{
					redirect('auth/login', 'refresh');
				}

				$user = $this->ion_auth->user()->row();

				if ($this->form_validation->run() == false)
				{
			//display the form
			//set the flash data error message if there is one
					$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

					$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
					$this->data['old_password'] = array(
						'name' => 'old',
						'id'   => 'old',
						'type' => 'password',
					);
					$this->data['new_password'] = array(
						'name' => 'new',
						'id'   => 'new',
						'type' => 'password',
						'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
					);
					$this->data['new_password_confirm'] = array(
						'name' => 'new_confirm',
						'id'   => 'new_confirm',
						'type' => 'password',
						'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
					);
					$this->data['user_id'] = array(
						'name'  => 'user_id',
						'id'    => 'user_id',
						'type'  => 'hidden',
						'value' => $user->id,
					);

			//render
					$this->_render_page('auth/change_password', $this->data);
				}
				else
				{
					$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

					$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

					if ($change)
					{
				//if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						$this->logout();
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/change_password', 'refresh');
					}
				}
			}

	//forgot password
			function my_home(){
				$this->data['message']="";
				$this->form_validation->set_rules('identity', 'Identity', 'required');
				$this->form_validation->set_rules('password', 'Password', 'required');
				if ($this->form_validation->run() == true)
				{
			//check to see if the user is logging in
			//check for "remember me"
					$remember = (bool) $this->input->post('remember');
					if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
					{
				//if the login is successful
						$this->session->set_flashdata('message', $this->ion_auth->messages());
				// redirect('/', 'refresh');
						redirect('user');
					}
					else
					{
				//if the login was un-successful
				//redirect them back to the login page
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('my_home','refresh');
				// redirect('auth/login?id='.lang('token'), 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
					}
				}
				else
				{
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
					$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

					$this->data['identity'] = array('name' => 'identity',
						'id' => 'identity',
						'class' => 'form-control',
						'type' => 'text',
						'value' => $this->form_validation->set_value('identity'),
					);
					$this->data['password'] = array('name' => 'password',
						'id' => 'password',
						'class' => 'form-control',
						'type' => 'password',
					);
			// $this->_render_page('auth/login', $this->data);
					$this->_render_page('home',$this->data);
				}
			}
			function forgot_password()
			{
				$this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
				if ($this->form_validation->run() == false)
				{
			//setup the input
					$this->data['email'] = array('name' => 'email',
						'id' => 'email',
					);

					if ( $this->config->item('identity', 'ion_auth') == 'username' ){
						$this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
					}
					else
					{
						$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
					}

			//set any errors and display the form
					$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
					$this->_render_page('auth/forgot_password', $this->data);
				}
				else
				{
			// get identity for that email
					$config_tables = $this->config->item('tables', 'ion_auth');
					$identity = $this->db->where('email', $this->input->post('email'))->limit('1')->get($config_tables['users'])->row();

			//run the forgotten password method to email an activation code to the user
					$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

					if ($forgotten)
					{
				//if there were no errors
						$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			//if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				//display the form

				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id'   => 'new',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
				$this->data['new_password_confirm'] = array(
					'name' => 'new_confirm',
					'id'   => 'new_confirm',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
				$this->data['user_id'] = array(
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
				);
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				//render
				$this->_render_page('auth/reset_password', $this->data);
			}
			else
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{

					//something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						//if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						$this->logout();
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			//if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}


	//activate the user
	function activate($id, $code=false)
	{
		// $registration_fee= lang('registration_fee');
		$registration_fee_value= lang('registration_fee_value');

		if ($code !== false)
		{
			$activation = $this->ion_auth->activate($id, $code);
			$add_registration_value = $this->ion_auth->add_registration_value($id,$registration_fee_value);
			
		}
		else if ($this->ion_auth->is_admin())
		{
			$activation = $this->ion_auth->activate($id);
			$add_registration_value = $this->ion_auth->add_registration_value($id,$registration_fee_value);
		}

		if ($activation)
		{
			//redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		}
		else
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
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

			$this->_render_page('auth/deactivate_user', $this->data);
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
			redirect('auth', 'refresh');
		}
	}

	//create a new user
	function create_user()
	{
		$this->data['title'] = "Create User";
		// $ip_info="";
                    // }

		// if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		// {
		// 	redirect('auth', 'refresh');
		// }
		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
		$this->form_validation->set_rules('bankname', $this->lang->line('create_user_validation_bankname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('bankacc', $this->lang->line('create_user_validation_bankacc_label'), 'required|xss_clean');
		$this->form_validation->set_rules('phone1', $this->lang->line('create_user_validation_phone1_label'), 'required|xss_clean|min_length[3]');
		// $this->form_validation->set_rules('phone2', $this->lang->line('create_user_validation_phone2_label'), 'required|xss_clean|min_length[3]|max_length[3]');
		$this->form_validation->set_rules('ipinfo', $this->lang->line('create_user_validation_ipinfo_label'));
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'));
		$this->form_validation->set_rules('company_reg', $this->lang->line('create_user_validation_company_reg_label'));
		$this->form_validation->set_rules('introducer', $this->lang->line('create_user_validation_introducer_label'), 'required|integer');
		$this->form_validation->set_rules('dob', $this->lang->line('create_user_validation_dob_label'), 'required|xss_clean');
		$this->form_validation->set_rules('address', $this->lang->line('create_user_validation_address_label'), 'required|xss_clean');
		$this->form_validation->set_rules('ic', $this->lang->line('create_user_validation_ic_label'), 'required|xss_clean');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true)
		{
			$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
			$email    = $this->input->post('email');
			$password = $this->input->post('password');
			$ic = $this->input->post('ic');
			$introducer = $this->input->post('introducer');
			// $ipinfo = $this->input->post('ipinfo');
			$additional_data = array(
				'bank_account' => $this->input->post('bankacc'),
				'bank_name' => $this->input->post('bankname'),
				'ic' => $this->input->post('ic'),
				'first_name' => $this->input->post('first_name'),
				'last_name'  => $this->input->post('last_name'),
				'ip_address'  => $this->input->post('ipinfo'),
				'company'    => $this->input->post('company'),
				'company_reg_no'    => $this->input->post('company_reg'),
				'company_add'    => $this->input->post('address'),
				'company_phone'    => $this->input->post('phone'),
				'gender'    => $this->input->post('gender'),
				'dob'    => $this->input->post('dob'),
				'phone'      => $this->input->post('phone1'),
			);
		}
		if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $introducer, $additional_data,$this->input->post('bankname'), $this->input->post('bankacc')))
		{
			//check to see if we are creating the user
			//redirect them back to the admin page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth/create_user");
		}
		else
		{
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['first_name'] = array(
				'name'  => 'first_name',
				'id'    => 'first_name',
				'class'    => 'form-control',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['ipinfo'] = array(
				'name'  => 'ipinfo',
				'id'    => 'ipinfo',
				'class'    => 'form-control',
				'type'  => 'hidden',
				'value' => $this->form_validation->set_value('ipinfo'),
			);
			$this->data['last_name'] = array(
				'name'  => 'last_name',
				'class'    => 'form-control',
				'id'    => 'last_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['email'] = array(
				'name'  => 'email',
				'class'    => 'form-control',
				'id'    => 'email',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('email'),
			);
			$this->data['bankname'] = array(
				'name'  => 'bankname',
				'class'    => 'form-control',
				'id'    => 'bankname',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('bankname'),
			);
			$this->data['dob'] = array(
				'name'  => 'dob',
				'class'    => 'form-control',
				'id'    => 'db',
				'type'  => 'date',
				'value' => $this->form_validation->set_value('dob'),
			);
			$this->data['bankacc'] = array(
				'name'  => 'bankacc',
				'class'    => 'form-control',
				'id'    => 'bankacc',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('bankacc'),
			);
			$this->data['address'] = array(
				'name'  => 'address',
				'class'    => 'form-control',
				'id'    => 'address',
				'style' => 'height: 50px;',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('address'),
			);
			$this->data['company'] = array(
				'name'  => 'company',
				'class'    => 'form-control',
				'id'    => 'company',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('company'),
			);
			$this->data['company_reg'] = array(
				'name'  => 'company_reg',
				'class'    => 'form-control',
				'id'    => 'company',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('company_reg'),
			); 
			$this->data['introducer'] = array(
				'name'  => 'introducer',
				'class'    => 'form-control',
				'id'    => 'introducer',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('introducer'),
			);
			$this->data['ic'] = array(
				'name'  => 'ic',
				'class'    => 'form-control',
				'id'    => 'ic',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('ic'),
			);
			$this->data['phone1'] = array(
				'name'  => 'phone1',
				'class'    => 'form-control',
				'id'    => 'phone1',
				'type'  => 'text',
				'placeholder' => 'e.g. +60 31234567',
				'value' => $this->form_validation->set_value('phone1'),
			);
			$this->data['hand_phone'] = array(
				'name'  => 'hand_phone',
				'class'    => 'form-control',
				'id'    => 'hand_phone',
				'type'  => 'text',
				'placeholder' => 'e.g. +60 123456789',
				'value' => $this->form_validation->set_value('hand_phone'),
			);
			
			$this->data['password'] = array(
				'name'  => 'password',
				'id'    => 'password',
				'class'    => 'form-control',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name'  => 'password_confirm',
				'class'    => 'form-control',
				'id'    => 'password_confirm',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);
			
			$this->_render_page('auth/create_user', $this->data);
		}
	}

	//edit a user
	function edit_user($id)
	{
		$this->data['title'] = "Edit User";

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		$groups=$this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();

		//process the phone number
		if (isset($user->phone) && !empty($user->phone))
		{
			$user->phone = explode('-', $user->phone);
		}

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('phone1', $this->lang->line('edit_user_validation_phone1_label'), 'required|xss_clean|min_length[3]|max_length[3]');
		$this->form_validation->set_rules('phone2', $this->lang->line('edit_user_validation_phone2_label'), 'required|xss_clean|min_length[3]|max_length[3]');
		$this->form_validation->set_rules('phone3', $this->lang->line('edit_user_validation_phone3_label'), 'required|xss_clean|min_length[4]|max_length[4]');
		$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required|xss_clean');
		$this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			{
				show_error($this->lang->line('error_csrf'));
			}

			$data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name'  => $this->input->post('last_name'),
				'company'    => $this->input->post('company'),
				'phone'      => $this->input->post('phone1') . '-' . $this->input->post('phone2') . '-' . $this->input->post('phone3'),
			);

			//Update the groups user belongs to
			$groupData = $this->input->post('groups');

			if (isset($groupData) && !empty($groupData)) {

				$this->ion_auth->remove_from_group('', $id);

				foreach ($groupData as $grp) {
					$this->ion_auth->add_to_group($grp, $id);
				}

			}

			//update the password if it was posted
			if ($this->input->post('password'))
			{
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');

				$data['password'] = $this->input->post('password');
			}

			if ($this->form_validation->run() === TRUE)
			{
				$this->ion_auth->update($user->id, $data);

				//check to see if we are creating the user
				//redirect them back to the admin page
				$this->session->set_flashdata('message', "User Saved");
				redirect("auth", 'refresh');
			}
		}

		//display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;

		$this->data['first_name'] = array(
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
		);
		$this->data['last_name'] = array(
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
		);
		$this->data['company'] = array(
			'name'  => 'company',
			'id'    => 'company',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('company', $user->company),
		);
		$this->data['phone1'] = array(
			'name'  => 'phone1',
			'id'    => 'phone1',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone1', $user->phone[0]),
		);
		$this->data['phone2'] = array(
			'name'  => 'phone2',
			'id'    => 'phone2',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone2', $user->phone[1]),
		);
		$this->data['phone3'] = array(
			'name'  => 'phone3',
			'id'    => 'phone3',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone3', $user->phone[2]),
		);
		$this->data['password'] = array(
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password'
		);
		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'type' => 'password'
		);

		$this->_render_page('auth/edit_user', $this->data);
	}

	// create a new group
	function create_group()
	{
		$this->data['title'] = $this->lang->line('create_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			// redirect('auth', 'refresh');
		}

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

		if ($this->form_validation->run() == TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if($new_group_id)
			{
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth", 'refresh');
			}
		}
		else
		{
			//display the create group form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = array(
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('group_name'),
			);
			$this->data['description'] = array(
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('description'),
			);

			$this->_render_page('auth/create_group', $this->data);
		}
	}

	//edit a group
	function edit_group($id)
	{
		// bail if no group id given
		if(!$id || empty($id))
		{
			redirect('auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if($group_update)
				{
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				}
				else
				{
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("auth", 'refresh');
			}
		}

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['group'] = $group;

		$this->data['group_name'] = array(
			'name'  => 'group_name',
			'id'    => 'group_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_name', $group->name),
		);
		$this->data['group_description'] = array(
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		);
		$this->_render_page('auth/edit_group', $this->data);
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

	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function _render_page($view, $data=null, $render=false)
	{

		$this->viewdata = (empty($data)) ? $this->data: $data;

		$view_html = $this->load->view($view, $this->viewdata, $render);

		if (!$render) return $view_html;
	}
	function get_merchants(){ // get merchants list
		$url=lang('api_point_elastic'); //api point;

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
			return 0;
		}
	}
	 public function get_categories(){ // ge categories list
		$url=lang('api_point_elastic'); //api point;

		// $get_merchants = "https://h4ett688p6.execute-api.ap-southeast-1.amazonaws.com/dev/api/v1/merchant";
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
	 		return 0;
	 	}
	 }

	}
