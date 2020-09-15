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


	public function getMessageList($data=[]){
		if(empty($data))
			return false;
		 	
		 	$status = 1;
			$cardBankUserFrom = $this->db->select("`users`.`userId`,concat(users.firstName,' ',users.lastName) as userName,
  							  `users`.`avatar` as `userPhoto`,`users`.loggedIn as `isLogin`,`user_details`.`designation`, `card_bank`.`status`")
					 ->where(
					 	array(
					 		'card_bank.toUser'=>$data['userId'],
					 	)
					 )
					 ->where_in("card_bank.status",array(1,3))
			         ->join('users', 'card_bank.fromUser=users.userId')
			         ->join('user_details', 'card_bank.fromUser=user_details.userId')
			         ->group_by('card_bank.fromUser')
			         ->order_by('card_bank.cardId','desc')
			         ->get($this->bank)->result_array();
			         echo vd();

			$cardBankUserTo = $this->db->select("`users`.`userId`,concat(users.firstName,' ',users.lastName) as userName,
  							  `users`.`avatar` as `userPhoto`,`users`.loggedIn as `isLogin`,`user_details`.`designation`, `card_bank`.`status`")
					 ->where(
					 	array(
					 		'card_bank.fromUser'=>$data['userId'],
					 	)
					 )
					 ->where_in("card_bank.status",array(1,3))
			         ->join('users', 'card_bank.toUser=users.userId')
			         ->join('user_details', 'card_bank.toUser=user_details.userId')
			         ->group_by('card_bank.toUser')
			         ->order_by('card_bank.cardId','desc')
			         ->get($this->bank)->result_array();         
					echo vd();
			$allConnections = array_merge($cardBankUserTo,$cardBankUserFrom);
			
			$receivers = array_merge(array_column($cardBankUserFrom, 'userId'),array_column($cardBankUserTo, 'userId'));
			$sentMgs = $this->db->query("SELECT tbl.messageId,tbl.message AS lastMessage,tbl.file AS fileUrl,tbl.createdAt AS lastMessageTime,tbl.status,'sent' AS 'msgType',`users`.`userId`,concat(users.firstName,' ',users.lastName) as userName,`users`.`avatar` as `userPhoto`,`users`.loggedIn as `isLogin`,`user_details`.`designation` ,(SELECT `card_bank`.`status` FROM card_bank WHERE card_bank.fromUser = 10005 AND card_bank.toUser = users.userId GROUP BY users.userId) s1, ( SELECT `card_bank`.`status` FROM card_bank WHERE card_bank.toUser = 10005 AND card_bank.fromUser = users.userId GROUP BY users.userId) s2 FROM (SELECT * FROM messages WHERE `sender`=".$data['userId']." GROUP BY messageId ORDER BY messageId DESC) AS tbl,users,user_details WHERE users .userId=tbl.receiver and user_details.userId = tbl.receiver GROUP BY tbl.receiver ORDER BY messageId DESC")->result_array();
echo vd();
			$untouchedConnections = array();
			$untouchedConnections =  array_diff($receivers,array_column($sentMgs, 'userId'));

			$receivedMsgs = array();
			
			if(!empty($receivers))			
				$receivedMsgs = $this->db->query("SELECT tbl.messageId,tbl.message AS lastMessage,tbl.file AS fileUrl,tbl.createdAt AS lastMessageTime,tbl.status,'received' AS 'msgType',`users`.`userId`,concat(users.firstName,' ',users.lastName) as userName,`users`.`avatar` as `userPhoto`,`users`.loggedIn as `isLogin`,`user_details`.`designation`,(SELECT `card_bank`.`status` FROM card_bank WHERE card_bank.fromUser = 10005 AND card_bank.toUser = users.userId GROUP BY users.userId) s1, ( SELECT `card_bank`.`status` FROM card_bank WHERE card_bank.toUser = 10005 AND card_bank.fromUser = users.userId GROUP BY users.userId) s2  FROM (SELECT * FROM messages WHERE `receiver`=".$data['userId']." AND `sender` IN (".implode(',',$receivers).") GROUP BY messageId ORDER BY messageId DESC) AS tbl,users,user_details WHERE users .userId=tbl.sender and user_details.userId = tbl.sender GROUP BY tbl.sender ORDER BY messageId DESC")->result_array(); 	

			echo vd();
			$untouchedConnections =  array_diff($untouchedConnections,array_column($receivedMsgs, 'userId'));
			
			$finalConnection = array_merge($sentMgs,$receivedMsgs);
			$finalConnectionUsers = array_unique(array_column($finalConnection, 'userId'));
			$temp = array();
			foreach ($finalConnection as $final) {
				if($final['status']==1 || $final['status']==3)
					$final['hasConnection'] =1;
				else	 
					$final['hasConnection'] =0;

				if(array_key_exists($final['userId'],$temp)){
					$tempDate = new DateTime($temp[$final['userId']]['lastMessageTime']);
					$finalDate = new DateTime($final['lastMessageTime']);
					if($finalDate > $tempDate)
						$temp[$final['userId']] = $final;
				}else{
					$temp[$final['userId']] = $final;
				}
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
			 		if($con['status']==1 || $con['status']==3)
						$connectionWithNoMsg[$i]['hasConnection'] =1;
					else	 
						$connectionWithNoMsg[$i]['hasConnection'] =0; 
			 	}
			 $i++;	
			}
			return array_merge($temp,$connectionWithNoMsg);
	}

	public function getConnections($data=[]){
		if(empty($data))
			return false;
		 	
		 	$status = 1;
			$cardBankUserFrom = $this->db->select("`users`.`userId`,concat(users.firstName,' ',users.lastName) as userName,
  							  `users`.`avatar` as `userPhoto`,`users`.loggedIn as `isLogin`,`user_details`.`designation`, `card_bank`.`status`, ((select count(distinct `card_bank`.`toUser`) from `card_bank` where ((`card_bank`.`fromUser` = `users`.`userId`) and (`card_bank`.`status` = 1))) + (select count(distinct `card_bank`.`fromUser`) from `card_bank` where ((`card_bank`.`toUser` = `users`.`userId`) and (`card_bank`.`status` = 1)))) AS `connections` ")
					 ->where(
					 	array(
					 		'card_bank.toUser'=>$data['userId'],
					 	)
					 )
					 ->where("card_bank.status",1)
			         ->join('users', 'card_bank.fromUser=users.userId')
			         ->join('user_details', 'card_bank.fromUser=user_details.userId')
			         ->group_by('card_bank.fromUser')
			         ->get($this->bank)->result_array();

			$cardBankUserTo = $this->db->select("`users`.`userId`,concat(users.firstName,' ',users.lastName) as userName,
  							  `users`.`avatar` as `userPhoto`,`users`.loggedIn as `isLogin`,`user_details`.`designation`, `card_bank`.`status`, ((select count(distinct `card_bank`.`toUser`) from `card_bank` where ((`card_bank`.`fromUser` = `users`.`userId`) and (`card_bank`.`status` = 1))) + (select count(distinct `card_bank`.`fromUser`) from `card_bank` where ((`card_bank`.`toUser` = `users`.`userId`) and (`card_bank`.`status` = 1)))) AS `connections` ")
					 ->where(
					 	array(
					 		'card_bank.fromUser'=>$data['userId'],
					 	)
					 )
					 ->where("card_bank.status",1)
			         ->join('users', 'card_bank.toUser=users.userId')
			         ->join('user_details', 'card_bank.toUser=user_details.userId')
			         ->group_by('card_bank.toUser')
			         ->get($this->bank)->result_array();         

		$allConnections = array_merge($cardBankUserTo,$cardBankUserFrom);
		return  array_values(array_map("unserialize", array_unique(array_map("serialize", $allConnections))));

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