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

  <p><?php echo HelpNPCs; ?></p>
  
<?php
if (!isset($_POST['character'])){
?>
  
  <form name="usersearch" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><?php echo Search_by_name; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Search; ?>"></p>
  </form>
  
  <div class="editiontitle"><?php echo NPCs; ?></div>
  
  <?php
  $query = 'select id from '.mod.'deth_characters where name like "%'.$filter.'%" and pc="0" order by name asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $character = new PlayerCharacter( $site->getDatalink(), $row[0] );
        $race = new Race($site->getDatalink(), $character->getRace() );
        $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
        echo '<form name="char'.$character->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <div class="field">
          <div class="charportrait">
            <div class="inner">'.$character->renderHtml( $module->getFolder(), true ).'</div>
          </div>
        </div>
        <div class="field"><b>'.$race->getName().' '.$class->getName().'</b><br><input type="submit" value="'.$character->getName().'"></div>
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
  $character = new PlayerCharacter( $site->getDatalink(), $_POST['character'] );
  if (isset($_POST['delete'])){
    echo $character->delete();
  }else{
    echo $character->save();
  }
  $race = new Race( $site->getDatalink(), $character->getRace() );
  $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
?>

  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_characters; ?>"></p>
  </form>
  
  <div class="charactersheet">
    
    <form name="editchar" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="character" value="<?php echo $character->getId(); ?>">
    
    <div class="character">
      <?php echo $character->renderHtml( $module->getFolder() ); ?>
    </div>
    
    <div class="stats">
      
      <p><?php 
        echo '<span class="desc">'.$character->getName().'</span><br>
        <b>'.$race->getName().' '.$class->getName().'</b>';
      ?></p>
      
      <?php
      $result = $site->getDatalink()->dbQuery( 'select user from '.mod.'deth_user where playercharacter="'.$character->getId().'"', 'result' );
      if ( isset( $result[0] ) ){
        $player = new User( $site->getDatalink(), $result[0][0]);
        echo '<p>'.Player_name.': <b>'.$player->getName().'</b><br>
        '.Email.': <b>'.$player->getEmail().'</b></p>';
      }
      ?>
      
      <p><?php 
        echo Level.' <span class="out">'.$character->getLevel().'</span><br>
        '.Experience.': <b>'.$character->getExperience().' / '.$character->expNextLevel().'</b>'; 
      ?></p>
      
      <p><?php 
        echo Wealth.': <span class="out">'.number_format($character->getCoins(), 0, ',', '.').Coins.'</span>'; 
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
    
    <p><input type="submit" value="<?php echo Save_character; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Delete_character; ?></p>
    
    </form>
    
  </div>
  
<?php
}
?>