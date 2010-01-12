<!-- $Id: vw_intermax.tpl 6959 2009-09-28 15:02:48Z phenxdesign $ -->

<script type="text/javascript">

function checkBilanFSE() {
  var oForm = document.BilanFSE;
  if (!checkForm(oForm)) {
  	return false;
  }
  
  var url = new Url("dPcabinet", "print_bilan_fse");
  url.addFormData(oForm);
  url.popup("700", "400", "Bilan FSE");
  return false;
}

</script>

<form name="BilanFSE" action="?" method="get" onsubmit="return checkBilanFSE()">

<table class="form">
  <tr>
    <th>{{mb_label object=$fse field="S_FSE_CPS"}}</th>
    <td>
      <select name="S_FSE_CPS" class="notNull ref">
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
    <th>{{mb_label object=$fse field=S_FSE_ETAT}}</th>
    <td>{{mb_field object=$fse field=S_FSE_ETAT defaultOption="&mdash; Tous"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$fse field=_date_min}}</th>
    <td>{{mb_field object=$fse field=_date_min form=BilanFSE register=true}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$fse field=_date_max}}</th>
    <td>{{mb_field object=$fse field=_date_max form=BilanFSE register=true}}</td>
  </tr>

  <tr>
    <td colspan="2" class="button">
      <button class="search">
        {{tr}}Search{{/tr}}
        {{tr}}CLmFSE{{/tr}}
      </button>
    </td>
  </tr>

</table>

</form>  
