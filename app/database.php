<?php

define('DB_NAME', 'porthu');
define('DB_PATH', realpath("..") . "/database/" . DB_NAME . ".sqlite3");

/**
 * Wrapper class for operations regarding SQLite.
 */
class DatabaseHandler
{
   /**
    * The main connection object.
    */
   private SQLite3 $_db;

   /**
    * Class contructor. Initializes the connection object. 
    */
   public function __construct()
   {
      $this->_db = new SQLite3(DB_PATH);

      if(!$this->_db) {
         echo $this->_db->lastErrorMsg();
      } else {
         echo "Opened database successfully\n";
      }
   }

   /**
    * Wrapper to close db connection.
    *
    * @return void
    */
   public function close() {
      if ($this->_db) {
         $this->_db->close();
     }
   }

   /**
    * Returns an indexed array of available days. 
    *
    * @return array
    */
   public function getAvailableDays(): array
   {
      return $this->_getRecords("program", "start_datetime");
   }

   /**
    * Returns an indexed array of channels. 
    *
    * @return array
    */
   public function getChannels(): array
   {
      return $this->_getRecords("channel");
   }

   /**
    * Wrapper for getting table records.
    *
    * @param string $table
    * @param string $fields
    * @return array
    */
   protected function _getRecords(string $table, string $fields = "*"): array
   {
   if (!($queryResponse = $this->_exec("SELECT ${fields} FROM ${table}"))) {
         return [];
      }

      $resultArray = [];

      while($row = $queryResponse->fetchArray(SQLITE3_ASSOC)) {
         $resultArray[] = $row;
      }

      return $resultArray;
   }

   /**
    * Wrapper for executing SQL queries. 
    *
    * @param string
    * @return SQLite3Result|false
    */
   protected function _exec(string $SQL)
   {
      if (!$this->_handleErrors()) {
         return false;
      }

      return $this->_db->query($SQL);
   }

   /**
    * An error handler that returns false if something's from with the DB.
    * 
    * @return bool
    */
   protected function _handleErrors(): bool
   {
      return (bool)$this->_db;
   }
}
