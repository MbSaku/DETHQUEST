<?php
if (isset($_POST['pag'])){
  $pag = $_POST['pag'];
}else{
  $pag = 0;
}
$regspag = 20;
?>
  <p><?php echo HelpMaster; ?></p>
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
<?php
if( !isset( $_POST['scenario'] ) ){
?>
  <div class="editiontitle"><?php echo Scenarios; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_scenario order by started asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $scenario = new Scenario( $site->getDatalink(), $row[0] );
        $map = new Map( $site->getDatalink(), $scenario->getMap() );
        echo '<form name="scenario'.$scenario->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="scenario" value="'.$scenario->getId().'">
        <div class="field"><b>'.$map->getName().'</b><br>'.strftime( '%d/%m/%Y %H:%M', $scenario->getStarted() ).'</div>
        <div class="field"><input type="submit" value="'.Close_scenario.'"></div>
        </div>
        </form>';
      }
      $i++;
    }
    echo '<div class="pags">';
    $dots = false;
    for ($a = 0; $a < $numpags; $a++){
      if ($a > $numpags - 2
      or $a < 3
      or ($a > $pag - 5 and $a < $pag + 5)){
        echo '<form name="pag'.$a.'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <input type="hidden" name="pag" value="'.$a.'">
        <input type="submit" value="';
        if ($a == $pag){ echo '['.$a.']'; }else{ echo $a; }
        echo '">
        </form>';
        $dots = false;
      }else{
        if (!$dots){
          echo ' ... ';
          $dots = true;
        }
      }
    }
    echo '</div>';
  }else{
    echo '<p class="error">'.No_data_to_show.'</p>';
  }
}else{
  $scenario = new Scenario( $site->getDatalink(), $_POST['scenario'] );
  echo $scenario->delete();
  ?>
  <form name="goback" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_list; ?>"></p>
  </form>
  <?php
}
?>