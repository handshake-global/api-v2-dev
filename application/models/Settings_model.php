<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'settings';
	    $this->social_account = 'social_account';
	    $this->locations = 'locations';
	    $this->track_swipes = 'track_swipes';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	}
 	
 	public function getSettings($data = NULL){
 		if($data == NULL)
 			return false;
 		$settings = $this->db->where('userId',$data['userId'])
 					->get($this->table)->row_array();
 		if(empty($settings)){
 			$settings['settingId'] = NULL;
 			$settings['global'] = 1;
 			$settings['maxDistance'] = 100;
 			$settings['userId'] = $data['userId'];
 			$settings['notifications'] = 1;
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

	 		if(!in_array('Google', $socialAccount))
	 			$social['Google'] = FALSE;
	 		else
	 			$social['Google'] = TRUE;

	 		if(!in_array('Linkedln', $socialAccount))
	 			$social['Linkedln'] = FALSE;
	 		else
	 			$social['Linkedln'] = TRUE;

	 		if(!in_array('Twitter', $socialAccount))
	 			$social['Twitter'] = FALSE;
	 		else
	 			$social['Twitter'] = TRUE;	
	 	}	
 		$settings['social'] = $social;

 		//getting locations
	 	$location = $this->db->where('userId',$data['userId'])
 				  ->get($this->locations)->result();
 		$settings['locations'] = $location;

 		//getting swipes
 		$swipes = $this->db->where('userId',$data['userId'])
 				  ->get($this->track_swipes)->result_array();

 		$leftSwipe = array_filter($swipes, function ($dataSet){
                if(isset($dataSet['type']))
                  return (trim($dataSet['type']) == 1);
            });		

        $rightSwipe = array_filter($swipes, function ($dataSet){
                if(isset($dataSet['type']))
                  return (trim($dataSet['type']) == 2);
            }); 

        $settings['leftSwipe'] = count($leftSwipe);         
        $settings['rightSwipe'] = count($rightSwipe);         
 		return $settings;			
 	}

 	public function setSettings(){
 		$data = $this->input->post();
 		if(empty($data))
 			return false;
 		$settings = array();
 		

		$settingExist = $this->db->where('userId',$data['userId'])
						->get($this->table)->row_array();

		if(!empty($settingExist)){
			$settings['global'] = isset($data['global']) ?$data['global'] 
								  : $settingExist['global'];
			$settings['maxDistance'] =isset($data['maxDistance'])? $data['maxDistance'] 
								   : $settingExist['maxDistance'];
			$settings['userId'] = $data['userId'];
			$settings['notifications'] = isset($data['notifications'])?$data['notifications']
										 : $settingExist['notifications'];
			
			$this->db->where('settingId',$settingExist['settingId'])
					->update($this->table,$settings);
			if($this->db->affected_rows()>0)
				return $this->db->where('settingId',$settingExist['settingId'])
 				->get($this->table)->row();
			else
				return false;
													 
		}

		$settings['global'] = isset($data['global']) ?$data['global'] : 0;
		$settings['maxDistance'] =isset($data['maxDistance'])? $data['maxDistance'] : 100;
		$settings['userId'] = $data['userId'];
		$settings['notifications'] = isset($data['notifications'])?$data['notifications'] : 1;
			
 		$this->db->insert($this->table,$settings);
 		if($settingId = $this->db->insert_id())
 			return $this->db->where('settingId',$settingId)
 				->get($this->table)->row();
 		else
 			return false;				
 	}

 	public function verifySocial(){
 		$data = $this->input->post();
 		if(empty($data))
 			return false;
 		$social = $this->db->where('userId',$data['userId'])
 						->where('source',$data['source'])
 						->where('accountId',$data['accountId'])
 				  ->get($this->social_account)->row_array(); 
 		if(empty($social)){
 			return $this->db->insert($this->social_account,$data);
 		}elseif($social['status']==0){
 			$this->db->where('socialId',$social['socialId'])
 			->update($this->social_account,array('status'=>1));
 			if($this->db->affected_rows())
 				return true;
 			else
 				return false;
 		}else{
 			return false;
 		}		  		
 	}

 	public function setLocation(){
 		$data = $this->input->post();
 		if(empty($data))
 			return false;
 		$location = $this->db->where($data)
 		->get($this->locations)->row();

 		if(!empty($location))
 			return false;
 		$this->db->insert($this->locations,$data);
 		if($locationId = $this->db->insert_id())
 			return $this->db->where('locationId',$locationId)
 			->get($this->locations)->row();
 		else
 			return false;	
 	}

 	public function deleteLocation($data=NULL){
 		if($data==NULL)
 			return false;
 		$this->db->where($data)
				->delete($this->locations);
		if($this->db->affected_rows()>0)
			return true;
		else
			return false;	
 	}
}
