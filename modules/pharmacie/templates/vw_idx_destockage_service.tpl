{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["service_id", "_date_min", "_date_max", "all_stocks"];
  filter = new Filter("filter-destockage", "{{$m}}", "httpreq_vw_destockages_service_list", "list-destockages", filterFields);
  filter.submit();
});

function refreshDestockagesList() {
  var url = new Url("pharmacie", "httpreq_vw_destockages_service_list");
  url.requestUpdate("list-destockages", { waitingText: null } );
}
</script>

<form name="filter-destockage" action="?" method="get" onsubmit="if(checkForm(this)){ return filter.submit(); } else { return false; }">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="limit" value="" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_min form=filter-destockage register=1}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_max form=filter-destockage register=1}}</td>
      <td>
        <select name="service_id">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
      </td>
      <td><label><input name="all_stocks" type="checkbox" {{if $all_stocks == 'true'}}checked="checked"{{/if}} /> tous les stocks</label></td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<div id="list-destockages"></div>