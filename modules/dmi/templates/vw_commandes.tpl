{{mb_include_script module=dPprescription script=prescription}}

<script type="text/javascript">
  function orderProduct(form){
    return onSubmitFormAjax(form, {
      onComplete: function(){
        location.reload();
      }
    });
  }
</script>

<form name="filterCommandes" action="?" method="get" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dmi" />
  <input type="hidden" name="tab" value="vw_commandes" />
  
  <table class="form">
    <tr>
      <th>Date</th>
      <td>{{mb_field object=$dmi_line field="date" prop="date" register=true form="filterCommandes" onchange="this.form.submit()"}}</td>
    </tr>
  </table>
</form>

<table class="tbl">
  <tr>
    <th colspan="2">{{mb_title class=CPrescriptionLineDMI field=product_id}}</th>
    <th>{{mb_title class=CProduct field=code}}</th>
    <th>Stock</th>
    <th style="width: 1%">Commander</th>
    <th style="width: 1%">Déjà comm.</th>
  </tr>
  <tr>
    <th>{{mb_title class=CPrescriptionLineDMI field=quantity}}</th>
    <th>Patient</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=praticien_id}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=date}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=type}}</th>
    <th>{{mb_title class=CPrescriptionLineDMI field=septic}}</th>
  </tr>
  {{foreach from=$lines_by_context key=_context_guid item=_lines}}
    <tr>
      <th colspan="20" class="title">
        {{$contexts.$_context_guid}}
        <button type="button" class="print notext" onclick="Prescription.printPrescription({{$contexts.$_context_guid->prescription_id}})"></button>
      </th>
    </tr>
    {{foreach from=$_lines item=_line_dmi}}
    <tbody class="hoverable">
      <tr>
        <td colspan="2">
          <strong onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_product_order_item_reception->_guid}}')">
            {{$_line_dmi->_ref_product}}
          </strong>
        </td>
        <td>{{mb_value object=$_line_dmi->_ref_product field=code}}</td>
        <td>
          {{mb_include module=dPstock template=inc_bargraph stock=$_line_dmi->_ref_product->_ref_stock_group}}
        </td>
        <td>
          <form name="product-reference-{{$_line_dmi->_id}}" action="?m=dmi&amp;tab=vw_commandes" method="post" onsubmit="return orderProduct(this)">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="_create_order" value="1" />
            <input type="hidden" name="_context_guid" value="COperation-{{$_line_dmi->operation_id}}" />
            <input type="hidden" name="reception_id" value="" />
            <input type="hidden" name="quantity" value="1" />
            <select name="reference_id">
              {{foreach from=$_line_dmi->_ref_product->_back.references item=_reference}}
                <option value="{{$_reference->_id}}" 
                        {{if $_line_dmi->_ref_product_order_item_reception->_ref_order_item->reference_id == $_reference->_id}}selected="selected"{{/if}}>
                  {{$_reference->_ref_societe}} (x{{$_reference->quantity}})
                </option>
              {{/foreach}}
            </select>
            <button class="add notext" type="submit">{{tr}}Add{{/tr}}</button>
          </form>
        </td>
        <td>
          {{foreach from=$_line_dmi->_orders item=_order}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_order->_guid}}')">
              {{$_order}}
            </span><br />
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <td>{{mb_value object=$_line_dmi field=quantity}}</td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_line_dmi->_ref_prescription->_guid}}')">
            {{$_line_dmi->_ref_prescription->_ref_patient}}
          </span>
        </td>
        <td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_line_dmi->_ref_praticien}}
        </td>
        <td>{{mb_value object=$_line_dmi field=date}}</td>
        <td>{{mb_value object=$_line_dmi field=type}}</td>
        <td>{{mb_value object=$_line_dmi field=septic}}</td>
      </tr>
    </tbody>
    {{foreachelse}}
      <tr>
        <td colspan="20">{{tr}}CPrescriptionLineDMI.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="20">{{tr}}CPrescriptionLineDMI.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>