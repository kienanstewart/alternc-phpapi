<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi\Tests;

use AlternCApi\AlternCApi;
use AlternCApi\AlternCAccount;

/**
 * @backupGlobals disabled
 */
class AccountTest extends \PHPUnit_FrameWork_TestCase {

   /**
    * @beforeClass
    */
   public static function before() {
      global $api, $account;
      $api = new AlternCApi(
         array(
            'url' => AlternCApiTestCredentials::$url,
            'user' => AlternCApiTestCredentials::$user,
            'secret' => AlternCApiTestCredentials::$secret[AlternCApiTestCredentials::$login_method],
            'login_method' => AlternCApiTestCredentials::$login_method,
            'token' => AlternCApiTestCredentials::$token,
            'request_method' => 'GET',
         )
      );
      $account = AlternCAccount::add($api, 'test', 'test@example.com', 'test', 'test', 'test');
   }

   /**
    * @afterClass
    */
   public static function after() {
      global $api, $account;
      $account->delete();
   }

   /**
    * @group integration
    */
   public function testFind() {
      global $api;
      $response = AlternCAccount::find($api);
      $this->assertInternalType('array', $response);
   }

   /**
    * @group integration
    */
   public function testCreateAndDelete() {
      global $api;
      $response = AlternCAccount::add($api, 'best', 'best@example.com', 'best', 'best', 'best');
      $this->assertInstanceOf('AlternCApi\AlternCAccount', $response);
      $id = $response->getUid();
      $this->assertTrue($response->delete());
      $r = AlternCAccount::find($api, 'uid', $id);
      $this->assertArrayNotHasKey($id, $r);
   }

   /**
    * @group integration
    */
   public function testLockUnlock() {
      global $account;
      $this->assertFalse($account->isLocked());
      $this->assertTrue($account->lock());
      $this->assertTrue($account->isLocked());
      // @TODO Maybe fetch a new account info and double check that the value
      // of 'enabled' changed in AlternC.
      $this->assertTrue($account->unlock());
      $this->assertFalse($account->isLocked());
   }

   /**
    * @group integration
    */
   public function testSetAdminUnsetAdmin() {
      global $account;
      $this->assertFalse($account->isAdmin());
      $this->assertTrue($account->setAdmin());
      $this->assertTrue($account->isAdmin());
      // @TODO Again, maybe fetch a new account info and double check that
      // the value of 'su' changed in AlternC.
      $this->assertTrue($account->unsetAdmin());
      $this->assertFalse($account->isAdmin());
   }

}
