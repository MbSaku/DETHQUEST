<?php
if( $dethuser->getCharacter() == 0 ){
  echo '<script>backend.link("character","character");</script>';
}else{
  include( 'game/interactions.php' );
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
  $types = array( 'healing', 'repairing', 'weapon', 'armor', 'equipment' );
  $titles = array( 
    'healing' => Healing_items,
    'repairing' => Repairing_items,
    'weapon' => Weapons,
    'armor' => Armor,
    'equipment' => Equipment
  );
  ?>
  <h1><?php echo Inventory; ?></h1>
  <div class="charselect">
    <div class="charportrait">
      <div class="inner"><?php echo $character->renderHtml( $module->getFolder(), true ); ?></div>
    </div>
    <div class="charoverview">
      <p><?php 
        echo '<span class="desc"><b>'.$character->getName().'</b></span><br>
        '.Level.' <span class="out">'.$character->getLevel().'</span>'; 
      ?></p>
      <?php echo $character->renderBars(); 
      if( $character->isPlaying() ){
        echo '<p>'.Actions.': <span class="turnaction">'.$character->getActions().'</span></p>';
      }
      ?>
    </div>
  </div>
  <div class="inventory">
  <?php
  foreach( $types as $type ){
    $rows = $character->getInventory( $type );
    if( count( $rows ) > 0 ){
      ?>
      <div class="editiontitle"><?php echo $titles[$type]; ?></div>
      <?php
      foreach( $rows as $irow ){
        switch( $irow->getType() ){
          case 'healing':     $item = new HealingItem( $site->getDatalink(), $irow->getItem() );    break;
          case 'repairing':   $item = new RepairingItem( $site->getDatalink(), $irow->getItem() );  break;
          case 'weapon':      $item = new Weapon( $site->getDatalink(), $irow->getItem() );         break;
          case 'armor':       $item = new Armor( $site->getDatalink(), $irow->getItem() );          break;
          case 'equipment':   $item = new Equipment( $site->getDatalink(), $irow->getItem() );      break;
        }
        ?>
        <div class="inventory-item hoverable">
          <div class="item-picture">
          <?php
          if( $item->getIcon() != '' ){
            echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'">';
          }
          ?>
          </div>
          <div class="item-name"><?php echo $item->getName(); ?></div>
          <div class="item-text">
          <?php
          switch( $irow->getType() ){
            case 'weapon':
              if( $item->getClipsize() > 0 ){
                echo Ammo.': <b>'.$irow->getValue().'</b> / '.$item->getClipsize().'<br>'.Clips.': <b>'.$irow->getMax().'</b>';
              }
            break;
            case 'armor':
              echo Hitpoints.': <b>'.$irow->getValue().'</b> / '.$item->getHitpoints().'<br>'.Protection.': <b>'.$item->getProtection().'</b>';
            break;
            case 'healing':
              echo Health_amount.': <b>'.$item->getHealth().'</b>
              <form name="use'.$irow->getId().'" method="post" action="" onsubmit="event.preventDefault(); backend.post(this, false);">
              <input type="hidden" name="operation" value="heal">
              <input type="hidden" name="invrow" value="'.$irow->getId().'">
              <p><input type="submit" value="'.Use_item.'"></p>
              </form>';
            break;
            case 'repairing':
              echo Armor_amount.': <b>'.$item->getArmor().'</b>
              <form name="use'.$irow->getId().'" method="post" action="" onsubmit="event.preventDefault(); backend.post(this, false);">
              <input type="hidden" name="operation" value="repair">
              <input type="hidden" name="invrow" value="'.$irow->getId().'">
              <p><input type="submit" value="'.Use_item.'"></p>
              </form>';
            break;
            case 'equipment':
              if( $item->getMaxhealth() != 0 ){ echo Health_boost.': <b>'.$item->getMaxhealth().'</b><br>'; }
              if( $item->getSpeed() != 0 ){ echo Speed_boost.': <b>'.$item->getSpeed().'</b><br>'; }
              if( $item->getStrength() != 0 ){ echo Strength_boost.': <b>'.$item->getStrength().'</b><br>'; }
              if( $item->getDexterity() != 0 ){ echo Dexterity_boost.': <b>'.$item->getDexterity().'</b><br>'; }
              if( $item->getConstitution() != 0 ){ echo Constitution_boost.': <b>'.$item->getConstitution().'</b><br>'; }
              if( $item->getIntelligence() != 0 ){ echo Intelligence_boost.': <b>'.$item->getIntelligence().'</b><br>'; }
            break;
          }
          echo '<p>'.$item->getDescription().'</p>';
          ?>
          </div>
        </div>
        <?php
      }
    }
  }
  ?>
  </div>
  <?php
}
?>
<script type="text/javascript">
  setHoverables( ".inventory .hoverable" );
</script>