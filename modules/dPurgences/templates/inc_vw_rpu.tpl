<script type="text/javascript">

function submitRPU(){
  var oForm = document.editRPU;
  submitFormAjax(oForm, 'systemMsg');
}

</script>

<form name="editRPU" action="?" method="post">
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
	<table class="form">
	  <tr>
	    <th class="category" colspan="2">Prise en charge infirmier</th>
	    <th class="category" colspan="2">Prise en charge praticien</th>
	  </tr> 
	  <tr>
	    <td colspan="2">{{mb_label object=$rpu field="diag_infirmier"}}
	    <!-- Aide a la saisie -->
        <select name="_helpers_diag_infirmier" size="1" onchange="pasteHelperContent(this); this.form.diag_infirmier.onchange();">
          <option value="">&mdash; Choisir une aide</option>
          {{html_options options=$rpu->_aides.diag_infirmier.no_enum}}
        </select>
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.diag_infirmier)">{{tr}}New{{/tr}}</button><br />
        
        </td>
	    <td colspan="2">{{mb_label object=$rpu field="motif"}}
	    <!-- Aide a la saisie -->
        <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this); this.form.motif.onchange();">
          <option value="">&mdash; Choisir une aide</option>
          {{html_options options=$rpu->_aides.motif.no_enum}}
        </select>
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.motif)">{{tr}}New{{/tr}}</button><br />      
	      
	      </td>
	  </tr>
	  <tr>
	    <td colspan="2">
	      {{mb_field object=$rpu field="diag_infirmier" onchange="submitRPU();"}}
      </td>
	    
	    <td colspan="2">
	      {{mb_field object=$rpu field="motif" onchange="submitRPU();"}}
	    </td>
	  </tr>
	  
	  <tr>
	    <th>{{mb_label object=$rpu field="_entree"}}</th>
	    <td>{{$rpu->_entree|date_format:"%d %b %Y à %H:%M"}}</td>
	 
	    <th>{{mb_label object=$rpu field="sortie"}}</th>
	    <td class="date">{{mb_field object=$rpu field="sortie" form="editRPU" onchange="submitRPU();"}}</td> 
	  </tr>
	  
	  <tr>
	    <th>{{mb_label object=$rpu field="ccmu"}}</th>
	    <td>{{mb_field object=$rpu field="ccmu" onchange="submitRPU();"}}</td>
	    
	    <th>{{mb_label object=$rpu field="mode_sortie"}}</th>
	    <td>{{mb_field object=$rpu field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="submitRPU();"}}</td>
	  </tr>
	  
	  <tr> 
	    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
	    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée" onchange="submitRPU();"}}</td>

      <th>{{mb_label object=$rpu field="destination"}}</th>
	    <td>{{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination" onchange="submitRPU();"}}</td> 
	  </tr>	  
	  
	  <tr>
	    <th>{{mb_label object=$rpu field="provenance"}}</th>
	    <td>{{mb_field object=$rpu field="provenance" defaultOption="&mdash; Provenance" onchange="submitRPU();"}}</td>
	   
	    <th>{{mb_label object=$rpu field="orientation"}}</th>
	    <td>{{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation" onchange="submitRPU();"}}</td>
	  </tr>
	 
	  <tr>   
	    <th>{{mb_label object=$rpu field="transport"}}</th>
	    <td>{{mb_field object=$rpu field="transport" defaultOption="&mdash; Type de transport" onchange="submitRPU();"}}</td>
	    
	    <td colspan="2" />
	  </tr>
	
	  <tr>
	    <th>{{mb_label object=$rpu field="prise_en_charge"}}</th>
	    <td>{{mb_field object=$rpu field="prise_en_charge" defaultOption="&mdash; Prise en charge" onchange="submitRPU();"}}</td>
	 
	    <td colspan="2" />  
	  </tr>
	  <tr>
	    <td class="button" colspan="4">
	      <button class="new" type="button" onclick="newConsultation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">
          Reconvoquer
        </button>
        <button class="new" type="button" onclick="newHospitalisation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">
          Hospitaliser
        </button>
	    </td>
	  </tr>
  </table>
</form>