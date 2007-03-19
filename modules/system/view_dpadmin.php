<?php /* $Id$ */

/*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Romain Ollivier
 */

// Old dP administration put in a tab

global $AppUI, $can, $m;

?>
<table>
<tr>
  <td>
    <?php echo dPshowImage( dPfindImage( 'rdf2.png', $m ), 42, 42, '' ); ?>
  </td>
  <td>
    <h2><?php echo $AppUI->_( 'Language Support' );?></h2>
  </td>
</tr>

<tr>
  <td />
  <td>
    <a href="?m=system&amp;tab=translate"><?php echo $AppUI->_( 'Translation Management' );?></a>
  </td>
</tr>

</table>