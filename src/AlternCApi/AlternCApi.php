<?php

/**
 * This file is part of AlternC PHP API
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace AlternCApi;

use AlternCApi\Exceptions\AlternCApiException;
use AlternCApi\AlternCAccount;

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
    * @var string AlternC URL to make requets to.
    */
   protected $url = '';

   /**
    * @var string|null User to log in as (user login method only).
    */
   protected $user;

   /**
    * @var string Shared secret (or password) for authentication.
    */
   protected $secret;

   /**
    * @var string The request_method to use.
    */
   protected $request_method;

   /**
    * @var string The login method to use for authentication.
    */
   protected $login_method;

   /**
    * @var string The API style to make requests against.
    */
   protected $api_style;

   /**
    * @var string|null The token to use once authenticated (or if it's still valid).
    */
   protected $token;

   /**
    * @var GuzzleHttp\Client The internal guzzle client.
    */
   protected $client;

   /**
    * Creates new AlternCApi instances.
    *
    * @param array $config Array containing configuration for the AlternCApi instance.
    *
    * Keys:
    *   string url - The url including protocol and port if necessary for the AlternC instance.
    *   string user - The user to use (only used if using using the login method 'user').
    *   string secret - The secret or password to authenticate with.
    *   string request_method - The request_method to use. 'GET' or 'POST'. (Default: POST).
    *   string login_method - The login method to use. 'user' or 'secret'. (Default: secret).
    *   string api_style - The endpoint api style. 'post' or 'rest'. (Default: rest).
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
            'token' => NULL,
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
      if (!$config['user']) {
         throw new AlternCApiException(
            "Required key 'user' not supplied in config and could not find fallback environment ' {static::ALTERNC_USER_ENV_NAME}'"
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

      if ($config['login_method'] == static::LOGIN_METHOD_USER && !$config['user']) {
         throw new AlternCApiException(
            "Config key 'user' not set and no fallback found in environment variable '{static::ALTERNC_USER_ENV_NAME}' while using login method 'user'"
         );
      }
      $this->request_method = $config['request_method'];
      $this->login_method = $config['login_method'];
      $this->api_style = $config['api_style'];
      $this->user = $config['user'];
      $this->url = $config['url'];
      $this->secret = $config['secret'];
      $this->token = $config['token'];
      $this->client = new \GuzzleHttp\Client;
   }

   /**
    * Gets a new token if none is set.
    */
   public function authenticate() {
      if ($this->token) {
         return TRUE;
      }
      $login_paths = array(
         static::LOGIN_METHOD_SHARED_SECRET => 'api/auth/sharedsecret',
         static::LOGIN_METHOD_USER => 'api/auth/login',
      );
      $params = array();
      if ($this->login_method == static::LOGIN_METHOD_SHARED_SECRET) {
         $params['secret'] = $this->secret;
         $params['login'] = $this->user;
      } else {
         $params['user'] = $this->user;
         $params['password'] = $this->secret;
      }
      $response = $this->sendRequest('GET', $login_paths[$this->login_method], '', $params);
      if (property_exists($response->getBody(), 'token')) {
         $this->token = $response->getBody()->token;
         return TRUE;
      }
      // @TODO Try to pass on why authentication failed, if we know.
      return FALSE;
   }

   /**
    * @param string $object The alternc name of the object.
    * @param string $action The alternc action to request.
    * @param array $params  The parameters to the request.
    */
   public function objectRequest($object, $action, $params = array()) {
      if ($this->api_style == static::API_STYLE_POST) {
         $params['object'] = $object;
         $params['action'] = $action;
      }
      return $this->request($this->getEndpointPath($object, $action), $params);
   }

   /**
    * A simpler interface for sending request than sendRequest
    *
    * @param string $path The path.
    * @param array $params Request parameters.
    *
    * @returns AlternCResponse response object.
    */
   public function request($path, $params = array()) {
      return $this->sendRequest($this->request_method, $path, $this->token, $params);
   }

   /**
    * Creates and sends a request.
    *
    * @param string $method The request method, eg. GET, POST, ...
    * @param string $path The request path.
    * @param string $token The token used to authenticate the request
    * @param array $params Query paramters for the request.
    *
    * @returns AlternCResponse response object.
    */
   public function sendRequest($method, $path, $token = '', $params = array()) {
      $request = new AlternCRequest($this, $method, $path, $token, $params);
      return $this->lastResponse = $this->_sendRequest($request);
   }

   /**
    * Does the actual sending of a request.
    */
   public function _sendRequest(AlternCRequest $request) {
      $client_args = array(
         'headers' => $request->getHeaders(),
         'query' => array(),
      );
      if ($request->getMethod() == static::REQUEST_METHOD_POST) {
         $client_args['json'] = $request->getParams();
         if ($this->token) {
            $client_args['query'] = array('token' => $this->token);
         }
         //unset($client_args['json']['token']);
      } else if ($request->getMethod() == static::REQUEST_METHOD_GET) {
         $client_args['query'] = $request->getParams();
      }
      print_r(array($request->getMethod(),
                    $request->getPath(),
                    $client_args));
      $response = $this->client->request($request->getMethod(), $request->getPath(), $client_args);
      return new AlternCResponse($response);
   }

   public function getToken() {
      return $this->token;
   }

   public function getURL() {
      return $this->url;
   }

   /**
    * Returns the endpoint based on the object, action, and api_style.
    */
   public function getEndpointPath($object, $action) {
      if ($this->api_style == static::API_STYLE_POST) {
         return "api/post";
      }
      else {
         return "api/rest/{$object}/{$action}";
      }
   }

   /**
    * Searches for alternc accounts.
    *
    * @param string $key The key to filter by. One of: uid, login, domain, creator.
    * @param string $value The value of the key to filter by: int for uid, creator; string for login, domain.
    *
    * @returns array An array of AlternCAccount objects indexed by uid.
    */
   public function find_accounts($key = '', $value = '') {
      $args = array();
      if ($key) {
         $args[$key] = $value;
      }
      $response = $this->objectRequest('account', 'find', $args);
      $users = array();
      print_r($response->getBody()->content);
      foreach ($response->getBody()->content as $id => $data) {
         $params = array();
         $users[$data->uid] = new AlternCAccount($this, $data->uid, $data->login, $data->prenom,
                                          $data->nom, $data->mail, array(
                                             'muid' => $data->muid,
                                             'pass' => $data->pass,
                                             'su' => $data->su,
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
                                             ));
      }
      return $users;
   }

   public function add_account($login, $mail, $password, $first_name, $last_name) {
      $params  = array(
         'login' => $login,
         'mail' => $mail,
         'pass' => $password,
         'nom' => $last_name,
         'prenom' => $first_name
      );
      $response = $this->objectRequest('account', 'add', $params);
      if ($response_body = $response->getBody()) {
         $user = $this->find_accounts('uid', $response_body->content)[$response_body->content];
         return $user;
      }
      return FALSE;
   }

}
