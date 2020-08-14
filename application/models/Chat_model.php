<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'messages';
	    $this->bank = 'card_bank';
	    $this->fcm = 'fcm_tokens';
	    $this->users = 'users';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	    $this->limit = 10;
	    $this->offset = 0;
	}

	public function sendMessage(){
		$data = $this->input->post();
		$data['createdAt'] = $this->createdAt;
		$this->db->insert($this->table,$data);
		if($messageId = $this->db->insert_id())
			return $this->db->select("messages.sender, messages.receiver, DATE_FORMAT(messages.createdAt, '%Y-%m-%d') date,messages.file as fileUrl, messages.message as msgContent ,users.avatar as userPhoto,DATE_FORMAT(messages.createdAt,'%H:%i:%s') time,messages.messageId,messages.status as bRead, messages.createdAt as dateTime,concat(users.firstName,' ',users.lastName) as userName, users.userId,messages.type as mimeType, messages.status as messageStatus")
		->where('messages.messageId',$messageId)
		->join('users', 'messages.sender = users.userId')
		->limit(1)
		->get($this->table)->row();
		else
			return false;
	} 

	public function getMessage($data=NULL){
		if(empty($data))
			return false;
		if(isset($data['pageIndex']) && $data['pageIndex']!=0){
			$this->offset = ($data['pageIndex']*$this->limit)+1;
			unset($data['pageIndex']);
		}
		
		//status update
		$this->db->where('messages.sender',$data['receiver'])
		->where('messages.receiver',$data['sender'])
		->update($this->table,array('status'=>3));

		$sendSide =  $this->db->select("DATE_FORMAT(messages.createdAt, '%Y-%m-%d') date,messages.file as fileUrl, messages.message as msgContent ,users.avatar as userPhoto,DATE_FORMAT(messages.createdAt,'%H:%i:%s') time,messages.messageId,messages.status as bRead, messages.createdAt as dateTime,concat(users.firstName,' ',users.lastName) as userName, users.userId,messages.type as mimeType, messages.status as messageStatus,messages.delReceiver , messages.delSender")
		->where('messages.sender',$data['sender'])
		->where('messages.receiver',$data['receiver'])
		->join('users', 'messages.sender = users.userId')
		->order_by('messageId','desc')
		->limit($this->limit, $this->offset)
		->get($this->table)->result_array();

		$receiveSide = $this->db->select("DATE_FORMAT(messages.createdAt, '%Y-%m-%d') date,messages.file as fileUrl, messages.message as msgContent ,users.avatar as userPhoto,DATE_FORMAT(messages.createdAt,'%H:%i:%s') time,messages.messageId,messages.status as bRead, messages.createdAt as dateTime,concat(users.firstName,' ',users.lastName) as userName, users.userId,messages.type as mimeType, messages.status as messageStatus,messages.delReceiver , messages.delSender")
		->where('messages.receiver',$data['sender'])
		->where('messages.sender',$data['receiver'])
		->join('users', 'messages.sender = users.userId')
		->order_by('messageId','desc')
		->limit($this->limit, $this->offset)
		->get($this->table)->result_array();

		function date_compare($a, $b)
		{
		    $t1 = strtotime($a['dateTime']);
		    $t2 = strtotime($b['dateTime']);
		    return $t2-$t1;
		}    
		$temp = array_merge($sendSide,$receiveSide);
		usort($temp, 'date_compare');

		return $temp;
	}


	public function getConnections($data=[]){
		if(empty($data))
			return false;
		 	
		 	$status = 1;
			$cardBankUserFrom = $this->db->select("profile.userId ,profile.userName, profile.userPhoto,profile.isLogin,profile.designation,card_bank.status")
					 ->where(
					 	array(
					 		'card_bank.toUser'=>$data['userId'],
					 	)
					 )
					 ->where_in("status",array(1,3))
			         ->join('profile', 'card_bank.fromUser=profile.userId')
			         ->group_by('card_bank.fromUser')
			         ->get($this->bank)->result_array();

			$cardBankUserTo = $this->db->select("profile.userId ,profile.userName, profile.userPhoto,profile.isLogin,profile.designation,card_bank.status")
					 ->where(
					 	array(
					 		'card_bank.fromUser'=>$data['userId'],
					 	)
					 )
					 ->where_in("status",array(1,3))
			         ->join('profile', 'card_bank.toUser=profile.userId')
			         ->group_by('card_bank.toUser')
			         ->get($this->bank)->result_array();         
			
			$allConnections = array_merge($cardBankUserTo,$cardBankUserFrom);
			
			$receivers = array_merge(array_column($cardBankUserFrom, 'userId'),array_column($cardBankUserTo, 'userId'));

			$sentMgs = $this->db->query("SELECT tbl.messageId, tbl.message as lastMessage,tbl.file as fileUrl, tbl.createdAt as 				lastMessageTime , tbl.status, 'sent' as 'msgType',  profile.userId ,profile.userName, profile.					userPhoto,profile.isLogin,profile.designation FROM
						(SELECT * FROM messages WHERE `sender` = ".$data['userId']." GROUP BY messageId
						ORDER BY messageId DESC) as tbl,profile where profile.userId = tbl.receiver
						GROUP BY tbl.receiver order by messageId desc")->result_array();
 
			$untouchedConnections = array();
			$untouchedConnections =  array_diff($receivers,array_column($sentMgs, 'userId'));

			$receivedMsgs = array();
			if(!empty($receivers))			
				$receivedMsgs = $this->db->query("SELECT tbl.messageId, tbl.message as lastMessage,tbl.file as fileUrl, tbl.createdAt as lastMessageTime , tbl.status , 'received' as 'msgType',profile.userId ,profile.userName, profile.					userPhoto,profile.isLogin,profile.designation   FROM
							(SELECT * FROM messages WHERE `receiver` = ".$data['userId']." 
							AND `sender` IN(".implode(',',$receivers).") GROUP BY messageId
							ORDER BY messageId DESC) as tbl,profile where profile.userId = tbl.sender
							GROUP BY tbl.sender order by messageId desc")->result_array(); 	
			
			$untouchedConnections =  array_diff($untouchedConnections,array_column($receivedMsgs, 'userId'));
			
			$finalConnection = array_merge($sentMgs,$receivedMsgs);
			$finalConnectionUsers = array_unique(array_column($finalConnection, 'userId'));
			$temp = array();
			foreach ($finalConnection as $final) {
				if(array_key_exists($final['userId'],$temp)){
					$tempDate = new DateTime($temp[$final['userId']]['lastMessageTime']);
					$finalDate = new DateTime($final['lastMessageTime']);
					if($finalDate > $tempDate)
						$temp[$final['userId']] = $final;
				}else{
					$temp[$final['userId']] = $final;
				}
				if($temp[$final['status']]==1)
					$temp[$final['hasConnection']] =1;
				else	 
					$temp[$final['hasConnection']] =0; 
			}

			$connectionWithNoMsg = array();
			$i = 0;
			foreach ($allConnections as $con) {
			 	if(in_array($con['userId'],$untouchedConnections)){
			 		$connectionWithNoMsg[$i]['messageId'] = '';
			 		$connectionWithNoMsg[$i]['lastMessage'] = '';
			 		$connectionWithNoMsg[$i]['fileUrl'] = '';
			 		$connectionWithNoMsg[$i]['lastMessageTime'] =''; 
			 		$connectionWithNoMsg[$i]['userId'] = $con['userId'] ;
			 		$connectionWithNoMsg[$i]['userName'] = $con['userName'];
			 		$connectionWithNoMsg[$i]['userPhoto'] = $con['userPhoto'];
			 		$connectionWithNoMsg[$i]['isLogin'] = $con['isLogin'];
			 		$connectionWithNoMsg[$i]['designation'] = $con['designation'];
			 		if($con['status']==1)
						$connectionWithNoMsg[$i]['hasConnection'] =1;
					else	 
						$connectionWithNoMsg[$i]['hasConnection'] =0; 
			 	}
			 $i++;	
			}
			return array_merge($temp,$connectionWithNoMsg);
	}

	public function loginStatus(){
		$data = $this->input->post();
		$this->db->where('userId',$data['userId'])->update('users',array('loggedIn'=>$data['status']));
		if($this->db->affected_rows()>0)
			return true;
		else
			return false;
	}

	public function markMessageRead($data = NULL){
		if(empty($data))
			return false;
		$this->db->where($data)->update($this->table,array('status'=>1));
		if($this->db->affected_rows()>0)
			return true;
		else
			return false;
	}

	public function changeMessageStatus($data = NULL){
		if(empty($data))
			return false;
		$messageId = $data['messageId'];
		$this->db->where('messageId',$data['messageId'])->update($this->table,array('status'=>$data['status']));
		if($this->db->affected_rows()>0)
			return $this->db->select("messages.sender, messages.receiver,messages.messageId, messages.status as messageStatus")
		->where('messages.messageId',$messageId)
		->join('users', 'messages.sender = users.userId')
		->limit(1)
		->get($this->table)->row();
		else
			return false;
	}

	public function changeMessageStatusBulk($data = NULL){
		if(empty($data))
			return false;
		$updated =false;
		$this->db->where('sender',$data['sender'])
				->where('receiver',$data['receiver'])
				->update($this->table,array('status'=>$data['status']));
		if($this->db->affected_rows()>0)
			$updated = true;		
		
		$this->db->where('sender',$data['receiver'])
				->where('receiver',$data['sender'])
				->update($this->table,array('status'=>$data['status']));
		if($this->db->affected_rows()>0)
			$updated = true;
					
		if($updated)
			return $data['sender'];
		else
			return false;
	}

	public function deleteMessage(){
		$data = $this->input->post();
		$this->db->where('messageId',$data['messageId'])
				->update('messages',$data);
		if($this->db->affected_rows()>0)
			return true;
		else
			return false;			
	}

} 