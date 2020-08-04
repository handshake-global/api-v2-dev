<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Card extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('card_model'); 
    }
    
    /**
     * card fields
     *
     * @access public
     * @return json
     */
    public function fields_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
   		if($this->form_validation->run('card_fields') == FALSE){
            $this->response(
                ['status'=>['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY]], parent::HTTP_UNPROCESSABLE_ENTITY
            ); 
            exit; 
        }    
    //getting card fields from card_fields table accroding to user id	
    	$card_fields = $this->card_model->card_fields();    

    // Send the return data as reponse
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $card_fields];
        $this->response($response, $status);
    }

    /**
     * card select
     *
     * @access public
     * @return json
     */
    public function select_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
        if($this->form_validation->run('card_select') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->select_card()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'selected card');
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
     * card create
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
        if($this->form_validation->run('fetch_cards') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->fetch_cards($this->get())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'card data');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No Result Found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    /**
     * card create
     *
     * @access public
     * @return json
     */
    public function index_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
    	if($this->form_validation->run('card_create') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->create_card()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'card created');
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
     * update existing card
     *
     * @access public
     * @return json
     */
    public function index_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
    	if($this->form_validation->run('card_update') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->update_card($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'card updated');
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
     * delete existing card
     *
     * @access public
     * @return json
     */
    public function index_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->delete());
    //create card using post data
        if($this->form_validation->run('card_delete') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->delete_card($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'card deleted');
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
     * mapping card data with existing card
     *
     * @access public
     * @return json
     */
    public function mapping_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    // map card using provided data
    	if($this->form_validation->run('card_mapping') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->map_card()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'card mapping success');
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
     * configuration card data with existing card
     *
     * @access public
     * @return json
     */
    public function config_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    // map card using provided data
        if($this->form_validation->run('card_config') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->config_card()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'card configuration success');
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
     * Store a newly media in storage.
     */
    public function media_post() {
        //uploading image if requested in post data
        if (isset($_FILES['media']) && !empty($_FILES['media']['name'])) {
            $images = do_upload('media', MEDIA_PATH,TRUE,NULL,TRUE);
            if (is_array($images) && !empty($images)){
                $response = [];
                foreach($images as $image)
                    $response[] = array('local'=>$image['orig_name'],'server'=>ltrim($image['clear_path'],'.'),'thumbnail'=>ltrim($image['thumb'],'.'));
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'media uploaded');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode); 
            }else{
               $statusCode = parent::HTTP_INTERNAL_SERVER_ERROR;
               $status = array('statusCode' => $statusCode,'error'=>$images); 
               $this->response(['status' =>$status,], parent::HTTP_INTERNAL_SERVER_ERROR); 
            } 
        } 
    }

     /**
     * share card later
     *
     * @access public
     * @return json
     */
    public function scannedCard_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
    
    //create card using post data
        if($this->form_validation->run('scannedCardGet') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->card_model->scannedCardGet($this->get())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'list of cards');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'No data found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }
}