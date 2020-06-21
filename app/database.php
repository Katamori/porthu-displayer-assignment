<?php

define('DB_NAME', 'porthu');

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
   public function __construct(string $dbPath)
   {
      $this->_db = new SQLite3($dbPath);

      if(!$this->_db) {
         echo $this->_db->lastErrorMsg();
      } else {
         echo "Opened database successfully\n";

         // debug: list tables
         /*
         while($row = $this->_exec("SELECT name FROM sqlite_master WHERE type='table';")->fetchArray(SQLITE3_ASSOC)) {
            var_dump($row);
         }
         */
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
    * Returns an indexed array of age restriction options. 
    *
    * @return array
    */
   public function getAgeRestrictions(): array
   {
      return $this->_getRecords("age_restriction");
   }

   /**
    * Adds a channel to the database.
    *
    * @param array $channel
    * @return SQLite3Result|false
    */
   public function addChannel(array $channel)
   {
      $channelName = $channel['name'];

      echo "INSERT INTO channel (name) VALUES ('${channelName}');";
      return $this->_exec("INSERT INTO channel (name) VALUES ('${channelName}');");
   }

   /**
    * Adds a program to the database.
    *
    * @param array $program
    * @return SQLite3Result|false
    */
    public function addProgram(array $program)
    {
      $title = $program['title'] ?? '';
      $desc = $program['short_description'] ?? '';
      $startTime = $program['start_datetime'] ?? null; 
      $channel = $program['channel'] ?? null;
      $ageLimit = $program['age_restriction'] ?? null;

      return $this->_exec("INSERT INTO program (title, short_description, start_datetime, channel, age_restriction) VALUES ('${title}', '${desc}', '${startTime}', '${channel}', '${ageLimit}');");
    }

   /**
    * Adds an age restriction rule to the database.
    *
    * @param array $ageRestriction
    * @return SQLite3Result|false
    */
    public function addAgeRestriction(array $ageRestriction)
    {
      $name = $ageRestriction['name'] ?? null;
      $limit = $ageRestriction['limit'] ?? null;
      $icon  = $ageRestriction['icon'] ?? null;

      return $this->_exec("INSERT INTO age_restriction ('name', 'limit', 'icon') "
                        . "VALUES ('${name}', '${limit}', '${icon}');");
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
