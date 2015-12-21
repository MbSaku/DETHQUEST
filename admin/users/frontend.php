<?php
$path = $site->getPath();
if (isset($path[1])){
  switch($path[1]){
    case 'signup':
      include('front/signup.php');
      break;
    case 'recovery':
      include('front/recovery.php');
      break;
  }
}else{
  if (!$_SESSION['logged']){  //Login, sign up or account recovery
    if (isset($_POST['username'])
    and isset($_POST['password'])){
      if ($site->getMaintenance()){
        $site->showMaintenance();
      }else{
        echo '<p class="error">'.Login_incorrect.'</p>';
      }
      $site->addLog('Login '.$_POST['username'].': ERROR');
    }
  ?>
  <fieldset><legend><?php echo Private_access; ?></legend>
  <form name="login" method="post" action="">
    <p class="pinput"><?php echo Username_or_email; ?>:<br>
    <input type="text" name="username" value=""></p>
    <p class="pinput"><?php echo Password; ?>:<br>
    <input type="password" name="password" value=""></p>
    <p><input type="submit" name="login" value="<?php echo Log_in; ?>"></p>
    <p>
    <?php
    if( $site->getFreereg() ){
      echo '<a href="'.$site->getBaselink().'/login/signup"><input type="button" value="'.Register.'"></a>';
    }
    ?>
    <a href="<?php echo $site->getBaselink().'/login/recovery'; ?>"><input type="button" value="<?php echo Account_recovery; ?>"></a></p>
  </form>
  </fieldset>
  <?php
  }else{  //User's private panel
    if (isset($_POST['username'])
    and isset($_POST['password'])){
      $site->addLog('Login '.$_POST['username'].': SUCCESS');
    }
  ?>
  <h1><?php echo $_SESSION['username']; ?></h1>
  <div class="usermenu">
  <form name="logout" method="post" action="">
    <?php echo $site->privateMenu($user); ?>
    <p><input type="submit" name="logout" value="<?php echo Log_out; ?>"></p>
  </form>
  </div>
  <div class="userdata">
  <?php
  if (isset($_POST['email'])
  and isset($_POST['emailchange'])){
    $user->setEmail($_POST['email']);
    echo $user->save();
  }
  if (isset($_POST['password'])
  and isset($_POST['passwordchange'])){
    echo $user->passwordChange($_POST['password']);
  }
  ?>
    <fieldset><legend><?php echo User_details; ?></legend>
      <a class="opener" onclick="javascript:cssOpen('emailchange')"><?php echo Change_email; ?></a>
      <div id="emailchange" style="overflow:hidden;max-height:0px">
        <div id="emailchange-in">
          <form name="emailchange" method="post" action="">
            <p class="pinput"><?php echo User_email; ?>:<br>
            <input type="email" name="email" value="<?php echo $user->getEmail(); ?>"></p>
            <p><input type="submit" name="emailchange" value="<?php echo Save_email; ?>"></p>
          </form>
        </div>
      </div>
      <a class="opener" onclick="javascript:cssOpen('passchange')"><?php echo Change_password; ?></a>
      <div id="passchange" style="overflow:hidden;max-height:0px">
        <div id="passchange-in">
          <form name="passchange" method="post" action="">
            <p class="help"><?php echo Password_change_help; ?></p>
            <p class="pinput"><?php echo New_password; ?>:<br>
            <input type="password" name="password" value=""></p>
            <p><input type="submit" name="passwordchange" value="<?php echo Save_password; ?>"></p>
          </form>
        </div>
      </div>
      <a class="opener" onclick="javascript:cssOpen('logininfo')"><?php echo Account_activity; ?></a>
      <div id="logininfo" style="overflow:hidden;max-height:0px">
        <div id="logininfo-in">
          <?php
          $query = 'select instant, browser, ip, data
          from int_overwatch
          where data like "%Login '.$user->getName().':%"
          or data like "%Login '.$user->getEmail().':%"
          order by instant desc
          limit 5';
          $result = $site->getDatalink()->dbQuery($query, 'result');
          foreach ($result as $row){
            echo '<p>';
            if (strpos($row[3], 'ERROR') > 0){
              echo '<span class="failure"><b>'.strftime ('%A, %d %B %Y %H:%M', $row[0]).'</b></span>';
            }else{
              echo '<b>'.strftime ('%A, %d %B %Y %H:%M', $row[0]).'</b>';
            }
            echo '<br>'.$row[2].' '.$row[1].'</p>';
          }
          ?>
        </div>
      </div>
    </fieldset>
  </div>
  <?php
  }
}