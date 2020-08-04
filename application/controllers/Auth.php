<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Auth extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('auth_model'); 
    }
    
    /**
     * register user in system REST API
     *
     * @access public
     * @return json
     */

    public function register_post()
    {   
        if($this->form_validation->run('register') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_OK);  
        }
        else{
            $response = $this->auth_model->register();
            if(!is_object($response) && $response == 409){
                $statusCode = parent::HTTP_OK;
                $response= $this->clean_response($response);
                $status = array('statusCode' => $statusCode,'message'=>'user already exist with social account');
                $response = array('status'=>$status,'data'=>[]);
                $this->response($response, $statusCode);  
            }
            elseif($response){
                // Create a token from the user data and send it as reponse
                $token = AUTHORIZATION::generateToken(['phoneNo' => $response->phoneNo]);
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $response->token = $token;
                $response= $this->clean_response($response);
                $status = array('statusCode' => $statusCode,'message'=>'user registered');
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
     *login user in system REST API
     *
     * @access public
     * @return json
     */
    public function login_post()
    {
        // Extract user data from POST request
        $phoneNo = urldecode($this->post('phoneNo'));
        $countryCode = urldecode($this->post('countryCode'));
        $password = urldecode($this->post('password'));

        if($this->form_validation->run('login') == FALSE){
            $this->response(
                ['status'=>['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_OK]], parent::HTTP_OK
            );  
        }
        else{
            if(base64_encode(base64_encode($countryCode.$phoneNo))!=$password){
                $this->response(['status'=>['error' =>'bad request','statusCode' => parent::HTTP_BAD_REQUEST]], parent::HTTP_BAD_REQUEST);
            }
            else{
                if($response = $this->auth_model->login()){
                    // Create a token from the user data and send it as reponse
                    $token = AUTHORIZATION::generateToken(['phoneNo' => $response->phoneNo]);

                    //check if user active 
                    if($response->status!=1){
                       $statusCode = parent::HTTP_UNAUTHORIZED;
                       $status = array('statusCode' => $statusCode,'error'=>'user is not active'); 
                       $this->response(['status' =>$status,], parent::HTTP_UNAUTHORIZED); 
                       exit;
                    }

                    //check if user active 
                    if($response->isVerified!=1){
                       $statusCode = parent::HTTP_OK;
                       $response->token = $token;
                       $response= $this->clean_response($response);
                       $status = array('statusCode' => $statusCode,'error'=>'user is not verified'); 
                       $this->response(['status' =>$status,'data'=>$response], parent::HTTP_UNAUTHORIZED); 
                       exit;
                    }

                    
                    // Prepare the response
                    $statusCode = parent::HTTP_OK;
                    $response->token = $token;
                    $response= $this->clean_response($response);
                    $status = array('statusCode' => $statusCode,'message'=>'user logged-in');
                    $response = array('status'=>$status,'data'=>$response);
                    $this->response($response, $statusCode);    
                    
                }   
                else{
                   $statusCode = parent::HTTP_NOT_FOUND;
                   $status = array('statusCode' => $statusCode,'error'=>'invalid credentials'); 
                   $this->response(['status' =>$status,], parent::HTTP_NOT_FOUND); 
                }
            } 
                      
        }
    } 
    
    /**
     *login user in system REST API
     *
     * @access public
     * @return json
     */
    public function socialLogin_post()
    { 

        if($this->form_validation->run('socialLogin') == FALSE){
            $this->response(
                ['status'=>['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY]], parent::HTTP_OK
            );  
        }
        else{
         
           if($response = $this->auth_model->socialLogin()){
             // Create a token from the user data and send it as reponse
                    $token = AUTHORIZATION::generateToken(['phoneNo' => $response->phoneNo]);
                    //check if user active 
                    if($response->status!=1){
                       $statusCode = parent::HTTP_UNAUTHORIZED;
                       $status = array('statusCode' => $statusCode,'error'=>'user is not active'); 
                       $this->response(['status' =>$status,], parent::HTTP_UNAUTHORIZED); 
                       exit;
                    }

                    
                    //check if user active 
                    if($response->isVerified!=1){
                       $statusCode = parent::HTTP_UNAUTHORIZED;
                       $response->token = $token;
                       $response= $this->clean_response($response);
                       $status = array('statusCode' => $statusCode,'error'=>'user is not verified'); 
                       $this->response(['status' =>$status,'data'=>$response], parent::HTTP_OK); 
                       exit;
                    }

                   
                    // Prepare the response
                    $statusCode = parent::HTTP_OK;
                    $response->token = $token;
                    $response= $this->clean_response($response);
                    $status = array('statusCode' => $statusCode,'message'=>'user logged-in');
                    $response = array('status'=>$status,'data'=>$response);
                    $this->response($response, $statusCode);    
                    
                }   
                else{
                   $statusCode = parent::HTTP_NOT_FOUND;
                   $status = array('statusCode' => $statusCode,'error'=>'invalid credentials'); 
                   $this->response(['status' =>$status,], parent::HTTP_NOT_FOUND); 
                }
        }
    } 
    /**
     *verify user in system REST API
     *
     * @access private
     * @return array
     */
    private function clean_response($response=NULL){
        if($response==NULL)
            return $response;
        unset($response->password);
        unset($response->updatedBy);
        unset($response->createdAt);
        unset($response->updatedAt);
        return $response;
    }

    /**
     *verify user in system REST API
     *
     * @access public
     * @return json
     */
    public function verify_post()
    {
        // Extract user data from POST request
        $phoneNo = urldecode($this->post('phoneNo'));
        $countryCode = urldecode($this->post('countryCode'));
        $password = urldecode($this->post('password'));
        $isVerify = urldecode($this->post('isVerify'));

        if($this->form_validation->run('login') == FALSE){
            $this->response(
                ['status'=>['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY]], parent::HTTP_UNPROCESSABLE_ENTITY
            );  
        }
        else{
            if(base64_encode(base64_encode($countryCode.$phoneNo))!=$password){
                $this->response(['status'=>['error' =>'bad request','statusCode' => parent::HTTP_BAD_REQUEST]], parent::HTTP_BAD_REQUEST);
            }
            else{
                if($response = $this->auth_model->verify()){
                    // Create a token from the user data and send it as reponse
                    $token = AUTHORIZATION::generateToken(['phoneNo' => $response->phoneNo]);
                    // Prepare the response
                    $statusCode = parent::HTTP_OK;
                    $status = array('statusCode' => $statusCode,'message'=>'user verified');
                    $response = array('status'=>$status,'token' => $token,'data'=>$response);
                    $this->response($response, $statusCode);    
                }   
                else{
                   $statusCode = parent::HTTP_NOT_FOUND;
                   $status = array('statusCode' => $statusCode,'error'=>'invalid credentials'); 
                   $this->response(['status' =>$status,], parent::HTTP_NOT_FOUND); 
                }
            } 
                      
        }
    }

    
}