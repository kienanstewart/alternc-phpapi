<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

use AlternCApi\Exceptions\AlternCApiException;

/**
 * Class AlternCApi.
 */
class AlternCApi {

   /**
    * Version number of the AlternC PHP API.
    */
   const VERSION = '0.0.1-dev';

   /**
    * The name of the environment variable for the AlternC URL.
    */
   const ALTERNC_URL_ENV_NAME = 'ALTERNC_PHPAPI_ALTERNC_URL';

   /**
    * The name of the environment variable for the AlternC User.
    */
   const ALTERNC_USER_ENV_NAME = 'ALTERNC_PHPAPI_ALTERNC_USER';

   /**
    * The name of the enviroment variable for the Alternc Secret.
    */
   const ALTERNC_SECRET_ENV_NAME = 'ALTERNC_PHPAPI_ALTERNC_SECRET';

   /**
    * POST Request method.
    */
   const REQUEST_METHOD_POST = 'POST';

   /**
    * GET Request method.
    */
   const REQUEST_METHOD_GET = 'GET';

   /**
    * Permitted Request Methods.
    */
   protected $request_methods = array(
      AlternCApi::REQUEST_METHOD_POST,
      AlternCApi::REQUEST_METHOD_GET,
   );

   /**
    * "REST" like API style.
    */
   const API_STYLE_REST = 'rest';

   /**
    * "post" like API style.
    */
   const API_STYLE_POST = 'post';

   /**
    * Permitted API Styles.
    */
   protected $api_styles = array(
      AlternCApi::API_STYLE_POST,
      AlternCApi::API_STYLE_REST,
   );

   /**
    * User based login method.
    */
   const LOGIN_METHOD_USER = 'user';

   /**
    * Shared secret login method.
    */
   const LOGIN_METHOD_SHARED_SECRET = 'secret';

   /**
    * Permitted login methods.
    */
   protected $login_methods = array(
      AlternCApi::LOGIN_METHOD_SHARED_SECRET,
      AlternCApi::LOGIN_METHOD_USER,
   );

   /**
    *
    */
   public function __construct(array $config = array()) {
      $config = array_merge(
         array(
            'url' => getenv(static::ALTERNC_URL_ENV_NAME),
            'user' => getenv(static::ALTERNC_USER_ENV_NAME),
            'secret' => getenv(static::ALTERNC_SECRET_ENV_NAME),
            'request_method' => static::REQUEST_METHOD_POST,
            'login_method' => static::LOGIN_METHOD_SHARED_SECRET,
            'api_style' => static::API_STYLE_REST,
            'token' => false,
         ), $config);

      if (!$config['url']) {
         throw new AlternCApiException(
            "Required key 'url' not supplied in config and could not find fallback environment '{static::ALTERNC_URL_ENV_NAME}'"
         );
      }
      if (!$config['secret']) {
         throw new AlternCApiException(
            "Required key 'secret' not supplied in config and could not find fallback environment '{static::ALTERNC_URL_ENV_NAME}'"
         );
      }
      $limited_config_options = array(
         'request_method' => $this->request_methods,
         'login_method' => $this->login_methods,
         'api_style' => $this->api_styles,
      );
      foreach ($limited_config_options as $key => $values) {
         if (!in_array($config[$key], $values)) {
            throw new AlternCApiException(
               "Config key '{$key}' with value '{$config[$key]}' not in allowed values:" . implode(', ', $values)
            );
         }
      }

      if ($config['login_method'] = static::LOGIN_METHOD_USER && !$config['user']) {
         throw new AlternCApiException(
            "Config key 'user' not set and no fallback found in environment variable '{static::ALTERNC_USER_ENV_NAME}' while using login method 'user'"
         );
      }
   }
}
