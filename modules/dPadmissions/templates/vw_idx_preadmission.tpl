{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=admissions script=admissions}}

<script type="text/javascript">
function submitPreAdmission(oForm) {
  return onSubmitFormAjax(oForm, reloadPreAdmission);
}

function reloadPreAdmission() {
  var admUrl = new Url("dPadmissions", "httpreq_vw_preadmissions");
  admUrl.addParam("date", "{{$date}}");
  admUrl.requestUpdate('listPreAdmissions');
}

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_preadmissions");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allPreAdmissions', { frequency: 120 });

  var listUpdater = new Url("dPadmissions", "httpreq_vw_preadmissions");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listPreAdmissions', { frequency: 120 });
});

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <a href="#legend" onclick="Admissions.showLegend()" class="button search">Légende</a>
      {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
    </td>
  </tr>
  <tr>
    <td id="allPreAdmissions">
    </td>
    <td id="listPreAdmissions" style="width: 100%">
    </td>
  </tr>
</table>