<script type="text/javascript">
editCharge = function(charge_id) {
  var url = new Url("planningOp", "edit_charge_price_indicator");
  url.addParam("charge_id", charge_id);
  url.requestModal(400, 400);
  return false;
}
</script>

<button class="new" type="button" onclick="editCharge(0)">{{tr}}CChargePriceIndicator-title-create{{/tr}}</button>

<table class="main tbl">
  <tr>
    <th>{{mb_title class=CChargePriceIndicator field=code}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=libelle}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=type}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=type_pec}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=actif}}</th>
  </tr>

  {{foreach from=$list_cpi item=_cpi}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="return editCharge({{$_cpi->_id}})">
          {{tr}}Edit{{/tr}}
        </button>
        {{mb_value object=$_cpi field=code}}
      </td>
      <td>{{mb_value object=$_cpi field=libelle}}</td>
      <td>{{mb_value object=$_cpi field=type}}</td>
      <td>{{mb_value object=$_cpi field=type_pec}}</td>
      <td>
        <form name="editActif{{$_cpi->_guid}}"  method="post" onsubmit="return onSubmitFormAjax(this)">
          {{mb_key object=$_cpi}}
          {{mb_class object=$_cpi}}
          {{mb_field object=$_cpi field="actif" onchange=this.form.onsubmit()}}
        </form>
      </td>
    </tr>
  {{/foreach}}
</table>
