<?php
if( $dethuser->getCharacter() == 0 ){
  echo '<script>backend.link("character", "character");</script>';
}else{
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
  if( isset( $_POST['map'] ) 
  and $character->isPlaying() == 0 ){
    $map = new Map( $site->getDatalink(), $_POST['map'] );
    $scenario = new Scenario( $site->getDatalink() );
    $scenario->setMap( $map->getId() );
    $scenario->setPlayers( 4 );
    $scenario->setStarted( time() );
    $scenario->setDoors( $map );
    $message = new Message( $site->getDatalink() );
    $scenario->save();
    $message->send( 0, $scenario->getId(), 0, $map->getDescription() );
    $character->deploy( $scenario->getId() );
  }
  if( isset( $_POST['scenario'] ) 
  and $character->isPlaying() == 0 ){
    $character->deploy( $_POST['scenario'], true );
  }
  if( $character->isPlaying() == 0 ){
    ?>
    <h1><?php echo New_mission; ?></h1>
      <div class="charportrait">
        <div class="inner"><?php echo $character->renderHtml( $module->getFolder(), true ); ?></div>
      </div>      
      <div class="charoverview">
        <p><?php 
          echo '<span class="desc"><b>'.$character->getName().'</b></span><br>
          '.Level.' <span class="out">'.$character->getLevel().'</span><br>
          '.Experience.': <b>'.$character->getExperience().' / '.$character->expNextLevel().'</b>'; 
        ?></p>
        <div class="xpbar">
          <div class="bar">
            <div class="bar-grey" style="width:<?php echo ( ( $character->getExperience() / $character->expNextLevel() ) * 100 ) ?>%"></div>
          </div>
        </div>
      </div>
      <?php
      $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_scenario order by started asc', 'result' );
      if( count( $result ) > 0 ){
        echo '<div class="editiontitle">'.Open_games.'</div>';
        foreach( $result as $row ){
          $scenario = new Scenario( $site->getDatalink(), $row[0] );
          $map = new Map( $site->getDatalink(), $scenario->getMap() );
          $place = new Place( $site->getDatalink(), $map->getPlace() );
          echo '<div class="opengame">
          <form name="deploy'.$scenario->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
          <input type="hidden" name="scenario" value="'.$scenario->getId().'">
          <p class="pinput"><input type="submit" value="'.Deploy.'"></p>
          </form>
          <p class="pinput"><span class="desc"><b>'.$map->getName().'</b></span>, '.$place->getName().'<br>
          '.Started.': <b>'.strftime( '%d/%m/%Y %H:%M', $scenario->getStarted() ).'</b>, '.Level.': <b>'.$scenario->getDifficulty().'</b><br>
          '.Players.': <b>'.$scenario->numPlayers().'</b></p>
          </div>';
        }
      }
      ?>    
    <form name="mission" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
      <div id="selectdest">        
        <h1><?php echo Select_destination; ?></h1>        
        <?php
        $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_places order by name asc', 'result' );
        foreach( $result as $row ){
          $place = new Place( $site->getDatalink(), $row[0] );
          echo '<a title="'.$place->getName().'" onclick="backend.showMissionPlace('.$place->getId().')">
          <div class="placebox">';
          if ( $place->getImage() != '' ){
            echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$place->getImage().'" onclick="backend.toggleMissionPic(this.src)">';
          }
          echo '</div>
          </a>';
        }
        ?>
        <div id="maplist-wrapper">
          <div id="maplist"></div>
        </div>
      </div>
    </form>
    <?php
  }else{
    $scenario = new Scenario( $site->getDatalink(), $character->isPlaying() );
    $map = new Map( $site->getDatalink(), $scenario->getMap() );
    ?>
    <div id="gamemsg"></div>    
    <div id="missionmap">
      <div id="map" style="height:<?php echo ($map->getHeight() * 3); ?>em;width:<?php echo ( $map->getWidth() * 4.5 + $map->getWidth() * 3 ); ?>em"></div>
    </div>
    <div id="mappanel" class="closed">
      <div id="mapicon" class="mapopen" onclick="mission.toggleMinimap()"></div>
      <div id="minimap-container" style="width:<?php echo $map->getWidth(); ?>em">
        <div id="minimap" style="height:<?php echo $map->getHeight(); ?>em;width:<?php echo $map->getWidth(); ?>em"></div>
      </div>
    </div>
    <div id="chatpanel" class="opened">
      <div id="chaticon" class="chatclose" onclick="gamechat.toggle()"></div>
      <div id="talking">
      <form name="gchat" onsubmit="event.preventDefault();gamechat.send($('#message').val())" method="post" action="" autocomplete="off">
        <p><span class="desc" id="chatname"></span><br>
        <input type="text" id="message" placeholder="<?php echo Say_something; ?>" autocomplete="off">
        <input type="submit" value="<?php echo Talk; ?>"></p>
      </form>
      </div>
      <div id="chat"></div>
    </div>
    <div class="mission-panel" id="mission-hud"></div>    
    <div class="mission-panel" id="mission-interaction">
      <div class="closegamewindow" onclick="mission.hideInteractions()"></div>
      <div id="mission-interaction-data"></div>
    </div>
    <script type="text/javascript">
      var mission = new Mission( <?php echo $scenario->getId().', '.$character->getId(); ?>, backend.script, backend.root, backend.module, backend.lang, backend.fingerprint );
      mission.getLogic();
      gamechat = new Chat( backend.script, backend.root, backend.module, backend.lang, backend.fingerprint );
      gamechat.scenario = "<?php echo $scenario->getId(); ?>";
      gamechat.load();
      var gameTimer = setInterval(function(){ mission.getData(); }, 60000);
      var chatTimer = setInterval(function(){ gamechat.load(); }, 10000);
      
      $( function () {
        $( document ).keydown( function( evt ) {
          mission.keyEvent( evt.keyCode, "down" );
        } );
      } );

      $( function () {
        $( document ).keyup( function( evt ) {
          mission.keyEvent( evt.keyCode, "up" );
        } );
      } );

    </script>
    <?php
  }
}
?>