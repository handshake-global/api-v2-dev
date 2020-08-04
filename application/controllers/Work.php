<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Work extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('work_model'); 
    }
   
     /**
     * get Education Levels
     *
     * @access public
     * @return json
     */
    public function empType_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
        if($response = $this->work_model->getEmpType()){
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'Employee Type');
            $response = array('status'=>$status,'data'=>$response);
            $this->response($response, $statusCode);  
        }   
        else{
           $statusCode = parent::HTTP_OK;
           $status = array('statusCode' => $statusCode,'error'=>'No emp type found'); 
           $this->response(['status' =>$status,], parent::HTTP_OK); 
        }
    }

 
     /**
     * insert education to  database
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
            if($response = $this->work_model->setWork()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Work added');
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
     * get user profile reviews
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
        if($this->form_validation->run('getWork') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->getWork($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Education');
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
     * delete existing user education
     *
     * @access public
     * @return json
     */
    public function index_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
       if(empty($this->delete()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->delete());

        if($this->form_validation->run('deleteWork') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->deleteWork($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Work deleted');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_INTERNAL_SERVER_ERROR;
               $status = array('statusCode' => $statusCode,'error'=>'something went wrong'); 
               $this->response(['status' =>$status,], parent::HTTP_INTERNAL_SERVER_ERROR); 
            }
        }
    }

     /**
     * update work
     * @access public
     * @return json
     */
    public function index_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateWork') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->updateWork($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Work updated');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'Nothing to Update'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }


     /**
     * insert reward to  database
     *
     * @access public
     * @return json
     */
    public function userReward_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setReward') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->setReward()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Reward added');
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
     * get user rewards
     *
     * @access public
     * @return json
     */
    public function userReward_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getReward') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->getReward($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Rewards');
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
     * update reward
     * @access public
     * @return json
     */
    public function userReward_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateUserReward') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->updateUserReward($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Reward updated');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'Nothing to Update'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

     /**
     * delete existing user userReward
     *
     * @access public
     * @return json
     */
    public function userReward_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
       if(empty($this->delete()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->delete());

        if($this->form_validation->run('deleteUserReward') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->deleteUserReward($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Reward deleted');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_INTERNAL_SERVER_ERROR;
               $status = array('statusCode' => $statusCode,'error'=>'something went wrong'); 
               $this->response(['status' =>$status,], parent::HTTP_INTERNAL_SERVER_ERROR); 
            }
        }
    }
     /**
     * insert Achievement to  database
     *
     * @access public
     * @return json
     */
    public function userAchievement_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setAchievement') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->setAchievement()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Achievement added');
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
     * get user Achievement
     *
     * @access public
     * @return json
     */
    public function userAchievement_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getAchievement') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->getAchievement($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Achievement');
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
     * delete existing user userReward
     *
     * @access public
     * @return json
     */
    public function userAchievement_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
       if(empty($this->delete()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->delete());

        if($this->form_validation->run('deleteUserAchievement') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->deleteUserAchievement($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Achievement deleted');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_INTERNAL_SERVER_ERROR;
               $status = array('statusCode' => $statusCode,'error'=>'something went wrong'); 
               $this->response(['status' =>$status,], parent::HTTP_INTERNAL_SERVER_ERROR); 
            }
        }
    }

    /**
     * update reward
     * @access public
     * @return json
     */
    public function userAchievement_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateUserAchievement') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->updateUserAchievement($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Achievement updated');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'Nothing to Update'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    /**
     * get user Achievement
     *
     * @access public
     * @return json
     */
    public function workHistory_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('workHistory') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->work_model->getWorkHistory($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Achievement');
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
}