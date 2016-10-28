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
                               'secret' => AlternCApiTestCredentials::$secret[AlternCApiTestCredentials::$login_method],
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
      $login_methods = AlternCApi::getAvailableLoginMethods(); //array('secret', 'user');
      $api_styles = AlternCApi::getAvailableApiStyles(); //array('rest', 'post');
      $request_methods = AlternCApi::getAvailableRequestMethods(); //array('GET', 'POST');
      foreach ($login_methods as $login_method) {
         foreach ($api_styles as $api_style) {
            foreach ($request_methods as $request_method) {
               print "Testing {$login_method} in {$api_style} style using {$request_method} requests...";
               $api = new AlternCApi(
                  array(
                     'url' => AlternCApiTestCredentials::$url,
                     'user' => AlternCApiTestCredentials::$user,
                     'secret' => AlternCApiTestCredentials::$secret[$login_method],
                     'login_method' => $login_method,
                     'api_style' => $api_style,
                     'request_method' => $request_method,
                     'token' => '',
                  )
               );
               $this->assertTrue($api->authenticate());
               print " OK!\n";
            }
         }
      }
   }

}
