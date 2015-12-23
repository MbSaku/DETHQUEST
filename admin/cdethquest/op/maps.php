<?php
if (isset($_POST['pag'])){
  $pag = $_POST['pag'];
}else{
  $pag = 0;
}
if (isset($_POST['filter'])){
  $filter = $_POST['filter'];
}else{
  $filter = '';
}
$regspag = 20;
?>
  <h1><?php echo World.' '.Maps; ?></h1>  
  <p><?php echo HelpMaps; ?></p>  
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>  
<?php
if (!isset($_POST['map'])){
?>
  <form name="usersearch" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p class="pinput"><?php echo Search_by_name; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Search; ?>"></p>
  </form>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="map" value="0">
    <p class="pinput"><input type="submit" value="<?php echo Add; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo Maps; ?></div>  
  <?php
  $query = 'select '.mod.'deth_maps.id 
  from '.mod.'deth_maps, '.mod.'deth_places 
  where ( '.mod.'deth_maps.name like "%'.$filter.'%"
  or '.mod.'deth_places.name like "%'.$filter.'%" )
  and '.mod.'deth_maps.place='.mod.'deth_places.id
  order by '.mod.'deth_places.name asc, '.mod.'deth_maps.name asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $map = new Map( $site->getDatalink(), $row[0] );
        $place = new Place( $site->getDatalink(), $map->getPlace() );
        echo '<form name="place'.$map->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="map" value="'.$map->getId().'">
        <div class="field">'.$place->getName().'</div>
        <div class="field"><input type="submit" value="'.$map->getName().'"></div>
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
        <input type="hidden" name="filter" value="'.$filter.'">
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
  $map = new Map( $site->getDatalink(), $_POST['map'] );
  if( isset( $_POST['place'] ) 
  and isset( $_POST['height'] ) 
  and isset( $_POST['width'] ) ){
    $map->setPlace( $_POST['place'] );
    $map->setHeight( $_POST['height'] );
    $map->setWidth( $_POST['width'] );
    echo $map->save();
  }
  $place = new Place( $site->getDatalink(), $map->getPlace() );
  if( isset( $_POST['name']) 
  and isset( $_POST['description'] )
  and isset( $_POST['graph'] ) 
  and isset( $_POST['level'] )
  and isset( $_POST['weather'] ) ){
    $map->setName( $_POST['name'] );
    if( isset( $_POST['playable'] ) ){
      $map->setPlayable( true );
    }else{
      $map->setPlayable( false );
    }
    $map->setDescription( $_POST['description'] );
    $map->setGraph( $_POST['graph'] );
    $map->setLevel( $_POST['level'] );
    $map->setWeather( $_POST['weather'] );
    $map->setDoors( $_POST['doors'] );
    $map->setSprites( $_POST['sprites'] );
    if( !isset( $_POST['delete'] ) ){
      echo $map->save();
    }else{
      echo $map->delete();
    }
  }
  ?>  
  <form name="goback" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back; ?>"></p>
  </form>  
<?php
  if( $place->getId() != 0 
  and $map->getWidth() > 0 
  and $map->getHeight() > 0 ){
    echo '<h1>'.Editing_map.' '.$map->getName().'</h1>
    <form name="mapedit" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="map" value="'.$map->getId().'">        
    <div id="texture-panel">    
      <p class="pinput"><b>'.Brush.'</b>:<br>
      <img id="brush" class="texture" src="'.$_GET['root'].'admin/'.$module->getFolder().'/css/images/square.png"></p>
      <p class="pinput"><b>'.Eraser.'</b>:<br>
      <img class="texture" src="'.$_GET['root'].'admin/'.$module->getFolder().'/css/images/square.png"
      onclick="editor.select('."'0'".','."'0'".',this.src)"></p>';
      $types = Array( 'floor', 'wall', 'pit', 'door', 'sprite' );
      foreach( $types as $type ){
        switch( $type ){
          case 'floor':
            echo '<p class="pinput">'.Floor.':<br>';
          break;
          case'wall':
            echo '<p class="pinput">'.Wall.':<br>';
          break;
          case 'pit':
            echo '<p class="pinput">'.Pit.':<br>';
          break;
          case 'door':
            echo '<p class="pinput">'.Door.':<br>';
          break;
          case 'sprite':
            echo '<p class="pinput">'.Sprite.':<br>';
          break;
        }
        $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_squares 
        where place="'.$place->getId().'" and type="'.$type.'" order by id asc', 'result' );
        foreach( $result as $row ){
          $square = new Square( $site->getDatalink(), $row[0] );
          $imgs[$square->getId()] = $square->getTexture();
          switch( $type ){
          case 'door':
            ?>
            <img class="texture <?php echo 'e'.$type; ?>" src="<?php echo $_GET['root'].'uploads/'.$module->getFolder().'/'.$square->getTexture(); ?>"
            onclick="editor.setDoor('<?php echo $square->getId(); ?>',this.src)">
            <?php
          break;
          case 'sprite':
            ?>
            <img class="texture <?php echo 'e'.$type; ?>" src="<?php echo $_GET['root'].'uploads/'.$module->getFolder().'/'.$square->getTexture(); ?>"
            onclick="editor.setSprite('<?php echo $square->getId(); ?>',this.src)">
            <?php
          break;
          default:
            ?>
            <img class="texture <?php echo 'e'.$type; ?>" src="<?php echo $_GET['root'].'uploads/'.$module->getFolder().'/'.$square->getTexture(); ?>"
            onclick="editor.select('<?php echo $square->getId(); ?>','<?php echo $square->numericType(); ?>',this.src)">
            <?php
          }
        }
        echo '</p>';
      }
      $lev = $map->getLevel();
      $gra = $map->getGraph();
      $wea = $map->getWeather();
      $doo = $map->getDoors();
      $spr = $map->getSprites();
      $mlev = $map->getMatrix( 'level' );
      $mgra = $map->getMatrix( 'graph' );
      $mwea = $map->getMatrix( 'weather' );
      $mdoo = $map->getMatrix( 'doors' );
      $mspr = $map->getMatrix( 'sprites' );
      ?>      
      <p class="pinput"><b><?php echo Weather; ?></b>:<br>
      <img class="texture eweather" src="<?php echo $_GET['root'].'admin/'.$module->getFolder().'/css/images/rain.gif'; ?>"
      onclick="editor.setWeather('1',this.src)">
      <img class="texture eweather" src="<?php echo $_GET['root'].'admin/'.$module->getFolder().'/css/images/snow.gif'; ?>"
      onclick="editor.setWeather('2',this.src)">
      <img class="texture" src="<?php echo $_GET['root'].'admin/'.$module->getFolder().'/css/images/square.png'; ?>"
      onclick="editor.setWeather('0',this.src)"></p>    
    </div>    
    <div id="map-panel">
      <h2><?php echo Map_data; ?></h2>
      <p class="pinput"><?php echo Map_name; ?>:<br>
      <input type="text" name="name" value="<?php echo $map->getName(); ?>"></p>
      <p class="pinput"><br>
      <input type="checkbox" name="playable" value="true"'<?php  if( $map->getPlayable() ){ echo ' checked'; } ?>> <?php echo Map_playable; ?></p>
      <p><?php echo Map_description; ?>:<br>
      <textarea name="description"><?php echo $map->getDescription(); ?></textarea></p>
      <h2><?php echo Map_editor; ?></h2>      
      <input type="button" class="inczoom" value="+" onclick="editor.increaseZoom(backend.speed)">
      <input type="button" class="deczoom" value="-" onclick="editor.decreaseZoom(backend.speed)">
      <div id="mapeditor" style="max-width:<?php echo ( 3.5 * $map->getWidth() ) ?>em">
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
            echo '<div id="sq'.$a.','.$b.'" 
            onmouseover="editor.updateSquare('.$a.','.$b.',editor.level,editor.graphic,document.getElementById('."'brush'".').src)"
            onmousedown="editor.updateSquare('.$a.','.$b.',editor.level,editor.graphic,document.getElementById('."'brush'".').src,true)"
            style="background-image:url('."'".$texture."'".')"  class="esquare';
            switch( $mlev[$a][$b] ){
              case 0: echo ' efloor"'; break;
              case 1: echo ' epit"';   break;
              case 2: echo ' ewall"';  break;
            }
            echo '>
              <div id="wt'.$a.','.$b.'"';
              switch( $mwea[$a][$b] ){
                case 1: echo ' class="eweather erain"'; break;
                case 2: echo ' class="eweather esnow"'; break;
              }
              echo '></div>
              <div id="dt'.$a.','.$b.'"';
              if( $mdoo[$a][$b] != 0 ){
                $texture = $_GET['root'].'uploads/'.$module->getFolder().'/'.$imgs[$mdoo[$a][$b]];
                echo ' class="esquare edoor" style="background-image:url('.$texture.');"';
              }
              echo '></div>
              <div id="st'.$a.','.$b.'"';
              if( $mspr[$a][$b] != 0 ){
                $texture = $_GET['root'].'uploads/'.$module->getFolder().'/'.$imgs[$mspr[$a][$b]];
                echo ' class="esquare esprite" style="background-image:url('.$texture.');"';
              }
              echo '></div>
            </div>';
          }
          echo '</div>';
        }
        ?>
        </div>
      </div>
      <textarea name="graph" id="inpgraph"><?php echo $gra; ?></textarea>
      <textarea name="level" id="inplevel"><?php echo $lev; ?></textarea>
      <textarea name="weather" id="inpweather"><?php echo $wea; ?></textarea>
      <textarea name="doors" id="inpdoors"><?php echo $doo; ?></textarea>
      <textarea name="sprites" id="inpsprites"><?php echo $spr; ?></textarea>
      <p><input type="submit" value="<?php echo Save; ?>">
      <input type="checkbox" name="delete" value="1"><?php echo Delete_item; ?></p>
    </div>        
    </form>
    <script type="text/javascript">    
    <?php
    echo 'function Map(){
      this.level = new Array('.$map->getHeight().');
      this.graph = new Array('.$map->getHeight().');
      this.weather = new Array('.$map->getHeight().');
      this.doors = new Array('.$map->getHeight().');
      this.sprites = new Array('.$map->getHeight().');
      for( i = 0; i < '.$map->getHeight().'; i++ ){
        this.level[i] = new Array('.$map->getWidth().');
        this.graph[i] = new Array('.$map->getWidth().');
        this.weather[i] = new Array('.$map->getWidth().');
        this.doors[i] = new Array('.$map->getWidth().');
        this.sprites[i] = new Array('.$map->getWidth().');
      }';
      for( $a = 0; $a < $map->getHeight(); $a++ ){  //Matrices javascript con los valores de las casillas.
        for( $b = 0; $b < $map->getWidth(); $b++ ){
          echo '
          this.level['.$a.']['.$b.'] = '.$mlev[$a][$b].';
          this.graph['.$a.']['.$b.'] = '.$mgra[$a][$b].';
          this.weather['.$a.']['.$b.'] = '.$mwea[$a][$b].';
          this.doors['.$a.']['.$b.'] = '.$mdoo[$a][$b].';
          this.sprites['.$a.']['.$b.'] = '.$mspr[$a][$b].';';
        }
      }
      echo '
    }';
    ?>    
    var map = new Map();
    var editor = new MapEditor( map );
    document.body.onmousedown = function() { 
      editor.mouseDown = true;
    };
    document.body.onmouseup = function() {
      editor.mouseDown = false;
    };    
    document.getElementById( "mapeditor" ).addEventListener( 
      "mousedown", function(e) {
        e.preventDefault();
      }, false 
    );    
    </script>
<?php
  }else{
    echo '<h1>'.Editing_map.'</h1>
    <form name="mapsetup" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="map" value="'.$map->getId().'">
    <p class="pinput">'.Map_place.':<br>
    <select name="place"><option value="0">'.Select.'</option>';
    $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_places order by name asc', 'result');
    foreach( $result as $row ){
      $place = new Place( $site->getDatalink(), $row[0] );
      echo '<option value="'.$place->getId().'">'.$place->getName().'</option>';
    }
    echo '</select></p>
    <p class="pinput">'.Map_height.':<br>
    <select name="height" style="width:4em;">';
    for( $a = 20; $a <= 40; $a++ ){      
      echo '<option value="'.$a.'">'.$a.'</option>';
    }
    echo '</select></p>
    <p class="pinput">'.Map_width.':<br>
    <select name="width" style="width:4em;">';
    for( $a = 20; $a <= 40; $a++ ){      
      echo '<option value="'.$a.'">'.$a.'</option>';
    }
    echo '</select></p>
    <p><input type="submit" value="'.Save.'"></p>
    </form>';
  }
}
?>