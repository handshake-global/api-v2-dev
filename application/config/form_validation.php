<?php
$config = array(
	'register' => array(
		array(
		'field' => 'firstName',
		'label' => 'First Name',
		'rules' => 'required'
		),
		array(
		'field' => 'lastName',
		'label' => 'Last Name',
		'rules' => 'required'
		),
		array(
		'field' => 'email',
		'label' => 'Email Address',
		'rules' => 'valid_email|is_unique[users.email]',
		"errors" => [
            			'is_unique' => '%s is already taken.',
        			]
		),
		array(
		'field' => 'phoneNo',
		'label' => 'Phone Number',
		'rules' => 'required|numeric|min_length[6]|max_length[15]|is_unique[users.phoneNo]',
		"errors" => [
            			'is_unique' => '%s is already taken.',
        			]
		),
		array(
		'field' => 'countryCode',
		'label' => 'Country Code',
		'rules' => 'required'
		),
		array(
		'field' => 'latitude',
		'label' => 'Latitude',
		'rules' => 'required'
		),
		array(
		'field' => 'longitude',
		'label' => 'Longitude',
		'rules' => 'required'
		),
		array(
		'field' => 'longitude',
		'label' => 'Longitude',
		'rules' => 'required'
		),
		array(
		'field' => 'source',
		'label' => 'Source',
		'rules' => 'required'
		),
		array(
		'field' => 'deviceId',
		'label' => 'Device ID',
		'rules' => 'required'
		),
		array(
		'field' => 'deviceType',
		'label' => 'Device Type',
		'rules' => 'required'
		)
	),
	//login validation Method: auth/login
	'login' => array(
		array(
		'field' => 'countryCode',
		'label' => 'Country Code',
		'rules' => 'required'
		),
		array(
		'field' => 'phoneNo',
		'label' => 'Phone No',
		'rules' => 'required'
		),
		array(
		'field' => 'password',
		'label' => 'password',
		'rules' => 'required'
		)
	),
	//login validation Method: auth/socialLogin
	'socialLogin' => array(
		array(
		'field' => 'source',
		'label' => 'Social source',
		'rules' => 'required'
		),
		array(
		'field' => 'accountId',
		'label' => 'Social Account Id',
		'rules' => 'required'
		) 
	),
	//card fields request validation Method: card/fields
	'card_fields' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		)
	),
	//fetch select validation Method: card/index_get
	'fetch_cards' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		)
	),
	//fetch select validation Method: card/index_get
	'set_default' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		)
	),
	//card select validation Method: card/select_post
	'card_select' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'categoryId',
		'label' => 'Category ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		)
	),
	//card create validation Method: card/index_post
	'card_create' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'categoryId',
		'label' => 'Category ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'rawData',
		'label' => 'Raw Data',
		'rules' => 'trim|required'
		),
		array(
		'field' => 'addedMode',
		'label' => 'Added Mode 1: scan_card, 2: template, 3: canvas',
		'rules' => 'trim|required|numeric'
		),
		array(
		'field' => 'status',
		'label' => 'Status 1: active, 2 draft, -1 delete',
		'rules' => 'required|numeric'
		)
	),
	//card improve validation Method: card/index_put
	'card_update' => array(
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'rawData',
		'label' => 'Raw Data',
		'rules' => 'trim|required'
		),
		array(
		'field' => 'editMode',
		'label' => 'Edit Mode 1: scan_card, 2: template, 3: canvas',
		'rules' => 'trim|required|numeric'
		)
	),
	//get default card of user Method: card/defaultCard
	'default_card' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		)
	),
	//card delete validation Method: card/card_delete
	'card_delete' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'categoryId',
		'label' => 'Category ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		)
	),
	//card mapping validation Method: card/mapping
	'card_mapping' => array(
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'side',
		'label' => 'Card Side 1:front or 2:back',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'fieldData',
		'label' => 'must be a valid json',
		'rules' => 'trim|required'
		)
	),
	//card config validation Method: card/config
	'card_config' => array(
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'side',
		'label' => 'Card Side 1:front or 2:back',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'rawData',
		'label' => 'must be a valid json',
		'rules' => 'trim|required'
		)
	),
