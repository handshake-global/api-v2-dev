<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Settings extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('settings_model'); 
    }
 
     /**
     * insert settings to  database
     *
     * @access public
     * @return json
     */
    public function index_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setWork') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->settings_model->setSettings()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Settings added');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_INTERNAL_SERVER_ERROR;
               $status = array('statusCode' => $statusCode,'error'=>'Something went wrong'); 
               $this->response(['status' =>$status,], parent::HTTP_INTERNAL_SERVER_ERROR); 
            }
        }
    }

    /**
     * get user settings
     *
     * @access public
     * @return json
     */
    public function index_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getSettings') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->settings_model->getSettings($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Settings');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No Data found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }


    /**
     * verify social account
     *
     * @access public
     * @return json
     */
    public function verifySocial_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('verifySocial') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->settings_model->verifySocial()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Verified');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_INTERNAL_SERVER_ERROR;
               $status = array('statusCode' => $statusCode,'error'=>'User already exist'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }
    

}