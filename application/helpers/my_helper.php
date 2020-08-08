<?php
defined('BASEPATH') OR exit('No direct script access allowed');

	// Helper For print_r
	function pr($var = '')
	{
		echo "<pre>";
		   print_r($var);	
		echo "</pre>";
	    // die();	
	}

	//Helper For base_url()
	function bs($value = '')
	{
		// public $url = ""
		echo base_url($value);
	}

	//Helper for $this->load->view()
	function view($value='', $data=array(), $output = false)
	{
		$CI =& get_instance();
		$CI->load->view($value,$data,$output);
	}

	//Helper For thsi->input->post()
	function post($value='')
	{
		$CI =& get_instance();
	    return $CI->input->post($value);
	}

	//Helper For thsi->input->get()
	function get($value='')
	{
		$CI =& get_instance();
	    return $CI->input->get($value);
	}

	//helper for var_dump
    function dd($value='')
	{
		echo "<pre>";
		   var_dump($value);	
		echo "</pre>";
		die();
	}

	//Helper for last_query()
	function vd()
	{
		$CI =& get_instance();
		return $CI->db->last_query();
	}
	function group_priviliges($value='')
	{
		$CI =& get_instance();

		$gp_id = $CI->session->userdata("group_id");

		$gp_result = $CI->ion_auth_model->user_gp_privilegs($gp_id);

		$gp_data = array();
		   
      	foreach($gp_result as $value)
        {
           //add all data to session
           $gp_data[] = $value->perm_name;

        }

	    return $gp_data;
	}
	function has($val)
	{
		if ($val) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Slugify Helper
	 *
	 * Outputs the given string as a web safe filename
	 */
	if ( ! function_exists('slugify'))
	{
		function slugify($string, $replace = array(), $delimiter = '-', $locale = 'en_US.UTF-8', $encoding = 'UTF-8') {
			if (!extension_loaded('iconv')) {
				throw new Exception('iconv module not loaded');
			}
			// Save the old locale and set the new locale
			$oldLocale = setlocale(LC_ALL, '0');
			setlocale(LC_ALL, $locale);
			$clean = iconv($encoding, 'ASCII//TRANSLIT', $string);
			if (!empty($replace)) {
				$clean = str_replace((array) $replace, ' ', $clean);
			}
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower($clean);
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
			$clean = trim($clean, $delimiter);
			// Revert back to the old locale
			// setlocale(LC_ALL, $oldLocale);
			return $clean;
		}
	} 

	  function do_upload($img=null,$path='./uploads/',$multi=false,$file_name=NULL,$thumb=false,$allowed='gif|jpg|png|jpeg|tif|mp4|MP4|3gp|3GP|flv|FLV|mkv|MKV|xlsx|xls|doc|docx|ppt|pptx|pdf|svg|txt') { 

	    if($multi==false){
	         $config['upload_path']   = $path; 
	         $config['allowed_types'] =  $allowed; 
	         $config['max_size']      = 9000000000;  
	         $config['overwrite']     = TRUE;  
	         if($file_name==NULL)
	         	$config['encrypt_name'] = TRUE;
	         else
	         	$config['file_name'] = $file_name;

	         $CI = get_instance();
	         $redirect = $CI->router->fetch_class().'/'.$CI->router->fetch_method();
			 $CI->load->library('upload', $config); 
				
	         if ( ! $CI->upload->do_upload($img)) {
	            return $CI->upload->display_errors();
	         }
				
	         else { 
	            return $CI->upload->data();
	         } 
     	}
         else{
         if(!empty($_FILES[$img]['name'])){
            $filesCount = count($_FILES[$img]['name']);
            $images = array();
            for($i = 0; $i < $filesCount; $i++){
                $_FILES['file']['name']     = $_FILES[$img]['name'][$i];
                $_FILES['file']['type']     = $_FILES[$img]['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES[$img]['tmp_name'][$i];
                $_FILES['file']['error']    = $_FILES[$img]['error'][$i];
                $_FILES['file']['size']     = $_FILES[$img]['size'][$i];
                
                // File upload configuration
                $uploadPath = $path;
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'gif|jpg|png|jpeg|tif|mp4|wma|mov|avi|flv|xlsx|xls|doc|docx|ppt|pptx|pdf|svg|txt'; 
                $config['encrypt_name'] = TRUE; 
                $config['max_size']      = 9000000000; 

                $videos = array('mp4','wma','mov','avi','flv');
                $docs = array("xlsx", "xls", "doc", "docx", "ppt", "pptx", "pdf","txt");
                // Load and initialize upload library
                $CI = get_instance();
                $redirect = $CI->router->fetch_class().'/'.$CI->router->fetch_method();
                $CI->load->library('upload', $config);
                $CI->upload->initialize($config);

                if ( ! $CI->upload->do_upload('file')) {
                	return $CI->upload->display_errors();
		         } 
		         else { 
		            $images[] = $CI->upload->data();
		            $images[$i]['clear_path'] = $path.'/'.$CI->upload->data('file_name');
		            $file_ext = pathinfo($_FILES[$img]['name'][$i], PATHINFO_EXTENSION);
		            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $CI->upload->data('file_name'));
		            $images[$i]['thumb'] = '';

		            if($thumb==true && in_array($file_ext,$videos)){
		            	$thumb_path = str_replace('/'.$CI->upload->data('file_name'),'',$CI->upload->data('full_path'));
		            	$thumb = exec("ffmpeg  -itsoffset -5  -i ".$CI->upload->data('full_path')." -vcodec mjpeg -vframes 1 -an -f rawvideo -s 1080x1920 ".$thumb_path."/".$withoutExt.".jpg"); 
		            	$images[$i]['thumb'] = $uploadPath.'/'.$withoutExt.".jpg";
		            }elseif(in_array($file_ext,$docs)){
		            	
		            }
		         } 
           	 }
           	 return $images;
          }  
      } 
   }   

    
function contains( $needle, $haystack ) {
    return preg_match( '#\b' . preg_quote( $needle, '#' ) . '\b#i', $haystack ) !== 0;
}

if ( ! function_exists('get_client_ip'))
	{
	function get_client_ip() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
}
 
function array_flatten($array) { 
  if (!is_array($array)) { 
    return FALSE; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } 
    else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
} 

function custom_404(){
	redirect('custom_404/index');
}
	
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}	


