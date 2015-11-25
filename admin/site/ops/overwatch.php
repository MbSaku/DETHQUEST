<?php
if( isset( $_POST['from'] )
and isset( $_POST['to'] )
and isset( $_POST['ip'] )
and isset( $_POST['username'] ) 
and isset( $_POST['search'] ) ){
  $filter = Array (
  'from' => $_POST['from'],
  'to' => $_POST['to'],
  'ip' => $_POST['ip'],
  'username' => $_POST['username'],
  'search' => $_POST['search']
  );
}else{
  $filter = Array (
  'from' => strftime('%d/%m/%Y', time()),
  'to' => strftime('%d/%m/%Y', time()),
  'ip' => '',
  'username' => '',
  'search' => ''
  );
}
?>
<fieldset><legend><?php echo $module->getName().' - '.Overwatch; ?></legend>
  <form name="overwatch" method="post" action="">
    <p class="pinput"><?php echo Date_from; ?><br>
    <input type="text" name="from" id="ovfrom" value="<?php echo $filter['from']; ?>"></p>
    <p class="pinput"><?php echo Date_to; ?><br>
    <input type="text" name="to" id="ovto" value="<?php echo $filter['to']; ?>"></p>
    <p class="pinput"><?php echo Ip_filter; ?><br>
    <select name="ip">
      <?php
      echo '<option value=""'; if ($filter['ip'] == ''){ echo ' selected'; } echo '>'.All_ips.'</option>';
      $query = 'select distinct ip from int_overwatch order by ip asc';
      $result = $site->getDatalink()->dbQuery($query, 'result');
      foreach($result as $row){
        echo '<option value="'.$row[0].'"'; if ($filter['ip'] == $row[0]){ echo ' selected'; } echo '>'.$row[0].'</option>';
      }
      ?>
    </select></p>
    <p class="pinput"><?php echo Username_filter; ?><br>
    <select name="username">
      <?php
      echo '<option value=""'; if ($filter['username'] == ''){ echo ' selected'; } echo '>'.All_users.'</option>';
      $query = 'select distinct username from int_overwatch order by username asc';
      $result = $site->getDatalink()->dbQuery($query, 'result');
      foreach($result as $row){
        echo '<option value="'.$row[0].'"'; if( $filter['username'] == $row[0] ){ echo ' selected'; } echo '>'.$row[0].'</option>';
      }
      ?>
    </select></p>
    <p class="pinput"><?php echo Search_filter; ?>:<br>
    <input type="text" name="search" value="<?php echo $filter['search']; ?>"></p>
    <p><input type="submit" name="viewlog" value="<?php echo View_log; ?>"></p>
  </form>
  <div class="overwatch">
    <?php
    $query = 'select instant, device, browser, ip, username, data
    from int_overwatch';
    $bits = explode('/', $filter['from']);
    if (count($bits) == 3){
      $from = strtotime($bits[2].'/'.$bits[1].'/'.$bits[0]);
    }else{
      $from = time();
    }
    $bits = explode('/', $filter['to']);
    if (count($bits) == 3){
      $to = strtotime($bits[2].'/'.$bits[1].'/'.$bits[0]);
    }else{
      $to = time();
    }
    $query .= ' where instant>='.$from.' and instant<='.($to + 86400);
    if ($filter['ip'] != ''){
      $query .= ' and ip="'.$filter['ip'].'"';
    }
    if ($filter['username'] != ''){
      $query .= ' and username="'.$filter['username'].'"';
    }
    if ($filter['search'] != ''){
      $query .= ' and data like "%'.$filter['search'].'%"';
    }
    $query .= ' order by id desc limit 500';
    $result = $site->getDatalink()->dbQuery($query, 'result');
    foreach ($result as $row){
      echo '<div class="log">
      <div class="header">'.strftime ('%A, %d %B %Y %H:%M', $row[0]).'</div>
      <div class="header">'.$row[4].'</div>
      <div class="header">'.$row[3].'</div>
      <div class="header">'.$row[1].'</div>
      <div class="header">'.$row[2].'</div>
      <div class="data">'.nl2br($row[5]).'</div>
      </div>';
    }
    ?>
  </div>
</fieldset>
<script type="text/javascript">  //Script del datepicker.
      g_l=[];g_l.MONTHS=["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
      g_l.DAYS_3=["dom","lun","mar","mie","jue","vie","sab"];
      g_l.MONTH_FWD="+1";
      g_l.MONTH_BCK="-1";
      g_l.YEAR_FWD="+12";
      g_l.YEAR_BCK="-12";
      g_l.CLOSE="";
      g_l.ERROR_2=g_l.ERROR_1="Date object invalid!";
      g_l.ERROR_4=g_l.ERROR_3="Target invalid";
      g_jsDatePickImagePath="<?php echo $site->getBaseroot(); ?>/js/datepicker/images/";
      cal1 = new JsDatePick({
        useMode:2,
        cellColorScheme:"julius",
        target:"ovfrom",
        dateFormat:"%d/%m/%Y", 
        imgPath:"images/",
        limitToToday:false, 
        selectedDate:{
          year:<?php echo strftime ('%Y', time ()); ?>,
          month:<?php echo strftime ('%m', time ()); ?>,
          day:<?php echo strftime ('%d', time ()); ?>
        }
      });
      cal2 = new JsDatePick({
        useMode:2,
        cellColorScheme:"julius",
        target:"ovto",
        dateFormat:"%d/%m/%Y", 
        imgPath:"images/",
        limitToToday:false, 
        selectedDate:{
          year:<?php echo strftime ('%Y', time ()); ?>,
          month:<?php echo strftime ('%m', time ()); ?>,
          day:<?php echo strftime ('%d', time ()); ?>
        }
      });
</script>