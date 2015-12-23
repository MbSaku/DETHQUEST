<?php
$buffer = ob_get_contents();
$buffer = str_replace(
  '<link rel="shortcut icon" type="image/png" href="'.$site->getBaseroot().'styles/'.$site->getStyle().'/icon.png">', 
  '<link rel="shortcut icon" type="image/png" href="'.$site->getBaseroot().'admin/'.$module->getFolder().'/icon.png">', 
  $buffer );
$buffer = str_replace(
  '<title>'.$site->getTitle().'</title>', 
  '<title>'.$module->getName().'</title>', 
  $buffer );
ob_end_clean();
ob_start();
echo $buffer;
define( 'mod', 'c' );
include('class/dethuser.class.php');
$basiclink = $site->getBaselink().'?adm='.$site->getAdmin();
$dethuser = new Dethuser( $site->getDatalink(), $_SESSION['uid'] );
$amenu = Array(  //Administration menu
  'administration' => 'Administration',
  'characters' => 'Characters',
  'maps' => 'World',
  'master' => 'Master',
  'items' => 'Items',
  'equipment' => 'Equipment',
  'account' => 'Account'
);
$mmenu = Array(  //Main menu
  'game' => 'Game',
  'character' => 'Player_character'
);
$ammenu = Array(  //Sub-menus
  'administration' => Array (
    'administration' => 'Users',
    'charges' => 'Charges',
    'permissions' => 'Permissions'
  ),
  'characters' => Array (
    'characters' => 'PCs',
    'npcs' => 'NPCs',
    'classes' => 'Classes',
    'races' => 'Races'
  ),
  'maps' => Array (
    'maps' => 'Maps',
    'world' => 'Places'
  ),
  'master' => Array (
    'master' => 'Scenarios'
  ),
  'items' => Array (
    'items' => 'Heal',
    'repair' => 'Repair'
  ),
  'equipment' => Array (
    'equipment' => 'Equipment',
    'weapon' => 'Weapons',
    'armor' => 'Armor'
  ),
  'account' => Array (
    'account' => 'Settings'
  )
);
$smmenu = Array(  //Sub-menus
  'character' => Array (
    'character' => 'Character_sheet',
    'inventory' => 'Inventory'
  ),
  'game' => Array (
    'game' => 'Mission',
    'city' => 'The_city'
  )
);
?>
<link rel="stylesheet" href="<?php echo $site->getBaseroot().'admin/'.$module->getFolder().'/css/'.$site->getDevice().'.css'; ?>" type="text/css">
<script type="text/javascript" src="<?php echo $site->getBaseroot().'admin/'.$module->getFolder().'/'; ?>js/dethquest.js"></script>
<script>
  var script = "<?php echo $site->getBaseroot().'admin/'.$module->getFolder().'/'; ?>";
  var root = "<?php echo $site->getBaseroot(); ?>";
  var module = "<?php echo $module->getUrl(); ?>";
  var lang = "<?php echo $_SESSION['lang']; ?>";
  var fprint = "<?php echo $dethuser->getFprint(); ?>";
  var username = "<?php echo $_SESSION['username']; ?>";
  backend = new Backend( script, root, module, lang, fprint, username);
  setTimeout( function(){ $("#wrapper").animate({ height:$("#main").height(), opacity:1 }, backend.speed); }, backend.speed );
</script>
<div id="<?php echo mod.'dethquest'; ?>">  
  <h1 class="dethtitle"><?php echo $module->getName(); ?></h1>  
  <a href="<?php echo $site->getBaselink().'/login'; ?>" class="modclose"><input type="button" value="X"></a>  
  <div id="administration">
  <ul class="admenu">
  <?php
  foreach( $amenu as $link => $title ){
    if( $dethuser->hasPermission( $link ) ){
      echo '<li onmouseenter="backend.playHoverSound()" class="adli"><a class="menulink">'.constant ($title).'</a>';
      if( count( $ammenu[$link] ) > 0){
        echo '<div class="adsubmenu">';
        foreach( $ammenu[$link] as $slink => $stitle ){
          if( $dethuser->hasPermission( $slink ) ){
            echo '<a onclick="backend.link('."'".$link."', '".$slink."'".')">'.constant ($stitle).'</a>';
          }
        }
        echo '</div>';
      }
      echo '</li>';
    }
  }
  ?>
  </ul>
  </div>    
  <div id="dethmenu" class="hoverable">
  <ul class="mainmenu">
  <?php
  foreach( $mmenu as $link => $title ) {
    if( $dethuser->hasPermission( $link ) ){
      echo '<li onmouseenter="backend.playHoverSound()" class="hoverable"><a class="menulink">'.constant ($title).'</a>';
      if( count( $smmenu[$link] ) > 0 ){
        echo '<div class="mainsubmenu">';
        foreach( $smmenu[$link] as $slink => $stitle ){
          if( $dethuser->hasPermission( $slink ) ){
            echo '<a onclick="backend.link('."'".$link."', '".$slink."'".')">'.constant ($stitle).'</a>';
          }
        }
        echo '</div>';
      }
      echo '</li>';
    }
  }
  ?>
  </ul>
  </div>  
  <audio id="audiohover" preload="auto"><source src="<?php echo $site->getBaseroot().'admin/'.$module->getFolder().'/css/sound/hover.mp3'; ?>"></source></audio>
  <audio id="audioclick" preload="auto"><source src="<?php echo $site->getBaseroot().'admin/'.$module->getFolder().'/css/sound/click.mp3'; ?>"></source></audio>
  <audio id="audiopost" preload="auto"><source src="<?php echo $site->getBaseroot().'admin/'.$module->getFolder().'/css/sound/post.mp3'; ?>"></source></audio>
  <div id="context">
    <div id="wrapper">
    <div id="main">
      <?php
      if( !$_SESSION['logged'] ){
        echo '<div class="dethlogin">';
        include( 'admin/users/frontend.php' );
        echo '</div>';
      }else{
        echo '<script type="text/javascript">backend.link("preloader", "preloader")</script>';
      }
      ?>
      </div>
    </div>
  </div>  
<div class="version">DethQuest v15.12.23</div>
</div>
<?php
if( $site->getDevice() != 'desktop' ){
?>
  <script type="text/javascript">
  setHoverables( "#administration" );
  $( ".adli" ).each( function() {
    $( this ).click( function() {
       $( this ).toggleClass( "hovered" );
       $( "#administration" ).toggleClass( "hovered" );
    } );
  });
  </script>
<?php
}
?>
