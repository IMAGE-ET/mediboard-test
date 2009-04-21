<!-- $Id$ -->

{{mb_include_script module="dpPatients" script="pat_selector"}}
{{include file="../../dPpatients/templates/inc_intermax.tpl"}}

<script type="text/javascript">

Intermax.ResultHandler["Consulter Vitale"] =
Intermax.ResultHandler["Lire Vitale"] = function() {
  PatSelector.set = function(id, view) {
    Console.debug(view, "Found and update patient");
    Console.debug(id, "With ID");
  }
  
  PatSelector.options.useVitale = 1;
  PatSelector.pop();
}

</script>

<table class="tbl">
  <!-- Yoplets for InterMax -->
  <tr>
    <th class="title">
      <button class="change intermax-result" onclick="Intermax.result();" style="float: right">
        Intégrer résultat
      </button>
      Fonctions LogicMax disponibles
    </th>
  </tr>

  {{foreach from=$intermaxFunctions key=category item=_functions}}
  <tr>
    <th>{{$category}}</th>
  </tr>
  <tr>
    <td class="button">
      {{foreach from=$_functions item=_function}}
      <button class="tick" onclick="Intermax.trigger('{{$_function}}');">
        {{$_function}}
      </button>
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  
</table>


<hr />

<script type="text/javascript">

function checkBilanCPS() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_bilan_cps");
  url.popup("800", "500", "Bilan CPS");
  return false;
}

</script>

<table class="tbl"> 
  <tr>
    <th class="title">Bilan d'utilisation de LogicMax</th>
  </tr>
  <tr>
    <td class="button">
      <button class="search" onclick="checkBilanCPS()">
	    	{{tr}}Compute{{/tr}}
      </button>
    </td>
  </tr>
</table>

<hr />

<script type="text/javascript">

function checkBilanFSE() {
  var oForm = document.BilanFSE;
  
  if (!checkForm(oForm)) {
  	return false;
  }
  
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_bilan_fse");
  $H(Form.toObject(oForm)).each(function (pair) { 
  	url.addParam(pair.key, pair.value) 
  } );
  
  url.popup("700", "400", "Bilan FSE");
  return false;
}

</script>

<form name="BilanFSE" action="?" method="get" onsubmit="return checkBilanFSE()">

<table class="form">
  <tr>
    <th class="title" colspan="2">
      Etat des FSE
    </th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field="S_FSE_CPS"}}</th>
    <td>
      <select name="S_FSE_CPS" class="notNull ref">
        <option value="">&mdash; Tous</option>
        {{foreach from=$prats item=_prat}}
        {{if $_prat->_id_cps}}
        <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id_cps}}" {{if $app->user_id == $_prat->_id}}selected="selected"{{/if}}>
          {{$_prat->_view}}
          [CPS #{{$_prat->_id_cps}}]
        </option>
        {{/if}}
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=S_FSE_ETAT}}</th>
    <td>{{mb_field object=$filter field=S_FSE_ETAT defaultOption="&mdash; Tous les types"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_date_min}}</th>
    <td class="date">{{mb_field object=$filter field=_date_min form=BilanFSE register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=_date_max}}</th>
    <td class="date">{{mb_field object=$filter field=_date_max form=BilanFSE register=true}}</td>
  </tr>

  <tr>
		{{if $filter->_spec->ds}}
    <td colspan="2" class="button">
      <button class="search">
        {{tr}}Search{{/tr}}
      </button>
    </td>
    {{else}}
    <td colspan="2">
      <div class="big-error">
	      Base de données LogicMax injoignable.
	      <br/>
	      Merci de vérifier la configuration du module LogicMax. 
      </div>
    </td>
    {{/if}}
  </tr>

</table>

</form>  
