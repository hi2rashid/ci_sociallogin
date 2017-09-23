<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	 function __construct(){
		parent::__construct();
		$this->load->config("oauth");
		$this->load->library("Facebook");
	 }
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function login(){
		

		$userData = array();
		$data['fb_authUrl'] = "#";
				// Check if user is logged in
				if($this->facebook->is_authenticated()){
					// Get user facebook profile details
					$userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture');
		
					// Preparing data for database insertion
					$userData['oauth_provider'] = 'facebook';
					$userData['oauth_uid'] = $userProfile['id'];
					$userData['first_name'] = $userProfile['first_name'];
					$userData['last_name'] = $userProfile['last_name'];
					$userData['email'] = $userProfile['email'];
					$userData['gender'] = $userProfile['gender'];
					$userData['locale'] = $userProfile['locale'];
					$userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
					$userData['picture_url'] = $userProfile['picture']['data']['url'];
		print_r($userData);
					// Insert or update user data
					$userID = 0;//$this->user->checkUser($userData);
		
					// Check user data insert or update status
					if(!empty($userID)){
						$data['userData'] = $userData;
						$this->session->set_userdata('userData',$userData);
					}else{
					   $data['userData'] = array();
					}
		
					// Get logout URL
					$data['logoutUrl'] = $this->facebook->logout_url();
				}else{
					$fbuser = '';
		
					// Get login URL
					$data['fb_authUrl'] =  $this->facebook->login_url();
				}
				print_r($data);
		$this->load->view("auth/login",$data);
	}

	public function facebook_logout(){
			// Remove local Facebook session
			$this->facebook->destroy_session();
	
			// Remove user data from session
			$this->session->unset_userdata('userData');
	
			// Redirect to login page
			redirect('/user_authentication');
	}

	public function facebook_authentication(){
		print_r($_REQUEST);
	}
}
