<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class User extends REST_Controller {
    public function __construct() {
        parent::__construct();
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
        $this->load->model('user_model'); 
    }
   
    /**
     * get user profile
     *
     * @access public
     * @return json
     */
    public function userProfile_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getUserProfile') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->getUserProfile($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Profile');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No user found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    /**
     * update profile
     *
     * @access public
     * @return json
     */
    public function userProfile_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateUserProfile') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->updateUserProfile($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User updated');
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
     * update profile
     *
     * @access public
     * @return json
     */
    public function userDetails_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateUserDetails') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->updateUserDetails($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Details updated');
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
     * get user profile
     *
     * @access public
     * @return json
     */
    public function userProfileRating_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getUserProfileRating') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->getUserProfileRating($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Rating');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No user found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }

    /**
     * insert rating to  database
     *
     * @access public
     * @return json
     */
    public function userProfileReview_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setUserProfileReview') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->setUserProfileReview()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Rating inserted');
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
    public function userProfileReview_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getUserProfileReview') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->getUserProfileReview($this->get())){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Rating');
                $response = array('status'=>$status,'data'=>$response);
                $this->response($response, $statusCode);  
            }   
            else{
               $statusCode = parent::HTTP_OK;
               $status = array('statusCode' => $statusCode,'error'=>'No user found'); 
               $this->response(['status' =>$status,], parent::HTTP_OK); 
            }
        }
    }
    


     /**
     * delete existing user userReview
     *
     * @access public
     * @return json
     */
    public function userReview_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
       if(empty($this->delete()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->delete());

        if($this->form_validation->run('deleteUserReview') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->deleteUserReview($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Review deleted');
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
     * insert testimonials to  database
     *
     * @access public
     * @return json
     */
    public function userTestimonials_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setUserTestimonials') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->setUserTestimonials()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Testimonial added');
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
     * delete existing user testimonials
     *
     * @access public
     * @return json
     */
    public function userTestimonials_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
       if(empty($this->delete()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->delete());

        if($this->form_validation->run('deleteUserTestimonials') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->deleteUserTestimonials($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Testimonials deleted');
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
     * update testimonials
     *
     * @access public
     * @return json
     */
    public function userTestimonials_put(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        $this->form_validation->set_data($this->put());
    //create card using post data
        if($this->form_validation->run('updateUserTestimonials') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->updateUserTestimonials($this->put())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Testimonial updated');
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
     * insert testimonials to  database
     *
     * @access public
     * @return json
     */
    public function userSkill_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setUserSkill') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->setUserSkill()){
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'User Skill added');
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
     * get Courses
     *
     * @access public
     * @return json
     */
    public function userSkill_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
        if($response = $this->user_model->getUserSkill($this->get())){
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'User Skill');
            $response = array('status'=>$status,'data'=>$response);
            $this->response($response, $statusCode);  
        }   
        else{
           $statusCode = parent::HTTP_OK;
           $status = array('statusCode' => $statusCode,'error'=>'No Skill found'); 
           $this->response(['status' =>$status,], parent::HTTP_OK); 
        }
    }


     /**
     * delete existing user userSkill
     *
     * @access public
     * @return json
     */
    public function userSkill_delete(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
       if(empty($this->delete()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->delete());

        if($this->form_validation->run('deleteUserSkill') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->deleteUserSkill($this->delete())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'Skill deleted');
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
     * get Courses
     *
     * @access public
     * @return json
     */
    public function userCategory_get(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
    
        if($response = $this->user_model->getUserCategory($this->get())){
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'User Categories');
            $response = array('status'=>$status,'data'=>$response);
            $this->response($response, $statusCode);  
        }   
        else{
           $statusCode = parent::HTTP_OK;
           $status = array('statusCode' => $statusCode,'error'=>'No Category found'); 
           $this->response(['status' =>$status,], parent::HTTP_OK); 
        }
    }

     /**
     * insert testimonials to  database
     *
     * @access public
     * @return json
     */
    public function setUserSwipe_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('setUserSwipe') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->setUserSwipe()){
                $statusCode = parent::HTTP_OK;
                if($_POST['type']==1){
                    $swipes = explode(',', $_POST['swiped']);
                    foreach($swipes as $swipe)
                         $this->trackLeftSwipeNotification($swipe,$_POST['userId'],$_POST);
                }

                $status = array('statusCode' => $statusCode,'message'=>'User swipe tracked');
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
     * get Courses
     *
     * @access public
     * @return json
     */
    public function getUserSwipe_get(){
     // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
        if(empty($this->get()))
            $this->form_validation->set_data(['']);
        else
            $this->form_validation->set_data($this->get());
        //create card using post data
        if($this->form_validation->run('getUserSwipe') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            if($response = $this->user_model->getUserSwipe($this->get())){
                // Prepare the response
                $statusCode = parent::HTTP_OK;
                $status = array('statusCode' => $statusCode,'message'=>'user swipe data');
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
     * insert testimonials to  database
     *
     * @access public
     * @return json
     */
    public function userIntroduction_post(){
    // Call the verification method and store the return value in the variable
        $request = AUTHORIZATION::verify_request();
              //create card using post data
        if($this->form_validation->run('userIntroduction') == FALSE){
          $this->response(['error' => $this->form_validation->error_array(),'statusCode' => parent::HTTP_UNPROCESSABLE_ENTITY], parent::HTTP_UNPROCESSABLE_ENTITY);  
        }
        else{
            $this->userIntroductionNotification($_POST);
            $statusCode = parent::HTTP_OK;
            $status = array('statusCode' => $statusCode,'message'=>'sent');
            $this->response(['status' =>$status,], parent::HTTP_OK);
        }
    }

     private function userIntroductionNotification($data){
       
        $userDetails = get_userDetails($data['referFromUserId']);   
        $userName = $userDetails->userName;
        $userPhoto = $userDetails->userPhoto;
        $userDesignation = $userDetails->designation;

        $noteMe = array(
          'userId'=>$data['referUserId'],
          'notification'=>$userName.' introduce '.$data['referToUserName'].' .',
          'type'=>'friendIntroduction',
          'createdOn'=>date('Y/m/d h:i:s a', time()),
          'userDetails'=>json_encode(
                            array(
                            'userName'=>$userName,
                            'userPhoto'=>$userPhoto,
                            'designation'=>$userDesignation,
                            )
                        ),
          'data'=>json_encode($data),
                            
        );
        setNotification($noteMe);
        
        $token = get_token($data['referUserId']);
        $notify = array(
            'userId'=> $data['referFromUserId'],
            'userName'=> $userName,
            'type'=>'friendIntroduction'
        );

        send_notification(
            $token ->token,
            array('title'=>'Friend Introduction','msg'=>$data,'img'=>''),
            $notify
        );
    }

    private function trackLeftSwipeNotification($userId,$swipedBy,$data){
       
        $userByDetails = get_userDetails($swipedBy);
        $noteMe = array(
          'userId'=>$userId,
          'notification'=>$userByDetails->userName.' viewed your profile.',
          'type'=>'leftSwiped',
          'createdOn'=>date('Y/m/d h:i:s a', time()),
          'userDetails'=>json_encode(
                            array(
                            'userName'=>$userByDetails->userName,
                            'userPhoto'=>$userByDetails->userPhoto,
                            'designation'=>$userByDetails->designation,
                            )
                        ),
          'data'=>json_encode($data),
                            
        );
        setNotification($noteMe);
        
        $token = get_token($userId);
        $notify = array(
            'userId'=> $userId,
            'userName'=> $userByDetails->userName,
            'type'=>'friendIntroduction'
        );

        send_notification(
            $token ->token,
            array('title'=>'Left Swiped','msg'=>$data,'img'=>''),
            $notify
        );
    }
}