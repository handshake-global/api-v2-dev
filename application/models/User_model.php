<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'users';
	    $this->details = 'user_details';
	    $this->users = 'users';
	    $this->reviews = 'reviews';
	    $this->profile = 'profile';
	    $this->testimonials = 'testimonials';
	    $this->fields = 'card_fields';
	    $this->skills = 'skills';
	    $this->skill_mapping = 'skill_mapping';
	    $this->category = 'category';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	}
 	
 	public function getUserProfile($data=NULL){
 		if(empty($data))
 			return false;
 		$profile =  $this->db->where('userId',$data['userId'])
 		->get($this->profile)->row_array();
 		unset($profile['skills']);
 		$skills = $this->db->where($data)->get($this->skill_mapping)->result_array();
 		$clean = array_map(
				    function (array $elem) {
				 		unset($elem['updatedAt']);
				 		unset($elem['createdAt']);
				        return $elem;             // and return it to be put into the result
				    },
				    $skills
				);
 		$profile['skills']  = $clean;
 		return $profile;

 	}

 	public function updateUserProfile($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where('userId',$data['userId'])->update($this->table,$data);
		if($this->db->affected_rows()>0)
			return true;
		else
			return false;
 	}

 	public function updateUserDetails($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where('userId',$data['userId'])->update($this->details,$data);
		if($this->db->affected_rows()>0)
			return true;
		else
			if(empty($this->db->where('userId',$data['userId'])->get($this->details)->row()))
				return $this->db->insert($this->details,$data);
			else
				return false;
 	}

 	public function getUserProfileRating($data=NULL){
 		if(empty($data))
 			return false;
 		$ratings = $this->db->select("rating,count(rating) ratingCount, round((COUNT(*) / (SELECT COUNT(*) FROM reviews where toUser = ".$data['userId']." )) * 100) AS percentage")
 		->where("toUser",$data['userId'])
 		->group_by('rating')
 		->get($this->reviews)->result_array();

 		$ratingCount = 0 ;
 		if(!empty($ratings))
 			$ratingCount = $ratings[0]['ratingCount'];

 		//removing rating count from array
 		$ratings = array_reduce($ratings, function($carry, $item){
		    $carry[] = array('rating'=>$item['rating'],'percentage'=>$item['percentage']);
		    return $carry;
		});
		 
 		//finding rating not available for user from 1 to 5 
 		if(!empty($ratings))
 			$ratingNotAv = array_diff([1,2,3,4,5], array_column($ratings, 'rating'));
 		else
 			$ratingNotAv = [1,2,3,4,5];
 		if(!empty($ratingNotAv)):
 			foreach($ratingNotAv as $rate)
 				$ratings[] = array('rating'=>$rate,'percentage'=>0);
 		endif;
 		//sorting rating according to rating
 		usort($ratings, function($a, $b) {
		    return $a['rating'] <=> $b['rating'];
		});
 		return array(
 			'totalRatings'=>$ratingCount,
 			'ratings'=>$ratings,
 			'testimonials'=>$this->db->where('userId',$data['userId'])->get($this->testimonials)->result()
 		);
 	}

 	public function setUserProfileReview(){
 		$data = $this->input->post();
 		$this->db->insert($this->reviews,$data);
 		if($reviewId = $this->db->insert_id())
			return true;
		else
			return false;
 	}


    public function getUserProfileReview($data=NULL){
    	if(empty($data))
    		return false;
    	return $this->db->select('reviews.*, profile.userName,profile.userPhoto,profile.designation')
         ->from($this->reviews)
         ->where('reviews.toUser',$data['userId'])
         ->join('profile', 'profile.userID = reviews.toUser')
		 ->get()->result();
    }

    public function setUserTestimonials(){
 		$data = $this->input->post();
 		$this->db->insert('testimonials',$data);
 		if($testiId = $this->db->insert_id())
			return $this->db->where('testiId',$testiId)->get($this->testimonials)->row();
		else
			return false;
 	}
 
 	public function setUserSkill(){
 		$data = $this->input->post();
 		$skillId = $data['skillId'];
 		//check if skill id is numeric or string
 		if(is_numeric($skillId))
 			$skills = $this->db->where('skillId',$skillId)->get($this->skills)->row();
 		elseif($skills = $this->setskills($skillId))
 			if(empty($skills))
 				return false;
 			else
 				$skills;
 		else 
 			return false;

 		$data = array(
 			'userId' => $data['userId'],
 			'skillId' => $skills->skillId,
 			'skill' => $skills->skill
 		);

 		$this->db->insert($this->skill_mapping,$data);
 		if($skillId = $this->db->insert_id())
 			return true;
 		else
 			return false;
 	}

 	public function setskills($skill=NULL){
 		$this->db->insert($this->skills,array('skill'=>$skill));
 		$skillId = $this->db->insert_id();
 		return $this->db->where('skillId',$skillId)->get($this->skills)->row();
 	}

 	public function getUserSkill($data = NULL){
 		if(!empty($data) && isset($data['skill']))
 			$this->db->like('skill', $data['skill']);
 		return $this->db->get($this->skills)->result_array();
 	}

 	public function deleteUserSkill($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where($data)->delete($this->skill_mapping);
 	}

 		public function getUserCategory($data = NULL){
 		return $this->db->where('status',1)->get($this->category)->result();
 	}

 	public function deleteUserReview($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where($data)->delete($this->reviews);
 	}

 	public function deleteUserTestimonials($data=NULL){
 		if(empty($data))
 			return false;
 		return $this->db->where($data)->delete($this->testimonials);
 	}

 	public function updateUserTestimonials($data=NULL){
 		if(empty($data))
 			return false;
 		$this->db->where('testiId',$data['testiId'])->update($this->testimonials,$data);
		if($this->db->affected_rows()>0)
			return $this->db->where('testiId',$data['testiId'])->get($this->testimonials)->row();
		else
			return false;
 	}

 	public function setUserSwipe($data=NULL){
 		if($data == NULL)
 			return false;
 		$this->db->insert('track_swipes',$data);
 		if($swipeId = $this->db->insert_id())
 			return true;
 		else
 			return false;
 	}
}
