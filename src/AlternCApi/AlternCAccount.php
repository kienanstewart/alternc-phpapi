<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

use AlternCApi\AlternCApi;

class AlternCAccount {

   protected $api;
   protected $uid;
   protected $login;
   protected $first_name;
   protected $last_name;
   protected $mail;
   protected $info = array();

   public function __construct(AlternCApi $api, $uid, $login = NULL,
                               $first_name, $last_name, $mail,
                               array $params = array()) {
      $this->api = $api;
      $this->uid = $uid;
      $this->login = $login;
      $this->first_name = $first_name;
      $this->last_name = $last_name;
      $this->mail = $mail;
      $this->info = $params;
   }

   /**
    * Lock the account.
    */
   public function lock() {
      return $this->api->objectRequest('account', 'lock', array('uid' => $this->uid));
   }

   /**
    * Unlock the account.
    */
   public function unlock() {
      return $this->api->objectRequest('account', 'unlock', array('uid' => $this->uid));
   }

   /**
    * Delete the account.
    */
   public function delete() {
      return $this->api->objectRequest('account', 'del', array('uid' => $this->uid));
   }

   public function setAdmin() {
      $this->api->objectRequest('account', 'setAdmin', array('uid' => $this->uid));
   }

   public function unsetAdmin() {
      $this->api->objectRequest('account', 'unsetAdmin', array('uid' => $this->uid));
   }

   public function getUid() {
      return $this->uid;
   }
}
