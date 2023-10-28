<?php

namespace Core\Utility;

/**
 * singleton class for encrypt and decrypt data after init()
 * using openssl methods
 */
class Encryption
{
    private string $cipherAlgo;
    private string $key;
    private string $iv;
    private int $options;
    //Singleton
    public static ?Encryption $encryption = null;
    private function __construct(string $cipherAlgo, string $key, string $iv, int $options = 0)
    {
        $this->cipherAlgo = in_array(strtolower($cipherAlgo), openssl_get_cipher_methods()) ? strtolower($cipherAlgo) : "aes-256-gcm";
        $this->key = $key;
        $this->iv = $iv;
        $availableOptions = [0, OPENSSL_RAW_DATA, OPENSSL_ZERO_PADDING];
        $this->options = in_array($options, $availableOptions) ? $options : 0;
    }

    public static function init(string $cipherAlgo, string $key, string $iv, int $options = 0): self
    {
        if (is_null(static::$encryption))
            static::$encryption = new self($cipherAlgo, $key, $iv, $options);
        return static::$encryption;
    }

    public function encrypt(string $data): string
    {
        return openssl_encrypt($data, $this->cipherAlgo, $this->key, $this->options, $this->iv);
    }
    public function decrypt(string $data): string
    {
        return openssl_decrypt($data, $this->cipherAlgo, $this->key, $this->options, $this->iv);
    }
    public function get_iv_length(): bool|int
    {
        return openssl_cipher_iv_length($this->cipherAlgo);
    }
    public function get_key_length(): bool|int
    {
        return openssl_cipher_key_length($this->cipherAlgo);
    }
    public function getCipherAlgo(): string
    {
        return $this->cipherAlgo;
    }
}