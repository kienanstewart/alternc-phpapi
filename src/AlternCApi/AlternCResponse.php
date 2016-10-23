<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

class AlternCResponse {

   /**
    * @var \GuzzleHttp\Client|null Raw response.
    */
   protected $raw_response = NULL;

   /**
    * @var stdObj|null Decoded response in object form.
    */
   protected $response = NULL;

   /**
    * Creates a new AlternC Response object.
    *
    * @param \GuzzleHttp\Psr7\Response $response The response object from a Guzzle request.
    */
   public function __construct(\GuzzleHttp\Psr7\Response $response) {
      $this->raw_response = $response;
   }

   /**
    * @returns object Object from decoding JSON response.
    */
   public function getBody() {
      // AlternC should always return the body in json format.
      if (!$this->response) {
         $this->response = json_decode($this->raw_response->getBody());
      }
      return $this->response;
   }

}
