{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var tabs;
Main.add(function () {
  tabs = Control.Tabs.create('tab_delivrances', true);
  refreshLists();
  refreshPatient();
});

function refreshPatient(){
  var form = getForm("filter");
  var url = new Url("pharmacie","httpreq_vw_list_patients");
  url.addParam("date_min", form._date_min.value);
  url.addParam("date_max", form._date_max.value);
  url.addParam("service_id", form.service_id.value);
  url.requestUpdate("patients");
}
</script>

<form name="filter" action="?" method="get" onsubmit="if(window.loadSuivi) loadSuivi($V(this.sejour_id)); return (checkForm(this) && refreshLists())">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="start" value="0" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=1}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=1}}</td>
      <td>
        <select name="service_id" onchange="refreshPatient();">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
      </td>
      <td id="patients"></td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>