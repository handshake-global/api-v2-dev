<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'users';
	}

	/**
     * Register user
     * @return array , user
     */
	public function register(){
		$data = $this->input->post();
		$data['ipAddress'] = get_client_ip();
        $data['createdAt'] = date('Y/m/d h:i:s a', time());
        $social = array();
        if(isset($data['social'])){
	 		$social = json_decode($data['social']);
	 		unset($data['social']);
	 		//check if user already exist
	 		if($this->db->where(
	 			array('source'=>$social->source,'accountId'=>$social->accountId))->get('social_account')->row()
	 		)
	 			return 409;

	 	}		
	 	$this->db->insert($this->table,$data);
	 	if($userId = $this->db->insert_id()){
	 		if(!empty($social))
	 			$this->db->insert("social_account",
	 				array(
	 					'userId'=>$userId,
	 					"source"=>$social->source,
	 					"accountId"=>$social->accountId,
	 					"data" => json_encode($social->data)
	 				)
	 			);
	 		
			return $this->db->get_where($this->table, array('userId' => $userId))->row();
		}	
		else{
			return false;
		}
	}

	/**
     * Register login
     * @return array , user
     */
	public function login(){
		$data = $this->input->post();
	 	$response = $this->db->where(array('phoneNo'=>$data['phoneNo'],'countryCode'=>'+'.$data['countryCode']))
	 				->get($this->table)->row(); 
	 	if(!empty($response))
	 		return $response;
	 	else
	 		return false;
	}

	public function socialLogin(){
		$data = $this->input->post();
		$user = $this->db->where(
			array('source'=>$data['source'],'accountId'=>$data['accountId'])
		)->get('social_account')->row();

		if(empty($user))
			return false;

		return $this->db->get_where($this->table, array('userId' => $user->userId))->row();
	}

	/**
     * Verify user in system
     * @return array , user
     */
	public function verify(){
		$data = $this->input->post();
		$isVerify = $data['isVerify']!='' ? $data['isVerify'] : 1 ;
		$where = array('phoneNo'=>$data['phoneNo'],'countryCode'=>'+'.$data['countryCode']);
	 	$this->db->where($where)->update($this->table,array('isVerified'=>$isVerify,'status'=>1));
	 	return $this->login();
	}
} 