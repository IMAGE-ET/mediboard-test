{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function popupVoies(){
  var url = new Url("dPprescription", "vw_voies");
  url.popup(300,700,"Voies");
}
</script>

<button class="search" onclick="popupVoies()" type="button">Afficher les voies</button>
