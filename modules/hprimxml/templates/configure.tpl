{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />
<script type="text/javascript">
function doAction(sAction) {
  var url = new Url;
  url.setModuleAction("hprimxml", "ajax_do_cfg_action");
  url.addParam("action", sAction);
  url.requestUpdate(sAction);
}
</script>
<table class="tbl">
  <tr>
    <th class="category" colspan="10">Installation des schémas HPRIM XML</th>
  </tr>
  <tr>
    <th class="category">Action</th>
    <th class="category">Status</th>
  </tr>
  <tr>
    <td onclick="doAction('evenementsServeurActes');">
      <button class="tick">Installation HPRIM XML 'Evénements Serveurs Actes'</button>
    </td>
    <td class="text" id="evenementsServeurActes"></td>
  </tr>
  <tr>
    <td onclick="doAction('evenementsPmsi');">
      <button class="tick">Installation HPRIM XML 'Evénements PMSI'</button>
    </td>
    <td class="text" id="evenementsPmsi"></td>
  </tr>
  <tr>
    <td onclick="doAction('evenementsPatients');">
      <button class="tick">Installation HPRIM XML 'Evénements Patients'</button>
    </td>
    <td class="text" id="evenementsPatients"></td>
  </tr>
</table>

<hr />
