<?php

require_once '../vendor/autoload.php';

use App\shorturl\ShortUrl;

dd(ShortUrl::to62String(123), '123 to62String');

dd(ShortUrl::toInt('7w'), '7w toInt');

// test1
$str = 'https://mbd.baidu.com/newspage/data/landingsuper?context=%7B%22nid%22%3A%22news_9485168774681337082%22%7D&n_type=0&p_from=1';
$code = ShortUrl::url2code($str);
dd($code, 'url2code: ' . $str);

$url = ShortUrl::code2url($code);

dd($url, 'code2url: ' . $code);


// test2
$str = 'http://news.sina.com.cn/s/2020-08-24/doc-iivhvpwy2835680.shtml';
$code = ShortUrl::url2code($str);
dd($code, 'url2code: ' . $str);

$url = ShortUrl::code2url($code);

dd($url, 'code2url: ' . $code);