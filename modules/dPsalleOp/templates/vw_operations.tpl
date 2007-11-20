{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$selOp}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}


<script type="text/javascript">

function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadTiming(oForm.operation_id.value) } });
}

function reloadTiming(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", "systemMsg");
}

function submitAnesth(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadAnesth(oForm.operation_id.value) } });
}

function reloadAnesth(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", "systemMsg", { onComplete: loadActes(operation_id,"{{$selOp->chir_id}}") });
}

function pageMain() {
  PairEffect.initGroup("acteEffect");
  
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  
  {{if $selOp->operation_id}}
  new Control.Tabs('main_tab_group');
  {{/if}}
}
</script>

<table class="main">
  <tr>
    <td style="width: 200px;" id="listplages"></td>
    <td class="greedyPane">
      <table class="form">
        {{if $selOp->operation_id}}
        {{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}
        <tr>
          <th class="title" colspan="2">
					  <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
					    <img src="images/icons/edit.png" alt="modifier" />
					  </a>
            {{$patient->_view}} 
            ({{$patient->_age}} ans 
            {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
            &mdash; Dr. {{$selOp->_ref_chir->_view}}
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Timmings</a></li>
              <li><a href="#two">Anesthésie</a></li>
              <li><a href="#three">Codage</a></li>
            </ul>
          </td>
        </tr>
          
        {{include file="inc_timings_anesth.tpl"}}
      
        <!-- <tbody id = "ccam"> -->
        <tr id="three">
          <th class="category" style="vertical-align: middle">
            Actes<br /><br />
            {{tr}}{{$selOp->_class_name}}{{/tr}}
            {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
              <br />
              Côté {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
              <br />
              ({{$selOp->temp_operation|date_format:"%Hh%M"}})
            {{/if}}
          </th>
          
          <td>
            <div id="cim">
              {{assign var="sejour" value=$selOp->_ref_sejour}}
              {{include file="inc_diagnostic_principal.tpl"}}
            </div>
            <div id="ccam">
              {{assign var="subject" value=$selOp}}
              {{include file="inc_gestion_ccam.tpl"}}
            </div>
          </td>
        </tr>
        <!-- </tbody> -->
        
        {{if $selOp->materiel}}
        <tr>
          <th class="category">Matériel</th>
          <td><strong>{{$selOp->materiel|nl2br}}</strong></td>
        </tr>
        {{/if}}
        {{if $selOp->rques}}
        <tr>
          <th class="category">Remarques</th>
          <td>{{$selOp->rques|nl2br}}</td>
        </tr>
        {{/if}}
        {{else}}
        <tr>
          <th class="title">
            Sélectionnez une opération
          </th>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>