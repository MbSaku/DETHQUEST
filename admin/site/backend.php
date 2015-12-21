<?php
  $basiclink = $site->getBaselink().'?adm='.$site->getAdmin();
  $sectionlinks = Array(  //Backend menu for the site configuration
    'general' => 'General',
    'languages' => 'Languages',
    'modules' => 'Modules',
    'permissions' => 'Permissions',
    'appearance' => 'Appearance',
    'overwatch' => 'Overwatch'
  );
  if (isset($_GET['op'])){
    $op = $_GET['op'];
  }else{
    $op = 'general';
  }
?>
<a href="<?php echo $site->getBaselink().'/login'; ?>" class="modclose"><input type="button" value="X"></a>
<link rel="stylesheet" href="<?php echo $site->getBaseroot(); ?>js/datepicker/datepicker.css" type="text/css">
<script type="text/javascript" src="<?php echo $site->getBaseroot(); ?>js/datepicker/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="<?php echo $site->getBaseroot(); ?>js/tinymce/tinymce.min.js"></script>
<ul class="modmenu">
  <?php
  foreach ($sectionlinks as $link => $title) {
    echo '
    <li><a href="'.$basiclink.'&op='.$link.'"';
    if ($op == $link){ echo ' class="active"'; } echo '>'.constant ($title).'</li></a>';
  }
  ?>
</ul>
<div class="modcontent">
  <?php
  switch ($op){
    case 'general':         include ('ops/general.php');              break;
    case 'languages':       include ('ops/languages.php');            break;
    case 'modules':         include ('ops/modules.php');              break;
    case 'permissions':     include ('ops/permissions.php');          break;
    case 'appearance':      include ('ops/appearance.php');           break;
    case 'overwatch':       include ('ops/overwatch.php');            break;
  }
  ?>
</div>
<div class="version">Site v15.12.14</div>