<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//notification routes
$route['updateFcmToken'] = 'notification/updateFcmToken';

//card 
$route['defaultCard'] = 'card/defaultCard';
$route['setCardDefault'] = 'card/setDefault';

//card bank
$route['getHomeConnections'] = 'CardBank/getConnections';
$route['sentCardRequest'] = 'CardBank/sentRequest';
$route['deleteConnection'] = 'CardBank/deleteConnection';

//Chat Routes
$route['sendMessage'] = 'chat/sendMessage';
$route['GetMessageHistory'] = 'chat/GetMessageHistory';
$route['markMessageRead'] = 'chat/markMessageRead';
$route['changeMessageStatus'] = 'chat/changeMessageStatus';
$route['changeMessageStatusBulk'] = 'chat/changeMessageStatusBulk';
$route['getConnections'] = 'chat/getConnections';
$route['getMessageList'] = 'chat/getMessageList';
$route['loginStatus'] = 'chat/loginStatus';
$route['typingStatus'] = 'chat/typingStatus';
$route['deleteMessage'] = 'chat/deleteMessage';

//User Profile Route
$route['getUserProfile'] = 'user/userProfile';
$route['updateUserProfile'] = 'user/userProfile';
$route['updateUserDetails'] = 'user/userDetails';
$route['userIntroduction'] = 'user/userIntroduction';

$route['getUserProfileRating'] = 'user/userProfileRating';
$route['setUserProfileReview'] = 'user/userProfileReview';
$route['getUserProfileReview'] = 'user/userProfileReview';

$route['SetUserTestimonials'] = 'user/userTestimonials';
$route['deleteUserTestimonials'] = 'user/userTestimonials';
$route['updateUserTestimonials'] = 'user/userTestimonials';

$route['setUserSkill'] = 'user/userSkill';
$route['getUserSkill'] = 'user/userSkill';
$route['deleteUserSkill'] = 'user/userSkill';

$route['deleteUserReview'] = 'user/userReview';
$route['getUserCategory'] = 'user/userCategory';

$route['trackUserSwipe'] = 'user/setUserSwipe';
$route['getUserSwipe'] = 'user/getUserSwipe';

//education Routes
$route['getEducationLevels']  = 'education/educationLevels';
$route['getEducationCourses']  = 'education/educationCourses';
$route['getEducationInstitution']  = 'education/educationInstitution';

//work history route
$route['getEmpType'] = 'work/empType';
$route['setUserReward'] = 'work/userReward';
$route['getUserReward'] = 'work/userReward';

$route['setUserAchievement'] = 'work/userAchievement';
$route['getUserAchievement'] = 'work/userAchievement';
$route['updateUserAchievement'] = 'work/userAchievement';

$route['getWorkHistory'] = 'work/workHistory';

$route['deleteUserReward'] = 'work/userReward';
$route['updateUserReward'] = 'work/userReward';
$route['deleteUserAchievement'] = 'work/userAchievement';
