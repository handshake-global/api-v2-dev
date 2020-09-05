<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bank_model extends CI_Model
{
    var $table;
    public function __construct()
    {
        parent::__construct();
        $this->table = 'card_bank';
        $this->card = 'card';
        $this->mapping = 'card_mapping';
        $this->users = 'users';
        $this->fields = 'card_fields';
        $this->conf = 'card_config';
        $this->createdAt = date('Y/m/d h:i:s a', time());
        $this->limit = 10;
	    $this->offset = 0;
    }

    public function init_cardRequest()
    {
        $data = $this
            ->input
            ->post();
        $data['createdAt'] = $this->createdAt;
        $data['createdBy'] = $data['fromUser'];

        $fromCard = $this
            ->db
            ->select('cardId')
            ->where(array(
            'isDefault' => 1,
            'userId' => $data['fromUser']
        ))->limit(1)
            ->order_by('cardId', 'desc')
            ->get('card')
            ->row();

        $toCard = $this
            ->db
            ->select('cardId')
            ->where(array(
            'isDefault' => 1,
            'userId' => $data['toUser']
        ))->limit(1)
            ->order_by('cardId', 'desc')
            ->get('card')
            ->row();
            
        if (empty($fromCard) or empty($toCard)) return false;
        $data['cardId'] = $fromCard->cardId;
        $data['targetCardId'] = $toCard->cardId;

        $ifRequested = $this->db->select("cardId")
                        ->where('cardId',$data['cardId'])
                        ->where('targetCardId',$data['targetCardId'])
                        ->where('targetCardId',$data['targetCardId'])
                        ->where('fromUser',$data['fromUser'])
                        ->where('toUser',$data['toUser'])
                        ->where('cardType',$data['cardType'])
                        ->where('status!=',3)
                        ->get($this->table)
                        ->num_rows();
        if($ifRequested)
           return false;                 

        $this
            ->db
            ->insert($this->table, $data);
        if ($requestId = $this
            ->db
            ->insert_id())
        {
            return $this->db->where('bankId',$requestId)
            ->get($this->table)->row_array();
        }
        else
        {
            return false;
        }
    }

    public function modify_cardRequest($status = 0)
    {
        $data = $this
            ->input
            ->post();
        $this
            ->db
            ->where(array(
            'bankId' => $data['bankId']
        ))->update($this->table, array(
            'status' => $status
        ));
        if ($this
            ->db
            ->affected_rows() > 0)
        {
            //iff  reqeuest accepted transfer atachement ot mes
            if($status==1){
                $msgBank = $this->db->select('note,fromUser,toUser,attachment,attachmentType')
                           ->where('bankId', $data['bankId'])
                           ->get($this->table)
                           ->row_array();
                if(!empty($msgBank))
                    $this->db->insert('messages',array(
                       'sender'=>$msgBank['fromUser'],     
                       'receiver'=>$msgBank['toUser'],     
                       'message'=>$msgBank['note'],     
                       'type'=>$msgBank['attachmentType'],     
                       'file'=>$msgBank['attachment']     
                    ));           
            }

            $request = $this
                ->db
                ->where('toUser', $data['userId'])->get($this->table)
                ->result_array();
            $clean = array_map(function (array $elem)
            {
                unset($elem['updatedBy']);
                unset($elem['updatedAt']);
                unset($elem['createdBy']);
                unset($elem['createdAt']); // modify $elem
                return $elem; // and return it to be put into the result
                
            }
            , $request);
            return $clean;
        }
        else
        {
            return false;
        }
    }

    public function get_cardBank($data = [], $status = 0,$cardType = NULL)
    {
        if (empty($data)) return false;

        if ($status == 0)
        {   
            if(isset($data['pageIndex']) && $data['pageIndex']!=0){
                $this->offset = $data['pageIndex']* $this->limit;
            }

            $request = $this
                ->db
                ->select('card_bank.*,users.userId,
						users.firstName,users.lastName,users.email,users.phoneNo,users.countryCode,users.avatar,user_details.designation')
                ->where(array(
                'card_bank.toUser' => $data['userId'],
                'card_bank.status' => $status
            ))
                ->join('users', 'card_bank.fromUser=users.userId')
                ->join('user_details', 'card_bank.fromUser=user_details.userId','left')
                ->order_by('users.firstName')
                ->limit($this->limit,$this->offset)
                ->get($this->table)
                ->result_array(); 
        }
        else
        {
        	if(isset($data['pageIndex']) && $data['pageIndex']!=0){
				$this->offset = $data['pageIndex']* $this->limit;
			}

            $search_keyword = isset($data['keyword']) ? ($data['keyword']) : '';
            $myCard = "SELECT `card_bank`.`bankId`, `card_bank`.`targetCardId` as `cardId`, `card_bank`.`status`, `card_bank`.`updatedBy`,`card_bank`.`updatedAt`, `card_bank`.`createdBy`, `card_bank`.`createdAt`, `card_bank`.`note`, `card_bank`.`attachment`,`users`.`userId`, `users`.`firstName`, `users`.`lastName`, `users`.`email`, `users`.`phoneNo`, `users`.`countryCode`, `users`.`avatar`, `user_details`.`designation`
			FROM `card_bank`
			JOIN `users` ON `card_bank`.`toUser`=`users`.`userId`
			LEFT JOIN `user_details` ON `card_bank`.`toUser`=`user_details`.`userId`
			WHERE `card_bank`.`fromUser` = " . $data['userId'] . "
			AND `card_bank`.`status` = " . $status . " ";
            if($cardType!=NULL)
                $myCard .=" and card_bank.cardType = $cardType ";

            if ($search_keyword != '')
            {
                $myCard .= " AND (`users`.`firstName` LIKE '".$search_keyword."%' ESCAPE '!'
				OR `users`.`lastName` LIKE '".$search_keyword."%' ESCAPE '!' ) ";
            }
            $myCard .= " 
				ORDER BY `users`.`firstName` 
				LIMIT ".$this->limit." OFFSET ".$this->offset." ";
            $myCard = $this
                ->db
                ->query($myCard)->result_array();
    echo vd();
        $otherCard = array(); 
         
        if(count($myCard)<$this->limit){    
        	$this->limit = $this->limit-(int)count($myCard);    
            $otherCard = "SELECT `card_bank`.`bankId`, `card_bank`.`cardId` as `cardId`, `card_bank`.`status`, `card_bank`.`updatedBy`,`card_bank`.`updatedAt`, `card_bank`.`createdBy`, `card_bank`.`createdAt`, `card_bank`.`note`, `card_bank`.`attachment`,`users`.`userId`, `users`.`firstName`, `users`.`lastName`, `users`.`email`, `users`.`phoneNo`, `users`.`countryCode`,`users`.`avatar`, `user_details`.`designation`
			FROM `card_bank`
			JOIN `users` ON `card_bank`.`fromUser`=`users`.`userId`
			LEFT JOIN `user_details` ON `card_bank`.`toUser`=`user_details`.`userId`
			WHERE `card_bank`.`toUser` = '" . $data['userId'] . "'
			AND `card_bank`.`status` = " . $status . " ";
            if($cardType!=NULL)
                $otherCard .=" and card_bank.cardType = $cardType ";

            if ($search_keyword != '')
            {
                $otherCard .= " AND (`users`.`firstName` LIKE '".$search_keyword."%' ESCAPE '!'
				OR `users`.`lastName` LIKE '".$search_keyword."%' ESCAPE '!') ";
            }
            $otherCard .= " 
						ORDER BY `users`.`firstName`
						LIMIT ".$this->limit." OFFSET ".$this->offset." ";

            $otherCard = $this
                ->db
                ->query($otherCard)->result_array();
		}	
        echo vd();
            $request = array_merge($myCard, $otherCard);
        }
        if (!empty($request))
        {
            $clean = array_map(function (array $elem)
            {
                //filter user
                $elem['user'] = array(
                    'userId' => $elem['userId'],
                    'firstName' => $elem['firstName'],
                    'lastName' => $elem['lastName'],
                    'email' => $elem['email'],
                    'countryCode' => $elem['countryCode'],
                    'phoneNo' => $elem['phoneNo'],
                    'avatar' => $elem['avatar'],
                    'designation' => $elem['designation'],
                );
                unset($elem['updatedBy']);
                // unset($elem['updatedAt']);
                unset($elem['createdBy']);
                unset($elem['userId']);
                unset($elem['designation']);
                unset($elem['firstName']);
                unset($elem['lastName']);
                unset($elem['email']);
                unset($elem['countryCode']);
                unset($elem['phoneNo']);
                unset($elem['avatar']);
                // unset($elem['createdAt']);        // modify $elem
                return $elem; // and return it to be put into the result
                
            }
            , $request);
            function compareByName($a, $b)
            {
                return strcmp($a['user']["firstName"], $b['user']["firstName"]);
            }
            usort($clean, 'compareByName');
            pr($clean);
            return $clean;
        }
        else
        {
            return false;
        }
    }

    public function shareCard()
    {
        $data = $this
            ->input
            ->post();
        $request = array();
        $request['createdAt'] = $this->createdAt;
        $request['createdBy'] = $data['userId'];

        $mobileNos = explode(',', $this
            ->input
            ->post('mobileNo'));
        if (empty($mobileNos)) return false;
        $fromUser = $notExist = array();
        foreach ($mobileNos as $mobile)
        {
            $mobile = str_replace('+', '', $mobile);
            $mobile = explode('-', $mobile);
            $countryCode = '+' . $mobile[0];
            if(isset($mobile[1])){
                $completeMobile = $countryCode . $mobile[1];
                $mobile = $mobile[1];
            }
            else{
                $completeMobile = $countryCode;
                $mobile = $mobile[0];
            }
            //see if user exist with mobile no in already card bank
            $user = $this
                ->db
                ->query("select toUser from card_bank where fromUser = " . $data['userId'] . " and toUser = (SELECT userId FROM `users` WHERE (countryCode ='" . $countryCode . "' and phoneNo = '" . $mobile . "') OR (CONCAT(countryCode,phoneNo) = '" . $completeMobile . "')) ")->row();

            //if user empty send card request
            if (empty($user))
            {
                $toCard = $this
                    ->db
                    ->query("select userId, addedMode, cardId from card where userId = (SELECT userId FROM `users`  WHERE (countryCode ='" . $countryCode . "' and phoneNo = '" . $mobile . "') OR (CONCAT(countryCode,phoneNo) = '" . $completeMobile . "')) limit 1")->row();
                // user exist not exist in sytem with given mobile no send sms
                if (empty($toCard))
                {
                    $notExist[] = $mobile;
                    $this->sendMsg();
                }
                // send card reqeust
                else
                {   
                    $request['fromUser'] = $data['userId'];
                    $request['toUser'] = $toCard->userId;
                    $request['cardId'] = $data['cardId'];
                    $request['targetCardId'] = $toCard->cardId;
                    $request['cardType'] = $toCard->addedMode;
                    $request['note'] = isset($data['note']) ?  $data['note'] : "Requested cause of mutual contact";
                    $request['attachment'] = isset($data['attachment'])?$data['attachment'] : '';

                    $this
                        ->db
                        ->insert($this->table, $request);
                    if (!$requestId = $this
                        ->db
                        ->insert_id()) return false;
                }
            }
        }
        //delete later on cards
        $this->shareLaterDelete($data);
        if(!empty($notExist))
            return $notExist;
        else
            return true;
    }

    private function sendMsg()
    {
        return true;
    }

    /** share card later **/
    public function shareLater()
    {
        $data = $this
            ->input
            ->post();
        $data['createdAt'] = $this->createdAt;
        $this
            ->db
            ->insert('shareCard_later', $data);
        if ($laterId = $this
            ->db
            ->insert_id()) if ($cards = $this
            ->db
            ->where('laterId', $laterId)->get('shareCard_later')
            ->row()) return $cards;
        else return false;
        else return false;
    }

    /**
     * get later on card
     * @return array , card
     */
    public function shareLaterGet($data = array())
    {
        if (empty($data)) return false;
        if ($cards = $this
            ->db
            ->where($data)->get('shareCard_later')
            ->row()) return $cards;
        else return false;
    }

    /**
     * delete card
     * @return array ,card
     */
    public function shareLaterDelete($data = array())
    {
        if (empty($data)) return false;
        if ($this
            ->db
            ->where(array(
            'cardId' => $data['cardId'],
            'userId' => $data['userId']
        ))->delete('shareCard_later')) return $this
            ->db
            ->affected_rows();
        else return false;
    }

    /**
     * update share later on  card
     * @return array ,card
     */
    public function shareLaterPut($data = array())
    {
        if (empty($data)) return false;
        $this
            ->db
            ->where(array(
            'userId' => $data['userId'],
            'cardId' => $data['cardId']
        ))->update('shareCard_later', array(
            'mobileNo' => $data['mobileNo'],
            'cardId' => $data['cardId']
        ));
        if ($this
            ->db
            ->affected_rows() > 0) return $this
            ->db
            ->get_where('shareCard_later', array(
            'userId' => $data['userId'],
            'cardId' => $data['cardId']
        ))->row();
        else return false;
    }

    public function getConnections($data = [])
    {
        if (empty($data)) return false;

        $status = 1;
        $cardBankUserFrom = $this
            ->db
            ->select("profile.userId as userId ,profile.userName, profile.userPhoto,profile.isLogin,profile.connections,profile.designation")
            ->where(array(
            'card_bank.toUser' => $data['userId'],
            'card_bank.status' => $status
        ))->join('profile', 'card_bank.fromUser=profile.userId')
            ->group_by('card_bank.fromUser')
            ->get($this->table)
            ->result_array();

        $cardBankUserTo = $this
            ->db
            ->select("profile.userId as userId ,profile.userName, profile.userPhoto,profile.isLogin,profile.connections,profile.designation")
            ->where(array(
            'card_bank.fromUser' => $data['userId'],
            'card_bank.status' => $status
        ))->join('profile', 'card_bank.toUser=profile.userId')
            ->group_by('card_bank.toUser')
            ->get($this->table)
            ->result_array();

        return array_merge($cardBankUserTo, $cardBankUserFrom);
    }

    public function sentCardRequest($data=NULL){
        if($data == NULL)
            return false;
        if(isset($data['pageIndex']) && $data['pageIndex']!=0){
                $this->offset = $data['pageIndex']* $this->limit;
            }
       $request = $this
                ->db
                ->select('card_bank.*,users.userId,
                        users.firstName,users.lastName,users.email,users.phoneNo,users.countryCode,users.avatar,user_details.designation')
                ->where(array(
                'card_bank.fromUser' => $data['userId'],
                'card_bank.status' => 0
            ))
                ->join('users', 'card_bank.toUser=users.userId')
                ->join('user_details', 'card_bank.toUser=user_details.userId','left')
                ->order_by('users.firstName')
                ->limit($this->limit,$this->offset)
                ->get($this->table)
                ->result_array(); 

        if (!empty($request))
        {
            $clean = array_map(function (array $elem)
            {
                //filter user
                $elem['user'] = array(
                    'userId' => $elem['userId'],
                    'firstName' => $elem['firstName'],
                    'lastName' => $elem['lastName'],
                    'email' => $elem['email'],
                    'countryCode' => $elem['countryCode'],
                    'phoneNo' => $elem['phoneNo'],
                    'avatar' => $elem['avatar'],
                    'designation' => $elem['designation'],
                );
                unset($elem['updatedBy']);
                // unset($elem['updatedAt']);
                unset($elem['createdBy']);
                unset($elem['userId']);
                unset($elem['designation']);
                unset($elem['firstName']);
                unset($elem['lastName']);
                unset($elem['email']);
                unset($elem['countryCode']);
                unset($elem['phoneNo']);
                unset($elem['avatar']);
                // unset($elem['createdAt']);        // modify $elem
                return $elem; // and return it to be put into the result
                
            }
            , $request);
            function compareByName($a, $b)
            {
                return strcmp($a['user']["firstName"], $b['user']["firstName"]);
            }
            usort($clean, 'compareByName');
            return $clean;
        }        
    }

     /**
     * delete card
     * @return array ,card
     */
    public function deleteConnection($data = array())
    {
        if (empty($data)) return false;
        if ($this
            ->db
            ->where(array(
            'bankId' => $data['bankId'],
        ))->delete($this->table)) return $this
            ->db
            ->affected_rows();
        else return false;
    }            
}

