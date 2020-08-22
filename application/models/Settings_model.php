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
 		$social = array();
 		if(empty($socialAccount)){
 			$social['Facebook'] = FALSE;
 			$social['Google'] = FALSE;
 			$social['Linkedln'] = FALSE;
 			$social['Twitter'] = FALSE;
 		}else{
	 		if(!in_array('Facebook', $socialAccount))
	 			$social['Facebook'] = FALSE;
	 		else
	 			$social['Facebook'] = TRUE;

	 		if(!isset($social['Google']))
	 			$social['Google'] = FALSE;
	 		else
	 			$social['Google'] = TRUE;

	 		if(!isset($social['Linkedln']))
	 			$social['Linkedln'] = FALSE;
	 		else
	 			$social['Linkedln'] = TRUE;

	 		if(!isset($social['Twitter']))
	 			$social['Twitter'] = FALSE;
	 		else
	 			$social['Twitter'] = TRUE;	
	 	}	

 		$settings['social'] = $social;

 		return $settings;			
 	}
}
