<?php
$basiclink = $site->getBaselink().'?adm='.$site->getAdmin();
$sectionlinks = Array(  //Backend menu for the site configuration
  'edition' => 'Edition',
  'images' => 'Images'
);
if (isset($_GET['op'])){
  $op = $_GET['op'];
}else{
  $op = 'edition';
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
    case 'edition':         include ('ops/edition.php');              break;
    case 'images':        	include ('ops/images.php');               break;
  }
  ?>
</div>
<div class="version">Content v15.06.15</div>
