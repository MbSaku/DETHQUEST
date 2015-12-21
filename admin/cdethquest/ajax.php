<?php
include('../../lib/sql.settings.php');
include('../../lib/functions.php');
include('../site/class/datalink.class.php');
include('../site/class/database.object.php');
include('../site/class/module.class.php');
include('../site/class/ajaxsite.class.php');
include('../site/class/image.class.php');
include('../users/class/user.class.php');
include('class/dethuser.class.php');
include('class/race.class.php');
include('class/charclass.class.php');
include('class/purchasable.interface.php');
include('class/healing.class.php');
include('class/repairing.class.php');
include('class/equipment.class.php');
include('class/weapon.class.php');
include('class/armor.class.php');
include('class/playercharacter.class.php');
include('class/inventory.class.php');
include('class/place.class.php');
include('class/square.class.php');
include('class/map.class.php');
include('class/scenario.class.php');
include('class/message.class.php');
if( isset( $_GET['fingerprint'] ) and $_GET['fingerprint'] != ''
and isset( $_GET['lang'] )
and isset( $_GET['module'] )
and isset( $_GET['root'] ) ){
  define('mod', "c");
  session_start();
  $datalink = new Datalink( $sqlparams, true );
  $site = new Ajaxsite( $datalink );
  $site->setRoot('../../');
  $site->inputCheck();
  $result = $datalink->dbQuery( 'select id from int_user 
  where fingerprint="'.$_GET['fingerprint'].'"', 'result' );
  if ( isset( $result[0] ) ){
    $user = new User ( $datalink, $result[0][0] );
    $user->updateActivity();
  }else{
    $user = new User ( $datalink );
  }
  $result = $datalink->dbQuery('select user from '.mod.'deth_user, int_user 
  where '.mod.'deth_user.fingerprint=int_user.fingerprint 
  and '.mod.'deth_user.fingerprint="'.$_GET['fingerprint'].'"
  ', 'result');
  if (isset($result[0]) and $row = $result[0]){
    $dethuser = new Dethuser( $datalink, $result[0][0]);
  }else{
    $dethuser = new Dethuser( $datalink, 0);
  }
  $_SESSION['username'] = $user->getName();
  $_SESSION['lang'] = $_GET['lang'];
  if ( file_exists( '../../lib/lang/'.$_SESSION['lang'].'.php' ) ){
    include( '../../lib/lang/'.$_SESSION['lang'].'.php' );
  }else{
    include( '../../lib/lang/en.php' );
  }
  if (file_exists('lang/'.$_SESSION['lang'].'.php')){
    include('lang/'.$_SESSION['lang'].'.php');
  }else{
    include('lang/en.php');
  }
  $module = new Module($datalink, $_GET['module']);
  if(isset($_GET['op'])
  and isset($_GET['me'])){
    $datalink->setDebug( true );
    $op = $_GET['op'];
    $me = $_GET['me'];
    if ($dethuser->hasPermission($op)){
      ?>
      <script type="text/javascript">
      if( typeof gameTimer != 'undefined' ){ clearTimeout(gameTimer); }
      if( typeof chatTimer != 'undefined' ){ clearTimeout(chatTimer); }
      </script>
      <?php
      switch ($op){
        case 'preloader':         include('op/preloader.php');           break;
        case 'administration':    include('op/users.php');               break;
        case 'charges':           include('op/charges.php');             break;
        case 'permissions':       include('op/permissions.php');         break;
        case 'characters':        include('op/characters.php');          break;
        case 'npcs':              include('op/npcs.php');                break;
        case 'classes':           include('op/classes.php');             break;
        case 'races':             include('op/races.php');               break;
        case 'world':             include('op/world.php');               break;
        case 'maps':              include('op/maps.php');                break;
        case 'master':            include('op/master.php');              break;
        case 'items':             include('op/heal.php');                break;
        case 'repair':            include('op/repair.php');              break;
        case 'equipment':         include('op/equipment.php');           break;
        case 'weapon':            include('op/weapons.php');             break;
        case 'armor':             include('op/armor.php');               break;
        case 'account':           include('op/account.php');             break;

        case 'character':         include('game/charactersheet.php');    break;
        case 'inventory':         include('game/inventory.php');         break;
        case 'game':              include('game/game.php');              break;
        case 'city':              include('game/city.php');              break;
      }
      ?>
      <script type="text/javascript">
      setTimeout(function(){ $('#wrapper').animate({ height:$('#main').height() }, backend.speed); }, backend.speed);
      </script>
      <?php
    }else{
      if( $op == 'preloader' ){
        include('op/preloader.php');
      }else{
        echo '<p class="error">'.Access_denied.'</p>';
      }
    }
  }else{
    if (isset($_GET['ask'])){
      switch($_GET['ask']){
        case 'autocomplete':
          if (isset($_GET['object'])
          and isset($_GET['field'])
          and isset($_GET['search']) and $_GET['search'] != ''
          and isset($_GET['target'])){
            switch($_GET['object']){
              case 'weapon':
              default:
                $table = mod.'deth_item_weapon';
            }
            $query = 'select distinct '.$_GET['field'].' from '.$table.' 
            where '.$_GET['field'].' like "%'.$_GET['search'].'%" 
            order by '.$_GET['field'].' asc limit 10';
            $result = $site->getDatalink()->dbQuery( $query, 'result' );
            if (count($result) > 0){
              echo '<div class="autocomplete">';
              foreach ($result as $row){
                echo '<div class="autofind" onclick="$('."'#".$_GET['target']."'".').val('."'".$row[0]."'".')">
                '.$row[0].'
                </div>';
              }
              echo '</div>';
            }
          }
        break;
        case 'randomname':
          $race = new Race( $site->getDatalink(), $_GET['race'] );
          echo $race->getRandomName($_GET['gender']);
        break;
        case 'missionmaplist':
          $place = new Place( $site->getDatalink(), $_GET['place'] );
          echo '<div class="placedetail"';
          if ( $place->getImage() != '' ){
            echo ' style"background-image:url('."'".$_GET['root'].'uploads/'.$module->getFolder().'/'.$place->getImage()."'".')"';
          }
          echo '>
          <h2>'.$place->getName().'</h2>
          <p>'.nl2br( $place->getDescription() ).'</p>';
          $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_maps where place="'.$place->getId().'" and playable=1 order by width asc, height asc', 'result' );
          foreach( $result as $row ){
            $map = new Map( $site->getDatalink(), $row[0] );
            echo '<div class="placemap">
            <p><input type="radio" name="map" value="'.$map->getId().'"><span class="desc">'.$map->getName().'</span></p>
            </div>';
          }
          echo'<p><input type="submit" value="'.Deploy.'"></p>
          </div>';
        break;
        case 'chargen':
          include( 'ajax/chargen.ajax.php' );
        break;
        case 'missionlogic':
          include( 'ajax/missionlogic.ajax.php' );
        break;
        case 'missiondata':
          include( 'ajax/missiondata.ajax.php' );
        break;
        case 'missionaction':
          include( 'ajax/missionaction.ajax.php' );
        break;
        case 'askinteraction':
          include( 'ajax/askinteraction.ajax.php' );
        break;
        case 'chat':
          include( 'ajax/chat.ajax.php' );
        break;
        case 'talk':
          include( 'ajax/talk.ajax.php' );
        break;
      }
    }
  }
}
?>