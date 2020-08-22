<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Notification extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('notification_model'); 
    }
 	
 	/**
     * Update FCM Tokens
     *
     * @access public
     * @return json
     */
    public function updateFcmToken_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
        if($this->form_validation->run('updateFcmToken') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->notification_model->updateFcmToken()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Token Updated');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'Nothing to update'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    /**
     * Send to multiple devices
     */
    public function sendToMultiple()
    {
        $token = array('Registratin_id1', 'Registratin_id2'); // array of push tokens
        $message = "Test notification message";

        $this->load->library('fcm');
        $this->fcm->setTitle('Test FCM Notification');
        $this->fcm->setMessage($message);
        $this->fcm->setIsBackground(false);
        // set payload as null
        $payload = array('notification' => '');
        $this->fcm->setPayload($payload);
        $this->fcm->setImage('https://firebase.google.com/_static/9f55fd91be/images/firebase/lockup.png');
        $json = $this->fcm->getPush();

        /** 
         * Send to multiple
         * 
         * @param array  $token     array of firebase registration ids (push tokens)
         * @param array  $json      return data from getPush() method
         */
        $result = $this->fcm->sendMultiple($token, $json);
    }

     /**
     * get Notification
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
        if($this->form_validation->run('getNotification') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->notification_model->getNotification($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Notification List');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No Notification found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }
    
}    