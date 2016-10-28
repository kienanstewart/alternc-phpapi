<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

use AlternCApi\AlternCApi;

class AlternCFtp {

   protected $api;
   protected $id;
   protected $login;
   protected $directory;
   protected $status;

   /**
    * Creates a new instance of AlternCFtp.
    *
    * @param AlternCApi $api An instance of AlternCApi to run requests against.
    * @param int $id The identifier of the FTP account in AlternC.
    * @param string $login The login identifier for the FTP account.
    * @param string $directory The directory where the FTP account is chrooted.
    * @param int $status The status of the acount. 1 enabled, 0 disabled.
    *
    * @returns AlternCFtp An instance of the AlternCFtp class.
    */
   public function __construct(AlternCApi $api, $id, $login, $directory, $status) {
      $this->api = $api;
      $this->id = $id;
      $this->login = $login;
      $this->directory = $directory;
      $this->status = $status;
   }

   /**
    * Adds a new FTP account in AlternC.
    *
    * @param AlternCApi $api An instance of AlternCApi to run requests against.
    * @param string $prefix A prefix for the account name (usually the domain, eg. example.com).
    * @param string $login The login for the ftp account, which is postpended to "$prefix_".
    * @param string $password The password for the FTP account (plain text).
    * @param string $directory The directory to chroot the FTP account at.
    * @param array $options An array of extra options to pass to the request, indexed by key.
    *
    * @returns NULL|AlternCFtp An instance of AlternCFtp, the new account created. NULL is returned on failure.
    */
   public static function add($api, $prefix, $login, $password, $directory) {
      $args = array(
         'prefix' => $prefix,
         'login' => $login,
         'pass' => $password,
         'dir' => $directory,
         );
      $response = $api->objectRequest('ftp', 'add', $args);
      if ($response->getBody() && $response->getBody()->content) {
         $account = AlternCFtp::get($api, static::predictFullLogin($prefix, $login));
         return $account;
      }
      return NULL;
   }

   /**
    * Returns a list of matching FTP Accounts.
    *
    * Note: AlternC doesn't currently provide any keys to filter the find on.
    *
    * @param AlternCApi $api An instance of AlternCApi to run requests against.
    * @param NULL|int $offset An offset for the result list. Defaults to NULL.
    * @param NULL|int $count The maximum number of results to return. Defaults to NULL.
    *
    * @returns array An array of AlternCFtp instances, indexed by FTP account id.
    */
   public static function find($api, $offset = NULL, $count = NULL) {
      $args = array();
      if ($offset !== NULL) {
         $args['offset'] = $offset;
      }
      if ($count !== NULL) {
         $args['count'] = $count;
      }
      $response = $api->objectRequest('ftp', 'find', $args);
      $results = array();
      if ($response->getBody() && $response->getBody()->content) {
         foreach ($response->getBody()->content as $index => $data) {
            $results[$data->id] = new AlternCFtp($api, $data->id, $data->login,
                $data->dir, $data->enabled
            );
         }
      }
      return $results;
   }

   /**
    * Gets a specific FTP account.
    *
    * @param AlternCApi $api An instance of AlternCApi to run requests against.
    * @param string $login The login for the AlternC account to get.
    *
    * @returns AlternCFtp|NULL An instance of AlternCFtp for the found account or NULL.
    */
   public static function get($api, $login) {
      $accounts  = AlternCFtp::find($api);
      foreach ($accounts as $index => $account) {
         if ($account->getLogin() == $login) {
            return $account;
         }
      }
      return NULL;
   }

   /**
    * Deletes the existing FTP Account.
    *
    * @returns bool TRUE on success, FALSE on failure.
    */
   public function delete() {
      $response = $this->api->objectRequest('ftp', 'del', array('id' => $this->id));
      if ($response->getBody() && $response->getBody()->content) {
         return TRUE;
      }
      return FALSE;
   }

   /**
    * Gets the login for this account.
    */
   public function getLogin() {
      return $this->login;
   }

   /**
    * Gets the status of the FPT account.
    */
   public function getStatus() {
      return $this->status;
   }

   /**
    * Gets the likely full login based on prefix and login.
    *
    * @param string $prefix The prefix for the FTO account. Usually the domain name.
    * @param string $login The login for the FTP account.
    *
    * @returns string A likely full login name.
    */
   public static function predictFullLogin($prefix, $login) {
      return "{$prefix}_{$login}";
   }
}
