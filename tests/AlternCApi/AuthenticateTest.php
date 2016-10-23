<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi\Tests;

use AlternCApi\AlternCApi;
//use AlternCApi\Tests\AlternCApi\TestCredentials;

/**
 * @backupGlobals disabled
 * Class for testing authentication methods.
 */
class AuthenticateTest extends \PHPUnit_FrameWork_TestCase {

   /**
    * @beforeClass
    */
   public static function initializeAlternCApi() {
      global $api;
      $api = new AlternCApi(array(
                               'url' => AlternCApiTestCredentials::$url,
                               'user' => AlternCApiTestCredentials::$user,
                               'secret' => AlternCApiTestCredentials::$secret,
                               'login_method' => AlternCApiTestCredentials::$login_method,
                               'token' => AlternCApiTestCredentials::$token,
                               )
      );
   }

   /**
    * @afterClass
    */
   public function teardown() {
      global $api;
      $api = NULL;
   }

   /**
    *
    */
   public function testAuthTokenAlreadySet() {
      $a = new AlternCApi(array(
                             'url' => 'x',
                             'login_method' => 'secret',
                             'user' => 'x',
                             'secret' => 'x',
                             'token' => 'xxx_token_xxx',
                             ));
      $this->assertTrue($a->authenticate());
   }

   /**
    * @group integration
    */
   public function testGetNewAuthToken() {
      $api = new AlternCApi(array(
                               'url' => AlternCApiTestCredentials::$url,
                               'user' => AlternCApiTestCredentials::$user,
                               'secret' => AlternCApiTestCredentials::$secret,
                               'login_method' => AlternCApiTestCredentials::$login_method,
                               'token' => ''
                               )
      );
      $this->assertTrue($api->authenticate());
   }

}
