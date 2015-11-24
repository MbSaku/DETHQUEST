<?php
if(isset($path[2])){
  $query = 'select id from int_user where activated=0 and valcode="'.$path[2].'"';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  if (isset($result[0])){
    $vuser = new User($site->getDatalink(), $result[0][0]);
    echo $vuser->validate();
  }
}else{
  $uname = '';
  $umail = '';
  if (isset($_POST['email'])
  and isset($_POST['user'])
  and isset($_POST['password'])
  and isset($_POST['register'])){
    $uname = $_POST['user'];
    $umail = $_POST['email'];
    if (!isset ($_POST['terms'])){
      echo '<p class="error">'.You_must_accept_terms.'</p>';
    }else{
      $nuser = new User($site->getDatalink());
      echo $nuser->register($_POST['user'], 0, $_POST['email'], $_POST['password']);
      if ($nuser->getId() != 0){
        $html = '<p>'.Thanks_for_signing_up_to.' <b>'.$site->getTitle().'</b></p>
        <p>'.Your_login_details_are.'<br>
        '.User_name.' <b>'.$_POST['user'].'</b><br>
        '.Password.': <b>'.$_POST['password'].'</b></p>
        <p><a href="'.$site->getBaselink().'/login/signup/'.$nuser->getValcode().'">'.Activate_your_account.'</a></p>';
        $uname = '';
        $umail = '';
        $email = new Email($site->adminEmail(), $nuser->getEmail(), Thanks_for_signing_up_to.' '.$site->getTitle(), $html);
        if ($email->send()){
          echo '<p class="fine">'.Registry_email_sent.'</p>';
        }else{
          echo '<p class="error">'.Registry_email_not_sent.'</p>';
        }
      }
    }
  }
?>
  <h1><?php echo Register; ?></h1>
  <p class="ayuda"><?php echo Register_help; ?></p>
  <form name="register" method="post" action="">
  <p class="pinput"><?php echo Email; ?><br>
  <input type="email" name="email" value="<?php echo $umail; ?>"></p>
  <p class="pinput"><?php echo User_name; ?><br>
  <input type="text" name="user" value="<?php echo $uname; ?>"></p>
  <p class="pinput"><?php echo Set_password; ?><br>
  <input type="password" name="password" value=""></p>
  <p><input type="checkbox" name="terms"> <?php echo Accept_terms_and_conditions; ?></p>
  <p><input type="submit" name="register" value="<?php echo Register; ?>"></p>
  </form>
<?php
}
?>