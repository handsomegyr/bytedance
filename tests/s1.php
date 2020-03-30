<?php
/*
 * This file is part of the php-code-coverage package.
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once '../vendor/autoload.php';

try {
    $openid = 'xxxx';
    // appid
    $appid = 'xxxxxxxxxxxxxx'; // appID
    // secret
    $secret = 'xxxxxxxxxxxxx'; // appsecret

    // 获得access_token

    // doGetAccessTokenTest($appid, $secret);
    $access_token = 'xxxxx';

    $client = new \Bytedance\Toutiao\Client();
    $client->setAccessToken($access_token);

    die('<br/>OK');
} catch (Exception $e) {
    die('<br/>ERROR:' . $e->getMessage());
}
