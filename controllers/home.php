<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('user_agent');
		$this->load->library('form_validation');
		$this->load->library('curl');
		$this->load->helper('url','form');
		$this->load->helper('download');
		$this->load->library('pagination');
		$this->load->library('unit_test');

		// Load MongoDB library instead of native db driver if required
		// $this->config->item('use_mongodb', 'ion_auth') ?
		// $this->load->library('mongo_db') :
		// $this->load->model('user_model');
		$this->load->database();
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
		$this->load->helper('file');

		$this->load->helper('language');
		$lang= isset($this->session->userdata['lang']) ? $this->session->userdata['lang']: "";
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


	public function index($loader_no=false)
	{
		$api_point_elastic=lang('api_point_elastic'); //api point
		
		if ($this->ion_auth->logged_in())
		{
			// redirect('my_home');
			$this->data['logged_in']=""; // user logged in
		}
		$this->data['merchants']= $this->get_merchants();
		$this->data['cats']= $this->get_categories();

		$query = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$page=0;
		$pager=0;
		$size=80;
		if ($this->input->get('page')){  //if not d 1st page
			$page=$this->input->get('page');
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
			if ($this->input->get('priceFrom')){
				$priceFrom=$this->input->get('priceFrom');
	    			$this->data['priceFrom']= $priceFrom; //send to frontend
	    		}
	    		if ($this->input->get('priceTo')){
	    			$priceTo=$this->input->get('priceTo');
	    			$this->data['priceTo']= $priceTo; //send to frontend
	    			$must=["range" => array(
	    				"biggleDiscount" => array(
	    					"gte" =>$priceFrom,
	    					"lt" => $priceTo
	    				)
	    			)];
	    		}
			    	// elastic search object for slct query
	    		$myArr= array( "from" => $pager,
	    			"size" => $size, 

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

	    					"must_not" =>

	    					["match"=>array(  
	    						"merchantId"=>"BGL_MY_LAZADA"
	    					)],

	    					"minimum_should_match" => 1, "boost" => 1.0,
	    					"must" => $must
	    				)
	    			)
	    		);
	    		$url = $api_point_elastic.'product/search';
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
	    	if ($this->input->get('referral') ){
	    		$this->data['referral'] = $this->input->get('referral');
	    	}

			if ($this->input->get('search') ){  //when searching

				$this->page_title = 'Compare and Get Free Promotion malaysia';

				$this->data['menu_display'] = 0; // display the logo menu
				$this->data['page_status'] = 1; // for index displays
				$this->data['search_key']= $this->input->get('search');
	   		 	$search_key = explode(" ", strtolower($this->input->get('search'))); //split the space into array each
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
			    if (!$this->input->get('sort_all')){
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
	       			// remove lazada manually
			     $filter = new stdClass;
			     $filter->match->merchantId='BGL_MY_LAZADA';
			     $merchant_filters[]= $filter; 

			     $must=[];
			     if ($this->input->get('priceFrom')){
			     	$priceFrom=$this->input->get('priceFrom');
	    			$this->data['priceFrom']= $priceFrom; //send to frontend
	    		}
	    		if ($this->input->get('priceTo')){
	    			$priceTo=$this->input->get('priceTo');
	    			$this->data['priceTo']= $priceTo; //send to frontend
	    			$must=["range" => array(
	    				"biggleDiscount" => array(
	    					"gte" =>$priceFrom,
	    					"lt" => $priceTo
	    				)
	    			)];
	    		}
			    $this->data['sort'] = $vars; // all merchants checked

			    $myArr= array( "from" => $pager,
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
					else{
						$myArr['query']['bool']['must_not']=["match"=>array(  
							"merchantId"=>"BGL_MY_LAZADA"
						)];
					}

	    		$url = $api_point_elastic.'product/search';

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
						curl_close($ch);
					} catch (Exception $ex) {
					}

				}
			if(!$search_key){ // homepage data
				$input= array('shirt','pants','tshirt','jeans','sunglasses','wedding','bag','tudung','skirt');
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
							"must_not" =>

							["match"=>array(  
								"merchantId"=>"BGL_MY_LAZADA"
							)],

						)

					)
				);
	    		$url = $api_point_elastic.'product/search';


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
			


			$this->render_page('home/index', $this->data);

		}

		public function product($id="",$merchant="",$title="") // product details page
		{
			if ($this->ion_auth->logged_in())
			{
			// redirect('my_home');
				$this->data['logged_in']='true';
			}
			else
			{
				$this->data['logged_in']='false';
			}
		$this->data['menu_display'] = 0; // display the logo menu

		$this->body_class[] = 'home';
		$this->page_title = $merchant.' | '.$title.' | Compare products & discount codes in Malaysia';
		// $id= $this->ion_auth->user()->row()->id;
		if ($id!=""){
			$this->data['id']= $id;
			$this->data['title']= $title;
			$this->data['merchant']= $merchant;
		}
		else{
			// redirect('goog');
		}
		$this->current_section = 'home';
		$this->data['sort'] = array();
		$this->data['page_status'] = 0; // for index displays

		$this->data['referral'] = "";

		$this->data['url'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		
	   $url = $api_point_elastic.'product/search';

		$myArr= array( 
			"query"=> array(
 								   		// "range"=> array("gte"=>1,"lte"=>110),
				"ids"=> [
					"type"=> 'productsData',
					"values"=> array($id)
										        
				]
										 
			)

		);
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);

			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($myArr));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 'false');   
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(             
				'X-API-KEY:iJSoNLeumMa5DeSmJWmaP2hFiVd9leh92hcFQIug',                                                             
				'Content-Type: application/json')                                                                       
		);           
			$response= curl_exec($ch);
			if(curl_error($ch))
			{
				$this->data['results']= curl_error($ch);
			}
			curl_close($ch);
		} catch (Exception $ex) {
		}
		
		$this->data['results']= $response;
		$this->data['merchants']= $this->get_merchants();
		$this->data['cats']= $this->get_categories();
		$this->render_page('home/product', $this->data);

	}


	public function account()
	{
		$this->body_class[] = 'Account';
		$this->page_title = 'Biggle Account';
		// $id= $this->ion_auth->user()->row()->id;
		$this->data['page_status'] = 0; // for index displays
		
			// redirect('goog');
		$this->current_section = 'home';
		$this->data['sort'] = array();
		$this->data['menu_display']=0;


		
		$this->data['merchants']= $this->get_merchants();
		$this->data['cats']= $this->get_categories();

	    // $this->data['me']='test';
		$this->render_page('home/account', $this->data);

	}

	public function shops()
	{
		$this->body_class[] = 'home';
		$this->page_title = 'Biggle Shops';
		// $id= $this->ion_auth->user()->row()->id;
		$this->data['page_status'] = 0; // for index displays
		
			// redirect('goog');
		$this->current_section = 'home';
		$this->data['sort'] = array();
		$this->data['menu_display']=0;

		$this->data['merchants']= $this->get_merchants();
		$this->data['cats']= $this->get_categories();

		$this->data['me']='test';
		$this->render_page('home/shops', $this->data);

	}

	public function terms()
	{
		$this->body_class[] = 'home';
		$this->page_title = 'Biggle app Terms';
		$this->current_section = 'terms and conditions';
	$this->data['page_status'] = 0; // for index displays
				$this->data['menu_display'] = 0; // display the logo menu

			// redirect('goog');
				$this->current_section = 'home';
				$this->data['sort'] = array();


				$this->data['merchants']= $this->get_merchants();
				$this->data['cats']= $this->get_categories();
				$this->render_page('home/terms', $this->data);

			}

			public function contact()
			{
				$this->body_class[] = 'contact us | biggle';
				$this->page_title = 'Biggle app contacts';
				$this->current_section = 'contact us | biggle';
		$this->data['page_status'] = 0; // for index displays
		$this->data['menu_display'] = 0; // display the logo menu
		
			// redirect('goog');
		$this->current_section = 'contact';
		$this->data['sort'] = array();

		$this->data['merchants']= $this->get_merchants();
		$this->data['cats']= $this->get_categories();
		$this->render_page('home/contact', $this->data);

	}

	public function invite()
	{
		$this->body_class[] = 'invite';
		$this->page_title = 'Biggle app invite';
		$this->current_section = 'Invitation';
	$this->data['page_status'] = 0; // for index displays
				$this->data['menu_display'] = 0; // display the logo menu

			// redirect('goog');
				$this->current_section = 'invite';
				$this->data['sort'] = array();


				$this->data['merchants']= $this->get_merchants();
				$this->data['cats']= $this->get_categories();
				$this->render_page('home/invite', $this->data);

			}
			public function privacy()
			{
				$this->body_class[] = 'Privacy';
				$this->page_title = 'Biggle app Privay Policy';
				$this->current_section = 'Invitation';
	$this->data['page_status'] = 0; // for index displays
				$this->data['menu_display'] = 0; // display the logo menu

			// redirect('goog');
				$this->current_section = 'Privacy';
				$this->data['sort'] = array();

				$this->data['merchants']= $this->get_merchants();
				$this->data['cats']= $this->get_categories();
				$this->render_page('home/privacy', $this->data);

			}


			public function favorites()
			{
				$this->body_class[] = 'Favorites';
				$this->page_title = 'Biggle Wishlist';
				$this->current_section = 'wishlist';
		$this->data['page_status'] = 0; // for index displays
				$this->data['menu_display'] = 0; // display the logo menu

			// redirect('goog');
				$this->current_section = 'gifts';
				$this->data['sort'] = array();
				$this->data['merchants']= $this->get_merchants();
				$this->data['cats']= $this->get_categories();


				$biggle_id= $this->session->userdata['biggle_id'];
	    		$url = $api_point_elastic.'user/products';
			// $query= 'select * from table as a where a.name like '%abc%' and (a.merchant_id=1 or a.merchant_id=2)';
				try{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('userKey: '.$biggle_id));
					curl_setopt($ch, CURLOPT_HTTPGET, 1);
		        // curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 100);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$response= curl_exec($ch);
					curl_close($ch);
				} catch (Exception $ex) {
					$response="false";
				}
				$this->data['fav_list'] = $response;

				$this->render_page('home/favorites', $this->data);

			}

			public function gifts()
			{
				$this->body_class[] = 'gifts';
				$this->page_title = 'Biggle Gifts| Promotion Codes Malaysia | Free Ecommerce Gifts ';
				$this->current_section = 'gifts';
				$this->data['page_status'] = 0; // for index displays
				$this->data['menu_display'] = 0; // display the logo menu

			// redirect('goog');
				$this->current_section = 'gifts';
				$this->data['sort'] = array();

				$this->data['merchants']= $this->get_merchants();
				$this->data['cats']= $this->get_categories();

				$biggle_id= $this->session->userdata['biggle_id'];

				$this->render_page('home/gifts', $this->data);

			}

			public function get_user_credit() {
				$data= $this->ion_auth->get_user_credit();
				echo json_encode($data);
			}
			public function get_categories(){
		// $get_merchants = "https://h4ett688p6.execute-api.ap-southeast-1.amazonaws.com/dev/api/v1/merchant";
				// $get = "https://h4ett688p6.execute-api.ap-southeast-1.amazonaws.com/dev/api/v1/";
	    		$url = $api_point_elastic.'categoy/menu-list';

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
	    		$url = $api_point_elastic.'categoy/mmerchant-list';
	
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
