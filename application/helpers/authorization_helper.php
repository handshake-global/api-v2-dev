<?php
class AUTHORIZATION
{ 
    public static function validateTimestamp($token)
    {
        $CI =& get_instance();
        $token = self::validateToken($token);
        if ($token != false && (now() - $token->timestamp < ($CI->config->item('token_timeout') * 60))) {
            return $token;
        }
        return false;
    }

    public static function validateToken($token)
    {
        $CI =& get_instance();
        return JWT::decode($token, $CI->config->item('jwt_key'));
    }

    public static function generateToken($data)
    {
        $CI =& get_instance();
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }

    public static function verify_user($phoneNo=NULL){
       $CI =& get_instance();
       if($phoneNo=='' || $phoneNo == NULL)
            return false;
       $user = $CI->db->where(array('phoneNo'=>$phoneNo,'isVerified'=>1,'status'=>1))->get('users')->row();
       if(empty($user))
            return false;
        else
            return true;
    }
    public static function verify_request()
{ 
    $CI =& get_instance();
    // Get all the headers
    $headers = $CI->input->request_headers();
    // Extract the token
    if(empty($headers['Authorization'])){
        $status = REST_Controller::HTTP_UNAUTHORIZED;
        $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
        $CI->response($response, $status);
        exit();
    } 
    
    $token = $headers['Authorization'];
    // Use try-catch
    // JWT library throws exception if the token is not valid
    try {
        // Validate the token
        // Successfull validation will return the decoded user data else returns false
        $data = self::validateToken($token);
       if ($data === false) {
            $status = REST_Controller::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            $CI->response($response, $status);
            exit();
        } else {
            if(isset($data->phoneNo) && self::verify_user($data->phoneNo)){
                return $data;
            }
            else{
                $status = REST_Controller::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                $CI->response($response, $status);
                exit();
            }

        }
    } catch (Exception $e) {
        // Token is invalid
        // Send the unathorized access message
        $status = REST_Controller::HTTP_UNAUTHORIZED;
        $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
        $CI->response($response, $status);
    }
}
  
}