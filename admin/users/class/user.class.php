<?php
include('password_hash.php');
class User {

  private $id = 0;
  private $name = '';
  private $charge = 0;
  private $level = 999;
  private $email = '';
  private $valcode = '';
  private $activated = false;
  private $ip = '';
  private $lastlogin = 0;
  private $hashoptions = array ('cost' => 8, 'constant' => PASSWORD_BCRYPT);
  private $datalink = null;
  private $inactivity = 1800;  //Time in seconds
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    $this->datalink = $datalink;
    if ($id != 0){
      $query = 'select id,
      name,
      charge,
      email,
      valcode,
      activated,
      ip,
      lastlogin
      from int_user
      where id="'.$id.'"
      limit 1';
      $result = $this->datalink->dbQuery($query, 'result');
      if (isset($result[0])){
        $this->id = $result[0][0];
        $this->name = $result[0][1];
        $this->charge = $result[0][2];
        $this->email = $result[0][3];
        $this->valcode = $result[0][4];
        $this->activated = $result[0][5];
        $this->ip = $result[0][6];
        $this->lastlogin = $result[0][7];
        $this->loadLevel();
        if($this->lastlogin < time() - $this->inactivity){
          $query = 'update int_user 
          set ip="", 
          session="", 
          fingerprint="" 
          where id="'.$this->id.'"';
          $this->datalink->dbQuery($query, 'query');
        }
      }
    }
    if ( !isset ( $_SESSION['username'] ) ){
      $this->sessionResetVals();
    }
  }

/*
 * GETTERS AND SETTERS
 */

  public function getId(){
    return $this->id;
  }
  
  public function getName(){
    return $this->name;
  }
  public function setName( $string ){
    $this->name = $string;
  }
  
  public function getCharge(){
    return $this->charge;
  }
  public function setCharge( $integer ){
    $this->charge = $integer;
  }
  
  public function getEmail(){
    return $this->email;
  }
  public function setEmail( $string ){
    $this->email = $string;
  }
  
  public function getValcode(){
    return $this->valcode;
  }
  
  public function getActivated(){
    return $this->activated;
  }
  public function setActivated( $boolean ){
    $this->activated = $boolean;
  }
  
  public function getIp(){
    return $this->ip;
  }
  
  public function getLastlogin(){
    return $this->lastlogin;
  }
  
  public function getLevel(){
    return $this->level;
  }

