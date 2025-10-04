<?php


//----------------------------------------------
//FILE NAME:  JwtService.php gen for Servit Framework Service
//DATE: 2019-05-03(Fri)  07:48:13

//----------------------------------------------
use Illuminate\Database\Capsule\Manager as Capsule;

class JwtService
{

    private static $member = null;


    public static  function  getToken($user = null)
    {
        if (!$user) return [];

        $privateKeyFile = "private.key";
        $publicKeyFile = "public.key";
        $header = [
            "alg"   => "RS256",
            "typ"   => "JWT"
        ];

        $issuedAt = time();
        $expirationTime = $issuedAt + (60 * 60 * USERJWT['exp']); // 8 hours
        // $expirationTime = $issuedAt + 60; // 8 hours
        $keys = USERJWT;
        $payload = [];
        // dump($user);
        // foreach ($keys as $key => $val) {
        //     if ($key == 'iat') {
        //         $payload[$key] = $issuedAt;
        // dump($user);
        // dump($user->id_random);
        // dump($user);
        $payload['iat']   = $issuedAt; // นี้ถูก Sign เมื่อไหร่
        $payload['exp']   = $expirationTime; //นี้จะหมดอายุเมื่อถึงเวลา(timestamp) นี้
        $payload['sub']   = $user->user_name; // ออกให้กับ user / service
        $payload['aud']   = $user->user_name; // ผู้ที่จะต้องรับ JWT นี้ไปใช้งาน
        $payload['name']  = $user->user_name;
        $payload['naem_thai']   = $user->name_thai;
        $payload['uid']   = $user->user_name;  // user id
        $payload['level'] = $user->user_level; // level
        $payload['iss'] = 'Rest Server API'; // ใครเป็นคน Sign Token นี้ให้
        $payload['typ'] = 'Bearer'; // ใครเป็นคน Sign Token นี้ให้

        //     } else if ($key == 'exp') {
        //         $payload[$key] = $expirationTime;
        //     } else {
        //         if (is_array(USERJWT[$key])) {
        //             $data = [];
        //             foreach (USERJWT[$key] as $keyv) {
        //                 $data[] = $user->{$keyv};
        //             }
        //             $payload[$key] = join(' ', $data);
        //         } else {
        //             dump($user->user_name);
        //             $payload[$key] = $user->user_name;
        //         }
        //     }
        // // }
        // dump($payload);

        // $verify = verifyJWT('sha256', $jwt, $publicKeyFile);
        $jwt = self::generateJWT('sha256', $header, $payload, $privateKeyFile);
        return $jwt;
        // var_dump($jwt); // string(277) "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.R-41ycm1V7Kvx_Lnw6nha6OAFQ-vYvdhAdgqa1Ugkj17X4dpSWSO0KRCmnq7yd6ZM-RLEMY3PEXyUAs4F1XtomT6M-CziCpIB5piLfYHLG6V1_FrtieuIOMGLZGs-PpqMZX-JgJf_L19Ly9jnqGjfl9zo6BTTandhgNECE7AVk0"
        // $verify = verifyJWT('sha256', $jwt, $publicKeyFile);
        // var_dump($verify);
    }

    public static function verify($jwt)
    {
        $str_jwt = (string)$jwt;
        // dump('jwt->', $str_jwt);
        if ($str_jwt) {
            $publicKeyFile = "public.key";
            $verify = self::verifyJWT('sha256', $str_jwt, $publicKeyFile);
            if ($verify) {
                return self::$member;
            } else {
                return $verify;
            }
        } else {
            return false;
        }
    }

    public static function getMember()
    {
        if (self::$member) {
            return self::$member;
        } else {
            return false;
        }
    }

    private static function base64UrlEncode($data)
    {
        $data = (string)$data;
        $urlSafeData = strtr(base64_encode($data), '+/', '-_');
        return rtrim($urlSafeData, '=');
    }

    private static function base64UrlDecode($data)
    {
        $urlUnsafeData = strtr($data, '-_', '+/');
        $paddedData = str_pad($urlUnsafeData, strlen($data) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($paddedData);
    }

    private static function getOpenSSLErrors()
    {
        $messages = [];
        while ($msg = openssl_error_string()) {
            $messages[] = $msg;
        }
        return $messages;
    }

    public static function generateJWT($algo, $header, $payload, $privateKeyFile)
    {
        $str_header = json_encode($header);
        $str_payload = json_encode($payload);
        $headerEncoded = self::base64UrlEncode($str_header);
        $payloadEncoded = self::base64UrlEncode($str_payload);
        $dataEncoded = "$headerEncoded.$payloadEncoded";
        $privateKey = "file://" . SRVPATH . '/configs/' . $privateKeyFile;
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        $result = openssl_sign($dataEncoded, $signature, $privateKeyResource, $algo);
        if ($result === false) {
            throw new RuntimeException("Failed to generate signature: " . implode("\n", self::getOpenSSLErrors()));
        }
        $signatureEncoded = self::base64UrlEncode($signature);
        $jwt  = "$dataEncoded.$signatureEncoded";
        $pubpath = SRVPATH . '/configs/public.key';
        $pubkey = file_get_contents($pubpath);
        return [
            'token' => $jwt,
            'pubkey' => $pubkey,
        ];
    }

    private static function verifyJWT($algo,  $jwt,  $publicKeyFile)
    {
        if (empty($jwt)) return false;
        if (!preg_match('(^[\w-]*\.[\w-]*\.[\w-]*$)', $jwt)) return false;
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
        $dataEncoded  = "$headerEncoded.$payloadEncoded";
        $signature = self::base64UrlDecode($signatureEncoded);
        $privateKey = "file://" . SRVPATH . '/configs/' . $publicKeyFile;
        $publicKeyResource = openssl_pkey_get_public($privateKey);
        $result = openssl_verify($dataEncoded, $signature, $publicKeyResource, $algo);
        // dump('result-->', $result);
        if ($result === 1) {
            $data = self::base64UrlDecode($payloadEncoded);
            $data = json_decode($data);
            $now = time();
            if ($now > $data->exp) {
                throw new \Exception('403 token expire', 403);
            }

            if ($data && USERCLASS) {
                $userclass = USERCLASS;
                $member = $userclass::where('user_name', $data->uid)->first();
                self::$member = $member;
                if ($member) {
                    return true;
                } else {
                    throw new \Exception('401 Unauthorized', 401);
                }
            } else {
                return false;
            }
        } else {
            // throw new RuntimeException("Failed to verify signature: " . implode("\n", self::getOpenSSLErrors()));
            // throw new RuntimeException("Failed to verify signature",403);
            throw new \Exception('403 Failed to verify signature', 403);
            // return false;
        }
    }

    private static function jwtdata(string $algo, string $jwt, string $publicKeyFile)
    {
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
        $data = self::base64UrlDecode($payloadEncoded);
        $data = json_decode($data);
        if (USERCLASS) {
            $userclass = "\\" . USERCLASS;
            $member = $userclass::find($data->uid);
            return  $member;
        } else {
            return null;
        }
    }
}
