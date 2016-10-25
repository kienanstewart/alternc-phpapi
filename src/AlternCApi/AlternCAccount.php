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
    * Is the account locked?
    *
    * @returns bool Whether or not the account is locked.
    */
   public function isLocked() {
      return (!(bool) $this->info['enabled']);
   }

   /**
    * Is the account admin?
    *
    * @returns bool Wether or not the account is an admin account.
    */
   public function isAdmin() {
      return (bool) $this->info['su'];
   }

   /**
    * Lock the account.
    *
    * @returns bool TRUE if the action succeeded, FALSE otherwise.
    */
   public function lock() {
      $response = $this->api->objectRequest('account', 'lock', array('uid' => $this->uid));
      if ($response->getBody()->content) {
         // Rather than re-request the whole user.
         $this->info['enabled'] = 0;
         return TRUE;
      }
      return FALSE;
   }

   /**
    * Unlock the account.
    *
    * @returns bool TRUE is the action succeeded, FALSE otherwise.
    */
   public function unlock() {
      $response = $this->api->objectRequest('account', 'unlock', array('uid' => $this->uid));
      if ($response->getBody()->content) {
         $this->info['enabled'] = 1;
         return TRUE;
      }
      return FALSE;
   }

   /**
    * Delete the account.
    *
    * @returns bool TRUE if the action succeeded, FALSE otherwise.
    */
   public function delete() {
      $response = $this->api->objectRequest('account', 'del', array('uid' => $this->uid));
      if ($response->getBody()->content) {
         return TRUE;
      }
      return FALSE;
   }

   /**
    * Set the account to have admin privileges.
    *
    * @returns bool TRUE if the action succeeded, FALSE otherwise.
    */
   public function setAdmin() {
      $response = $this->api->objectRequest('account', 'setAdmin', array('uid' => $this->uid));
      if ($response->getBody()->content) {
         $this->info['su'] = 1;
         return TRUE;
      }
      return FALSE;
   }

   /**
    * Remove admin privileges from the account.
    *
    * @returns bool TRUE if the action succeeded, FALSE otherwise.
    */
   public function unsetAdmin() {
      $response = $this->api->objectRequest('account', 'unsetAdmin', array('uid' => $this->uid));
      if ($response->getBody()->content) {
         $this->info['su'] = 0;
         return TRUE;
      }
      return FALSE;
   }

   public function getUid() {
      return $this->uid;
   }
}
