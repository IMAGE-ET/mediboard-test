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
    Control.Tabs.create('tabs-main_patient_state', true, {
      afterChange: function(container) {
        switch (container.id) {
          case "patient_stats":
            PatientState.stats_filter();
            break;
        }
      }});
  });
</script>

<table class="main layout">
  <tr>
    <td class="narrow" style="white-space: nowrap;">
      <ul id="tabs-main_patient_state" class="control_tabs_vertical">
        <li><a href="#patient_manage">{{tr}}CPatientState.manage{{/tr}}</a></li>
        <li><a href="#patient_stats">{{tr}}Stats{{/tr}}</a></li>
      </ul>
    </td>

    <td id="patient_manage">
      <script>
        Main.add(function () {
          new Url("dPpatients", "ajax_filter_patient_state")
            .requestUpdate("patient_manage");
        });
      </script>
    </td>
    <td id="patient_stats">
    </td>
  </tr>
</table>