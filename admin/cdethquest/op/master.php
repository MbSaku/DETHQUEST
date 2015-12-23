<?php
if( isset( $_POST['pag'] ) ){
  $pag = $_POST['pag'];
}else{
  $pag = 0;
}
$regspag = 20;
?>
<h1><?php echo Master; ?></h1>
<p><?php echo HelpMaster; ?></p>
<p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
<?php
if( !isset( $_POST['scenario'] ) ){
?>
  <div class="editiontitle"><?php echo Scenarios; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_scenario order by started asc';
  $rows = $site->dbQuery( $query, 'rows' );
  $numpags = ceil( $rows / $regspag );
  $result = $site->getDatalink()->dbQuery( $query, 'result', ( $regspag * $pag ) );
  $i = 0;
  if( $rows > 0 ){
    foreach( $result as $row ){
      if( $i < $regspag ){
        $scenario = new Scenario( $site->getDatalink(), $row[0] );
        $map = new Map( $site->getDatalink(), $scenario->getMap() );
        $place = new Place( $site->getDatalink(), $map->getPlace() );
        echo '<form name="scenario'.$scenario->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="scenario" value="'.$scenario->getId().'">
        <div class="field">
          <b>'.$map->getName().'</b><br>
          '.$place->getName().'<br>
          '.Started.': '.strftime( '%d/%m/%Y %H:%M', $scenario->getStarted() ).'
        </div>
        <div class="field">
          '.Players.': <b>'.$scenario->numPlayers().'</b><br>
          '.Level.': <b>'.$scenario->getDifficulty().'</b>
        </div>
        <div class="field"><input type="submit" value="'.View.'"></div>
        </div>
        </form>';
      }
      $i++;
    }
    echo '<div class="pags">';
    $dots = false;
    for( $a = 0; $a < $numpags; $a++ ){
      if( $a > $numpags - 2
      or $a < 3
      or( $a > $pag - 5 and $a < $pag + 5 ) ){
        echo '<form name="pag'.$a.'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <input type="hidden" name="pag" value="'.$a.'">
        <input type="submit" value="';
        if( $a == $pag ){ echo '['.$a.']'; }else{ echo $a; }
        echo '">
        </form>';
        $dots = false;
      }else{
        if( !$dots ){
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
  $map = new Map( $site->getDatalink(), $scenario->getMap() );
  $place = new Place( $site->getDatalink(), $map->getPlace() );
  if( isset( $_POST['coordx'] ) 
  and isset( $_POST['coordy'] ) 
  and isset( $_POST['entity'] ) ){
    $data = explode( '-', $_POST['entity'] );
    if( count( $data ) == 2 ){
      switch( $data[0] ){
        case 'char':
          $entity = new PlayerCharacter( $site->getDatalink(), $data[1] );
          $entity->place( $scenario->getId(), $_POST['coordx'], $_POST['coordy'] );
        break;
      }
    }
  }
  if( isset( $_POST['delete'] ) ){
    echo $scenario->delete();
  }
  ?>
  <form name="goback" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_list; ?>"></p>
  </form>
  <h1><?php echo $place->getName().'<br>'.$map->getName(); ?></h1>
  <p>
    <?php 
    echo Started.': <b>'.strftime( '%d/%m/%Y %H:%M', $scenario->getStarted() ).'</b><br>
    '.Players.': <b>'.$scenario->numPlayers().'</b><br>
    '.Level.': <b>'.$scenario->getDifficulty().'</b>';
    ?>
  </p>
  <form name="master" onsubmit="event.preventDefault(); backend.post(this,false);" enctype="multipart/form-data" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="scenario" value="<?php echo $scenario->getId(); ?>">
    <div class="edblock">
      <?php
      $types = Array( 'floor', 'wall', 'pit', 'door', 'sprite' );
      foreach( $types as $type ){
        $result = $site->dbQuery( 'select id from '.mod.'deth_squares 
        where place="'.$place->getId().'" and type="'.$type.'" order by id asc', 'result' );
        foreach( $result as $row ){
          $square = new Square( $site->getDatalink(), $row[0] );
          $imgs[$square->getId()] = $square->getTexture();
        }
      }
      $mlev = $map->getMatrix( 'level' );
      $mgra = $map->getMatrix( 'graph' );
      $mwea = $map->getMatrix( 'weather' );
      $mdoo = $map->getMatrix( 'doors' );
      $mspr = $map->getMatrix( 'sprites' );
      $mdrs = $scenario->getMatrix( 'level' );
      ?>
      <div id="mapeditor" class="mapmaster" style="max-width:<?php echo ( 3.5 * $map->getWidth() ) ?>em">
        <div id="map">
          <?php
          for( $a = 0; $a < $map->getHeight(); $a++ ){
            echo '<div class="maprow" style="width:'.( 3 * $map->getWidth() ).'em">';
            for ($b = 0; $b < $map->getWidth(); $b++){
              if( isset( $imgs[$mgra[$a][$b]] ) ){
                $texture = $_GET['root'].'uploads/'.$module->getFolder().'/'.$imgs[$mgra[$a][$b]];
              }else{
                $texture = $_GET['root'].'admin/'.$module->getFolder().'/css/images/square.png';
              }
              echo '<div id="sq'.$a.','.$b.'" style="background-image:url('."'".$texture."'".')"  class="esquare';
              switch( $mlev[$a][$b] ){
                case 0: echo ' efloor"'; break;
                case 1: echo ' epit"';   break;
                case 2: echo ' ewall"';  break;
              }
              echo ' title="'.$b.','.$a.'" onclick="setMasterSquare('.$b.','.$a.')">
                <div id="wt'.$a.','.$b.'"';
                switch( $mwea[$a][$b] ){
                  case 1: echo ' class="eweather erain"'; break;
                  case 2: echo ' class="eweather esnow"'; break;
                }
                echo '></div>
                <div id="dt'.$a.','.$b.'"';
                if( $mdoo[$a][$b] != 0 ){
                  $texture = $_GET['root'].'uploads/'.$module->getFolder().'/'.$imgs[$mdoo[$a][$b]];
                  echo ' class="esquare edoor"';
                  if( $mdrs[$a][$b] != 0 ){
                     echo ' style="background-image:url('.$texture.');"';
                  }
                }
                echo '></div>
                <div id="st'.$a.','.$b.'"';
                if( $mspr[$a][$b] != 0 ){
                  $texture = $_GET['root'].'uploads/'.$module->getFolder().'/'.$imgs[$mspr[$a][$b]];
                  echo ' class="esquare esprite" style="background-image:url('.$texture.');"';
                }
                echo '></div>';
                $result = $site->dbQuery( 'select type, entity from '.mod.'deth_scenario_entity 
                where coordx="'.$b.'" and coordy="'.$a.'"
                and scenario = "'.$scenario->getId().'"
                limit 1' );
                if( isset( $result[0] ) ){
                  switch( $result[0][0] ){
                    case 'char':
                      $entity = new PlayerCharacter( $site->getDatalink(), $result[0][1] );
                      echo '<div class="eentity">'.$entity->renderHtml( $module->getFolder() ).'</div>';
                    break;
                  }
                }
              echo '</div>';
            }
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </div>
    <div class="edblock">
      <h2><?php echo Entity_placement ?></h2>
      <p class="pinput"><?php echo Coordinate_x; ?>:<br>
      <input type="number" id="master-x" name="coordx" value="0" style="width:6em;"></p>
      <p class="pinput"><?php echo Coordinate_y; ?>:<br>
      <input type="number" id="master-y" name="coordy" value="0" style="width:6em;"></p>
      <p class="pinput"><input type="radio" name="entity" value="none" checked> <?php echo Place_nothing; ?></p>
      <div class="editiontitle"><?php echo Scenario_entities; ?></div>
      <?php
      $result = $site->dbQuery( 'select type, entity, coordx, coordy, value, target from '.mod.'deth_scenario_entity 
      where scenario = "'.$scenario->getId().'"
      order by coordy asc, coordx asc' );
      foreach( $result as $row ){
        echo '<div class="editionitem">';
        switch( $row[0] ){
          case 'char':
            $entity = new PlayerCharacter( $site->getDatalink(), $row[1] );
            echo '<div class="field">
              <div class="charportrait">
                <div class="inner">'.$entity->renderHtml( $module->getFolder(), true ).'</div>
              </div>
            </div>
            <div class="field">
              <p><b>'.$entity->getName().'</b><br>';
              if( $entity->isPlayer() ){ echo Player_character.' '; }
              echo Level.' <b>'.$entity->getLevel().'</b><br>
              '.Coordinates.': <b>'.$row[2].', '.$row[3].'</b><br>
              <input type="radio" name="entity" value="'.$row[0].'-'.$entity->getId().'"> '.Move_entity.'</p>
            </div>';
          break;
        }
        echo '</div>';
      }
      ?>
      <div class="editiontitle"><?php echo Available_characters; ?></div>
      <?php
      $result = $site->dbQuery( 'select '.mod.'deth_characters.id from '.mod.'deth_characters
      where not exists ( 
        select entity from '.mod.'deth_scenario_entity 
        where '.mod.'deth_scenario_entity.type="char"
        and '.mod.'deth_characters.id='.mod.'deth_scenario_entity.entity 
        limit 1 
      )' );
      foreach( $result as $row ){
        echo '<div class="editionitem">';
        $entity = new PlayerCharacter( $site->getDatalink(), $row[0] );
        echo '<div class="field">
          <div class="charportrait">
            <div class="inner">'.$entity->renderHtml( $module->getFolder(), true ).'</div>
          </div>
        </div>
        <div class="field">
          <p><b>'.$entity->getName().'</b><br>';
          if( $entity->isPlayer() ){ echo Player_character.' '; }
          echo Level.' <b>'.$entity->getLevel().'</b><br>
          <input type="radio" name="entity" value="char-'.$entity->getId().'"> '.Place_entity.'</p>
        </div>
        </div>';
      }
      ?>
    </div>
    <p><input type="submit" value="<?php echo Apply_changes; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Close_scenario; ?></p>
  </form>
  <script type="text/javascript">
    
    function setMasterSquare( x, y ){
      $( '#master-x' ).val( x );
      $( '#master-y' ).val( y );
    }
    
  </script>
  <?php
}
?>