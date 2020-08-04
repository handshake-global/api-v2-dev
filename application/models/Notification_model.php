<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'fcm_tokens';
	    $this->users = 'users';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	}

	public function updateFcmToken(){
		$data = $this->input->post();
		$token = $this->db->where(array('deviceId'=>$data['deviceId'],'userId'=>$data['userId']))
				->get($this->table)->result();
		if(empty($token))
			return $this->db->insert($this->table,$data);
		else
			$this->db->where(array('deviceId'=>$data['deviceId'],'userId'=>$data['userId']))
			->update($this->table,$data);
			if($this->db->affected_rows() == 0 )
				return false;
			else 
				return true;
	} 
} 