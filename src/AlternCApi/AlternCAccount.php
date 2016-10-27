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
    * Searches for alternc accounts.
    *
    * @param string $key The key to filter by. One of: uid, login, domain, creator.
    * @param string $value The value of the key to filter by: int for uid, creator; string for login, domain.
    *
    * While AlternC says the the uid & creator finds are "strict", if one
    * deletes a user by uid, then do find_accounts('uid', $deleted_id),
    * AlternC returns a list of _all_ accounts instead of saying there is
    * no such account by that ID. To check that an account is deleted, you
    * must therefore iterate through the array that is returned by this and make
    * sure the key you are expecting to have deleted is no longer there.
    * yay AlternC!
    *
    * @returns array An array of AlternCAccount objects indexed by uid.
    */
   public static function find($api, $key = '', $value = '') {
      $args = array();
      if ($key) {
         $args[$key] = $value;
      }
      $response = $api->objectRequest('account', 'find', $args);
      $users = array();
      foreach ($response->getBody()->content as $id => $data) {
         $params = array();
         $users[$data->uid] = new AlternCAccount(
            $api, $data->uid, $data->login, $data->prenom,
            $data->nom, $data->mail, array(
               'muid' => $data->muid,
               'pass' => $data->pass,
               'su' => $data->su,
               'enabled' => $data->enabled,
               'lastaskpass' => $data->lastaskpass,
               'lastfail' => $data->lastfail,
               'lastip' => $data->lastip,
               'creator' => $data->creator,
               'canpass' => $data->canpass,
               'warnlogin' => $data->warnlogin,
               'warnfailed' => $data->warnfailed,
               'admlist' => $data->admlist,
               'type' => $data->type,
               'db_server_id' => $data->db_server_id,
               'notes' => $data->notes,
               'created' => $data->created,
               'duration' => $data->duration,
               'parentlogin' => $data->parentlogin,
               'expiry' => $data->expiry,
               'status' => $data->status,
            )
         );
      }
      return $users;
   }

   /**
    * Creates a new AlternC account.
    *
    * @param AlternCApi $api The api object to use for requests.
    * @param string $login The desired login for the account.
    * @param string $mail The e-mail address to associate with the account.
    * @param string $password The password for the account (plain text).
    * @param string $first_name The first name of the user for the account.
    * @param string $last_name The last name of the user for the account.
    *
    * @returns NULL|AlternCAccount An AlternC account object, or NULL in the case of failure.
    */
   public static function add($api, $login, $mail, $password, $first_name, $last_name) {
      $params  = array(
         'login' => $login,
         'mail' => $mail,
         'pass' => $password,
         'nom' => $last_name,
         'prenom' => $first_name
      );
      $response = $api->objectRequest('account', 'add', $params);
      if ($response_body = $response->getBody()) {
         $user = AlternCAccount::find($api, 'uid', $response_body->content);
         if (isset($user[$response_body->content])) {
            $user = $user[$response_body->content];
            return $user;
         }
      }
      return NULL;
   }

   /**
    * Gets a user by login.
    *
    * Note: AlternC doesn't actually have get() for accounts, but this is added
    * for consistency across objects in this API. find() is used in the backround.
    *
    * @param AlternCApi $api The Api object to use for the requests.
    * @param string $login The login for the account to get.
    *
    * @returns NULL|AlternCAccount NULL on failure, or an instance of AlternCAccount.
    */
   public static function get($api, $login) {
      $accounts = AlternCAccount::find($api, $login);
      $account = NULL;
      // Iterate over answers and verify the login is an exact match.
      // Find returns any accounts with logins matching "%{$login}%".
      foreach ($accounts as $uid => $a) {
         if ($a->getLogin() == $login) {
            $account = $a;
            break;
         }
      }
      return $account;
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

   /**
    * Gets the login for the account.
    *
    * @returns string The account login.
    */
   public function getLogin() {
      return $this->login;
   }

}
