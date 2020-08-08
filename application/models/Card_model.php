<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Card_model extends CI_Model {
	var $table ; 
	public function __construct(){
	    parent::__construct(); 
	    $this->table = 'card';
	    $this->mapping = 'card_mapping';
	    $this->users = 'users';
	    $this->fields = 'card_fields';
	    $this->conf = 'card_config';
	    $this->createdAt = date('Y/m/d h:i:s a', time());
	    $this->limit = 10;
	    $this->offset = 0;
	}

	/**
     * retrive card fields
     * @return array , fields
     */
	public function card_fields(){
		return $this->db->query(" select card_fields.* from card_fields , users where  card_fields.status =1 and users.userId = ".$this->input->post('userId')." and card_fields.categoryId in ( 0 , users.categoryId) ")->result();
		return $this->db->select('card_fields.*')
         ->from('card_fields')
         ->join('users', 'users.categoryId = card_fields.categoryId')
         ->where(array('card_fields.status'=>1,'users.userId'=>$this->input->post('userId')))
	 	 ->get()->result();
	}	

	/**
     * select card
     * @return array , card
     */
	public function select_card(){
		$data = $this->input->post();

		$this->db->select('card_config.rawData,card_config.side,card_config.cardImage,card_config.cardVideo,card_config.videoThumbnail,card.userId');
         if(isset($data['side']))
			$this->db->where('side',$data['side']);
         $this->db->join('card', 'card_config.cardId = card.cardId');
		$card = $this->db->where('card_config.cardId',$data['cardId'])->get($this->conf)->result();
		return $this->_sorting_card($card,$data['cardId'],$data['userId']);
	}

	/**
     * fetch cards
     * @return array , card
     */
	public function fetch_cards($data=[]){
		if(empty($data))
			return false;
		$showcase = isset($data['showcase']) && strtolower($data['showcase']) == "true" ? TRUE : FALSE;
		$final_contacts  = array();
		if(isset($data['pageIndex']) && $data['pageIndex']!=0){
			$this->offset = $data['pageIndex'] * $this->limit;
		}
		if($showcase==false){
			$cards_for_limit = $this->db->query("
						SELECT 
						  cardId
						FROM
						    card_config
						WHERE cardId in 
						(select cardId from $this->table where userId = ".$data['userId']." and addedMode != 4 ) group by cardId
						LIMIT ".$this->limit." OFFSET ".$this->offset." 
						") 
						->result_array();
			if(!empty($cards_for_limit)){				
				$cards =  $this->db->query("
							SELECT 
							  cardId,
							  side,
							  cardImage,
							  cardVideo,
							  videoThumbnail,
							  (select userId from card where cardId = card_config.cardId) userId
							FROM
							    card_config
							WHERE cardId in (".implode(',',array_column($cards_for_limit, 'cardId')).") 
							") 
							->result();
			}else{
				$cards = array();
			}				
		}
		else{
			$final_contacts = $this->suggestions($data['userId']);
 			$connections = !empty($final_contacts['connections']) ? $final_contacts['connections'] : [];
 			$final_contacts = !empty($final_contacts['suggestions']) ? $final_contacts['suggestions'] : [];
			if(!empty($final_contacts)){
				$cards_for_limit = $this->db->query("
							SELECT
							  cardId
							FROM
							    card_config
							WHERE cardId in 
							(
								select cardId from $this->table where 
								userId in (".implode(',',$final_contacts).") and isDefault = 1
							)
							group by cardId
							LIMIT ".$this->limit." OFFSET ".$this->offset." 
						")
						->result_array();
				if(!empty($cards_for_limit)){					
					$cards =  $this->db->query("
								SELECT
								  cardId,
								  side,
								  cardImage,
								  cardVideo,
								  videoThumbnail,
								  (select userId from card where cardId = card_config.cardId and addedMode != 4) userId
								FROM
								    card_config
								WHERE cardId in 
								(".implode(',',array_column($cards_for_limit, 'cardId')).") 
							")
							->result();
				}else{
					$cards = array();
				}			
			}
			else{
				$cards_for_limit = $this->db->query("
							SELECT
							  cardId
							FROM
							    card_config
							WHERE cardId not in 
							(select cardId from $this->table where userId = ".$data['userId']." and addedMode != 4 )
							group by cardId
							LIMIT ".$this->limit." OFFSET ".$this->offset." 	
						")
						->result_array();
				echo vd();
				pr($cards_for_limit);
				if(!empty($cards_for_limit)){			
					$cards =  $this->db->query("
								SELECT
								  cardId,
								  side,
								  cardImage,
								  cardVideo,
								  videoThumbnail,
								  (select userId from card where cardId = card_config.cardId and addedMode != 4) userId
								FROM
								    card_config
								WHERE cardId not in 
								(".implode(',',array_column($cards_for_limit, 'cardId')).")
							")
							->result();	
							echo vd();
				}else{
					$cards = array();
				}				
			}
							
		}exit;
		$real_card = $mutualsContacts = array();
		$cards_array = json_decode(json_encode($cards), true);
		pr($cards_array);
		foreach($cards as $card):
			if($card->side==1)
				$real_card[$card->cardId][] = array('frontImage' =>$card->cardImage,'frontVideo'=>$card->cardVideo,'frontVideoThumbnail'=>ltrim($card->videoThumbnail,'.'));
			else
				$real_card[$card->cardId][] = array('backImage' =>$card->cardImage,'backVideo'=>$card->cardVideo,'backVideoThumbnail'=>ltrim($card->videoThumbnail,'.'));

			// if($showcase!=false && !empty($connections))
			// 	$mutualsContacts = array_intersect($this->getMutuals($card->userId),$connections);
			
			// if(!empty($mutualsContacts))
			// 	$mutualsContacts = ($showcase==false) ? [] : 
			// 				$this->db->select('userId,userName,userPhoto,designation,connections,bio,rating')
			// 				->where_in('userId',$mutualsContacts)
			// 				->get('profile')->result();
			 

			//if both side exist with current card
			if(isset($real_card[$card->cardId][0]) && isset($real_card[$card->cardId][1])){
				
				$x = array(
					'cardId'=>$card->cardId,
					'frontImage'=>isset($real_card[$card->cardId][0]['frontImage'])?$real_card[$card->cardId][0]['frontImage']:'',	
					'frontVideo'=>isset($real_card[$card->cardId][0]['frontVideo']) ? $real_card[$card->cardId][0]['frontVideo'] :'',
					'frontVideoThumbnail'=>isset($real_card[$card->cardId][0]['frontVideoThumbnail']) ? $real_card[$card->cardId][0]['frontVideoThumbnail']:'',	
					'backImage'=>isset($real_card[$card->cardId][1]['backImage']) ? $real_card[$card->cardId][1]['backImage'] : '',	
					'backVideo'=> isset($real_card[$card->cardId][1]['backVideo']) ? $real_card[$card->cardId][1]['backVideo']: '',	
					'backVideoThumbnail'=>isset($real_card[$card->cardId][1]['backVideoThumbnail']) ? $real_card[$card->cardId][1]['backVideoThumbnail']: '',	
					'userId' => $card->userId
					//'user' => $this->db->select('userId,userName,userPhoto,designation,connections,bio,rating')
								//->where('userId',$card->userId)->get('profile')->row(),
					//'mutuals' => $mutualsContacts
								 	
				);
				unset($real_card[$card->cardId]);
				$real_card[$card->cardId] = $x;	
			}
			
			//if only front side exist with current card
			elseif($this->check($cards_array, array("cardId",'side'), array($card->cardId,"1")) == true && $this->check($cards_array, array("cardId",'side'), array($card->cardId,"2"))==false && isset($real_card[$card->cardId][0])){
				
				$x = array(
					'cardId'=>$card->cardId,
					'frontImage'=>isset($real_card[$card->cardId][0]['frontImage']) ? $real_card[$card->cardId][0]['frontImage']:'',	
					'frontVideo'=>isset($real_card[$card->cardId][0]['frontVideo']) ? $real_card[$card->cardId][0]['frontVideo'] : '',
					'frontVideoThumbnail'=>isset($real_card[$card->cardId][0]['frontVideoThumbnail']) ? $real_card[$card->cardId][0]['frontVideoThumbnail'] : '',	
					'backImage'=>'',	
					'backVideo'=>'',	
					'backVideoThumbnail'=>'',	
					'userId' => $card->userId
					//'user' => $this->db->select('userId,userName,userPhoto,designation,connections,bio,rating')
								//->where('userId',$card->userId)->get('profile')->row(),
					//'mutuals' =>$mutualsContacts			
				);
				unset($real_card[$card->cardId]);
				$real_card[$card->cardId] = $x;	
			}
			//if only back side exist with current card
			elseif($this->check($cards_array, array("cardId",'side'), array($card->cardId,"1")) == false && $this->check($cards_array, array("cardId",'side'), array($card->cardId,"2"))==true && isset($real_card[$card->cardId][1])){
				$x = array(
					'cardId'=>$card->cardId,
					'frontImage'=>'',	
					'frontVideo'=>'',
					'frontVideoThumbnail'=>'',	
					'backImage'=>isset($real_card[$card->cardId][1]['backImage']) ? $real_card[$card->cardId][1]['backImage'] : '',	
					'backVideo'=>isset($real_card[$card->cardId][1]['backVideo']) ? $real_card[$card->cardId][1]['backVideo'] : '',	
					'backVideoThumbnail'=>isset($real_card[$card->cardId][1]['backVideoThumbnail']) ? $real_card[$card->cardId][1]['backVideoThumbnail']: '',
					'userId' => $card->userId
					//'user' => $this->db->select('userId,userName,userPhoto,designation,connections,bio,rating')
								//->where('userId',$card->userId)->get('profile')->row(),
					//'mutuals' =>$mutualsContacts		
				);
				unset($real_card[$card->cardId]);
				$real_card[$card->cardId] = $x;	
			}
		endforeach;	
		return array_values($real_card);	
	}

	private function suggestions($userId=NULL){
		$suggestions = array();
		
		if($userId==NULL)
			return $suggestions;
		$data['userId'] = $userId;
		$connections = $this->db->query("select DISTINCT(userId) from users where 
		 				userId in 
		 					(SELECT CFROM.toUser FROM `card_bank` CFROM WHERE CFROM.`fromUser` = ".$data['userId']." AND CFROM.`status` = 1)
						OR
						userId in 
						(SELECT CTO.fromUser FROM `card_bank` CTO WHERE CTO.`toUser` = ".$data['userId']." AND CTO.`status` = 1 )")
		 				->result_array();
		if(empty($connections))
			return $suggestions;

		if(!empty($connections))
			$connections = array_column($connections, 'userId');

		$suggestion = $this->db->query("SELECT fromUser,toUser
					FROM `card_bank`
					WHERE (`toUser` in (".implode(',', $connections).")
					OR `fromUser` in (".implode(',', $connections).") )  and toUser!= ".$data['userId']." 
					and fromUser !=  ".$data['userId']." ")
					->result_array(); 
		if(empty($suggestion))
			return $suggestions;

		$suggestion = array_unique(array_merge(array_column($suggestion, 'fromUser'),array_column($suggestion, 'toUser'))); 
		
		return array('suggestions'=>array_diff($suggestion,$connections),'connections'=>$connections);
	}
	private function getMutuals($userId=NULL){
		if($userId==NULL)
			return [];

		$data['userId'] = $userId;
		$connections = $this->db->query("select DISTINCT(userId) from users where 
		 				userId in 
		 					(SELECT CFROM.toUser FROM `card_bank` CFROM WHERE CFROM.`fromUser` = ".$data['userId']." AND CFROM.`status` = 1)
						OR
						userId in 
						(SELECT CTO.fromUser FROM `card_bank` CTO WHERE CTO.`toUser` = ".$data['userId']." AND CTO.`status` = 1 )")
		 				->result_array(); 
		if(!empty($connections)) 				
			return array_column($connections, 'userId');
		else
			return [];
	}

	public function check($array, $keys, $values) {
	    foreach ($array as $item){
	        if ($item[$keys[0]] == $values[0] && $item[$keys[1]] == $values[1])
	            return true;
	    }
	    return false;
	}
	/**
     * sotring card
     * @return array , card
     */

	private function _sorting_card($data,$card_id,$userId){
		$real_card= array();
		$real_card['card_id'] = $card_id;
		foreach($data as $card):
			$real_card['userId'] = $userId;
			if($card->side==1){
				$real_card['frontSide']['rawData'] = $card->rawData;
				$real_card['frontSide']['frontImage'] = $card->cardImage;
				$real_card['frontSide']['frontVideo'] = $card->cardVideo;
				$real_card['frontSide']['frontVideoThumbnail'] =ltrim($card->videoThumbnail,'.');
			}
			if($card->side==2){
				$real_card['backSide']['rawData'] = $card->rawData;
				$real_card['backSide']['backImage'] = $card->cardImage;
				$real_card['backSide']['backVideo'] = $card->cardVideo;
				$real_card['backSide']['backVideoThumbnail'] =ltrim($card->videoThumbnail,'.');
			}
		endforeach;	
		return $real_card;
	}

	/**
     * create card
     * @return array , card
     */
	// if($cardId = $this->db->insert_id()){
	//  		if($this->config_card(array('cardId'=>$cardId,'side'=>1)))
	//  			if($this->config_card(array('cardId'=>$cardId,'side'=>2)))
	//  				$this->db->get_where($this->table, array('cardId' => $cardId))->row();
	//  			else
	//  				return false;	
	//  		else
	//  			return false;	
	// 	else
	// 		return false;
	public function create_card(){
		$data = $this->input->post();
		$data['ipAddress'] = get_client_ip();
        $data['createdAt'] = $this->createdAt;
        $data['createdBy'] = $data['userId'];
	 	$this->db->insert($this->table,$data);
	 	if($cardId = $this->db->insert_id())
	 		if(!empty($this->config_card(array('cardId'=>$cardId,'side'=>1))))
	 			if(!empty($this->config_card(array('cardId'=>$cardId,'side'=>2))))
	 				return $this->db->get_where($this->table, array('cardId' => $cardId))->row();
	 			else
	 				return false;	
	 		else
	 			return false;	
		else
			return false;
	} 

	/**
     * update card
     * @return array , card
     */
	public function update_card($data=array()){
		if(empty($data))
			return false;
		$data['ipAddress'] = get_client_ip();
        $data['updatedBy'] = $data['userId'];
	 	$this->db->where(array('userId'=>$data['userId'],'cardId'=>$data['cardId']))->update($this->table,$data);
	 	if($this->db->affected_rows()>0)
			return $this->db->get_where($this->table,array('userId'=>$data['userId'],'cardId'=>$data['cardId']))->row();
		else
			return false;
	} 

	/**
     * delete card
     * @return array , card
     */
	public function delete_card($data=array()){
		if(empty($data))
			return false;
		$side = isset($data['side']) ? $data['side'] : NULL;
		if($side!=NULL)
			$this->db->where(array('cardId'=>$data['cardId'],'side'=>$side))->delete($this->conf);
		else
			$this->db->where('cardId',$data['cardId'])->delete($this->table);
		return $this->db->affected_rows(); 
	} 

	/**
     * mapping card 
     * @return array , card
     */
	public function map_card(){
		$data = $this->input->post();
		$fieldData = json_validate($data['fieldData'],true);
		foreach($fieldData as $field):
			$map = array(
				'cardId' => $data['cardId'],
				'side' => $data['side'],
				'fieldId' => $field->fieldId,
				'field' => $field->field,
				'value' => $field->value,
				'axis' => json_encode($field->axis),
				'rawData' => json_encode($field->rawData),
				'createdAt'	 => $this->createdAt
			);
	 		$this->db->where(array('cardId'=>$data['cardId'],'side'=>$data['side'] ,'fieldId'=>$map['fieldId']))->update($this->mapping,$map);
	 		if($this->db->affected_rows() == 0 )
				if(!$this->db->insert($this->mapping,$map))
					return false;
		endforeach;	
		return $this->db->get_where($this->mapping, array('cardId' =>$data['cardId']))->result();
	} 
	
	public function config_card($data=array()){
		if(empty($data))
			$data = $this->input->post();
		$conf = array(
			'cardId' => $data['cardId'],
			'side' => $data['side'],
			'bgColor' => isset($data['bgColor']) ? $data['bgColor'] : '' ,			
			'bgImage' => isset($data['bgImage']) ? $data['bgImage'] : '' ,		
			'bgVideo' => isset($data['bgVideo']) ? $data['bgVideo'] : '' ,				
			'cardImage' => isset($data['cardImage']) ? $data['cardImage'] : '' ,				
			'cardVideo' => isset($data['cardVideo']) ? $data['cardVideo'] : '' ,		
			'videoThumbnail' => isset($data['videoThumbnail']) ? $data['videoThumbnail'] : '' ,		
			'rawData' => isset($data['rawData']) ? $data['rawData'] : '' ,		
		);	
		if(empty($this->db->where('cardId',$data['cardId'])->get($this->table)->row()))
			return 404;
		if($this->db->where(array('cardId'=>$data['cardId'],'side'=>$data['side']))->get($this->conf)->row()){
	 		$this->db->where(array('cardId'=>$data['cardId'],'side'=>$data['side']))->update($this->conf,$conf);
		}
		else{
			if(!$this->db->insert($this->conf,$conf))
				return false;
			else
				return $this->db->get_where($this->conf, array('cardId' =>$data['cardId'],'side'=>$data['side']))
				->result();
		}
		return $this->db->get_where($this->conf, array('cardId' =>$data['cardId'],'side'=>$data['side']))
				->result();
	}
 	

 	/**
     * scanned cards
     * @return array , card
     */
	public function scannedCardGet($data=[]){
		if(empty($data))
			return false;
		$cards =  $this->db->query("
						SELECT
						  cardId,
						  side,
						  cardImage,
						  cardVideo,
						  videoThumbnail
						FROM
						    card_config
						WHERE cardId in 
						(select cardId from $this->table where userId = ".$data['userId']." and addedMode = 4 )")
						->result();
		$real_card = array();
		foreach($cards as $card):
			if($card->side==1)
				$real_card[$card->cardId][] = array('frontImage' =>$card->cardImage,'frontVideo'=>$card->cardVideo,'frontVideoThumbnail'=>$card->videoThumbnail);
			else
				$real_card[$card->cardId][] = array('backImage' =>$card->cardImage,'backVideo'=>$card->cardVideo,'backVideoThumbnail'=>$card->videoThumbnail);  
			if(isset($real_card[$card->cardId][0]) && isset($real_card[$card->cardId][1])){
				$x = array(
					'cardId'=>$card->cardId,
					'frontImage'=>$real_card[$card->cardId][0]['frontImage'],	
					'frontVideo'=>$real_card[$card->cardId][0]['frontVideo'],
					'frontVideoThumbnail'=>$real_card[$card->cardId][0]['frontVideoThumbnail'],	
					'backImage'=>$real_card[$card->cardId][1]['backImage'],	
					'backVideo'=>$real_card[$card->cardId][1]['backVideo'],	
					'backVideoThumbnail'=>$real_card[$card->cardId][1]['backVideoThumbnail'],	
				);
				unset($real_card[$card->cardId]);
				$real_card[$card->cardId] = $x;	
			}
		endforeach;	
		return array_values($real_card);	
	}

} 