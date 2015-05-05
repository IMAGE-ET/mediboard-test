{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $current >= $total}}
  <script type="text/javascript">
    Main.add(function() {
      stopUpdateMontant();
    });
  </script>

  <div class="small-info">Mise à jour des montants terminée : {{$total}} actes traités</div>
{{else}}
  <div class="small-info">Nombre d'actes à traiter : {{$total}}</div>
  <div class="small-info">Nombre d'actes traités : {{$current}}</div>
{{/if}}