<?php


namespace Settee;

/**
* CouchDB Server Manager
*/
class Server {

  /**
  * Base URL of the CouchDB REST API
  */
  private $conn_url;
  
  /**
  * HTTP REST Client instance
  */
  protected $rest_client;

  
  /**
   * Class constructor
   * 
   * @param $conn_url
   *    (optional) URL of the CouchDB server to connect to. Default value: http://127.0.0.1:5984
   */
  function __construct($conn_url = "http://127.0.0.1:5984") {
    $this->conn_url = rtrim($conn_url, ' /');
    $this->rest_client = RestClient::get_instance($this->conn_url);
  }
  
  /**
  * Create database
  *
  * @param $db
  *     Either a database object or a String name of the database.
  *
  * @return
  *     json string from the server.
  *
  *  @throws CreateDatabaseException
  */
  function create_db($db) {
    if ($db instanceof Database) {
      $db = $db->get_name();
    }
    $ret = $this->rest_client->http_put($db);
    if (!empty($ret['decoded']->error)) {
      throw new DatabaseException("Could not create database: " . $ret["json"]);
    }
    return $ret['decoded'];
  }
  
  /**
  * Drop database
  *
  * @param $db
  *     Either a database object or a String name of the database.
  *
  * @return
  *     json string from the server.
  *
  *  @throws DropDatabaseException
  */
  function drop_db($db) {
    if ($db instanceof Database) {
      $db = $db->get_name();
    }
    $ret =  $this->rest_client->http_delete($db);
    if (!empty($ret['decoded']->error)) {
      throw new DatabaseException("Could not create database: " . $ret["json"]);
    }
    return $ret['decoded'];
  }
  
  /**
  * Instantiate a database object
  *
  * @param $dbname
  *    name of the newly created database
  *
  * @return Database
  *     new Database instance.
  */
  function get_db($dbname) {
    return new Database($this->conn_url, $dbname);
  }


  /**
  * Return an array containing all databases
  *
  * @return Array
  *    an array of database names in the CouchDB instance
  */
  function list_dbs() {
    $ret = $this->rest_client->http_get('_all_dbs');
    if (!empty($ret['decoded']["error"])) {
      throw new DatabaseException("Could not get list of databases: " . $ret["json"]);
    }
    return $ret['decoded'];
  }

}

class ServerErrorException extends \Exception {}
class DatabaseException extends \Exception {}
class WrongInputException extends \Exception {}
