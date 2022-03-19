<?php

namespace vaersaagod\toolmate\services;

use Craft;
use craft\base\Component;
use craft\helpers\FileHelper;

use vaersaagod\toolmate\ToolMate;

use yii\web\Cookie;

/**
 * Tool Service
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class ToolService extends Component
{
    /**
     * Inlines local or remote file.
     *
     * @param string $fileName
     * @param bool $remote
     * @return false|string
     */
    public function inline(string $fileName, bool $remote = false): bool|string
    {
        if ($remote) {
            if (str_starts_with($fileName, '//')) {
                $protocol = Craft::$app->getRequest()->isSecureConnection ? 'https:' : 'http:';
                $fileName = $protocol . $fileName;
            }

            return @file_get_contents($fileName);
        }

        $documentRoot = ToolMate::getInstance()?->getSettings()->publicRoot;
        $filePath = FileHelper::normalizePath($documentRoot . '/' . $fileName);

        if ($fileName !== '' && file_exists($filePath)) {
            return @file_get_contents($filePath);
        }

        Craft::error('File `' . $filePath . '` not found, mate!', __METHOD__);
        return '';
    }

    /**
     * Stamps a file name or path with timestamp or hash.
     *
     * @param string $fileName
     * @param string $mode
     * @param string $type
     * @return string
     */
    public function stamp(string $fileName, string $mode = 'file', string $type = 'ts'): string
    {
        $documentRoot = ToolMate::getInstance()?->getSettings()->publicRoot;
        $filePath = FileHelper::normalizePath($documentRoot . '/' . $fileName);

        if ($fileName === '' || !file_exists($filePath)) {
            Craft::error('File `' . $filePath . '` not found, mate!', __METHOD__);
            return '';
        }

        $path_parts = pathinfo($fileName);

        $stamp = $type === 'hash' ? $this->numHashFile($filePath) : filemtime($filePath);

        if ($mode === 'file') {
            return $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $stamp . '.' . $path_parts['extension'];
        }

        if ($mode === 'folder') {
            return $path_parts['dirname'] . '/' . $stamp . '/' . $path_parts['filename'] . '.' . $path_parts['extension'];
        }

        if ($mode === 'query') {
            return $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $path_parts['extension'] . '?ts=' . $stamp;
        }

        if ($mode === 'tsonly' || $mode === 'only') {
            return (string)$stamp;
        }

        Craft::error('Stamp mode `' . $mode . '` is unknown to me, mate!', __METHOD__);
        return '';
    }

    /**
     * @param array $params
     * @param bool $secure
     * @throws \Exception
     */
    public function setCookie(array $params, bool $secure = false): void
    {
        $defaults = [
            'name' => '',
            'value' => '',
            'expire' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => false,
            'sameSite' => null,
        ];

        // todo : Make model and validate
        $params = array_merge($defaults, $params);

        if ($params['name'] === '') {
            throw new \Exception('Parameter `name` is required for setCookie, mate!', __METHOD__);
        }

        if ($params['value'] === '') {
            Craft::$app->response->cookies->remove($params['name']);
            return;
        }

        $params['domain'] = empty($params['domain']) ? Craft::$app->getConfig()->getGeneral()->defaultCookieDomain : $params['domain'];
        $params['expire'] = (int)$params['expire'];

        if ($secure) {
            $cookie = new Cookie(['name' => $params['name'], 'value' => '']);

            try {
                $cookie->value = Craft::$app->security->hashData(base64_encode(serialize($params['value'])));
            } catch (\Throwable $e) {
                Craft::error('Error setting secure cookie: ' . $e->getMessage(), __METHOD__);
                return;
            }
            
            $cookie->expire = $params['expire'];
            $cookie->path = $params['path'];
            $cookie->domain = $params['domain'];
            $cookie->secure = $params['secure'];
            $cookie->httpOnly = $params['httpOnly'];
            
            if (PHP_VERSION_ID >= 70300) {
                $cookie->sameSite = $params['sameSite'];
            }
            
            Craft::$app->response->cookies->add($cookie);
        } else {
            if (PHP_VERSION_ID >= 70300) {
                setcookie($params['name'], $params['value'], [
                    'expires' => $params['expire'],
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => true,
                    'httponly' => $params['httpOnly'],
                    'samesite' => $params['sameSite'],
                ]);
            } else {
                setcookie($params['name'], $params['value'], $params['expire'], $params['path'], $params['domain'], $params['secure'], $params['httpOnly']);
            }

            $_COOKIE[$params['name']] = $params['value'];
        }
    }

    /**
     * @param string $name
     * @param bool $secure
     * @return mixed|string
     */
    public function getCookie(string $name, bool $secure = false): mixed
    {
        $result = '';

        if ($secure) {
            $cookie = Craft::$app->request->cookies->get($name);
            
            if ($cookie !== null) {
                try {
                    $data = Craft::$app->security->validateData($cookie->value);
                } catch (\Throwable $e) {
                    Craft::error('Error getting secure cookie: ' . $e->getMessage(), __METHOD__);
                    $data = false;
                }

                if ($cookie && !empty($cookie->value) && $data !== false) {
                    $result = unserialize(base64_decode($data), ['allowed_classes' => false]);
                }
            }
        } else if (isset($_COOKIE[$name])) {
            $result = $_COOKIE[$name];
        }
        
        return $result;
    }


    /**
     * ----- Private methods -----------------------------------------------
     */

    /**
     * Exactly like hash_file except that the result is always numeric
     * (consisting of characters 0-9 only).
     *
     * @param string $filePath
     * @return string
     */
    private function numHashFile(string $filePath): string
    {
        return implode(unpack('C*', hash_file('crc32b', $filePath, true)));
    }
}
