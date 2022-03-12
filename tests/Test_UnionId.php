<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 3/12/2022
 * Time: 4:45 PM
 */

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Tencent\QQ\Open\UserAPI;

class Test_UnionId extends TestCase {

    public function testGetUnionId() {
        $appId = '';
        $appKey = '';

        $accessToken = '';

        try {
            $result = (new UserAPI($appId, $appKey))->getUnionId($accessToken);
        } catch (Throwable $e) {
            print_r('Error Message: ' . $e->getMessage());
            print_r('Error Code: ' . $e->getCode());

            if ($e instanceof ClientException) {
                print_r($e->getResponse());
            } elseif ($e instanceof BadResponseException) {
                print_r(json_decode($e->getResponse()->getBody(), true));
            }

            die;
        }

        /**
         * [
         *   'client_id' => '123456789',
         *   'openid'    => 'E10ADC3949BA59ABBE56E057F20F883E',
         *   'unionid'   => 'UID_E10ADC3949BA59ABBE56E057F20F883E'
         * ]
         */
        print_r($result);

        $this->assertTrue($result['unionid'] ?? null);
    }
}
