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
   }

}