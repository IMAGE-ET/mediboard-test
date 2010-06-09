{{* $Id: inc_deliveries_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{* {{mb_include module=system template=inc_pagination change_page=refreshOrders current=$start step=30}} *}}

<script type="text/javascript">
window.ordersTabs = null; // global var

Main.add(function(){
  var tabs = $("tab_orders");
  
  window.ordersTabs = Control.Tabs.create(tabs, true);
  $$('a[href=#list-orders] small')[0].update("("+({{$total}}-$$('tr.done').length)+")");
  
  Event.observe(window, "scroll", function(){
    tabs.setStyle({marginTop: document.viewport.getScrollOffsets().top+"px"});
  });
});
</script>

<table class="main layout">
  <tr>
    <td style="white-space: nowrap; width: 0.1%;">
      <ul class="control_tabs_vertical" id="tab_orders">
      {{foreach from=$list_services item=_service}}
        <li>
          <a class="{{if $_service->_count_deliveries == 0}}empty{{/if}}" href="#tab-{{$_service->_guid}}">
            {{$_service}} <small>({{$_service->_count_deliveries}})</small>
          </a>
          
          <script type="text/javascript">
            Main.add(function(){
              $$('a[href=#tab-{{$_service->_guid}}] small')[0].update("("+({{$_service->_count_deliveries}}-$('tab-{{$_service->_guid}}').select('tr.done').length)+")");
            });
          </script>
        </li>
      {{/foreach}}
      </ul>
    </td>
    <td>
      <table class="tbl">
        {{foreach from=$list_services item=_service}}
          <tbody id="tab-{{$_service->_guid}}" style="display: none;">
            <!-- Affichage des delivrances globales -->
            <tr>
              <th style="width: 0.1%;">{{tr}}CProductDelivery-patient_id{{/tr}}</th>
              <th colspan="2">{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
              <th>
                <label style="float: left;">
                  <input type="checkbox" onclick="$$('tr.done').invoke('setVisible', $V(this))"/> Afficher les commandes terminées
                </label>
                {{tr}}CProduct{{/tr}}
              </th>
              <th>{{tr}}CProductDelivery-comments{{/tr}}</th>
              {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
                <th>Stock pharmacie</th>
              {{/if}}
              
              {{* 
              {{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
                <th>Stock service</th>
              {{/if}}
              *}}
              
              <th style="width: 0.1%;">
                <button type="button" onclick="dispenseAll('tab-{{$_service->_guid}}', refreshOrders)" class="tick">
                  Disp. les {{$_service->_ref_deliveries|@count}} visibles
                </button>
              </th>
              <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
            </tr>
            {{foreach from=$_service->_ref_deliveries item=_delivery}}
              {{include file="inc_vw_line_order.tpl" curr_delivery=$_delivery nodebug=true}}
            {{foreachelse}}
            <tr>
              <td colspan="10">{{tr}}CProductDelivery.order.none{{/tr}}</td>
            </tr>
          </tbody>
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
