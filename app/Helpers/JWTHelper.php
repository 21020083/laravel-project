<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\JWT;

class JWTHelper
{
    /**
     * Encode data to JWT
     *
     * @param array $data
     * @param string $alg
     * @param bool $hasExpire
     *
     * @return string
     */
    public static function encodeData(array $data, string $alg = 'HS256', bool $hasExpire = false): string
    {
        // expires in 1 hour
        $payload = $hasExpire ? array_merge($data, ['exp' => time() + 60 * 60]) : $data;
        return JWT::encode($payload, env('JWT_SECRET'), $alg);
    }

    /**
     * Decode JWT to data
     *
     * @param string $jwt
     * @return object | bool
     */
    public static function decodeData(string $jwt): object|bool
    {
        try {
            return JWT::decode($jwt, env('JWT_SECRET'));
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Decode JWT to data without secret
     *
     * @param string $jwt
     * @return object
     * @throws Exception
     */
    public static function decodeDataWithoutSecret(string $jwt): object
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new Exception('Wrong number of segments');
        }
        return JWT::jsonDecode(JWT::urlsafeB64Decode($tks[1]));
    }
}
