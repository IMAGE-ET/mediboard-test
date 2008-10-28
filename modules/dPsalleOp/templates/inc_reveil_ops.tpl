<script type="text/javascript">

// faire le submit de formOperation dans le onComplete de l'ajax
checkPersonnel = function(oFormAffectation, oFormOperation){
  oFormOperation.entree_reveil.value = 'current';
  // si affectation renseignée, on submit les deux formulaires
  if(oFormAffectation && oFormAffectation.personnel_id.value != ""){
    submitFormAjax(oFormAffectation, 'systemMsg', {onComplete: submitOperationForm(oFormOperation,1)} );
  }
  else {
  // sinon, on ne submit que l'operation
    submitOperationForm(oFormOperation,1);
  }
}
submitOperationForm = function(oFormOperation,sens) {
  submitFormAjax(oFormOperation,'systemMsg', {onComplete: function(){
      var url = new Url;
      url.setModuleAction("dPsalleOp", "httpreq_reveil_ops");
      url.addParam('date',"{{$date}}");
      url.requestUpdate("ops");
      if(sens==1) {
	      url.setModuleAction("dPsalleOp", "httpreq_reveil_reveil");
	      url.addParam('date',"{{$date}}");
	      url.requestUpdate("reveil");
      }
    }
  });
}
</script>


<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    {{if $isbloodSalvageInstalled}}
      <th>{{tr}}SSPI.RSPO{{/tr}}</th>
    {{/if}}
    <th>{{tr}}SSPI.SortieSalle{{/tr}}</th>
    <th>{{tr}}SSPI.EntreeReveil{{/tr}}</th>
  </tr>    
  {{foreach from=$listOps item=curr_op}}
  <tr>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
    {{if $isbloodSalvageInstalled}}
      <td>
        {{if $curr_op->blood_salvage->_id}}
        <div style="float:left ; display:inline">
          <a href="#" title="Voir la procédure RSPO" onclick="viewRSPO({{$curr_op->_id}});">         
          <img src="images/icons/search.png" title="Voir la procédure RSPO" alt="vw_rspo">
          {{if $curr_op->blood_salvage->totaltime > "00:00:00"}}  
            Débuté à {{$curr_op->blood_salvage->_recuperation_start|date_format:"%Hh%M"}}
          {{else}}
            Non débuté
          {{/if}} 
        </a>
        </div>
        {{if $curr_op->blood_salvage->totaltime|date_format:"%H:%M" > "05:00"}} 
        <div style="float:right; display:inline">
        
        <img src="images/icons/warning.png" title="Durée légale bientôt atteinte !" alt="alerte-durée-RSPO">
        {{/if}}
        </div>
        {{else}} 
          Non inscrit
        {{/if}}
      </td>
    {{/if}}
    <td>
      {{if $can->edit}}
      <form name="editSortieBlocFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$curr_op field="sortie_salle"}}
        <button class="tick notext" type="button" onclick="submitOperationForm(this.form);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
      {{mb_value object=$curr_op field="sortie_salle"}}
      {{/if}}
    </td>
    <td>
      {{if $can->edit || $modif_operation}}
      
      {{if $personnels !== null}}
      <form name="selPersonnel{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpersonnel" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="object_class" value="{{$curr_op->_class_name}}" />
        <input type="hidden" name="tag" value="reveil" />
        <input type="hidden" name="realise" value="0" />
        <select name="personnel_id">
        <option value="">&mdash; Personnel</option>
        {{foreach from=$personnels item="personnel"}}
        <option value="{{$personnel->_id}}">{{$personnel->_ref_user->_view}}</option>
        {{/foreach}}
        </select>
      </form>
      {{/if}}
      
      <form name="editEntreeReveilFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="entree_reveil" value="" /> 
        <button class="tick notext" type="button" onclick="checkPersonnel(document.selPersonnel{{$curr_op->_id}}, this.form);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}
        -
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $('liops').innerHTML = {{$listOps|@count}};
  $('heure').innerHTML = "{{$hour|date_format:"%Hh%M"}}";
</script>
