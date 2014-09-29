{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
editStockLocation = function(stock_location_id) {
  var url = new Url("dPstock", "httpreq_vw_stock_location_form");
  if (!Object.isUndefined(stock_location_id)) {
    url.addParam("stock_location_id", stock_location_id);
  }
  url.requestUpdate("stock-location-form");
};

Main.add(function() {
  editStockLocation();
  
  Control.Tabs.create("location-tabs");
});
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <ul class="control_tabs small" id="location-tabs">
        {{foreach from=$lists key=_class item=_list}}
          <li>
            <a href="#location-{{$_class}}" {{if $_list|@count == 0}} class="empty" {{/if}}>
              {{$classes.$_class}} <small>({{$_list|@count}})</small>
            </a>
          </li>
        {{/foreach}}
      </ul>

      {{foreach from=$lists key=_class item=_list}}
        <div id="location-{{$_class}}" style="display: none;">
          <table class="tbl">
            <tr>
              <th>{{mb_title class=CProductStockLocation field=name}}</th>
              <th>{{mb_title class=CProductStockLocation field=desc}}</th>
              <th>{{mb_title class=CProductStockLocation field=object_id}}</th>
              <th>{{mb_title class=CProductStockLocation field=position}}</th>
            </tr>
            {{foreach from=$_list item=_location}}
              <tr>
                <td class="text">
                  <a href="#1" onclick="editStockLocation({{$_location->_id}})"
                     title="{{tr}}CProductStockLocation-title-modify{{/tr}}">
                    {{mb_value object=$_location field=name}}
                  </a>
                </td>
                <td>{{mb_value object=$_location field=desc}}</td>
                <td>{{mb_value object=$_location field=object_class}} - {{mb_value object=$_location field=object_id}}</td>
                <td>{{mb_value object=$_location field=position}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="5" class="empty">{{tr}}CProductStockLocation.none{{/tr}}</td>
              </tr>
            {{/foreach}}
          </table>
        </div>
      {{/foreach}}
    </td>
    <td class="halfPane" id="stock-location-form"></td>
  </tr>
</table>