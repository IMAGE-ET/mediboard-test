{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
editStockLocation = function(stock_location_id) {
  var url = new Url("dPstock", "httpreq_vw_stock_location_form");
  if (!Object.isUndefined(stock_location_id))
    url.addParam("stock_location_id", stock_location_id);
  url.requestUpdate("stock-location-form");
}

Main.add(editStockLocation);
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th>{{mb_title class=CProductStockLocation field=name}}</th>
          <th>{{mb_title class=CProductStockLocation field=desc}}</th>
          <th>{{mb_title class=CProductStockLocation field=position}}</th>
        </tr>
        {{foreach from=$list_locations item=curr_location}}
        <tr>
          <td class="text">
            <a href="#1" onclick="editStockLocation({{$curr_location->_id}})"
               title="{{tr}}CProductStockLocation-title-modify{{/tr}}">
              {{mb_value object=$curr_location field=name}}
            </a>
          </td>
					<td>{{mb_value object=$curr_location field=desc}}</td>
          <td>{{mb_value object=$curr_location field=position}}</td>
        </tr>
				{{foreachelse}}
				<tr>
          <td colspan="5">{{tr}}CProductStockLocation.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>  
    </td>
    <td class="halfPane" id="stock-location-form"></td>
  </tr>
</table>