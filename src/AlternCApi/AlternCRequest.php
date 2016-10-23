<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

/**
 * Request class.
 */
class AlternCRequest {

   /**
    * AlternC Api instance.
    */
   protected $api;

   /**
    * Path for the request.
    */
   protected $path;

   /**
    * Method for the request.
    */
   protected $method;

   /**
    * Token to use to authenticate this request.
    */
   protected $token;

   /**
    * Parameters for the request.
    */
   protected $params;

   /**
    * Headers for the request.
    */
   protected $headers;

   /**
    * Creates a new instance of the request class.
    *
    * @param AlternCApi $api An instance of the AlternCApi class that spawned the request
    * @param string $method The method to use for the request
    * @param string $path The path to send the request to, eg. '/api/someAction'
    * @param string $token The token use to use to authenticate the request
    * @param array $params An array of key-value pairs to add to the request.
    */
   public function __construct(AlternCApi $api, $method, $path, $token, array $params = array()) {
      $this->api = $api;
      $this->path = $path;
      $this->method = $method;
      $this->token = $token;
      $this->params = $params;
      $this->headers = AlternCRequest::getDefaultHeaders();
   }

   /**
    * Default headers for all requests.
    */
   public static function getDefaultHeaders() {
      return array(
         'User-Agent' => 'alternc-php-api-' . AlternCApi::VERSION,
         'Accept-Encoding' => '*',
      );
   }

   /**
    * Returns the method.
    */
   public function getMethod() {
      return $this->method;
   }

   /**
    * Returns the full path of the request.
    */
   public function getPath() {
      return $this->api->getUrl() . '/' . $this->path;
   }

   /**
    * Sets the headers.
    */
   public function setHeaders(array $headers) {
      $this->headers = array_merge($headers, $this->headers);
   }

   /**
    * Returns the headers for the request.
    */
   public function getHeaders() {
      return $this->headers;
   }

   /**
    * Returns the parameters for the request.
    */
   public function getParams() {
      $extra = array();
      if ($this->token) {
         $extra['token'] = $this->api->getToken();
      }
      return array_merge($extra, $this->params);
   }

}
