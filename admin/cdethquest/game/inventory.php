<?php
if( $dethuser->getCharacter() == 0 ){
  echo '<script>backend.link("character","character");</script>';
}else{
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
      <?php echo $character->renderBars(); ?>
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
      foreach( $rows as $row ){
        $irow = new Inventory( $site->getDatalink(), $row );
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
  $( ".inventory .hoverable" ).each( function() {
    $( this ).click( function() {
      $( this ).toggleClass( "hovered" );
    } );
  });
</script>