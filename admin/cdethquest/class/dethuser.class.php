<?php
class Dethuser {
  
  private $user = 0;
  private $charge = 0;
  private $character = 0;
  private $fingerprint = '';
  private $datalink = null;
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct ($datalink, $user){
    $this->datalink = $datalink;
    $this->user = $user;
    $query = 'select charge, playercharacter
    from '.mod.'deth_user
    where user="'.$user.'"
    limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0]) 
    and $row = $result[0]){
      $this->charge = $row[0];
      $this->character = $row[1];
    }else{
      $query = 'select id from '.mod.'deth_charges order by level desc';
      $result = $this->datalink->dbQuery($query, 'result');
      if (isset($result[0])
      and $row = $result[0]){
        $charge = $row[0];
      }else{
        $charge = 0;
      }
      if ($this->user != 0){
        $query = 'insert into '.mod.'deth_user (
        user, 
        charge
        ) values (
        "'.$this->user.'", 
        "'.$charge.'")';
        $this->datalink->dbQuery($query, 'query');
      }
    }
    $this->setFprint();
  }
  
/*
 * GETTERS
 */

  public function getUser(){
    return $this->user;
  }
  
  public function getCharge(){
    return $this->charge;
  }
  
  public function getCharacter(){
    return $this->character;
  }
  
  public function getFprint(){
    return $this->fingerprint;
  }
 
/*
 * SETTERS
 */

  public function setCharge($charge){
    $this->grupo = $charge;
  }
  
  public function setCharacter($character){
    $this->character = $character;
  }
 
/*
 * PUBLIC METHODS
 */
  
  public function save(){
    $query = 'update '.mod.'deth_user set
    charge="'.$this->charge.'",
    playercharacter="'.$this->character.'"
    where user="'.$this->user.'"';
    if ($this->datalink->dbQuery($query, 'query') > 0){
      return true;
    }else{
      return false;
    }
  }
  
  public function hasPermission($url){
    $us = new User($this->datalink, $this->user);
    $query = 'select charge, access
    from '.mod.'deth_permissions
    where charge="'.$this->charge.'"
    and access="'.$url.'"';
    return (($this->datalink->dbQuery($query, 'rows') > 0 or $us->isAdmin()));
  }
    
/*
 * PRIVATE METHODS
 */

  private function setFprint() {
    $query = 'select fingerprint from int_user where id="'.$this->user.'" and session="'.session_id().'"';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0]) and $row = $result[0]){
      $query = 'update '.mod.'deth_user set fingerprint="'.$row[0].'" where user="'.$this->user.'"';
      $this->datalink->dbQuery($query, 'query');
      $this->fingerprint = $row[0];
    }
  }
  
}
?>
