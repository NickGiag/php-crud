<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTAuthentication {
    
    protected $jwt;
    protected $secret_key;
    protected $issuer_claim;

    public function __construct($SECRET_KEY, $ISSUER_CLAIM) {
        $this->secret_key = $SECRET_KEY;
        $this->issuer_claim = $ISSUER_CLAIM;
    }

    public function validateJWT() {

        $secret_key = $this->secret_key;
        $issuer_claim = $this->issuer_claim;

        if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {
            http_response_code(401);
            die;
        }

        try {
            $jwt = preg_split('/ /', $_SERVER['HTTP_AUTHORIZATION'])[1];
            $token = JWT::decode($jwt, new Key($secret_key,'HS256'));

            $data = json_encode(array(
                "success" => true,
                "message" => "JWT token valid",
                "data" => $token
            ));

            return data;
        } catch (UnexpectedValueException $exception) {

        }
    }

    public function createJWT($username, $permission, $authorizations) {
        $secret_key = $this->secret_key;
        $issuer_claim = $this->issuer_claim; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); //issued at
        $notbefore_claim = $issuedat_claim + 10; // not before in seconds
        $expire_claim = $issuedat_claim + 3200; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "username" => $username,
                "permission" => $permission,
                "authorization" => $authorizations
            )
        );

        $jwt = JWT::encode($token, $secret_key , 'HS256');
        return json_encode(
            array(
                'success' => true,
                "token" => $jwt,
                "expireAt" => $expire_claim
            )
        );
    }

}
?>