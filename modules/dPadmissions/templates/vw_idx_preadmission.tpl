{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=admissions script=admissions}}

<script>
function submitPreAdmission(oForm) {
  return onSubmitFormAjax(oForm, Admissions.updateListPreAdmissions);
}

function reloadPreAdmission() {
  Admissions.updateListPreAdmissions();
}

Main.add(function () {
  Admissions.target_date = '{{$date}}';

  // load the first elements
  Admissions.updateSummaryPreAdmissions();
  Admissions.updateListPreAdmissions();

  //start periodical
  Admissions.updatePeriodicalSummaryPreAdmissions();
  Admissions.updatePeriodicalPreAdmissions();

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