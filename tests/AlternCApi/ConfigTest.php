<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi\Tests;

use AlternCApi\AlternCApi;

class ConfigTest extends \PHPUnit_Framework_TestCase {

   /**
    * @expectedException \AlternCApi\Exceptions\AlternCApiException
    */
   public function testInstantiatingWithoutUrlThrows() {
      putenv(AlternCApi::ALTERNC_URL_ENV_NAME.'=');
      $config = array(
         'user' => 'x',
         'secret' => 'x',
      );
      new AlternCApi($config);
   }

   /**
    * @expectedException \AlternCApi\Exceptions\AlternCApiException
    */
   public function testInstantiatingWithoutSecretThrows() {
      putenv(AlternCApi::ALTERNC_SECRET_ENV_NAME.'=');
      $config = array(
         'url' => 'x',
      );
      new AlternCApi($config);
   }

   /**
    * @expectedException \AlternCApi\Exceptions\AlternCApiException
    */
   public function testInstantiatingWithInvalidLoginMethodThrows() {
      $config = array(
         'url' => 'x',
         'secret' => 'x',
         'login_method' => 'foo',
      );
      new AlternCApi($config);
   }

   /**
    * @expectedException \AlternCApi\Exceptions\AlternCApiException
    */
   public function testInstantiatingWithInvalidRequestMethodThrows() {
      $config = array(
         'url' => 'x',
         'secret' => 'x',
         'request_method' => 'foo',
      );
      new AlternCApi($config);
   }

   /**
    * @expectedException \AlternCApi\Exceptions\AlternCApiException
    */
   public function testInstantiatingWithInvalidApiStyleThrows() {
      $config = array(
         'url' => 'x',
         'secret' => 'x',
         'api_style' => 'foo',
      );
      new AlternCApi($config);
   }

   /**
    * @expectedException \AlternCApi\Exceptions\AlternCApiException
    */
   public function testInstantiatingWithoutUserForUserLoginThrows() {
      putenv(AlternCApi::ALTERNC_USER_ENV_NAME.'=');
      $config = array(
         'url' => 'x',
         'secret' => 'x',
      );
      new AlternCApi($config);
   }

}