<?php
if( $dethuser->getCharacter() == 0 ){
  echo '<script>backend.link("character","character");</script>';
}else{
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
  if( isset( $_GET['section'] ) ){
    $building = $_GET['section'];
  }
  ?>
  <div id="thecity">
  <?php
  echo '<h1>'.The_city.'</h1>';
  if( !$character->isPlaying() ){
    if( isset( $_POST['type'] ) 
    and isset( $_POST['item'] ) ){
      switch( $_POST['type'] ){
        case 'weapon':
          $item = new Weapon( $site->getDatalink(), $_POST['item'] );
        break;
        case 'armor':
          $item = new Armor( $site->getDatalink(), $_POST['item'] );
        break;
      }
      echo $character->buy( $_POST['type'], $item );
    }
    if( isset( $_POST['rowsold'] ) ){
      echo $character->sell( $_POST['rowsold'] );
    }
    echo '<div class="charportrait">
      <div class="inner">'.$character->renderHtml( $module->getFolder(), true ).'</div>
    </div>      
    <div class="charoverview">
      <p><span class="desc"><b>'.$character->getName().'</b></span><br>
        '.Level.' <span class="out">'.$character->getLevel().'</span><br>
        '.Experience.': <b>'.$character->getExperience().' / '.$character->expNextLevel().'</b>
      </p>
      <div class="xpbar">
        <div class="bar">
          <div class="bar-grey" style="width:'.( ( $character->getExperience() / $character->expNextLevel() ) * 100 ).'%"></div>
        </div>
      </div>
      <p>'.Wealth.': <span class="out">'.number_format($character->getCoins(), 0, ',', '.').Coins.'</span><br>
      '.Premium_wealth.': <span class="out">'.number_format( $character->getPremium(), 0, ',', '.' ).'</span></p>
    </div>
    <p>'.Welcome.' <b>'.$character->getName().'</b>.</p>';
    $buildings = Array( 'melee', 'ranged', 'workshop'/*, 'clinic', 'faction'*/ );
    if( !in_array( $building, $buildings ) ){
      echo '<p>'.HelpCity.'</p>
      <div class="blist">';
      foreach( $buildings as $building ){
        echo '<a class="building" onclick="backend.link('."'".$me."'".','."'".$op."'".','."'".$building."'".');return false">'.constant( $building ).'</a>';
      }
      echo '</div>';
    }else{
      echo '<p><a class="building" onclick="backend.link('."'".$me."'".','."'".$op."'".');return false">'.Back_city.'</a></p>
      <div class="city-interior">
      <div class="city-inventory equipsheet">
      <h1>'.$character->getName().'</h1>
      <p><b>'.Inventory.'</b></p>';
      switch( $building ){
        case 'ranged':
        case 'melee':
          $inventory = $character->getInventory( 'weapon' );
        break;
        case 'workshop':
          $inventory = $character->getInventory( 'armor' );
        break;
        default:
          $inventory = $character->getInventory( 'healing' );
      }
      foreach( $inventory as $line ){
        $irow = new Inventory( $site->getDatalink(), $line );
        switch( $irow->getType() ){
          case 'weapon':
            $item = new Weapon( $site->getDatalink(), $irow->getItem() );
          break;
          case 'armor':
            $item = new Armor( $site->getDatalink(), $irow->getItem() );
          break;
        }
        echo '<div class="shop-item hoverable">
        <div class="item-picture">';
        if( $item->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'">';
        }
        echo '</div>
        <div class="item-name">'.$item->getName().'</div>
        <div class="item-text">';
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
        echo '<form name="sellweapon'.$item->getId().'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false)">
        <input type="hidden" name="rowsold" value="'.$irow->getId().'">
        <p><input type="submit" value="'.Sell_for.' '.$item->getSellingprice().Coins.'"></p>
        </form>
        </div>
        </div>';
      }
      echo '</div>
      <div class="city-forsale">
      <h1>'.constant( $building ).'</h1>
      <p><b>'.For_sale.'</b></p>';
      switch( $building ){
        case 'ranged':
          $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_item_weapon where close=0 and forsale=1 order by price asc', 'result' );
        break;
        case 'melee':
          $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_item_weapon where close=1 and forsale=1 order by price asc', 'result' );
        break;
        case 'workshop':
          $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_item_armor where forsale=1 order by price asc', 'result' );
        break;
      }
      foreach( $result as $row ){
        switch( $building ){
          case 'ranged':
          case 'melee':
            $item = new Weapon( $site->getDatalink(), $row[0] );
            $type = 'weapon';
          break;
          case 'workshop':
            $item = new Armor( $site->getDatalink(), $row[0] );
            $type = 'armor';
          break;
          default:
            $item = new HealingItem( $site->getDatalink(), $row[0] );
            $type = 'healing';
        }
        echo '<div class="shop-item hoverable">
        <div class="item-picture">';
        if( $item->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'">';
        }
        echo '</div>
        <div class="item-name">'.$item->getName().'</div>
        <div class="item-price"><b>'.$item->getPrice().Coins.'</b>';
        if( $item->getPremium() > 0 ){
          echo '<br><b>'.$item->getPremium().'</b> '.Premium_coins;
        }
        echo '</div>
        <div class="item-text">
        <form name="buyitem'.$item->getId().'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false)">
        <input type="hidden" name="type" value="'.$type.'">
        <input type="hidden" name="item" value="'.$item->getId().'">';
        switch( $building ){
          case 'ranged':
          case 'melee':
            if( $item->getClipsize() > 0 ){
              echo Item_clipsize.': <b>'.$item->getClipsize().'</b><br>
              '.Rate_of_fire.': <b>'.$item->getAttacks().'</b>';
            }
            echo '<p><input type="submit" value="'.Buy_weapon.'"></p>';
          break;
          case 'workshop':
            echo Hitpoints.': <b>'.$item->getHitpoints().'</b><br>
            '.Protection.': <b>'.$item->getProtection().'</b>';
            echo '<p><input type="submit" value="'.Buy_armor.'"></p>';
          break;
          default:
            echo '<p><input type="submit" value="'.Buy.'"></p>';
        }
        echo '</form>
        '.$item->getDescription().'
        </div>
        </div>';
      }
      echo '</div>
      </div>';
    }
  }else{
    echo '<p>'.Return_when_not_playing.'.</p>';
  }
  ?>
  </div>
  <?php
}
?>
<script type="text/javascript">
  setHoverables( "#thecity .hoverable" );
</script>