if ( ! function_exists('create_slug')){

		function create_slug($name=null,$table="",$title=""){
	      	$config = array(
				'field' => 'slug',
				'table' => $table,
			);
			$CI = get_instance();
			$CI->load->library('slug', $config);

			$data = array(
				'slug' => $title 
			); 
			 return $data['uri'] = $CI->slug->create_uri($data); 
      } 
	} 

     
//crate thumbnail dynamically //
if ( ! function_exists('thumb')){ 

	 function thumb($fullname, $width, $height){	
    	$filename = pathinfo($fullname, PATHINFO_FILENAME);  
        
        // Get the CodeIgniter super object
        $CI = &get_instance();
        // get src file's extension and file name
        $extension = pathinfo($fullname, PATHINFO_EXTENSION); 
        // Path to image thumbnail in your root
        $dir = substr($fullname, 0, strpos($fullname, $filename . "." . $extension));
        $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/' . $dir;

        $image_org = $dir . $filename . "." . $extension; 
        $image_thumb = $dir . $filename . "-" . $height . '_' . $width . "." . $extension;
        $image_returned = $url . $filename . "-" . $height . '_' . $width . "." . $extension;
        $exist_file = "file:///".$_SERVER['DOCUMENT_ROOT'].'/'.str_replace('.', '', $dir.$filename. "-" . $height . '_' . $width)."." . $extension;
 
 		clearstatcache(); 
        if (file_exists($exist_file)===FALSE) {  
            // LOAD LIBRARY
            $CI->load->library('image_lib');
            // CONFIGURE IMAGE LIBRARY
            $config['image_library'] = 'gd2';
            $config['source_image'] = str_replace('..','',str_replace('//..','', $_SERVER['DOCUMENT_ROOT'] .'/'. $image_org));
            $config['new_image'] = str_replace('..','',str_replace('//..','',$_SERVER['DOCUMENT_ROOT'] .'/'.$image_thumb));
            $config['width'] = $width;
            $config['height'] = $height;
            $CI->image_lib->initialize($config);
            $CI->image_lib->resize();
      		// 		if (! $CI->image_lib->resize()) { 
		    //     echo $CI->image_lib->display_errors();
		    //     echo $fullname;
		    //     exit;
		    // }        
            $CI->image_lib->clear();
        } 
	        return $image_returned;
	    }
  }
 

