<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Education_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'education';
	    $this->courses = 'courses';
	    $this->edu_level = 'edu_level';
	    $this->institutions = 'institutions';
	   
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	}
 	
 	public function getEducationLevels(){
 		return $this->db->where('status',1)
 		->get($this->edu_level)->result();
 	}

 	public function getEducationCourses($data = NULL){
 		if(!empty($data) && isset($data['course']))
 			$this->db->like('course', $data['course']);
 		return $this->db->where('status',1)
 		->get($this->courses)->result();
 	}

 	public function getEducationInstitution($data = NULL){
 		if(!empty($data) && isset($data['institute']))
 			$this->db->like('institution', $data['institute']);
 		return $this->db->where('status',1)
 		->get($this->institutions)->result();
 	}

 	public function setEducation(){
 		$data = $this->input->post();
 		$levelId = $courseId = $institutionId = 0;
 		//check if level id is numeric or string
 		if(is_numeric($data['levelId']))
 			$levelId = $data['levelId'];
 		elseif($levelId = $this->setEducationLevel($data['levelId']))
 			if(is_numeric($levelId))
 				$levelId;
 			else
 				return false;
 		else 
 			return false;
 		//end with courseId id

 		//check if courseId is numeric or string
 		if(is_numeric($data['courseId']))
 			$courseId = $data['courseId'];
 		elseif($courseId = $this->setEducationCourse($data['courseId']))
 			if(is_numeric($courseId))
 				$courseId;
 			else
 				return false;
 		else 
 			return false;
 		//end with level id

 		//check if institutionId is numeric or string
 		if(is_numeric($data['institutionId']))
 			$institutionId = $data['institutionId'];
 		elseif($institutionId = $this->setEducationInstitution($data['institutionId']))
 			if(is_numeric($institutionId))
 				$institutionId;
 			else
 				return false;
 		else 
 			return false;
 		//end with level id

 		$data = array(
 			'userId' => $data['userId'],
 			'levelId' => $levelId,
 			'courseId' => $courseId,
 			'institutionId' => $institutionId,
 			'startYear' => isset($data['startYear']) ? $data['startYear'] : '',
 			'endYear' => isset($data['endYear']) ? $data['endYear']: '',
 		);

 		$this->db->insert($this->table,$data);
 		if($educationId = $this->db->insert_id())
 			return $this->db->query("SELECT education.educationId,education.userId,edu_level.level,courses.course,institutions.institution,education.startYear,education.endYear FROM `education`
		LEFT JOIN edu_level ON education.levelId = edu_level.levelId
		LEFT JOIN courses ON education.courseId = courses.courseId
		LEFT JOIN institutions ON education.institutionId = institutions.institutionId
		WHERE education.educationId = ".$educationId."  ")->row();
 		else
 			return false;
 	}
 	 
 	public function setEducationLevel($level=NULL){
 		if($level==NULL)
 			$data = $this->input->post();
 		else
 			$data['level'] = $level;
 		$this->db->insert($this->edu_level,$data);
 		if($levelId = $this->db->insert_id())
			return $levelId;
		else
			return false;
 	} 

 	public function setEducationCourse($course=NULL){
 		if($course==NULL)
 			$data = $this->input->post();
 		else
 			$data['course'] = $course;
 		$this->db->insert($this->courses,$data);
 		if($courseId = $this->db->insert_id())
			return $courseId;
		else
			return false;
 	} 

 	public function setEducationInstitution($institution=NULL){
 		if($institution==NULL)
 			$data = $this->input->post();
 		else
 			$data['institution'] = $institution;
 		$this->db->insert($this->institutions,$data);
 		if($institutionId = $this->db->insert_id())
			return $institutionId;
		else
			return false;
 	} 

 	function getEducation($data=NULL){
 		if(empty($data))
 			return false;
 		$userId = $data['userId'];
 		return $this->db->query("SELECT education.educationId,education.userId,edu_level.level,courses.course,institutions.institution,education.startYear,education.endYear FROM `education`
		LEFT JOIN edu_level ON education.levelId = edu_level.levelId
		LEFT JOIN courses ON education.courseId = courses.courseId
		LEFT JOIN institutions ON education.institutionId = institutions.institutionId
		WHERE education.userId = ".$userId."
		")->result();
 	}


	/**
     * delete education
     * @return array , card
     */
	public function deleteEducation($data=array()){
		if(empty($data))
			return false;
		$this->db->where('educationId',$data['educationId'])->delete($this->table);
		return $this->db->affected_rows(); 
	} 

	public function updateEducation($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where('educationId',$data['educationId'])
 			->where('userId',$data['userId'])
 			->update($this->table,$data);

 		if($this->db->affected_rows()>0)
 			return $this->db->where('educationId',$data['educationId'])->get($this->table)->row();
		else
			return false;	
 	}
}
