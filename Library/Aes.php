<?php
namespace App\Services;

class Aes
{
    private $iv = '#########';
    private $key = '*********';
    private $method = 'AES-128-CBC';

    public function encrypt($str, $key = '', $iv = '')
    {
        if (empty($key)) {
            $key = $this->key;
        }
        if (empty($iv)) {
            $iv = $this->iv;
        }
        $secret_str = openssl_encrypt($str, $this->method, $key, 0, $iv);
        return $secret_str;
    }

    public function decrypt($str, $key = '', $iv = '')
    {
        if (empty($key)) {
            $key = $this->key;
        }
        if (empty($iv)) {
            $iv = $this->iv;
        }
        $secret_str = openssl_decrypt($str, $this->method, $key, 0, $iv);
        return $secret_str;
    }
}
