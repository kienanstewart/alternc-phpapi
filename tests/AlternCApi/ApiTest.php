<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi\Tests;

use AlternCApi\AlternCApi;

/**
 * @backupGlobals disabled
 */
class ApiTest extends \PHPUnit_FrameWork_TestCase {

   /**
    * @beforeClass
    */
   public static function init() {
      global $api;
      $api = new AlternCApi(array(
                               'url' => AlternCApiTestCredentials::$url,
                               'user' => AlternCApiTestCredentials::$user,
                               'secret' => AlternCApiTestCredentials::$secret,
                               'login_method' => AlternCApiTestCredentials::$login_method,
                               'token' => AlternCApiTestCredentials::$token,
                               'request_method' => 'GET',
                               )
      );
   }

   /**
    * @group integration
    */
   public function testFind() {
      global $api;
      $response = $api->find_accounts();
      $this->assertInternalType('array', $response);
   }

   /**
    * @group integration
    */
   public function testCreateAndDelete() {
      global $api;
      $response = $api->add_account('test', 'test@example.com', 'test', 'test', 'test');
      $this->assertInstanceOf('AlternCApi\AlternCAccount', $response);
      $id = $response->getUid();
      $response->delete();
      $this->assertEmpty($api->find_accounts('uid', $id));
   }

}