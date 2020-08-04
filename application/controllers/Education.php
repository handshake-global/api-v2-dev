<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Education extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('education_model'); 
    }
   
     /**
     * get Education Levels
     *
     * @access public
     * @return json
     */
    public function educationLevels_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
        if($response = $this->education_model->getEducationLevels()){
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'Education Levels');
            $response = array('status'=>$status,'data'=>$response);
            $this->response($response, $statusCode);  
        }   
        else{
           $statusCode = parent::HTTP_OK;
           $status = array('statusCode' => $statusCode,'error'=>'No Level found'); 
           $this->response(['status' =>$status,], parent::HTTP_OK); 
        }
    }


     /**
     * get Courses
     *
     * @access public
     * @return json
     */
    public function educationCourses_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
        if($response = $this->education_model->getEducationCourses($this->get())){
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'Education Courses');
            $response = array('status'=>$status,'data'=>$response);
            $this->response($response, $statusCode);  
        }   
        else{
           $statusCode = parent::HTTP_OK;
           $status = array('statusCode' => $statusCode,'error'=>'No Course found'); 
           $this->response(['status' =>$status,], parent::HTTP_OK); 
        }
    }


    /**
     * get Courses
     *
     * @access public
     * @return json
     */
    public function educationInstitution_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
        if($response = $this->education_model->getEducationInstitution($this->get())){
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'Insitutions');
            $response = array('status'=>$status,'data'=>$response);
            $this->response($response, $statusCode);  
        }   
        else{
           $statusCode = parent::HTTP_OK;
           $status = array('statusCode' => $statusCode,'error'=>'No Insitution found'); 
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
        if($this->form_validation->run('setEducation') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->education_model->setEducation()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Education added');
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
        if($this->form_validation->run('getEducation') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->education_model->getEducation($this->get())){
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

        if($this->form_validation->run('deleteEducation') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->education_model->deleteEducation($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Data deleted');
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
    public function index_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateEducation') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->education_model->updateEducation($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Education updated');
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
}