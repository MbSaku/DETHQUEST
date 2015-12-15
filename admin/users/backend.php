<?php
$basiclink = $site->getBaselink().'?adm='.$site->getAdmin();
$sectionlinks = Array(  //Backend menu for the site configuration
  'users' => 'Users',
  'charges' => 'Charges'
);
if( isset($_GET['op'] ) ){
  $op = $_GET['op'];
}else{
  $op = 'users';
}
?>
<a href="<?php echo $site->getBaselink().'/login'; ?>" class="modclose"><input type="button" value="X"></a>
<ul class="modmenu">
  <?php
  foreach( $sectionlinks as $link => $title ){
    echo '
    <li><a href="'.$basiclink.'&op='.$link.'"';
    if( $op == $link ){
      echo ' class="active"';
    }
    echo '>'.constant( $title ).'</li></a>';
  }
  ?>
</ul>
<div class="modcontent">
  <?php
  switch( $op ){
    case 'users':         include( 'ops/users.php' );              break;
    case 'charges':       include( 'ops/charges.php' );            break;
  }
  ?>
</div>
<div class="version">Users v15.12.15</div>