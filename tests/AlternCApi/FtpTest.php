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
use AlternCApi\AlternCDomain;
use AlternCApi\AlternCFtp;

/**
 * @backupGlobals disabled
 */
class FtpTest extends \PHPUnit_FrameWork_TestCase {

   /**
    * @beforeClass
    */
   public static function before() {
      global $api, $account, $domain;
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
      $domain = AlternCDomain::add($api, 'ftptest.com', FALSE, NULL, TRUE);
   }

   /**
    * @afterClass
    */
   public static function after() {
      global $api, $account, $domain;
      $domain->delete();
      $account->delete();
   }

   /**
    * @group integration
    */
   public function testFindCreateGetDelete() {
      global $api, $domain;
      $this->assertInternalType('array', AlternCFtp::find($api));
      $ftp1 = AlternCFtp::add($api, $domain->getName(), 'ftp1', 'abdc1234', 'a');
      $this->assertInstanceOf('AlternCApi\AlternCFtp', $ftp1);
      $this->assertEquals('ftptest.com_ftp1', $ftp1->getLogin());
      $this->assertEquals(1, $ftp1->getStatus());
      $ftps = AlternCFtp::find($api);
      $this->assertNotEmpty($ftps);
      $this->assertTrue($ftp1->delete());
   }

}