/** Card Bank validations **/	
	//card card bank request validation Method: card/index_post
	'cardBankRequest' => array(
		array(
		'field' => 'fromUser',
		'label' => 'From User',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'toUser',
		'label' => 'To User',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardType',
		'label' => '1: scan_card, 2: template, 3: canvas	',
		'rules' => 'trim|required|numeric'
		) 
	),
	//card card bank request validation Method: card/index_post
	'acceptCardRequest' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'bankId',
		'label' => 'Bank ID',
		'rules' => 'required|numeric'
		)
	),
	//card card bank request validation Method: card/index_post
	'rejectCardRequest' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'bankId',
		'label' => 'Bank ID',
		'rules' => 'required|numeric'
		)
	),
	//card card bank request validation Method: card/index_post
	'pendingCardRequest' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		)
	),
	//card card bank request validation Method: card/index_post
	'shareCard' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'mobileNo',
		'label' => 'At least one required',
		'rules' => 'required'
		),
	),
	//share later card bank request validation Method: card/index_post
	'shareLater' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'mobileNo',
		'label' => 'At least one required',
		'rules' => 'required'
		),
	),
	//share later card bank request validation Method: card/index_post
	'shareLaterGet' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		)
	),
	//share later card bank request validation Method: card/index_post
	'shareLaterDelete' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		)
	),
	//share later card bank request validation Method: card/index_post
	'shareLaterPut' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'cardId',
		'label' => 'Card ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'mobileNo',
		'label' => 'At least one required',
		'rules' => 'required'
		),
	),
	'scannedCardGet' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
	),
	'updateFcmToken' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'deviceId',
		'label' => 'Device ID',
		'rules' => 'required'
		),
		array(
		'field' => 'token',
		'label' => 'Fcm Token',
		'rules' => 'required'
		),
	),
	'sendMessage' => array(
		array(
		'field' => 'sender',
		'label' => 'Sender',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'receiver',
		'label' => 'Receiver',
		'rules' => 'required'
		),
		// array(
		// 'field' => 'message',
		// 'label' => 'Message',
		// 'rules' => 'required'
		// ),
		array(
		'field' => 'type',
		'label' => 'Type',
		'rules' => 'required'
		),
	),
	'getMessage' => array(
		array(
		'field' => 'sender',
		'label' => 'Sender',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'receiver',
		'label' => 'Receiver',
		'rules' => 'required'
		),
	),
	'markMessageRead' => array(
		array(
		'field' => 'messageId',
		'label' => 'Message ID',
		'rules' => 'required|numeric'
		),
	),
	'changeMessageStatus' => array(
		array(
		'field' => 'messageId',
		'label' => 'Message ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'status',
		'label' => 'Status',
		'rules' => 'required|numeric'
		),
	),
	'changeMessageStatusBulk' => array(
		array(
		'field' => 'sender',
		'label' => 'Sender',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'receiver',
		'label' => 'Receiver',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'status',
		'label' => 'Status',
		'rules' => 'required|numeric'
		),
	),
	'getConnections' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
	),
	'loginStatus' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'status',
		'label' => 'Status',
		'rules' => 'required|numeric'
		),
	),
	'typingStatus' => array(
		array(
		'field' => 'receiver',
		'label' => 'Receiver User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'sender',
		'label' => 'Sender User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'isTyping',
		'label' => 'Is Typing ',
		'rules' => 'required'
		),
	),
	'getUserProfile' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'updateUserProfile' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'updateUserDetails' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'getUserProfileRating' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'setUserProfileReview' => array(
		array(
		'field' => 'title',
		'label' => 'Title',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'review',
		'label' => 'Review',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'rating',
		'label' => 'Rating',
		'rules' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]'
		),
		array(
		'field' => 'fromUser',
		'label' => 'From User',
		'rules' => 'required|numeric|trim'
		), 
		array(
		'field' => 'toUser',
		'label' => 'To User',
		'rules' => 'required|numeric|trim'
		), 
	),
	'getUserProfileReview' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'setUserTestimonials' => array(
		array(
		'field' => 'clientName',
		'label' => 'Client Name',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'designation',
		'label' => 'Designation',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'content',
		'label' => 'Testimonial',
		'rules' => 'required'
		),
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric|trim'
		)
	),
	'updateUserTestimonials' => array(
		array(
		'field' => 'testiId',
		'label' => 'Testimonial ID',
		'rules' => 'required|numeric|trim'
		),
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric|trim'
		)
	),
	'setEducation' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric|trim'
		),
		array(
		'field' => 'levelId',
		'label' => 'Level ID',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'courseId',
		'label' => 'Course ID',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'institutionId',
		'label' => 'Institution ID',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'startYear',
		'label' => 'Sart Year',
		'rules' => 'numeric|trim'
		),
		array(
		'field' => 'endYear',
		'label' => 'End Year',
		'rules' => 'numeric|trim'
		),

	),
	'getEducation' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'deleteEducation' => array(
		array(
		'field' => 'educationId',
		'label' => 'Education ID',
		'rules' => 'required|numeric'
		), 
	),
	'updateEducation' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'educationId',
		'label' => 'Education ID',
		'rules' => 'required|numeric'
		), 
	),
	'setWork' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'title',
		'label' => 'Title',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'empType',
		'label' => 'Employee Type',
		'rules' => 'required|trim'
		),
		array(
		'field' => 'location',
		'label' => 'Location',
		'rules' => 'required|trim'
		)
	),
	'getWork' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'updateWork' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'workId',
		'label' => 'Work ID',
		'rules' => 'required|numeric'
		), 
	),
	'deleteWork' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'workId',
		'label' => 'Work ID',
		'rules' => 'required|numeric'
		), 
	),
	'setReward' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'title',
		'label' => 'Title',
		'rules' => 'required|trim'
		), 
	),
	'getReward' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'updateUserReward' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'rewardId',
		'label' => 'Reward ID',
		'rules' => 'required|numeric'
		), 
	),
	'setAchievement' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'title',
		'label' => 'Title',
		'rules' => 'required|trim'
		), 
	),
	'updateUserAchievement' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
		array(
		'field' => 'achId',
		'label' => 'Achievement ID',
		'rules' => 'required|numeric'
		), 
	),
	'getAchievement' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'workHistory' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		), 
	),
	'setUserSkill' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'skillId',
		'label' => 'Skill Id',
		'rules' => 'required'
		), 
	),
	'deleteUserSkill' => array(
		array(
		'field' => 'userId',
		'label' => 'User ID',
		'rules' => 'required|numeric'
		),
		array(
		'field' => 'skillId',
		'label' => 'Skill Id',
		'rules' => 'required'
		), 
	),
	'deleteUserReview' => array(
		array(
		'field' => 'reviewId',
		'label' => 'Review Id',
		'rules' => 'required'
		), 
	),
	'deleteUserReward' => array(
		array(
		'field' => 'rewardId',
		'label' => 'Reward Id',
		'rules' => 'required'
		), 
	),
	'deleteUserAchievement' => array(
		array(
		'field' => 'achId',
		'label' => 'Achievement Id',
		'rules' => 'required'
		), 
	),
	'deleteUserTestimonials' => array(
		array(
		'field' => 'testiId',
		'label' => 'Testimonial Id',
		'rules' => 'required'
		), 
	),
	'deleteMessage' => array(
		array(
		'field' => 'messageId',
		'label' => 'Message Id',
		'rules' => 'required|trim'
		), 
		array(
		'field' => 'delReceiver',
		'label' => 'Deleted from Receiver',
		'rules' => 'numeric|trim'
		), 
		array(
		'field' => 'delSender',
		'label' => 'Deleted from sender',
		'rules' => 'numeric|trim'
		), 
	),
);
