<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'settings';
	    $this->social_account = 'social_account';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	}
 	
 	public function getSettings($data = NULL){
 		if($data == NULL)
 			return false;
 		$settings = $this->db->where('userId',$data['userId'])
 					->get($this->table)->row();
 		if(empty($settings)){
 			$settings['settingId'] = NULL;
 			$settings['global'] = NULL;
 			$settings['maxDistance'] = NULL;
 			$settings['userId'] = $data['userId'];
 		}
 		$social = $this->db->where('userId',$data['userId'])
 				  ->get($this->social_account)->result_array();

 		$socialAccount = array_column($social, 'source');
 		if(empty($socialAccount)){
 			$socialAccount['Facebook'] = FALSE;
 			$socialAccount['Google'] = FALSE;
 			$socialAccount['Linkedln'] = FALSE;
 			$socialAccount['Twitter'] = FALSE;
 		}else{
	 		if(!in_array('Facebook', $socialAccount))
	 			$socialAccount['Facebook'] = FALSE;
	 		else
	 			$socialAccount['Facebook'] = TRUE;

	 		if(!isset($socialAccount['Google']))
	 			$socialAccount['Google'] = FALSE;
	 		else
	 			$socialAccount['Google'] = TRUE;

	 		if(!isset($socialAccount['Linkedln']))
	 			$socialAccount['Linkedln'] = FALSE;
	 		else
	 			$socialAccount['Linkedln'] = TRUE;

	 		if(!isset($socialAccount['Twitter']))
	 			$socialAccount['Twitter'] = FALSE;
	 		else
	 			$socialAccount['Twitter'] = TRUE;	
	 	}	

 		$settings['social'] = $socialAccount;

 		return $settings;			
 	}
}
