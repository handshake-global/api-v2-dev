<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Work_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'work_history';
	    $this->empType = 'empType';
	    $this->work_schedule = 'work_schedule';
	    $this->rewards = 'rewards';
	    $this->achievement = 'achievement';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	}
 	

 	public function getEmpType(){
 		return $this->db->where('status',1)->get($this->empType)->result();
 	}


 	public function setWork(){
 		$data = $this->input->post();
 		$data['createdAt'] = $this->createdAt;
 		$schedule = NULL;

 		if(isset($data['schedule'])):
 			$schedule = $data['schedule'];
 			unset($data['schedule']);
 		endif;
 		$this->db->insert($this->table,$data);

 		if($workId = $this->db->insert_id()){
	 		//setting schedule if available
	 		if($schedule!=NULL){
	 			$schedule = json_decode($schedule); 
	 			foreach($schedule as $key => $pickme):
	 				$schData = array(
	 					'workId' =>$workId,
	 					'day' =>$key,
	 					'time' =>json_encode($pickme),
	 				);
	 			$scheduleId = $this->db->insert($this->work_schedule,$schData);	 
	 			endforeach;  
	 			if($scheduleId)
	 				return true;
	 			else
	 				return false;		
	 		}else{
	 			return true;
	 		}

 		}else{
 			return false;
 		}
 	} 

 	public function getWork($data=NULL){
 		if(empty($data))
 			return false;
	 	$work = $this->db->select('work_history.*,empType.emp as employeeType',false)
	    ->from('work_history')
	    ->join('empType', 'work_history.empType = empType.typeId', 'left')
	    ->where('work_history.userId',$data['userId'])
	    ->get()->result_array();
 		if(empty($work))
 			return false;
 		$completeSchedule = array();

 		$i = 0;
 		foreach ($work as $wk) {
 		 	$schedule = $this->db->select('day,time as timeData')
 		 	->where('workId',$wk['workId'])->get($this->work_schedule)->result_array();
 		 	$wk['schedule'] = $this->tempSchedule($schedule);
 		 	$completeSchedule[] = $wk;
 		 	$i++;
 		 }
 		return  $completeSchedule;
 	}

 	public function updateWork($data=NULL){
 		if(empty($data))
 			return false;

 		$schedule = NULL;
 		if(isset($data['schedule'])):
 			$schedule = $data['schedule'];
 			unset($data['schedule']);
 		endif;

 		$this->db->where('workId',$data['workId'])
 			->where('userId',$data['userId'])
 			->update($this->table,$data);

 		//setting schedule if available
	 		if($schedule!=NULL){
	 			$this->db->where('workId',$data['workId'])->delete($this->work_schedule);
	 			$schedule = json_decode($schedule); 
	 			if($schedule!=NULL):
		 			foreach($schedule as $key => $pickme):
		 				$schData = array(
		 					'workId' =>$data['workId'],
		 					'day' =>$key,
		 					'time' =>json_encode($pickme),
		 				);
		 			$scheduleId = $this->db->insert($this->work_schedule,$schData);	 
		 			endforeach;  
		 			if($scheduleId)
		 				return true;
		 			else
		 				return false;
		 		endif;				
	 		}else{
	 			return true;
	 	}
	 	return true;	
 	}

 	public function deleteWork($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where($data)->delete($this->table);
 		if($this->db->affected_rows()>0)
			return $this->db->where('workId',$data['workId'])->delete($this->work_schedule);
		else
			return false;
 	}


 	function tempSchedule($schedule){
 		 $temp = array();
 		 foreach ($schedule as $value) {
 		 	$temp[] = array('day'=>$value['day'],'timeData'=>json_decode($value['timeData'])); 
 		 }
 		 return $temp;
 	}

 	public function setReward(){
 		$data = $this->input->post();
 		$data['createdAt'] = $this->createdAt;

 		$this->db->insert($this->rewards,$data)
 		if($rewardId = $this->db->insert_id())
 			return $this->db->where('rewardId',$rewardId)->get($this->rewards)->row();
 		else
 			return false;
 	}

 	public function getReward($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where('userId',$data['userId'])
 		->get($this->rewards)->result();
 	}

 	public function setAchievement(){
 		$data = $this->input->post();
 		$data['createdAt'] = $this->createdAt;
 		$this->db->insert($this->achievement,$data);
 		if($achiId = $this->db->insert_id())
 			return $this->db->where('achId',$achiId)->get($this->achievement)->row();
 		else
 			return false;
 	}

 	public function getAchievement($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where('userId',$data['userId'])
 		->get($this->achievement)->result();
 	}

 	public function getWorkHistory($data=NULL){
 		if(empty($data))
 			return false;
 		$work = $this->getWork($data);
 		$rewards = $this->getReward($data);
 		$achievement = $this->getAchievement($data);

 		return array(
 			'work' => $work,
 			'rewards' => $rewards,
 			'achievements' => $achievement
 		);
 	}

 	public function deleteUserReward($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where($data)->delete($this->rewards);
 	}

 	public function updateUserReward($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where('rewardId',$data['rewardId'])
 			->where('userId',$data['userId'])
 			->update($this->rewards,$data);

 		if($this->db->affected_rows()>0)
 			return $this->db->where('rewardId',$data['rewardId'])->get($this->rewards)->row();
		else
			return false;	
 	}

 	public function deleteUserAchievement($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where($data)->delete($this->achievement);
 	}

 	public function updateUserAchievement($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where('achId',$data['achId'])
 			->where('userId',$data['userId'])
 			->update($this->achievement,$data);

 		if($this->db->affected_rows()>0)
 			return $this->db->where('achId',$data['achId'])->get($this->achievement)->row();
		else
			return false;	
 	}
}
