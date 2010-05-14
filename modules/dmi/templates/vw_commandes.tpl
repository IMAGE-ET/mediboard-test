<form name="filterCommandes" action="?" method="get" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dmi" />
  <input type="hidden" name="tab" value="vw_commandes" />
  
  <table class="form">
    <tr>
      <th>Date</th>
      <td>{{mb_field object=$DMI field="date" prop="date" register=true form="filterCommandes" onchange="this.form.submit()"}}</td>
    </tr>
  </table>
</form>

<table class="tbl">
  <tr>
    <th>Stock</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=product_id}}</th>
    <th>{{mb_title class=CProduct field=code}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=praticien_id}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=date}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=type}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=septic}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    <th>Patient</th>
    <th style="width: 1%">Commander</th>
  </tr>
  {{foreach from=$listDMI item=_line_dmi}}
    <tr>
      <td>
        {{mb_include module=dPstock template=inc_bargraph stock=$_line_dmi->_ref_product->_ref_stock_group}}
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_product->_guid}}')">
          {{$_line_dmi->_ref_product}}
        </span>
      </td>
      <td>{{mb_value object=$_line_dmi->_ref_product field=code}}</td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_line_dmi->_ref_praticien}}
      </td>
      <td>{{mb_value object=$_line_dmi field=date}}</td>
      <td>{{mb_value object=$_line_dmi field=type}}</td>
      <td>{{mb_value object=$_line_dmi field=septic}}</td>
      <td>{{mb_value object=$_line_dmi->_ref_product_order_item_reception field=code}}</td>
      <td>{{mb_value object=$_line_dmi->_ref_product_order_item_reception field=lapsing_date}}</td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_prescription->_guid}}')">
          {{$_line_dmi->_ref_prescription->_ref_patient}}
        </span>
      </td>
      <td>
        <form name="product-reference-{{$_line_dmi->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {alert('commandé');} })">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_item_aed" />
          <input type="hidden" name="_create_order" value="1" />
          <input type="hidden" name="_context_guid" value="COperation-{{$_line_dmi->operation_id}}" />
          <input type="hidden" name="reception_id" value="" />
          <input type="hidden" name="quantity" value="1" />
          <select name="reference_id">
            {{foreach from=$_line_dmi->_ref_product->_back.references item=_reference}}
            <option value="{{$_reference->_id}}">{{$_reference->_ref_societe}} - {{$_reference}}</option>
            {{/foreach}}
          </select>
          <button class="add notext" type="submit" title="{{tr}}Add{{/tr}}">{{tr}}Add{{/tr}}</button>
        </form>
      </td>
    </tr>
  {{/foreach}}
</table>