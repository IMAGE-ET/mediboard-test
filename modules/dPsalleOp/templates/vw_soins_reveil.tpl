{{if $op_reveil->_id}}
{{assign var="chir_id" value=$op_reveil->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$op_reveil}}
{{mb_include module=dPsalleOp template=js_codage_ccam}}

<script type="text/javascript">

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim", { waitingText : null } );
}

Main.add(function () {
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_list_patients_reveil");
  opsUpdater.addParam("date"        , "{{$date}}");
  opsUpdater.addParam("bloc_id"     , "{{$bloc->_id}}");
  opsUpdater.addParam("op_reveil_id", "{{$op_reveil->_id}}");
  opsUpdater.periodicalUpdate("listpatients", { frequency: 90 });
  
  {{if $op_reveil->_id}}
  // Initialisation des onglets
	if ($('main_tab_group')){
    Control.Tabs.create('main_tab_group', true);
	}
	if ($('codage_tab_group')){
    Control.Tabs.create('codage_tab_group', true);
	}
	
  // Effet sur la liste des patients
	if ($('listpatients') && $('listpatients-trigger')){
    new PairEffect("listpatients", { sEffect : "appear", bStartVisible : true });
	}
  {{/if}}
  
  Calendar.regField(getForm("selection").date, null, {noView: true});
});

</script>

<table class="main">
  <tr>
    <th colspan="2">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_soins_reveil" />
        {{$date|date_format:$dPconfig.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        <select name="bloc_id" onchange="this.form.submit();">
          <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
          {{foreach from=$blocs_list item=curr_bloc}}
            <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
              {{$curr_bloc->nom}}
            </option>
          {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
          {{/foreach}}
        </select>
      </form>
    </th>
  </tr>
  <tr>
    <td style="width: 220px;" id="listpatients">
    </td>
    <td>
      {{if $op_reveil->_id}}
        {{include file=inc_reveil_operation.tpl}}
      {{else}}
        <div class="big-info">
          Veuillez sélectionner une intervention dans la liste pour pouvoir :
          <ul>
      	    <li>coder des actes</li>
      	    <li>consulter le dossier</li>
          </ul>
        </div>
      {{/if}}
    </td>
  </tr>
</table>