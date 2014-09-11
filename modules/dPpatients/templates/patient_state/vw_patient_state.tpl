{{*
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module="dPpatients" script="patient_state"}}
{{mb_script module="dPpatients" script="patient"}}

<script>
  Main.add(function () {
    Control.Tabs.create('tabs-main_patient_state', true);
  });
</script>

<table class="main layout">
  <tr>
    <td class="narrow">
      <ul id="tabs-main_patient_state" class="control_tabs_vertical">
        <li><a href="#patient_manage">{{tr}}CPatientState.manage{{/tr}}</a></li>
        <li onmousedown="PatientState.stats_filter()"><a href="#patient_stats">{{tr}}Stats{{/tr}}</a></li>
      </ul>
    </td>
    <td id="patient_manage">
      {{mb_include module="dPpatients" template="patient_state/inc_manage_patient_state"}}
    </td>
    <td id="patient_stats">
    </td>
  </tr>
</table>