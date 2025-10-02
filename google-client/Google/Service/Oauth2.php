<?php
class Google_Service_Oauth2 {
    public $userinfo;
    public function __construct($client) { $this->userinfo = new Google_Service_Oauth2_Userinfo(); }
}
class Google_Service_Oauth2_Userinfo {
    public function get() { return (object)['email'=>'demo@example.com','name'=>'Demo User','id'=>'12345']; }
}
