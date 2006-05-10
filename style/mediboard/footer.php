<?php
	echo $AppUI->getMsg();
?>
	</td>
</tr>
</table>

<?php

if( !function_exists('memory_get_usage') ) {
  function memory_get_usage() {
    if ( substr(PHP_OS,0,3) == 'WIN') {
      $output = array();
      $pid = getmypid();
      exec( 'tasklist /FI "PID eq $pid" /FO LIST', $output );
     
      // Sometimes PID filter is not available...
      if (!count($output)) {
        exec( 'tasklist /FO LIST', $output );
        for ($process_key = 0; $process_key < count($output); $process_key += 6) {
          if (preg_replace( '/[\D]/', '', $output[$process_key + 2]) == $pid) {
            $output = array_slice($output, $process_key, 6);
            break;
          }
        }
      }

      return preg_replace( '/[\D]/', '', $output[5] ) * 1024;
    } else {
      $pid = getmypid();
      exec("ps -eo%mem,rss,pid | grep $pid", $output);
      $output = explode("  ", $output[0]);
      return $output[1] * 1024;
    }
  }
}

?>
<?php if ($dPconfig['debug']) { ?>
  <div style="margin: 10px; text-align: center;">
    Page générée en <?php echo number_format($phpChrono->total, 3); ?> secondes
    par PHP, utilisant <?php echo mbConvertDecaBinary(memory_get_usage()); ?> de mémoire
    <?php foreach($dbChronos as $dbConfigName => $dbChrono) { ?>
    <br />
    <?php echo number_format($dbChrono->total, 3); ?> secondes prises 
    par la base de données <strong><?php echo $dbConfigName; ?></strong> en 
    <?php echo $dbChrono->nbSteps; ?> requêtes SQL.
    <?php  } ?>
  </div>
<?php } ?>

<?php if ($dPconfig['demo_version']) { ?>
<div style="margin: 10px; float:right">
  <a href="http://www.sourceforge.net/projects/mediboard/" title="Projet Mediboard sur Sourceforge">
    <img src="http://www.sourceforge.net/sflogo.php?group_id=112072&amp;type=2" alt="Sourceforge Project Logo" />
  </a>
</div>
<?php } ?>
</body>

</html>
