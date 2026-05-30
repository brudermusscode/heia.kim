<?php

// TODO: Time to remove Database\Manager class 😂

namespace Bruder\Database;

date_default_timezone_set('Europe/Berlin');

use Bruder\Application\Logger;
use Bruder\Utils\Arr;
use PDO;
use Exception;

class Manager
{
  private $db;

  public function __construct()
  {
    /**
     * Fetch environment variables file.
     */
    $config = _env();

    /**
     * Build the connection string.
     */
    $dbHost = $config->MYSQL_HOST;
    $dbUsername = $config->MYSQL_USER;
    $dbPassword = $config->MYSQL_PASSWORD;
    $dbName = $config->MYSQL_DATABASE;
    $dbCharset = $config->MYSQL_CHARSET;

    try {
      $this->db = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=$dbCharset",
        $dbUsername,
        $dbPassword
      );
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $this->db->setAttribute(PDO::ATTR_TIMEOUT, 3600);
    } catch (Exception $e) {

      echo $e->getMessage();
      echo "<div style='display:flex;flex-direction:column;align-items:center;padding:2.4em;background:rgba(0,0,0,.12);margin:2.4em;border-radius:24px;line-height:.2em;'>";
      echo "<p>💔 <strong>Database Connection Failed</strong> 〰️</p>";
      echo "<p>Check your database settings in 📂 <strong>/.env</strong></p>";
      echo "</div>";

      return null;
    }
  }

  public function get_connection()
  {
    return $this->db;
  }

  /**
   * Begin a database transaction.
   *
   * @return bool True if the transaction started successfully, false otherwise.
   * @throws \Exception If an error occurs while starting the transaction.
   */
  public function beginTransaction()
  {
    return $this->db->beginTransaction();
  }

  /**
   * Commit the current database transaction.
   *
   * @return bool True if the transaction was successfully committed, false otherwise.
   * @throws \Exception If an error occurs while committing the transaction.
   */
  public function commit()
  {
    return $this->db->commit();
  }

  /**
   * Roll back the current database transaction.
   *
   * @return bool True if the transaction was successfully rolled back, false otherwise.
   * @throws \Exception If an error occurs while rolling back the transaction.
   */
  public function rollback()
  {
    return $this->db->rollback();
  }

  /**
   * Execute a SELECT query and fetch the result as an associative array.
   *
   * @param string $sql The SELECT query to execute.
   * @param array $params An associative array of column-value pairs for the new rows.
   * @param bool $fetchAll Whether or not to fetch many rows or just one.
   * @return object|null The result of the query as an associative array, or null if no result found.
   * @throws \PDOException If an error occurs during the query execution and logs it
   */
  public function select(string $query, array $params = [], bool $fetchAll = false, $fetchMode = PDO::FETCH_ASSOC)
  {
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute($params);

      $return = $fetchAll ? $stmt->fetchAll($fetchMode) : $stmt->fetch($fetchMode);

      if ($fetchAll)
        $return = (array) Arr::objectify($return);
      else
        $return = (object) $return;

      if (isset($return->scalar))
        $return = null;

      if (is_object($return) && empty(get_object_vars($return)))
        $return = null;

      return $return;
    } catch (\PDOException $e) {
      Logger::to_file($e);

      return null;
    }
  }

  /**
   * Execute a SQL query.
   *
   * @param string $sql The SQL query to execute.
   * @param array $params An associative array of column-value pairs for the new rows.
   * @return object True if the query executed successfully, false otherwise.
   * @throws \PDOException If an error occurs during the query execution and logs it
   */
  public function execute(string $query, array $params = [])
  {
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute($params);

      return 1;
    } catch (\PDOException $e) {
      Logger::to_file($e);

      return null;
    }
  }

  /**
   * Insert new rows into the database table and log any exceptions on failure.
   * Rolls back the data when failed
   *
   * @param string $query The full query.
   * @param array $params An associative array of column-value pairs for the new rows.
   * @return int Returns the last inserted id.
   * @throws \PDOException If an error occurs during the insertion process.
   */
  public function insert(string $query, array $params = [])
  {
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute($params);

      return $this->db->lastInsertId();
    } catch (\PDOException $e) {
      Logger::to_file($e);

      return null;
    }
  }

  /**
   * Update database rows based on specified conditions.
   *
   * @param string $query The full query.
   * @param array $params An associative array of column-value pairs to update.
   * @return bool Returns the outcome as true or false.
   * @throws \PDOException If an error occurs during the update process.
   */
  public function update(string $query, array $params = [])
  {
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute($params);

      return 1;
    } catch (\PDOException $e) {
      Logger::to_file($e);

      return false;
    }
  }
}
