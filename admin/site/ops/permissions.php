<?php
if (isset ($_POST['rebuildpermissions'])){  //Permisos de acceso a las gestiones administrativas.
  $errors = '';
  $site->getDatalink()->dbQuery('truncate int_permissions', 'query');
  foreach ($_POST as $key => $value){
    $permission = explode ('-', $key);
    if (count($permission) == 2){
      $query = 'insert into int_permissions (charge, module) values ("'.$permission[0].'", "'.$permission[1].'")';
      if ($site->getDatalink()->dbQuery($query, 'query') <= 0){
        $errors .= Permission_setting_error.' '.$permission[0].', '.$permission[1].'<br>';
      }
    }
  }
  if ($errors != ''){
    echo '<p class="error">'.$errors.'</p>';
  }else{
    echo '<p class="fine">'.Permissions_rebuilt.'</p>';
  }
}
?>
<fieldset><legend><?php echo $module->getName().' - '.Permissions; ?></legend>
  <form name="permissions" method="post" action="">
    
  <p><?php echo Permission_help; ?></p>
  
  <table>
  <tr><th><?php echo Module_charge; ?></th>
    <?php
    $charges = Array();
    $query = 'select id, name 
    from int_charges 
    order by level asc';
    $result = $site->getDatalink()->dbQuery($query, 'result');
    foreach ($result as $row){
      echo '<th>'.$row[1].'</th>';
      $charges[] = $row[0];
    }
    ?></tr>
    <?php
    $query = 'select id 
    from int_admin 
    where active=1
    order by corder asc';
    $result = $site->getDatalink()->dbQuery($query, 'result');
    foreach ($result as $row){
      $moduled = new Module($site->getDatalink(), $row[0]);
      echo '<th>'.$moduled->getName().'</th>';
      foreach ($charges as $k => $charge){
        $query = 'select charge, module 
        from int_permissions
        where charge="'.$charge.'" 
        and module="'.$moduled->getId().'"';
        if ($k == 0){
          echo '<td><input type="checkbox" checked disabled="true"></td>';
        }else{
          if ($site->getDatalink()->dbQuery($query, 'rows') > 0){
            echo '<td><input name="'.$charge.'-'.$moduled->getId().'" type="checkbox" value="true" checked></td>';
          }else{
            echo '<td><input name="'.$charge.'-'.$moduled->getId().'" type="checkbox" value="true"></td>';
          }
        }
      }
      echo '</tr>';
    }
    ?>

  </table>
  <p><input type="submit" name="rebuildpermissions" value="<?php echo Rebuild_permissions; ?>"></p>
  </form>
</fieldset>