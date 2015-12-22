<?php
$prace = 0;
$pgender = 'male';
$pbody = 0;
$phead = 0;
$phair = 0;
$pface = 0;
$pclass = 0;
$pname = '';
if ( isset( $_POST['race'] ) ){  $prace = $_POST['race']; }
if ( isset( $_POST['gender'] ) ){  $pgender = $_POST['gender']; }
if ( isset( $_POST['body'] ) ){  $pbody = $_POST['body']; }
if ( isset( $_POST['head'] ) ){  $phead = $_POST['head']; }
if ( isset( $_POST['hair'] ) ){  $phair = $_POST['hair']; }
if ( isset( $_POST['face'] ) ){  $pface = $_POST['face']; }
if ( isset( $_POST['class'] ) ){  $pclass = $_POST['class']; }
if ( isset( $_POST['name'] ) ){  $pname = $_POST['name']; }
if( $dethuser->getCharacter() == 0
and isset( $_POST['race'] )
and isset( $_POST['gender'] )
and isset( $_POST['name'] ) ){
  $character = new PlayerCharacter( $site->getDatalink()) ;
  echo $character->pcGeneration( $pclass, $prace, $pgender, $pbody, $phair, $phead, $pface, $pname );
  if ( $character->getId() != 0){
    $dethuser->setCharacter( $character->getId() );
    $dethuser->save();
  }
}
if($dethuser->getCharacter() == 0){
  $race = new Race( $site->getDatalink(), $prace );
?>
<h1><?php echo Character_creation; ?></h1>
<form name="chargen" method="post" action="" onsubmit="event.preventDefault();backend.post(this);">
<div class="chargen">
  <div class="chargen-config">
    <div class="chargenp">
      <div class="genoption"><?php echo Gender; ?>:</div>
      <div class="genselection">
        <input type="radio" name="gender" value="male" onchange="preview.loadGender(this.value)" checked> <?php echo Male; ?>
      </div>
      <div class="genselection">
        <input type="radio" name="gender" value="female" onchange="preview.loadGender(this.value)"> <?php echo Female; ?>
      </div>
    </div>    
    <div class="chargenp">
      <div class="genoption"><?php echo Race; ?>:</div>
      <?php
      $result = $site->getDatalink()->dbQuery('select id from '.mod.'deth_races where playable=1 order by name asc', 'result');
      foreach ($result as $row){
        $race = new Race($site->getDatalink(), $row[0]);
        echo '<div id="ra'.$race->getId().'" class="genicon race" onclick="preview.loadRace('.$race->getId().', '.$race->randomAppearance( 'body' ).', '.$race->randomAppearance( 'hair' ).', '.$race->randomAppearance( 'face' ).', '.$race->randomAppearance( 'head' ).')">';
        if( $race->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getIcon().'" title="'.$race->getName().'">'; 
        }else{
          echo $race->getName();
        }
        echo '</div>';
      }
      ?>
    </div>
    <div class="chargenp">
      <div class="genoption"><?php echo CharClass; ?>:</div>
      <?php
      $result = $site->getDatalink()->dbQuery('select id from '.mod.'deth_classes where playable=1 order by name asc', 'result');
      foreach ($result as $row){
        $class = new CharacterClass($site->getDatalink(), $row[0]);
        echo '<div id="cl'.$class->getId().'" class="genicon class" onclick="preview.loadClass('.$class->getId().')">';
        if( $class->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$class->getIcon().'" title="'.$class->getName().'">'; 
        }else{
          echo $class->getName();
        }
        echo '</div>';
      }
      ?>
    </div>
    <div class="chargen-descriptions">
      <div id="racedesc" class="description"><?php echo Chargen_text; ?></div>
      <div id="classdesc" class="description"></div>    
    </div>    
  </div>  
  <div class="chargen-details">    
    <div class="facegen">
      <div class="prev-selectors">
        <a onclick="preview.previousAppearance('hair')"><</a>
        <a onclick="preview.previousAppearance('face')"><</a>
        <a onclick="preview.previousAppearance('head')"><</a>
      </div>
      <div class="charportrait">
        <div class="inner" id="chargen-portrait"></div>
      </div>
      <div class="next-selectors">
        <a onclick="preview.nextAppearance('hair')">></a>
        <a onclick="preview.nextAppearance('face')">></a>
        <a onclick="preview.nextAppearance('head')">></a>
      </div>
    </div>    
    <p class="chargenp">
      <?php echo Character_name; ?>:<br>
      <input type="text" name="name" id="nameinput" value="<?php echo $pname; ?>" style="width:10em;"><br>
      <a onclick="preview.loadRandomName($('#nameinput'))"><?php echo Random_name; ?></a>
    </p>
    <div class="chargen-preview">
      <div class="prev-selectors">
        <a onclick="preview.previousAppearance('body')"><</a>
      </div>
      <div id="character" class="divchar">
        <div class='charlayer' style='background-image:url(<?php echo $_GET['root'].'admin/'.$module->getFolder(); ?>/css/images/generic-male.png);background-size:90% 90%'></div>
      </div>
      <div class="next-selectors">
        <a onclick="preview.nextAppearance('body')">></a>
      </div>
      <div id="chargen-inputs">
        <input type="number" name="race" id="inprace" value="<?php echo $prace; ?>">
        <input type="number" name="class" id="inpclass" value="<?php echo $pclass; ?>">
        <input type="number" name="hair" id="inphair" value="0">
        <input type="number" name="face" id="inpface" value="0">
        <input type="number" name="head" id="inphead" value="0">
        <input type="number" name="body" id="inpbody" value="0">
      </div>
      <p class="chargenp">
        <input type="submit" value="<?php echo Create_character; ?>">
      </p>
    </div>
  </div>    
</div>
</form>
<script type="text/javascript">
  preview = new CharacterGen( backend.script, backend.root, backend.module, backend.lang, backend.fingerprint, '#character', '#racedesc', '#classdesc', 
  <?php echo $prace.", '".$pgender."', ".$pbody.", ".$phair.", ".$phead.", ".$pface.", ".$pclass; ?>);
  preview.loadGraphics();
</script>
<?php
}else{
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
  $race = new Race( $site->getDatalink(), $character->getRace() );
  $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
  if ( $character->getId() != 0 ){
    include( 'game/interactions.php' );
?>
  <h1><?php echo $character->getName(); ?></h1>  
  <div class="charactersheet">    
    <div class="stats">      
      <div class="charportrait">
        <div class="inner"><?php echo $character->renderHtml( $module->getFolder(), true ); ?></div>
      </div>      
      <div class="charoverview">
        <p><?php 
          echo '<span class="desc">'.$race->getName().' '.$class->getName().'</span><br>
          '.Level.' <span class="out">'.$character->getLevel().'</span><br>
          '.Experience.': <b>'.$character->getExperience().' / '.$character->expNextLevel().'</b>'; 
        ?></p>
        <div class="xpbar">
          <div class="bar">
            <div class="bar-grey" style="width:<?php echo ( ( $character->getExperience() / $character->expNextLevel() ) * 100 ) ?>%"></div>
          </div>
        </div>
      </div>      
      <p><?php 
        echo Wealth.': <span class="out">'.number_format( $character->getCoins(), 0, ',', '.' ).Coins.'</span><br>
        '.Premium_wealth.': <span class="out">'.number_format( $character->getPremium(), 0, ',', '.' ).'</span>'; 
      ?></p>
      <div class="charstats">
        <div class="stat">
          <div class="name"><?php echo Health; ?></div>
          <div class="value"><?php echo $character->getHealth().' / '.$character->getMaxhealth(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Speed; ?></div>
          <div class="value"><?php echo $character->getSpeed(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Strength; ?></div>
          <div class="value"><?php echo $character->getStrength(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Dexterity; ?></div>
          <div class="value"><?php echo $character->getDexterity(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Constitution; ?></div>
          <div class="value"><?php echo $character->getConstitution(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Intelligence; ?></div>
          <div class="value"><?php echo $character->getIntelligence(); ?></div>
        </div>
      </div>            
    </div>    
    <div class="character">
      <?php echo $character->renderHtml( $module->getFolder() ).$character->renderBars(); ?>
    </div>    
    <div class="equipsheet">      
      <form name="equip" method="post" action="" onsubmit="event.preventDefault(); backend.post(this, false);">
      <input type="hidden" name="operation" value="equip">
      <?php
      if( $character->isPlaying() ){
        echo '<p>'.Actions.': <span class="turnaction">'.$character->getActions().'</span></p>';
      }
      ?>
      <p><?php echo Hands.': <b>'.$character->remainingHands().'</b>'; ?><br>
      <?php 
      echo Equip.': <select name="invrow">
      <option value="0">'.Select_item.'</option>';
      $inventory = array_merge( $character->getInventory( 'weapon' ), $character->getInventory( 'armor' ), $character->getInventory( 'equipment' ) );
      foreach( $inventory as $line ){
        $info = '';
        switch( $line->getType() ){
          case 'armor':
          default:
            $item = new Armor( $site->getDatalink(), $line->getItem() );
            $info = $line->getValue().'/'.$item->getHitpoints();
          break;
          case 'weapon':
            $item = new Weapon( $site->getDatalink(), $line->getItem() );
            if( $item->getClipsize() > 0 ){
              $info = $line->getValue().'/'.$item->getClipsize().' ('.$line->getMax().')';
            }
          break;
          case 'equipment':
            $item = new Equipment( $site->getDatalink(), $line->getItem() );
          break;          
        }
        echo '<option value="'.$line->getId().'"><b>'.$item->getName().'</b> '.$info.'</option>';
      }
      echo '</select> <input type="submit" value="'.Equip.'">';
      ?>
      </p>
      </form>
      <?php
      echo '<div class="equip-title">'.Weapons.'</div>';
      $weapons = $character->getEquipment( 'weapon' );
      foreach( $weapons as $weapondata ){
        $weapon = new Weapon( $site->getDatalink(), $weapondata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $weapon->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$weapon->getIcon().'" title="'.$weapon->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$weapon->getName().'</b>';
        if( $weapon->getClipsize() > 0 ){
          echo '<br>'.Ammo.': <b>'.$weapondata[2].'</b> / '.$weapon->getClipsize().'<br>'.Clips.': <b>'.$weapondata[3].'</b>';
        }
        echo '</div>
        <div class="equip-block">';
        if( $weapon->getClipsize() > 0 and $weapondata[2] < $weapon->getClipsize() and $weapondata[3] > 0 ){
          echo '<form name="reload'.$weapondata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
          <input type="hidden" name="operation" value="reload">
          <input type="hidden" name="invrow" value="'.$weapondata[0].'">
          <input type="submit" value="'.Reload.'">
          </form>';
        }
        echo '<form name="unequip'.$weapondata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$weapondata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      echo '<div class="equip-title">'.Armor.'</div>';
      $armors = $character->getEquipment( 'armor' );
      foreach( $armors as $armordata ){
        $armor = new Armor( $site->getDatalink(), $armordata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $armor->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$armor->getIcon().'" title="'.$armor->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$armor->getName().'</b>
        <br>'.Hitpoints.': <b>'.$armordata[2].'</b> / '.$armor->getHitpoints().'<br>'.Protection.': <b>'.$armor->getProtection().'</b>
        </div>
        <div class="equip-block">
        <form name="unequip'.$armordata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$armordata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      echo '<div class="equip-title">'.Equipment.'</div>';
      $equipment = $character->getEquipment( 'equipment' );
      foreach( $equipment as $equipmentdata ){
        $item = new Equipment( $site->getDatalink(), $equipmentdata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $item->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'" title="'.$item->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$item->getName().'</b>';
        echo '</div>
        <div class="equip-block">
        <form name="unequip'.$equipmentdata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$equipmentdata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      ?>
    </div> 
  </div>
<?php
  }
}
?>