<?php
	echo $AppUI->getMsg();
?>
	</td>
</tr>
</table>

<?php if ($dPconfig['debug']) { ?>
  <div style="margin: 10px; text-align: center;">
    Page générée en <?php echo number_format($phpChrono->total, 3); ?> secondes
    par PHP.
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
