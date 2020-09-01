<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Chat extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('chat_model'); 
    }

    /**
     * Update FCM Tokens
     *
     * @access public
     * @return json
     */
    public function sendMessage_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
        if($this->form_validation->run('sendMessage') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->sendMessage()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $this->sendChatNotification($response);
	                $status = array('statusCode' => $statusCode,'message'=>'Message Sent');
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
     * Delete Message
     *
     * @access public
     * @return json
     */
    public function deleteMessage_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
        if($this->form_validation->run('deleteMessage') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->deleteMessage()){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Message Deleted');
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

   

    private function sendChatNotification($data){
        //send chat notification on success 
        $token = get_token($data->receiver);
        if($token==false || !isset($token->token) || empty($data))
        	return false;
        $notify = array(
        	'ReceiverUserId'=> $data->receiver,
        	'SenderUserId'=>$data->sender,
        	'userName'=> $data->userName,
        	'fileUrl'=> $data->fileUrl,
        	'dateTime'=> $data->dateTime,
        	'messageId'=> $data->messageId,
        	'msgContent'=> $data->msgContent,
        	'userPhoto'=> $data->userPhoto,
        	'time'=> $data->time,
        	'date'=> $data->date,
        	'type'=>'ChatMessage'
        );
        
        send_notification(
        	$token ->token,
        	array('title'=>$data->userName,'msg'=>$data->msgContent,'img'=>$data->userPhoto),
        	$notify
        );

    }

     /**
     * get messages
     *
     * @access public
     * @return json
     */
    public function GetMessageHistory_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getMessage') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->getMessage($this->get())){
            	if($this->input->get('pageIndex')=='' || $this->input->get('pageIndex') == 0)
            		$this->sendBulkHistoryNotification($this->get('receiver'),$this->get('sender'));
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Message History');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No messages found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    private function sendBulkHistoryNotification($userId=NULL,$sender=NULL){
        $token = get_token($userId);
        if($token==false || !isset($token->token) || $userId == NULL || $sender == NULL)
        	return false;
        $notify = array(
        	'userId'=> $sender,
        	'type'=>'AllRead'
        );
        
        send_notification(
        	$token ->token,
        	array('title'=>'BulkMessageStatus','msg'=>'ALL messsages read','img'=>''),
        	$notify
        );
    }

     /**
     * update message status
     *
     * @access public
     * @return json
     */
    public function markMessageRead_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->put()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('markMessageRead') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->markMessageRead($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Message mark as read');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'Nothing to update.'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

     /**
     * update message status
     *
     * @access public
     * @return json
     */
    public function changeMessageStatus_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->put()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('changeMessageStatus') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->changeMessageStatus($this->put())){
            	$this->sendStatusNotification($response,$response->messageStatus);
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Message status changed');
                $response = array('status'=>$status,'data'=>array());
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'Nothing to update.'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    private function sendStatusNotification($data=NULL,$status=0){
        //send chat notification on success 
        $messageType = 'Test';
        if($status == 1)
        	$status = 'MessageSent';
        elseif($status == 2)
        	$status = 'MessageReceived';
        elseif($status == 3)
        	$status = 'MessageRead';
        else
        	$status = 'test';

        $token = get_token($data->sender);
        if($token==false || !isset($token->token) || empty($data))
        	return false;
        $notify = array(
        	'userId'=> $data->receiver,
        	'messageId'=> $data->messageId,
        	'type'=>$status
        );
        
        send_notification(
        	$token ->token,
        	array('title'=>'MessageStatus','msg'=>$status,'img'=>''),
        	$notify
        );
    }

     /**
     * update message status bulk
     *
     * @access public
     * @return json
     */
    public function changeMessageStatusBulk_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->put()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('changeMessageStatusBulk') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->changeMessageStatusBulk($this->put())){
            	$this->sendBulkStatusNotification($response);
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Message status changed');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'message'=>'Nothing to update.'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    private function sendBulkStatusNotification($userId=NULL){
        $token = get_token($userId);
        if($token==false || !isset($token->token) || $userId == NULL)
        	return false;
        $notify = array(
        	'userId'=> $userId,
        	'type'=>'AllRead'
        );
        
        send_notification(
        	$token ->token,
        	array('title'=>'BulkMessageStatus','msg'=>'ALL messsages read','img'=>''),
        	$notify
        );
    }

    /**
     * get connection list
     *
     * @access public
     * @return json
     */
    public function getConnections_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getConnections') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->getConnections($this->get())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Connections');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No connection found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    /**
     * get connection list
     *
     * @access public
     * @return json
     */
    public function getMessageList_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getConnections') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->getMessageList($this->get())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Message List');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No connection found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }


     /**
     * Update FCM Tokens
     *
     * @access public
     * @return json
     */
    public function loginStatus_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
        if($this->form_validation->run('loginStatus') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->chat_model->loginStatus()){
                // Prepare the response
                $data = $this->input->post();
                $this->sendLoginNotification($data['userId'],$data['status']);
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Status Updated');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'Nothing to Update'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    private function sendLoginNotification($userId=NULL,$status=0){
        //send chat notification on success 
        $token = get_token($userId);
        if($token==false || !isset($token->token) || $userId==NULL)
        	return false;
        $notify = array(
        	'userId'=> $userId,
        	'status'=> $status,
        	'type'=>'LoginStatus'
        );
        
        send_notification(
        	$token ->token,
        	array('title'=>'LoginStatus','msg'=>'LoginStatus','img'=>''),
        	$notify
        );
    }

     /**
     * Update typing stauts
     *
     * @access public
     * @return json
     */
    public function typingStatus_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
    //create card using post data
        if($this->form_validation->run('typingStatus') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
        	$this->sendTypingNotification($this->input->post('sender'),$this->input->post('receiver'),$this->input->post('isTyping'));

            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'Status Updated');
            $response = array('status'=>$status,'data'=>array());
            $this->response($response, $statusCode); 
        }
    }

    private function sendTypingNotification($sender=NULL,$receiver=NULL,$isTyping=false){
        //send chat notification on success 
        $token = get_token($receiver);
        if($token==false || !isset($token->token) || $receiver==NULL || $sender==NULL )
        	return false;
        $notify = array(
        	'userId'=> $sender,
        	'isTyping'=> $isTyping,
        	'type'=> 'TypingStatus',
        );
        send_notification(
        	$token ->token,
        	array('title'=>'typingStatus','msg'=>'typingStatus','img'=>''),
        	$notify
        );
    }
}    
