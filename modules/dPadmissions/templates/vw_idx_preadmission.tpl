{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function showLegend() {
  var url = new Url("dPadmissions", "vw_legende");
  url.popup(300, 170, "Legende");
}

function submitPreAdmission(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadPreAdmission });
}

function reloadPreAdmission() {
  var admUrl = new Url("dPadmissions", "httpreq_vw_preadmissions");
  admUrl.addParam("date", "{{$date}}");
  admUrl.requestUpdate('listPreAdmissions', { waitingText : null });
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
      <a href="#" onclick="showLegend()" class="button search">L�gende</a>
    </td>
  </tr>
  <tr>
    <td id="allPreAdmissions">
    </td>
    <td id="listPreAdmissions" style="width: 100%">
    </td>
  </tr>
</table>