<?php
if( isset( $path[2] ) ){
  $result = $site->dbQuery( 'select id from int_user 
  where valcode="'.$path[2].'"', 'result' );
  if (isset($result[0])){
    $vuser = new User( $site->getDatalink(), $result[0][0] );
    if( !$vuser->getActivated() ){
      echo $vuser->validate();  //Account not activated.
    }else{
      $npass = substr( md5( uniqid( rand(), true ) ), 0, 10 );
      $vuser->passwordChange($npass);
      $html = '<p>'.Password_reset.'</b></p>
      <p>'.Your_login_details_are.'<br>
      '.User_name.' <b>'.$vuser->getName().'</b><br>
      '.Password.': <b>'.$npass.'</b></p>';
      $email = new Email( $site->adminEmail(), $vuser->getEmail(), Account_recovery_for.' '.$site->getTitle(), $html );
      if( $email->send() ){
        echo '<p class="fine">'.Password_email_sent.'</p>';
      }else{
        echo '<p class="error">'.Recovery_email_not_sent.'</p>';
      }
    }
  }
}else{
  if( isset( $_POST['email'] )
  and isset( $_POST['recover'] ) ){
    $result = $site->dbQuery( 'select id from int_user where email="'.$_POST['email'].'"', 'result' );
    if( isset( $result[0] ) ){
      $nuser = new User( $site->getDatalink(), $result[0][0] );
      $nuser->genValcode();
      $html = '<p>'.Account_recovery_for.' <b>'.$site->getTitle().'</b></p>
      <p><a href="'.$site->getBaselink().'/login/recovery/'.$nuser->getValcode().'">'.Recovery_instructions.'</a></p>';
      $email = new Email( $site->adminEmail(), $nuser->getEmail(), Account_recovery_for.' '.$site->getTitle(), $html );
      if( $email->send() ){
        echo '<p class="fine">'.Recovery_email_sent.'</p>';
      }else{
        echo '<p class="error">'.Recovery_email_not_sent.'</p>';
      }
    }else{
      echo '<p class="error">'.This_email_not_exists.'</p>';
    }
  }
?>
  <h1><?php echo Account_recovery; ?></h1>
  <p><?php echo Recovery_help; ?></p>
  <form name="recovery" method="post" action="">
  <p><input type="email" name="email" placeholder="<?php echo User_email; ?>"> 
    <input type="submit" name="recover" value="<?php echo Recover_account; ?>"></p>
  </form>
<?php
}
?>