/*
 * PUBLIC METHODS
 */

  private function loadLevel(){
    $query = 'select level from int_charges where id="'.$this->charge.'"';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0])){
        $this->level = $result[0][0];
    }
  }

  public function chargeName(){
    $query = 'select name from int_charges where id="'.$this->charge.'"';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0])){
        return $result[0][0];
    }else{
      return '?';
    }
  }

  public function jsessionReset (){  //Resets the session values
    if (isset ($_SESSION['uid'])){
      $query = 'update int_user 
      set ip="", 
      session="", 
      fingerprint="" 
      where id="'.$_SESSION['uid'].'"';
      $this->datalink->dbQuery($query, 'query');
    }
    $this->sessionResetVals();
  }

  public function jsessionCheck( $site ){
    if (isset($_SESSION['logged'])
    and $_SESSION['logged']){ //Comprobamos los datos del usuario.
      $query = 'select session, 
      fingerprint,
      activated,
      id,
      name,
      charge,
      email
      from int_user 
      where id="'.$_SESSION['uid'].'"
      limit 1';
      $result = $this->datalink->dbQuery($query, 'result');
      if ((isset($result[0])
      and !$site->getMaintenance())
      or (isset($result[0]) 
      and $site->getMaintenance()
      and $result[0][5] == 1)){
        if ($result[0][0] == session_id()
        and $result[0][1] == $this->calculateFingerPrint()
        and $result[0][2]){
          $this->charge = $result[0][5];
          $this->email = $result[0][6];
          $this->loadLevel();
          $this->jsessionConfigure($result[0][3], $result[0][4], $result[0][5]);
        }else{
          $this->jsessionReset();  //Identity check failure
        }
      }else{
        $this->jsessionReset();  //No matches found
      }
    }else{
      $this->jsessionReset();  //No login
    }
    if (isset($_POST['login'])
    and isset($_POST['username'])
    and isset($_POST['password'])){
      $this->login($_POST['username'], $_POST['password']);
    }
    if (isset($_POST['logout'])){  //Ends the current user session
      $this->jsessionReset();
    }
    if (isset($_POST['lang'])){
      $_SESSION['lang'] = $_POST['lang'];
    }
  }

  public function login( $user, $pass ){
    $query = 'select password, 
    id,
    name, 
    charge,
    email
    from int_user 
    where (name like "'.$user.'" 
      or email like "'.$user.'")
      and activated=1';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0]) 
    and password_verify($pass, $result[0][0])){  //Check the provided password for that user using password_hash()
      $this->email = $result[0][4];
      $this->jsessionConfigure($result[0][1], $result[0][2], $result[0][3]);
      $query = 'update int_user set 
      lastlogin="'.time ().'"
      where id="'.$result[0][1].'"';
      $this->datalink->dbQuery($query, 'query');
      return true;
    }else{
      return false;
    }
  }
  
  public function register( $name, $charge, $email, $password ){
    $doable = true;
    $errors = '';
    $query = 'select id
    from int_user
    where name like "'.$name.'"
    limit 1';
    if ($name == ''
    or $this->datalink->dbQuery($query, 'rows') > 0){ //Look for existent username
      $doable = false;
      $errors .= Username_unavailable.'<br>';
    }
    $query = 'select id
    from int_user
    where email like "'.$email.'"
    limit 1';
    if ($email == ''
    or $this->datalink->dbQuery($query, 'rows') > 0){ //Look for existent email
      $doable = false;
      $errors .= Email_unavailable.'<br>';
    }
    $query = 'select id
    from int_charges
    where id="'.$charge.'"
    limit 1';
    if ($this->datalink->dbQuery($query, 'rows') <= 0){ //If charge not found take the least significant one
      $query = 'select id
      from int_charges
      order by level desc
      limit 1';
      $result = $this->datalink->dbQuery($query, 'result');
      $charge = $result[0][0];
    }
    if ($password == ''){  //Password empty
      $doable = false;
      $errors .= Password_needed.'<br>';
    }
    if ($doable){
      $this->genValcode();
      $query = 'insert into int_user (
      name,
      charge,
      email,
      lastlogin,
      valcode,
      activated,
      session,
      ip,
      fingerprint,
      password
      ) values (
      "'.$name.'",
      "'.$charge.'",
      "'.$email.'",
      "'.time().'",
      "'.$this->valcode.'",
      "0",
      "",
      "",
      "",
      "'.password_hash($password, $this->hashoptions['constant'], array( 'cost' => $this->hashoptions['cost'] )).'"
      )';  //Password encryption
      $this->datalink->dbQuery($query, 'query');
      $query = 'select id from int_user order by id desc';
      $result = $this->datalink->dbQuery($query, 'result');
      $this->id = $result[0][0];
      $this->name = $name;
      $this->charge = $charge;
      $this->email = $email;
    }
    if ($errors != ''){
      $errors = '<p class="error">'.$errors.'</p>';
    }
    return $errors;
  }

  public function genValcode(){
    $this->valcode = md5(uniqid(rand(), true));
    $query = 'update int_user 
    set valcode="'.$this->valcode.'"
    where id="'.$this->id.'"';
    if ($this->getId() != 0){
      $this->datalink->dbQuery($query, 'query');
    }
  }

  public function passwordChange( $newpass ){
    $query = 'update int_user 
    set password="'.password_hash($newpass, $this->hashoptions['constant'], array( 'cost' => $this->hashoptions['cost'] )).'"
    where id="'.$this->id.'"';
    if ($newpass != ''
    and $this->datalink->dbQuery($query, 'query') > 0){
      return '<p class="fine">'.Password_updated.'</p>';
    }else{
      return '<p class="error">'.Password_not_updated.'</p>';
    }
  }

  public function delete(){
    $query = 'delete from int_user 
    where id="'.$this->id.'"';
    if ($this->datalink->dbQuery($query, 'query') > 0){
      $this->id = 0;
      $this->name = '';
      $this->charge = 0;
      $this->email = '';
      $this->activated = false;
      $this->ip = '';
      $this->lastlogin = 0;
      return '<p class="fine">'.User_deleted.'</p>';
    }else{
      return '<p class="error">'.User_not_deleted.'</p>';
    }

  }

  public function validate(){
    $query = 'update int_user
    set activated=1
    where id="'.$this->id.'"';
    if ($this->datalink->dbQuery($query, 'query') > 0){
      $this->activated = 1;
      return '<p class="fine">'.Account_activated.'</p>';
    }else{
      return '<p class="error">'.Error_activating_account.'</p>';
    }
    
  }

  public function checkPermission( $moduleid ){
    if( $this->charge != 1 ){
      $query = 'select charge, module
      from int_permissions
      where charge="'.$this->charge.'"
      and module="'.$moduleid.'"';
      return( $this->datalink->dbQuery( $query, 'rows' ) > 0 );
    }else{
      return true;
    }
  }

  public function save(){
    $query = 'update int_user
    set name="'.$this->name.'",
    email="'.$this->email.'",
    charge="'.$this->charge.'"
    where id="'.$this->id.'"';
    if ($this->datalink->dbQuery($query, 'query') > 0){
      return '<p class="fine">'.User_data_saved.'</p>';
    }else{
      return '<p class="error">'.User_data_not_saved.'</p>';
    }
  }

  public function isAdmin() {
    return ($this->charge == 1);
  }
  
  public function updateActivity(){
    $query = 'update int_user 
    set lastlogin="'.time().'"
    where id="'.$this->id.'"';
    $this->datalink->dbQuery($query, 'query');
  }

/*
 * PRIVATE METHODS
 */

  private function calculateFingerPrint(){  //Calculates a unique user fingerprint
    return md5($_SERVER['HTTP_USER_AGENT'].$_SESSION['uid'].$_SESSION['charge'].$_SESSION['username'].session_id());
  }

  private function jsessionConfigure ($id, $username, $charge){  //Configuración de sesión del usuario actual.
    $_SESSION['username'] = $username;
    $_SESSION['uid'] = $id;
    $_SESSION['logged'] = true;  //FINGERPRINT: hash md5 from user agent header, user id and session id.
    $_SESSION['charge'] = $charge;
    $this->id = $id;
    $this->charge = $charge;
    $this->name = $username;
    $query = 'update int_user 
    set session="'.session_id().'", 
    ip="'.$_SERVER['REMOTE_ADDR'].'",
    fingerprint="'.$this->calculateFingerprint().'"
    where id="'.$id.'"';
    $this->datalink->dbQuery($query, 'query');
    $this->updateActivity();
  }

  private function sessionResetVals(){
    $_SESSION['username'] = 'Visitor';
    $_SESSION['uid'] = 0;
    $_SESSION['logged'] = false;
    $_SESSION['charge'] = 0;
  }
}
?>
