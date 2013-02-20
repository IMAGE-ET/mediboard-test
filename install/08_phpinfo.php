<?php
/**
 * PHP general installation
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkauth.php";

showHeader();
?>

<div style="font-size: 14px">
  
  <?php phpinfo(); ?>
  
</div>

<?php showFooter(); ?>