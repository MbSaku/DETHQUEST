<?php
class Datalink {  //Manages database queries

  private $sql = null;
  private $debug = false;
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct($sqlparams, $debug = false){  //MYSQLI
    $this->debug = $debug;
    if ($sql = mysqli_connect ($sqlparams['host'], $sqlparams['user'], $sqlparams['pass'], $sqlparams['dtbs'])){  //Database connection
      $this->sql = $sql;
    }else{
      echo ('<p class="Error">'.$sql_dtbs.' database missconfigured.</p>');
    }
  }
  
/*
 * PUBLIC METHODS
 */
 
  public function setDebug($debug){
   $this->debug = $debug;
 }
 
  public function sqlDump($filename){  //Executes SQL code from a file
    $lines = file($filename);
    $templine = '';
    foreach ($lines as $line){
      if (substr($line, 0, 2) == '--' 
      or $line == ''){  //Comments or ignored lines
        continue;
      }
      $templine .= $line;
      if (substr(trim($line), -1, 1) == ';'){
        $this->dbQuery($templine);
        $templine = '';
      }
    }
  }

  public function dbQuery($query, $type = 'query', $seek = 0){  //Executes a query and returns its useful information (MYSQLI)
    $return = false;
    $error = false;
    if (!$result = mysqli_query($this->sql, $query)
    or $this->sql == null){
      if ($this->debug){
        echo "<script>console.log('DB QUERY: ".str_replace(array("\n", "\r", "'"), '', str_replace('"', "'", trim($query)))."');</script>";
        echo "<script>console.log('DB ERROR: ".str_replace(array("\n", "\r", "'"), '', mysqli_error($this->sql))."');</script>";
      }
      $error = true;
    }
    if (!$error){
      switch ($type){
        case 'result':
        default:
          $return = array();
          if ($seek > 0 and $seek < mysqli_num_rows($result)){  //Data seeking in result
            mysqli_data_seek($result, $seek);
          }
          while ($row = mysqli_fetch_row($result)){
            $return[] = $row;
          }
          break;
        case 'rows':
          $return = mysqli_num_rows($result);
          break;
        case 'query':
          $return = mysqli_affected_rows($this->sql);
          break;
      }
    }else{
      return false;
    }
    return $return;  //Data returned
  }
  
  public function closeDb(){
    mysqli_close($this->sql);
  }
  
}