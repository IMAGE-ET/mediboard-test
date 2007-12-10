<!-- $Id: vw_resume.tpl 1748 2007-03-20 18:58:41Z MyttO $ -->

{{mb_include_script module="dpPatients" script="pat_selector"}}
{{include file="../../dPpatients/templates/inc_intermax.tpl" debug="false"}}

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
      <button class="change" onclick="Intermax.result();" style="float: right">
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

<script type="text/javascript">

function checkBilanFSE() {
  var oForm = document.BilanFSE;
  
  if (!checkForm(oForm)) {
  	return;
  }
  
  var url = new Url;
  url.setModuleAction("dPcabinet", "vw_bilan_fse");
  $A(oForm.elements).each(url.addElement.bind(url));
  url.popup("600", "400", "Bilan FSE");
  return false;
}

function pageMain() {
  regFieldCalendar("BilanFSE", "_date_min");
  regFieldCalendar("BilanFSE", "_date_max");
} 

</script>

<form name="BilanFSE" action="?" method="get" onsubmit="return checkBilanFSE()">

<table class="form">
  <tr>
    <th class="title" colspan="2">
      Requêter les FSE
    </th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=S_FSE_MODE_SECURISATION}}</th>
    <td>{{mb_field object=$filter field=S_FSE_MODE_SECURISATION}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_date_min}}</th>
    <td class="date">{{mb_field object=$filter field=_date_min form=BilanFSE}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=_date_max}}</th>
    <td class="date">{{mb_field object=$filter field=_date_max form=BilanFSE}}</td>
  </tr>

  <tr>
    <td colspan="2" class="button">
      <button class="search">
        {{tr}}Search{{/tr}}        
      </button>
    </td>
  </tr>

</table>

</form>  
