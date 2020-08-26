<?php

namespace App\shorturl;

/**
 * 长链转短链
 * 前置发号器+62进制表示法
 */
class ShortUrl
{
    const BASE62CHAR = 'o7cFVDQ8TqGCigUuANkeIZBx1jtv0J6KMl9L5ryp4ab3HfOWnd2XSYEPsRhzmw'; // 62进制字符A-Za-z0-9

    private static $redis;

    // url转短链
    public static function url2code($url)
    {
        if (empty($url) || strlen($url) < 20) {
            return $url;
        }
        $redis = self::getRedis();
        $code = $redis->get('shorturl:url2code:' . md5($url));
        if (empty($code)) {
            $code = self::generateCode($url);
            // 保证code不重复
            while ($redis->get('shorturl:code2url:' . $code)) {
                $code = self::generateCode($url);
            }

            $redis->set('shorturl:url2code:' . md5($url), $code);
            $redis->set('shorturl:code2url:' . $code, $url);
        }
        $host = ''; // http://short.com/
        return $host . $code;
    }

    // url转长链
    public static function code2url($code)
    {
        if (empty($code)) {
            return '';
        }
        $redis = self::getRedis();
        $url = $redis->get('shorturl:code2url:' . $code);

        return $url ?: '';
    }

    // url生成code,62进制表示法
    public static function generateCode($url)
    {
        $id = self::generateId($url);
        $code = self::to62String($id);

        return $code;
    }

    // url生成唯一ID
    public static function generateId($url)
    {
        // 1 MurmurHash算法生成
        // 2 前置发号器方案
        $redis = self::getRedis();
        $baseId = 10000000;
        $senderCount = 100; // 指定100个发号器
        $senderId = crc32($url) % $senderCount;  // 0 ~ 99
        // 0: 100 200 300.. 1: 101 201 301.. 2: 102 202 302.. ..... 99: 199 299 399..
        $id = $baseId + $senderId + $redis->incrBy('shorturl:sender:' . $senderId, $senderCount);

        return $id;
    }

    // 10进制转为62进制
    public static function to62String($int)
    {
        $base62 = '';
        while ($int > 0) {
            $mod = $int % 62;
            $int = (int)($int / 62);
            $base62 = self::BASE62CHAR[$mod] . $base62;
        }
        return $base62;
    }

    // 62进制转为10进制
    public static function toInt($str62)
    {
        $int = 0;
        for ($i = strlen($str62) - 1; $i >= 0; $i--) {
            $mod = strpos(self::BASE62CHAR, $str62[$i]);
            $int += $mod * pow(62, strlen($str62) - 1 - $i);
        }
        return $int;
    }

    private static function getRedis()
    {
        if (!self::$redis) {
            self::$redis = new \Redis();
            self::$redis->connect('127.0.0.1', '6379');
        }
        return self::$redis;
    }
}