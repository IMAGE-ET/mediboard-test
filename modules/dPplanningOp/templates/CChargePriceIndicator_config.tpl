<script type="text/javascript">
editCharge = function(charge_id) {
  var url = new Url("planningOp", "edit_charge_price_indicator");
  url.addParam("charge_id", charge_id);
  url.requestModal(400, 400);
}
</script>

<button class="new" onclick="editCharge(0)">{{tr}}CChargePriceIndicator-title-create{{/tr}}</button>

<table class="main tbl">
  <tr>
    <th>{{mb_title class=CChargePriceIndicator field=code}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=libelle}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=type}}</th>
    <th>{{mb_title class=CChargePriceIndicator field=actif}}</th>
  </tr>

  {{foreach from=$list_cpi item=_cpi}}
    <tr>
      <td>
        <a href="#1" onclick="return editCharge({{$_cpi->_id}})">{{mb_value object=$_cpi field=code}}</a>
      </td>
      <td>
        <a href="#1" onclick="return editCharge({{$_cpi->_id}})">{{mb_value object=$_cpi field=libelle}}</a>
      </td>
      <td>{{mb_value object=$_cpi field=type}}</td>
      <td>{{mb_value object=$_cpi field=actif}}</td>
    </tr>
  {{/foreach}}
</table>
