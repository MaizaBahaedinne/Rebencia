<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('jwt'); // Will create this helper
    }

    /**
     * Authenticate API request
     */
    protected function authenticate()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            return false;
        }

        $token = substr($authHeader, 7);
        
        try {
            $decoded = verifyJWT($token);
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check API rate limiting
     */
    protected function checkRateLimit($userId)
    {
        $cache = \Config\Services::cache();
        $key = 'api_rate_limit_' . $userId;
        $limit = 100; // requests per minute
        
        $requests = $cache->get($key) ?? 0;
        
        if ($requests >= $limit) {
            return false;
        }
        
        $cache->save($key, $requests + 1, 60);
        return true;
    }
}
