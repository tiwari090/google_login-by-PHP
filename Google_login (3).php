<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Google_login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Google_login_model');
    }

   public function index()
{
    require_once APPPATH . "libraries/vendor/autoload.php";

    $google_client = new \Google_Client();
    $google_client->setClientId('941376910319-i5ra41efom8380oegha3da0iikcig8ic.apps.googleusercontent.com');
    $google_client->setClientSecret('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');
    $google_client->setRedirectUri('https://celebrationstation.gange.in/login');
    $google_client->addScope('email');
    $google_client->addScope('profile');

    if(isset($_GET['code'])){
        $token=$google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        if(!isset($token['error'])){
            $google_client->setAccessToken($token['access_token']);
            $this->session->set_userdata('access_token',$token['access_token']);
            //to get the profile data
            $google_service= new \Google_Service_Oauth2($google_client);
            $data1=$google_service->userinfo->get();
            if($this->Google_login_model->Is_already_resister($data1['id'])){
                //user update user
                $userdata=[
                    'first_name'=>$data1['givenName'],
                    'last_name'=>$data1['family_name'],
                    'email'=>$data1['email'],
                    'profile_pic'=>$data1['picture'],
                    'updated_at'=>date('Y-m-d H:i:s')
                    ];
                    $this->Google_login_model->Update_user_data($userdata,$data1['id']);
                 $this->session->set_userdata('username',$data1['givenName']);
                   return redirect(site_url('welcome/index'));
            }else{
                //user create user
                $userdata=[
                    'oauth_id'=>$data1['id'],
                    'first_name'=>$data1['givenName'],
                    'last_name'=>$data1['family_name'],
                    'email'=>$data1['email'],
                    'profile_pic'=>$data1['picture'],
                    'created_at'=>date('Y-m-d H:i:s')
                    ];
                    $this->Google_login_model->Insert_user_data($userdata);
                    $this->session->set_userdata('username',$data1['givenName']);
                    return redirect(site_url('welcome/index'));
            }
        }
    }
  $data = array();
if (!$this->session->userdata('access_token')) {
    $data['logins'] = $google_client->createAuthUrl();
    
    return $this->load->view('frontview/login-2', $data);
    print_r($data);
}


    }
    
    
    public function logout(){
      $this->session->sess_destroy();
       redirect(site_url('login'));
        
    }
    
    
     //select box
    
    public function distics()
      {
        $this->load->model('Select_model');
        $id = $this->input->post('id');
        // echo $id;
        $distic=  $this->Select_model->get_distic($id);
        echo json_encode($distic);
        // print_r($distic);
        
       }

}

/* End of file google_login.php */
/* Location: ./application/controllers/google_login.php */
