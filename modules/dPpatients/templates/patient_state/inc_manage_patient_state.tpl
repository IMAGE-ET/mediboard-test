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

<script>
  Main.add(function () {
    Control.Tabs.create('tabs-patient_state', true, {
      afterChange: function(container) {
        switch (container.id) {
          case "patient_prov":
            PatientState.getListPatientByState("prov");
            break;
          case "patient_dpot":
            PatientState.getListPatientByState("dpot");
            break;
          case "patient_anom":
            PatientState.getListPatientByState("anom");
            break;
          case "patient_cach":
            PatientState.getListPatientByState("cach");
            break;
          case "patient_vali":
            PatientState.getListPatientByState("vali");
            break;
        }
      }});
    Control.Tabs.setTabCount("patient_prov", {{$patients_count.prov}});
    Control.Tabs.setTabCount("patient_dpot", {{$patients_count.dpot}});
    Control.Tabs.setTabCount("patient_anom", {{$patients_count.anom}});
    Control.Tabs.setTabCount("patient_cach", {{$patients_count.cach}});
    Control.Tabs.setTabCount("patient_vali", {{$patients_count.vali}});
  });
</script>

<form name="filter_patient_state" method="post" onsubmit="return PatientState.filterPatientState(this)">
  <table class="form">
    <tr>
      <th class="title" colspan="2">Filtre de recherche</th>
    </tr>
    <tr>
      <th>{{mb_label class="CPatientState" field="_date_min"}}</th>
      <td>{{mb_field class="CPatientState" field="_date_min" value=$date_min register=true form="filter_patient_state"}}</td>
    </tr>
    <tr>
      <th>{{mb_label class="CPatientState" field="_date_max"}}</th>
      <td>{{mb_field class="CPatientState" field="_date_max" value=$date_max register=true form="filter_patient_state"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<br/>

<ul id="tabs-patient_state" class="control_tabs">
  <li><a href="#patient_prov">{{tr}}CPatientState.state.PROV{{/tr}}</a></li>
  <li><a href="#patient_dpot">{{tr}}CPatientState.state.DPOT{{/tr}}</a></li>
  <li><a href="#patient_anom">{{tr}}CPatientState.state.ANOM{{/tr}}</a></li>
  <li><a href="#patient_cach">{{tr}}CPatientState.state.CACH{{/tr}}</a></li>
  <li><a href="#patient_vali">{{tr}}CPatientState.state.VALI{{/tr}}</a></li>
</ul>

<div id="patient_prov"></div>
<div id="patient_dpot"></div>
<div id="patient_anom"></div>
<div id="patient_cach"></div>
<div id="patient_vali"></div>