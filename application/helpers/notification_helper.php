<?php
defined('BASEPATH') OR exit('No direct script access allowed');



if ( ! function_exists('send_notification')){
	function send_notification($token=NULL,$data=array(),$payload=array()){
 			
        $message = $data['msg'];
        $title = $data['title'];
        $img = $data['img'];

        $CI =& get_instance();
        $CI->load->library('fcm');
        $CI->fcm->setTitle($title);
        $CI->fcm->setMessage($message);

        /**
         * set to true if the notificaton is used to invoke a function
         * in the background
         */
        $CI->fcm->setIsBackground(false);

        /**
         * payload is userd to send additional data in the notification
         * This is purticularly useful for invoking functions in background
         * -----------------------------------------------------------------
         * set payload as null if no custom data is passing in the notification
         */
        $CI->fcm->setPayload($payload);

        /**
         * Send images in the notification
         */
        if($img=='')
        	$CI->fcm->setImage('https://firebase.google.com/_static/9f55fd91be/images/firebase/lockup.png');
        else
        	$CI->fcm->setImage($img);

        /**
         * Get the compiled notification data as an array
         */
        $json = $CI->fcm->getPush();
        print_r($json);
        echo $token;
        $p = $CI->fcm->send($token, $json);
        print_r($p);
        exit;
    }
}    

//get token and user details
if ( ! function_exists('get_token')){ 
	function get_token($userId=NULL){	
		if($userId==NULL)
			return false;
		$CI =& get_instance();
		return $CI->db->select('token')->where('userId',$userId)->get('fcm_tokens')->row();
	}
}

//get token and user details
if ( ! function_exists('get_userName')){ 
    function get_userName($userId=NULL){   
        if($userId==NULL)
            return false;
        $CI =& get_instance();
        return $CI->db->select("concat(users.firstName,' ',users.lastName) as userName")
        ->where('userId',$userId)
        ->get('users')
        ->row();
    }
}

//get token and user details
if ( ! function_exists('get_userDetails')){ 
    function get_userDetails($userId=NULL){   
        if($userId==NULL)
            return false;
        $CI =& get_instance();
        return $CI->db->select("userName,userPhoto,designation")
        ->where('userId',$userId)
        ->get('profile')
        ->row();
    }
}

//get token and user details
if ( ! function_exists('setNotification')){ 
    function setNotification($data=NULL){   
        if($data==NULL)
            return false;
        $CI =& get_instance();
        return $CI->db->insert('notifications',$data);
    }
}	   