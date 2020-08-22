<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'fcm_tokens';
	    $this->notify = 'notifications';
	    $this->users = 'users';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
        $this->limit = 10;
	    $this->offset = 0;
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

	public function getNotification($data=NULL){
		if($data==NULL)
			return false;
		if(isset($data['pageIndex']) && $data['pageIndex']!=0)
            $this->offset = $data['pageIndex']* $this->limit;
        
        if($this->offset == 0)
        	$this->db->where('userId',$data['userId'])
        	->update($this->notify,array('isRead'=>1));
        	
        return $this->db->where('userId',$data['userId'])
        		->order_by('notifyId')
                ->limit($this->limit,$this->offset)
                ->get($this->notify)
                ->result_array();  
	}
} 