<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include APPPATH.'third_party/faker/autoload.php';
    
class Faker extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
     
    public function index(){
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 10000; $i++) {
            $countryCode = fake_country_code();
            $phoneNo = $faker->numerify('##########');
            $data = array(
                'firstName' => $faker->firstName,
                'lastName' => $faker->lastName,
                'countryCode' => '+'.$countryCode,
                'phoneNo' =>  $phoneNo,
                'latitude' => $faker->latitude($min = 20, $max = 21),     
                'longitude' => $faker->longitude($min = 78, $max = 79),
                'source' => 'Android',
                'deviceId'=>'were3432132',
                'deviceType' =>'FAKE',
                'status' => 1,
                'isVerified' => 1,
                'password' => base64_encode((base64_encode($countryCode.$phoneNo))),
            );
            $data['ipAddress'] = get_client_ip();
            $data['createdAt'] = date('Y/m/d h:i:s a', time());
            $this->db->insert('users',$data);
        }   
    } 
}