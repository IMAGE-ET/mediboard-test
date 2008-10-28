
<script type="text/javascript">

codageCCAM = function(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_codage_actes_reveil");
  url.addParam("operation_id", operation_id);
  url.popup(700,500,"Actes CCAM");
}


submitReveil = function(oFormAffectation, oFormOperation){
  oFormOperation.entree_reveil.value = '';
  // s'il y a une affectation, on submit les deux formulaires

  if(oFormAffectation.affect_id.value != ""){
    submitFormAjax(oFormAffectation, 'systemMsg', {onComplete: submitReveilForm(oFormOperation,0)} ); 
  }
  else {
  // sinon, on ne submit que l'operation
    submitReveilForm(oFormOperation,1);  
  }
}
// Sens:
// 0 Rafraichit seulement le tableau des reveils.
// 1 Rafraichit le tableau des attentes et celui des reveils
// 2 Rafraichit le tableau des reveils et celui des sorties

submitReveilForm = function(oFormOperation,sens) {
  submitFormAjax(oFormOperation,'systemMsg', {onComplete: function($sens){
		  var url = new Url;
		  url.setModuleAction("dPsalleOp", "httpreq_reveil_reveil");
		  url.addParam('date',"{{$date}}");
		  url.requestUpdate("reveil");

      if(sens == 1) {
			  url.setModuleAction("dPsalleOp", "httpreq_reveil_ops");
	      url.addParam('date',"{{$date}}");
	      url.requestUpdate("ops");
     }
      if(sens == 2) {
	      url.setModuleAction("dPsalleOp", "httpreq_reveil_out");
	      url.addParam('date',"{{$date}}");
	      url.requestUpdate("out");
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
    <th>{{tr}}SSPI.Chambre{{/tr}}</th>    
    {{if $isbloodSalvageInstalled}}
      <th>{{tr}}SSPI.RSPO{{/tr}}</th>
    {{/if}}
    <th>{{tr}}SSPI.SortieSalle{{/tr}}</th>
    <th>{{tr}}SSPI.EntreeReveil{{/tr}}</th>
    <th>{{tr}}SSPI.SortieReveil{{/tr}}</th>

  </tr>    
  {{foreach from=$listReveil key=key item=curr_op}}
  <tr>
    <td>{{$curr_op->_ref_salle->nom}}</td>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
    <td class="text">
      <div style="float: left; display: inline">
      {{$curr_op->_ref_sejour->_ref_patient->_view}}
      </div>
      <div style="float: right; display: inline">
        <a href="#" onclick="codageCCAM('{{$curr_op->_id}}');">
        <img src="images/icons/anesth.png" alt="Anesth" />
        </a>
      </div>
    
    </td>
    <td class="text">
      {{assign var="affectation" value=$curr_op->_ref_sejour->_ref_first_affectation}}
      {{if $affectation->affectation_id}}
      {{$affectation->_ref_lit->_view}}
      {{else}}
      Non placé
      {{/if}}
    </td>
    {{if $isbloodSalvageInstalled}}
	    <td>
	      {{if $curr_op->blood_salvage->_id}}
	      <div style="float:left ; display:inline">
	        <a href="#" title="Voir la procédure RSPO" onclick="viewRSPO({{$curr_op->_id}});">         
	        <img src="images/icons/search.png" title="Voir la procédure RSPO" alt="vw_rspo" />
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
        <form name="editSortieBlocFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$curr_op field="sortie_salle"}}
          <button class="tick notext" type="button" onclick="submitReveilForm(this.form,0);">{{tr}}Modify{{/tr}}</button>
        </form>
      {{else}}
      {{mb_value object=$curr_op field="sortie_salle"}}
      {{/if}}
    </td>
    <td>
    
      
      <form name="delPersonnel{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpersonnel" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="affect_id" value="{{$curr_op->_ref_affectation_reveil->_id}}" />
      </form>
      
      
      <form name="editSortieBlocFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{if $curr_op->_ref_sejour->type=="exte"}}
        -
        {{elseif $can->edit}}
        {{mb_field object=$curr_op field="entree_reveil"}}
        <button class="tick notext" type="button" onclick="submitReveilForm(this.form,0);">{{tr}}Modify{{/tr}}</button>
        <button class="cancel notext" type="button" onclick="submitReveil(document.delPersonnel{{$curr_op->_id}}, this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="entree_reveil" onchange="submitReveilForm(this.form,0);">
          <option value="">-</option>
          {{foreach from=$timing.$key.entree_reveil|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $curr_op->entree_reveil}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button class="cancel notext" type="button" onclick="$V(this.form.entree_reveil, '') ; submitReveilForm(this.form,1);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{mb_value object=$curr_op field="entree_reveil"}}
        {{/if}}
      </form>
       
      
      {{if $curr_op->_ref_affectation_reveil->_id}}
      <br />{{$curr_op->_ref_affectation_reveil->_ref_personnel->_ref_user->_view}}
      {{/if}}
      
    </td>
    <td class="button">
      {{if $can->edit || $modif_operation}}
      <form name="editEntreeReveilFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="sortie_reveil" value="" />
        <button class="tick notext" type="button" onclick="$V(this.form.sortie_reveil, 'current') ; submitReveilForm(this.form,2);">{{tr}}Modify{{/tr}}</button>
      </form>
      {{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="20">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $('lireveil').innerHTML = {{$listReveil|@count}};
</script>