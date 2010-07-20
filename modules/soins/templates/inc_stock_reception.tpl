{{* $Id: inc_restockages_service_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
printReceptionReport = function(service_id){
  var url = new Url("soins", "httpreq_vw_stock_reception");
  url.addParam("service_id", service_id);
  url.addParam("mode", "print");
  url.popup(800, 600, "Rapport des commandes");
}

changeReceptionPage = function(start) {
  $V(getForm('filter-reception').start, start); 
}
</script>

<div style="float: left;">
  <button class="print" onclick="printReceptionReport({{$service_id}})">{{tr}}Print{{/tr}}</button>
</div>

{{mb_include module=system template=inc_pagination change_page="changeReceptionPage" 
    total=$deliveries_count current=$start step=30}}
    
<form name="filter-reception" action="?" method="get" onsubmit="return (checkForm(this) && refreshReceptions())">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="start" value="{{$start}}" onchange="refreshReceptions()" />
</form>

<table class="tbl">
  <tr>
    <th>{{*tr}}CProductDelivery-service_id{{/tr*}}Pour</th>
    <th style="width: 0.1%;">
      <button type="button" onclick="terminateAll('list-reception')" class="send notext">Tout marquer comme terminé (ne met pas à jour le stock du service)</button>
    </th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th colspan="2">Date commande</th>
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th>
      <button type="button" onclick="receiveAll('list-reception')" class="tick">Tout recevoir</button>
    </th>
  </tr>
  {{foreach from=$deliveries item=curr_delivery}}
    <tr>
      <td>
        {{if $curr_delivery->patient_id}}
          {{$curr_delivery->_ref_patient->_view}}
        {{else}}
          {{$curr_delivery->_ref_service->_view}}
        {{/if}}
      </td>
      <td>
        <form name="delivery-force-{{$curr_delivery->_id}}-receive" action="?" method="post" 
              class="force {{if $curr_delivery->isReceived()}}valid{{/if}}"
              onsubmit="return onSubmitFormAjax(this, {onComplete: refreshReceptions})">
          <input type="hidden" name="m" value="dPstock" /> 
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_delivery_aed" />
          {{mb_key object=$curr_delivery}}
          <input type="hidden" name="date_delivery" value="now" />
          <button type="submit" class="send notext">Marquer comme terminé</button>
        </form>
      </td>
      <td>
        {{if $curr_delivery->stock_id}}
          <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">
            {{if $curr_delivery->comments}}
              <strong>Commentaires: </strong>{{mb_value object=$curr_delivery field=comments}}
              <hr />
            {{/if}}
            {{$curr_delivery->_ref_stock->_ref_product->_quantity}}
          </div>
          {{if $curr_delivery->_ref_stock->canEdit()}}
          <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_service_id={{$curr_delivery->_ref_stock->_id}}">
          {{/if}}
            <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')">
              {{$curr_delivery->_ref_stock}}
            </span>
          {{if $curr_delivery->_ref_stock->canEdit()}}
          </a>
          {{/if}}
        {{else}}
          {{mb_value object=$curr_delivery field=comments}}
        {{/if}}
      </td>
      <td style="text-align: center;">{{mb_ditto name=date value=$curr_delivery->date_dispensation|date_format:$dPconfig.date}}</td>
      <td style="text-align: center;">{{mb_ditto name=time value=$curr_delivery->date_dispensation|date_format:$dPconfig.time}}</td>
      <td style="text-align: right">{{mb_value object=$curr_delivery field=quantity}}</td>
      <td>{{mb_value object=$curr_delivery->_ref_stock->_ref_product field=_unit_title}}</td>
      <td>
        <table class="layout">
        {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
          <tr>
            <td>
              {{assign var=id value=$trace->_id}}
              <form name="delivery-trace-{{$id}}-receive" action="?" method="post" onsubmit="return receiveLine(this)">
                <input type="hidden" name="m" value="dPstock" /> 
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
                <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
                {{if !$trace->date_reception}}
                  {{mb_field object=$trace field=quantity increment=1 form=delivery-trace-$id-receive size=2}}
                  {{mb_field object=$trace field=code size=10 title="Code"}}
                  <input type="hidden" name="date_reception" value="now" />
                  <button type="submit" class="tick notext">Recevoir</button>
                {{else}}
                  <button type="submit" class="cancel notext">Annuler</button>
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$trace->_guid}}')">
                    {{mb_value object=$trace field=date_reception}} - 
                    <strong>{{mb_value object=$trace field=quantity}} éléments</strong>
                    {{if $trace->code}}
                      [{{mb_value object=$trace field=code}}]
                    {{/if}}
                  </span>
                  <input type="hidden" name="_unreceive" value="1" />
                {{/if}}
              </form>
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td>Pas encore sorti de la pharmacie</td>
          </tr>
        {{/foreach}}
        </table>
      </td>
    </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $$('a[href=#list-reception] small').first().update('({{$deliveries_count}})');
</script>