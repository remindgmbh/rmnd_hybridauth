<?php

namespace Remind\RmndHybridauth\Login;

/**
 * @author Marco Wegner <m.wegner@remind.de>
 */
class HashGenerator
{
    /**
     *
     * @param string $ip
     * @param string $token
     * @return string
     */
    private static function buildLoginKey(string $ip, string $token): string
    {
        return $ip . '-' . $token;
    }


    /**
     *
     * @param string $ip
     * @param string $token
     * @return string
     */
    public static function generateLoginHash(string $ip, string $token): string
    {
        $loginKey = self::buildLoginKey($ip, $token);
        $loginHash = self::generateHash($loginKey);

        return $loginHash;
    }

    /**
     * Validate login information
     *
     * @param string $ip
     * @param string $token
     * @param type $hash
     * @return bool
     */
    public static function isValidLogin(string $ip, string $token, $hash): bool
    {
        $loginKey = self::buildLoginKey($ip, $token);
        return \password_verify($loginKey, $hash);
    }

    /**
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomHash(int $length): string
    {
        $hash = \substr(\md5(\openssl_random_pseudo_bytes(20)), -$length);
        return $hash;
    }

    /**
     *
     * @param string $text
     * @return string
     */
    public static function generateHash(string $text): string
    {
        /* PHP7.2 */
        $hash = \password_hash($text, \PASSWORD_ARGON2I);

        return $hash;
    }
}
