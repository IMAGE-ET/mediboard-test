{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  function synchronizeSejours() {
    var url = new Url();
    url.setModuleAction("dPhospi", "httpreq_do_synchronize_sejours");
    url.addElement(document.synchronizeFrm.dateMin);
    url.requestUpdate("synchronize");
  }
</script>

<form name="synchronizeFrm" method="get">
  <table class="form">
    <tr>
      <th colspan="2" class="title">
        Synchronisation des dates de sortie des séjours et des affectations
      </th>
    </tr>
    <tr>
      <td>
        Date minimale de sortie : <input type="text" name="dateMin" value="AAAA-MM-JJ" />
        <br />
        <button type="button" class="tick" onclick="synchronizeSejours()">Synchroniser</button>
      </td>
      <td id="synchronize"></td>
    </tr>
  </table>
</form>