{{* $Id: vw_idx_admission.tpl 11880 2011-04-15 09:35:38Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11880 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 *}}

{{mb_script module=admissions script=admissions}}

<script type="text/javascript">
function reloadFullPermissions(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_all_permissions");
  url.addParam("date"      , "{{$date}}");
  url.requestUpdate('allAdmissions');
	reloadAdmission(filterFunction);
}

function reloadPermission(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_permissions");
  url.addParam("date"      , "{{$date}}");
	if(!Object.isUndefined(filterFunction)){
	  url.addParam("filterFunction", filterFunction);
	}
  url.requestUpdate('listPermissions');
}

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_permissions");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allPermissions', { frequency: 120 });

  var listUpdater = new Url("dPadmissions", "httpreq_vw_permissions");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listPermissions', { frequency: 120 });
});

</script>

<table class="main">
<tr>
  <td>
    <a href="#legend" onclick="Admissions.showLegend()" class="button search">Légende</a>
    {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
  </td>
</tr>
  <tr>
    <td id="allPermissions" style="width: 250px">
    </td>
    <td id="listPermissions" style="width: 100%">
    </td>
  </tr>
</table>