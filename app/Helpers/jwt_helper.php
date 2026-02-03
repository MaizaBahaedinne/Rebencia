<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    /**
     * Generate JWT token
     */
    function generateJWT($data)
    {
        $key = getenv('JWT_SECRET_KEY') ?: 'rebencia_secret_key_2026';
        $issuedAt = time();
        $expirationTime = $issuedAt + (3600 * 24); // 24 hours

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'iss' => base_url(),
            'data' => $data
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('verifyJWT')) {
    /**
     * Verify and decode JWT token
     */
    function verifyJWT($token)
    {
        $key = getenv('JWT_SECRET_KEY') ?: 'rebencia_secret_key_2026';

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            throw new \Exception('Token invalide: ' . $e->getMessage());
        }
    }
}
