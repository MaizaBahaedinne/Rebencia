<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public string $baseURL = 'https://rebencia.com/';
    public array $allowedHostnames = [];
    public string $indexPage = '';
    public string $uriProtocol = 'REQUEST_URI';
    public string $defaultLocale = 'fr';
    public bool $negotiateLocale = true;
    public array $supportedLocales = ['fr', 'ar', 'en'];
    public string $appTimezone = 'Africa/Tunis';
    public string $charset = 'UTF-8';
    public bool $forceGlobalSecureRequests = false;
    public int $sessionExpiration = 7200;
    public string $sessionSavePath = WRITEPATH . 'session';
    public bool $sessionMatchIP = false;
    public int $sessionTimeToUpdate = 300;
    public bool $sessionRegenerateDestroy = false;
    public string $cookiePrefix = '';
    public string $cookieDomain = '';
    public string $cookiePath = '/';
    public bool $cookieSecure = false;
    public bool $cookieHTTPOnly = true;
    public string $cookieSameSite = 'Lax';
    public bool $CSRFProtection = true;
    public string $CSRFTokenName = 'csrf_token_rebencia';
    public string $CSRFHeaderName = 'X-CSRF-TOKEN';
    public string $CSRFCookieName = 'csrf_cookie_rebencia';
    public int $CSRFExpire = 7200;
    public bool $CSRFRegenerate = true;
    public bool $CSRFRedirect = true;
    public string $CSRFSameSite = 'Lax';
    public bool $CSPEnabled = false;
}
