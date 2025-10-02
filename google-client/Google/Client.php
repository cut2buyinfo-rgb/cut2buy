<?php
class Google_Client {
    public function setClientId($id) {}
    public function setClientSecret($secret) {}
    public function setRedirectUri($uri) {}
    public function addScope($scope) {}
    public function createAuthUrl() { return 'https://accounts.google.com/o/oauth2/auth'; }
    public function fetchAccessTokenWithAuthCode($code) { return ['access_token'=>'demo_token']; }
    public function setAccessToken($token) {}
}
