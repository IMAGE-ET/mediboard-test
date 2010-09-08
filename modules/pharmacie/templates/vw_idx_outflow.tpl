{{* $Id: vw_idx_delivrance.tpl 8542 2010-04-13 09:02:43Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 8542 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function checkOutflow(form) {
  return checkForm(form) && ($V(form.comments) || $V(form.service_id) != 0);
}

Main.add(function(){
  getForm("filter").onsubmit();
});

function changePage(start) {
  $V(getForm("filter").start, start);
}

function removeOutflow(delivery_id, view) {
  var form = getForm("outflow-delete");
  $V(form.delivery_id, delivery_id);
  confirmDeletion(form,{typeName: 'la sortie manuelle de', ajax: true, objName: view}, {
    onComplete: function(){
      getForm("filter").onsubmit();
    }
  });
}
</script>


<form name="outflow-delete" method="post" action="?">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="delivery_id" value="" />
</form>

<form name="filter" action="?" method="get" onsubmit="return Url.update(this, 'outflows-list')">
  <input type="hidden" name="m" value="pharmacie" />
  <input type="hidden" name="a" value="httpreq_vw_list_outflows" />
  <input type="hidden" name="start" value="{{$start}}" onchange="this.form.onsubmit()" />
  
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<form name="newOutflow" method="post" action="?m={{$m}}&amp;tab={{$tab}}" onsubmit="return checkOutflow(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="date_dispensation" value="now" />
  <input type="hidden" name="manual" value="1" />
  
  <table class="table tbl">
    <tr>
      <th style="width: 0.1%;">{{mb_title class=CProductStockGroup field=product_id}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=quantity}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=date_delivery}}</th>
      <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=service_id}}</th>
      <th>{{mb_title class=CProductDelivery field=comments}}</th>
      <th style="width: 0.1%;"></th>
    </tr>
    
    <tr>
      <td>{{mb_field class=CProductStockGroup field=product_id form="newOutflow" autocomplete="true,1,100,false,true" style="width: 30em;"}}</td>
      <td>{{mb_field object=$delivrance field=quantity increment=true form="newOutflow" size=2}}</td>
      <td>{{mb_field object=$delivrance field=date_delivery form="newOutflow" register=1}}</td>
      <td>
        <select name="service_id">
          <option value=""> &ndash; {{tr}}CService{{/tr}}</option>
          {{foreach from=$list_services item=_service}}
            <option value="{{$_service->_id}}">{{$_service}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>{{mb_field object=$delivrance field=comments}}</td>
      <td><button class="tick" type="submit">Délivrer</button></td>
    </tr>
    
    <tbody id="outflows-list"></tbody>
  </table>
</form>