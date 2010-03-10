{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button class="new" onclick="loadEndowment(0)">{{tr}}CProductEndowment-title-create{{/tr}}</button>
      
<form name="edit_endowment" action="" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_product_endowment_aed" />
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="callback" value="loadEndowment" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$endowment}}
  
  <table class="form">
    <tr>
      {{if $endowment->_id}}
      <th class="title modify text" colspan="2">{{$endowment->name}}</th>
      {{else}}
      <th class="title text" colspan="2">{{tr}}CProductEndowment-title-create{{/tr}}</th>
      {{/if}}
    </tr>
    
    <tr>
      <th>{{mb_label object=$endowment field="name"}}</th>
      <td>{{mb_field object=$endowment field="name"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$endowment field="service_id"}}</th>
      <td>{{mb_field object=$endowment field="service_id" form="edit_endowment" autocomplete="true,1,50,false,true"}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="4">
        {{if $endowment->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$endowment->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $endowment->_id}}
<table class="form">
  <tr>
    <th class="category">{{tr}}CProductEndowment-back-endowment_items{{/tr}}</th>
  </tr>
  {{foreach from=$endowment->_back.endowment_items item=_item}}
    <tr>
      <td>
        {{assign var=_item_id value=$_item->_id}}
        <form name="edit_endowment_item_{{$_item->_id}}" action="" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){if ($V(this.del) == 1) loadEndowment({{$endowment->_id}})}.bind(this)})">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_product_endowment_item_aed" />
          <input type="hidden" name="del" value="0" />
          {{mb_key object=$_item}}
          {{mb_field object=$_item field=quantity form="edit_endowment_item_$_item_id" increment=true size=2 onchange="this.form.onsubmit()"}}
          <button class="remove notext" type="button" onclick="confirmDeletion(this.form,{ajax:true,objName:'{{$_item->_view|smarty:nodefaults|JSAttribute}}'})"></button>
        </form>
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_item->_ref_product->_guid}}')">
          {{$_item->_ref_product}}
        </strong>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2">{{tr}}CProductEndowmentItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <td colspan="2">
      <form name="edit_endowment_item" action="?m=dPstock" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_product_endowment_item_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="callback" value="loadEndowment" />
        <input type="hidden" name="cancelled" value="0" disabled="disabled" />
        {{mb_field object=$endowment field=endowment_id hidden=true}}
        {{mb_field class=CProductEndowmentItem field=endowment_item_id hidden=true}}
        {{mb_field class=CProductEndowmentItem field=product_id form="edit_endowment_item" autocomplete="true,1,50,false,true"}}
        {{mb_field class=CProductEndowmentItem field=quantity form="edit_endowment_item" increment=true size=2 value=1}}
        <button class="add notext" type="submit"></button>
      </form>
    </td>
  </tr>
</table>
{{/if}}