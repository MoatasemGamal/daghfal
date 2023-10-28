<?php

namespace Core\Utility;

/**
 * Custom session handler
 * encrypt sessions
 */
class Session extends \SessionHandler
{
    private string $sessionName = "SESSID";
    private int $sessionMaxLifeTime = 0;
    private bool $sessionSSL = false;
    private bool $sessionHTTPOnly = true;
    private string $sessionPath = "/";
    private ?string $sessionDomain = null;
    private string $sessionSavePath = SESSIONS_PATH;
    private int $ttl = 30;
    public function __construct()
    {
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.save_handler', 'files');

        session_name($this->sessionName);
        session_save_path($this->sessionSavePath);

        session_set_cookie_params(
            lifetime_or_options: $this->sessionMaxLifeTime,
            path: $this->sessionPath,
            domain: $this->sessionDomain,
            secure: $this->sessionSSL,
            httponly: $this->sessionHTTPOnly
        );

        session_set_save_handler($this, true);
    }

    /**
     * start session if not started
     * @return void
     */
    public function start(): self
    {
        if (session_status() === PHP_SESSION_NONE) {
            pre("SESSION started");
            if (session_start()) {
                $this->setSessionStartTime();
                $this->verifyFingerPrint();
            }
        }
        return $this;
    }

    public function read($id): string
    {
        return app('encryption')->decrypt(parent::read($id));
    }

    public function write($id, $data): bool
    {
        return parent::write(
            $id,
            app('encryption')->encrypt($data)
        );
    }

    public function kill()
    {
        session_unset();
        setcookie(
            $this->sessionName,
            '',
            time() - 1000,
            $this->sessionPath,
            $this->sessionDomain,
            $this->sessionSSL,
            $this->sessionHTTPOnly
        );
        session_destroy();
    }

    private function setSessionStartTime(): void
    {
        if (!isset($_SESSION['sessionStartTime']))
            $_SESSION['sessionStartTime'] = time();
        if ((time() - $_SESSION['sessionStartTime']) > $this->ttl) {
            session_regenerate_id(true);
            $_SESSION['sessionStartTime'] = time();
        }
    }
    private function verifyFingerPrint()
    {
        if (!isset($_SESSION["sessionFingerPrint"]))
            $_SESSION["sessionFingerPrint"] = md5($_SERVER["HTTP_USER_AGENT"]);
        if (md5($_SERVER["HTTP_USER_AGENT"]) != $_SESSION["sessionFingerPrint"])
            $this->kill();
    }
    public function __set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($_SESSION[$key]);
    }
    public function __unset($key)
    {
        unset($_SESSION[$key]);
    }
    public function __get($key): mixed
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }
}
