<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi\Tests;

use AlternCApi\AlternCApi;
use AlternCApi\AlternCDomain;

/**
 * @backupGlobals disabled
 */
class DomainTest extends \PHPUnit_FrameWork_TestCase {

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
   public function testDomainFindCreateGetAndDelete() {
      global $api;
      $domains = AlternCDomain::find($api);
      $this->assertInternalType('array', $domains);
      // AlternC may have no domains installed, so we'll create one.
      $domain = AlternCDomain::add($api, 'test.com', False, False, True);
      $this->assertInstanceOf('AlternCApi\AlternCDomain', $domain);
      $this->assertEquals('test.com', $domain->getName());
      // Internally, the add() function calls get() so we won't test that again.
      // However, now we can make sur find() returns at least one domain.
      $domains = AlternCDomain::find($api);
      $this->assertInternalType('array', $domains);
      $this->assertNotEmpty($domains);
      // Clean up our test.com domain.
      $this->assertTrue($domain->delete());
      $domain = AlternCDomain::get($api, 'test.com');
      $deleted = ($domain === NULL || $domain->isDeleted());
      $this->assertTrue($deleted);
   }

}