<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

use AlternCApi\AlternCApi;

class AlternCDomain {

   protected $api;
   protected $id;
   protected $name;
   protected $owner;
   protected $manage_dns;
   protected $manage_mx;
   protected $noerase;
   protected $dns_action;
   protected $dns_result;
   protected $ttl;
   protected $subdomains;

   /**
    * Creates a new AlternCDomain object.
    *
    * $param AlternCApi $api The api object for running queries.
    * $param int $id The id of the domain in the AlternC database.
    * $param string $name The domain name.
    * $param int $owner The uid of the domain owner in AlternC.
    */
   public function __construct($api, $id, $name, $owner, $manage_dns = 0,
                               $manage_mx = 0, $noerase = 0, $dns_action = 'OK',
                               $dns_result = 0, $ttl = 86400) {
      $this->api = $api;
      $this->id = $id;
      $this->name = $name;
      $this->owner = $owner;
      $this->manage_dns = $manage_dns;
      $this->manage_mx = $manage_mx;
      $this->noerase = $noerase;
      $this->dns_action = $dns_action;
      $this->dns_result = $dns_result;
      $this->ttl = $ttl;
   }

   public function getName() {
      return $this->name;
   }

   public function getDnsAction() {
      return $this->dns_action;
   }

   public function getNoerase() {
      return $this->noerase;
   }

   /**
    * Creates a new domain in AlternC.
    *
    * @param AlternCApi $api The api object through which to make the calls.
    * @param string $name The domain name.
    * @param bool $manage_dns TRUE if the dns should be locally managed.
    * @param NULL|bool $noerase Set to TRUE (admin only) to prevent the domain from being erasable in the interface. Default NULL.
    * @param NULL|bool $force Force the creation of the domain (@TODO When should this be used?). Default NULL.
    * @param NULL|bool $is_slave @TODO What is this? Default NULL.
    * @param NULL|string $slave_domain @TODO What is this? Default NULL.
    *
    * @returns NULL|AlternCDomain NULL on failure, otherwise an instance of AlternCDomain is returned.
    *
    * @throws \AlternCApi\Exceptions\AlternCApiException
    */
   public static function add($api, $name, $manage_dns, $noerase = NULL,
                              $force = NULL, $is_slave = NULL, $slave_domain = NULL) {
      $args = array(
         'domain' => $name,
         'dns' => $manage_dns,
      );
      if ($noerase !== NULL) {
         $args['noerase'] = $noerase;
      }
      if ($force !== NULL) {
         $args['force'] = $force;
      }
      if ($is_slave !== NULL) {
         $args['isslave'] = $is_slave;
      }
      if ($slave_domain !== NULL) {
         $args['slavedom'] = $slave_domain;
      }
      $response = $api->objectRequest('domain', 'add', $args);
      if (!$response->getBody() || !$response->getBody()->content) {
         return NULL;
      }
      if ($response->getBody()->content) {
         $domain = self::get($api, $name);
         if ($domain) {
            // If it's NULL, it will fall through and throw an error.
            return $domain;
         }
      }
      throw new \AlternCApi\Exceptions\AlternCApiException("Couldn't find newly created domain even though AlternC said the creation worked.");
   }

   /**
    * Get domains stored in AlternC.
    *
    * If the requesting user is not admin, only their domains will be listed.
    * If the uid is set, the domains returned will be restricted to those owned
    * by that user.
    *
    * @param int|NULL $uid The uid whose domains should be retrieve. Default NULL.
    * @param int|NULL $offset The offset for the query (for paging).
    * @param int|NULL $count The number of domains to return (for paging).
    *
    * @returns array An array containing AlternCDomain objects, indexed by domain name.
    *
    * Note: this api function doesn't get subdomain information for the domain.
    * If that is needed, call AlternCDomain::get($api, $name) instead.
    */
   public static function find($api, $uid = NULL, $offset = NULL, $count = NULL) {
      $args = array();
      if ($uid) {
         $args['uid'] = $uid;
      }
      if ($offset !== NULL) {
         $args['offset'] = $offset;
      }
      if ($count !== NULL) {
         $args['count'] = $count;
      }
      $domains = array();
      $response = $api->objectRequest('domain', 'find', $args);
      if (!$response->getBody()->content) {
         return $domains;
      }
      foreach ($response->getBody()->content as $index => $data) {
         $domains[$data->domaine] = new AlternCDomain(
            $api,
            $data->id,
            $data->domaine,
            $data->compte,
            $data->gesdns,
            $data->gesmx,
            $data->noerase,
            $data->dns_action,
            $data->dns_result,
            $data->zonettl);
      }
      return $domains;
   }

   /**
    * Gets a domain by name.
    *
    * @param AlternCApi $api The api object to use for the request(s) needed.
    * @param string $name The domain name.
    * @param int|NULL $owner The uid of the owner of the domain to limit the search scope.
    *
    * Note searches are limited to your own user, unless your user has admin set.
    *
    * @returns NULL|AlternCDomain An object with the domain information. NULL returned on failure.
    */
   public static function get($api, $name, $owner = NULL) {
      $args = array(
         'dom' => $name,
      );
      if ($owner !== NULL) {
         $args['uid'] = $owner;
      }
      $response = $api->objectRequest('domain', 'get', $args);
      if (!$response->getBody()->content) {
         return NULL;
      }
      $data = $response->getBody()->content;
      $domain = new AlternCDomain(
         $api,
         $data->id,
         $data->name,
         $data->dns,
         $data->mail,
         $data->noerase,
         $data->dns_action,
         $data->dns_result,
         $data->zonettl
      );
      // @TODO Add subdomains.
      return $domain;
   }

   /**
    * Deletes the domain.
    *
    * @returns bool TRUE on success, FALSE otherwise.
    */
   public function delete() {
      $args = array(
         'domain' => $this->name,
      );
      $response = $this->api->objectRequest('domain', 'del', $args);
      if ($response->getBody()->content) {
         return TRUE;
      }
      return FALSE;
   }

   public function isDeleted() {
      return ($this->dns_action == 'DELETE' || $this->noerase == 'DELETE');
   }

}
