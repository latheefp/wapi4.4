<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class TokenComponent extends Component
{
    private $key = 'askjkd80098oi234234llaldfj98090982048098230984ASDFASDFEERHYFGUJFEFXCT';

    public function generateToken($userData)
    {
        $payload = [
            'iss' => "APP_NAME",
            'aud' => 'WAJunctionChat',
            'iat' => time(),
            'exp' => $userData['expiry'], // Token expiration time (e.g., 1 hour)
            'sub' => $userData['user_id'],
            'account_id'=>$userData['account_id']
            // You can include additional user data in the token payload if needed
        ];

        //`return JWT::encode($payload, $this->key);
        return JWT::encode($payload, $this->key, 'HS256'); // Adding the algorithm parameter
    }

    public function validateToken($token)
    {
      //  debug($token);
        try {
           // $decoded = JWT::decode($token, $this->key, 'HS256');
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            // Perform additional validation if needed
            return $decoded;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}