//set flashdata for error and messages
if ( ! function_exists('set_flash')){ 
	function set_flash($type=NULL,$flash=NULL){	
		if($type==NULL || $flash == NULL )
			return false;
		$CI =& get_instance();
		return $CI->session->set_flashdata($type,$flash);
	}
}	

//get flashdata for error and messages
if ( ! function_exists('get_flash')){ 
	function get_flash($type=NULL){	
		$CI =& get_instance();
		if($type!=NULL)
			return $CI->session->flashdata($type);

		if($CI->session->flashdata('error')!='')
			echo "<script>toastr.error('".$CI->session->flashdata('error')."', 'Error!')</script>";

		if($CI->session->flashdata('msg'))
			echo "<script>toastr.success('".$CI->session->flashdata('msg')."', 'Message')</script>";
	}
}

//check if user logged in 
if ( ! function_exists('logged_in')){ 
	function logged_in(){	
		$CI =& get_instance();
		if($CI->session->userdata('user'))
			return $CI->session->userdata('user');
		else
			return FALSE;
	}
}

if(! function_exists('fake_country_code')){
	function fake_country_code(){
		$codes = array(1,1,7,7,20,27,30,31,32,33,34,36,39,40,41,43,44,45,46,47,47,48,49,51,52,53,54,55,56,57,58,60,61,61,61,62,63,64,64,65,66,81,82,84,86,90,91,92,93,94,95,98,211,212,212,213,216,218,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,248,249,250,251,252,253,254,255,256,257,258,260,261,262,262,263,264,265,266,267,268,269,290,291,297,298,299,350,351,352,353,354,355,356,357,358,359,370,371,372,373,374,375,376,377,378,379,380,381,382,383,385,386,387,389,420,421,423,500,501,502,503,504,505,506,507,508,509,590,590,591,592,593,595,597,598,599,599,670,672,673,674,675,676,677,678,679,680,681,682,683,685,686,687,688,689,690,691,692,850,852,853,855,856,880,886,960,961,962,963,964,965,966,967,968,970,971,972,973,974,975,976,977,992,993,994,995,996,998,1-242,1-246,1-264,1-268,1-284,1-340,1-345,1-441,1-473,1-649,1-664,1-670,1-671,1-684,1-721,1-758,1-767,1-784,1-787, 1-939,1-809, 1-829, 1-849,1-868,1-869,1-876,44-1481,44-1534,44-1624);
		return array_rand($codes,1);
	}
}
if(! function_exists('json_validate')){
	function json_validate($string,$is_server=false)
	{	
		// Get the CodeIgniter super object
        $CI = &get_instance();
	    // decode the JSON data
	    $result = json_decode($string);

	    // switch and check possible JSON errors
	    switch (json_last_error()) {
	        case JSON_ERROR_NONE:
	            $error = ''; // JSON is valid // No error has occurred
	            break;
	        case JSON_ERROR_DEPTH:
	            $error = 'The maximum stack depth has been exceeded.';
	            break;
	        case JSON_ERROR_STATE_MISMATCH:
	            $error = 'Invalid or malformed JSON.';
	            break;
	        case JSON_ERROR_CTRL_CHAR:
	            $error = 'Control character error, possibly incorrectly encoded.';
	            break;
	        case JSON_ERROR_SYNTAX:
	            $error = 'Syntax error, malformed JSON.';
	            break;
	        // PHP >= 5.3.3
	        case JSON_ERROR_UTF8:
	            $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
	            break;
	        // PHP >= 5.5.0
	        case JSON_ERROR_RECURSION:
	            $error = 'One or more recursive references in the value to be encoded.';
	            break;
	        // PHP >= 5.5.0
	        case JSON_ERROR_INF_OR_NAN:
	            $error = 'One or more NAN or INF values in the value to be encoded.';
	            break;
	        case JSON_ERROR_UNSUPPORTED_TYPE:
	            $error = 'A value of a type that cannot be encoded was given.';
	            break;
	        default:
	            $error = 'Unknown JSON error occured.';
	            break;
	    }

	    if ($error !== '') {
	        // throw the Exception or exit // or whatever :)
	        if($is_server)
	        	$CI->response(['status'=>['error' =>$error,'statusCode' => REST_Controller::HTTP_BAD_REQUEST]], REST_Controller::HTTP_BAD_REQUEST);
	        else
	        	return $error;
	    }

	    // everything is OK
	    return $result;
	}
}	 
 /* End of file my_helpers.php */
/* Location: ./app/helpers/my_helpers.php */