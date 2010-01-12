<!-- $Id: vw_intermax.tpl 6959 2009-09-28 15:02:48Z phenxdesign $ -->

<script type="text/javascript">

function checkBilanLot() {
  var oForm = document.BilanLot;
  
  if (!checkForm(oForm)) {
  	return false;
  }

  var url = new Url("dPcabinet", "print_bilan_lot");
  url.addFormData(oForm);
  url.popup("700", "400", "Bilan Lot");
  return false;
}

</script>

<form name="BilanLot" action="?" method="get" onsubmit="return checkBilanLot()">

<table class="form">
  <tr>
    <th>{{mb_label object=$lot field="S_LOT_CPS"}}</th>
    <td>
      <select name="S_LOT_CPS" class="notNull ref">
        <option value="">&mdash; Tous</option>
        {{foreach from=$prats item=_prat}}
        {{if $_prat->_id_cps}}
        <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id_cps}}" {{if $app->user_id == $_prat->_id}}selected="selected"{{/if}}>
          {{$_prat}}
          [CPS #{{$_prat->_id_cps}}]
        </option>
        {{/if}}
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$lot field=S_LOT_ETAT}}</th>
    <td>{{mb_field object=$lot field=S_LOT_ETAT defaultOption="&mdash; Tous"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$lot field=_date_min}}</th>
    <td>{{mb_field object=$lot field=_date_min form=BilanLot register=true}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$lot field=_date_max}}</th>
    <td>{{mb_field object=$lot field=_date_max form=BilanLot register=true}}</td>
  </tr>

  <tr>
    <td colspan="2" class="button">
      <button class="search">
        {{tr}}Search{{/tr}}
        {{tr}}CLmLot{{/tr}}
      </button>
    </td>
  </tr>

</table>

</form>  
