{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}

<script type="text/javascript">

Antecedent = {
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'cet antécédent',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  },
  
  cancel: function(oForm, onComplete) {
    $V(oForm.annule, 1);
    onSubmitFormAjax(oForm, {onComplete: onComplete});
    $V(oForm.annule, '');
  },
  
  restore: function(oForm, onComplete) {
    $V(oForm.annule, '0');
    onSubmitFormAjax(oForm, {onComplete: onComplete});
    $V(oForm.annule, '');
  },

  toggleCancelled: function(list) {
    $(list).select('.cancelled').each(Element.show);
  }
};

Traitement = {
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'ce traitement',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  }
};

// Transfert de la ligne du dossier medical vers la prescription de sejour
transfertLineTP = function(line_id, sejour_id){
  var oForm = document.transfert_line_TP;
  $V(oForm.prescription_line_medicament_id, line_id);
  $V(oForm.sejour_id, sejour_id);
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    DossierMedical.reloadDossierSejour();
  } } );
}

</script>

<form name="transfert_line_TP" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_transfert_line_tp_aed" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="sejour_id" value="" />
</form>

{{if $dossier_medical->_count_cancelled_antecedents}}
<button class="search" style="float: right" onclick="Antecedent.toggleCancelled('antecedents-{{$dossier_medical->_guid}}')">
  Afficher les {{$dossier_medical->_count_cancelled_antecedents}} annulés
</button>
{{/if}}

<strong>Antécédents du patient</strong>

<ul id="antecedents-{{$dossier_medical->_guid}}">
	{{if $dossier_medical->_count_antecedents}}
	  {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
	  {{foreach from=$list_antecedent item=curr_antecedent}}
	  <li {{if $curr_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
	    <form name="delAntFrm-{{$curr_antecedent->_id}}" action="?m=dPcabinet" method="post">
	      <input type="hidden" name="m" value="dPpatients" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="dosql" value="do_antecedent_aed" />
	      <input type="hidden" name="antecedent_id" value="{{$curr_antecedent->_id}}" />
	      <input type="hidden" name="annule" value="" />
	             
	      <!-- Seulement si l'utilisateur est le créateur -->
	      {{if $curr_antecedent->_ref_first_log && $curr_antecedent->_ref_first_log->user_id == $app->user_id}}
	      <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="Antecedent.remove(this.form, DossierMedical.reloadDossierPatient)">
	        {{tr}}Delete{{/tr}}
	      </button>
	      {{/if}}
	      
	      {{if $_is_anesth && $sejour->_id}}
	      <button title="{{tr}}Add{{/tr}}" class="add notext" type="button" onclick="copyAntecedent({{$curr_antecedent->_id}})">
	        {{tr}}Add{{/tr}}
	      </button>
	      {{/if}}         
		  </form>
	
	    <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_antecedent->_guid}}')">
		    <strong>
		    	{{if $curr_antecedent->type    }} {{mb_value object=$curr_antecedent field=type    }} {{/if}}
		    	{{if $curr_antecedent->appareil}} {{mb_value object=$curr_antecedent field=appareil}} {{/if}}
		    </strong>
	      {{if $curr_antecedent->date}}
	        [{{mb_value object=$curr_antecedent field=date}}] : 
	      {{/if}}
	      {{$curr_antecedent->rques}}
	    </span>
	  </li>
	  {{/foreach}}
	  {{/foreach}}
	{{else}}
		<li><em>{{tr}}CAntecedent.none{{/tr}}</em></li>
	{{/if}}
</ul>

{{if $dossier_medical->_ref_prescription}}
	<strong>Traitements du patient</strong>
	<ul>
	{{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}
	  <li>
	    <form name="delTraitementDossierMedPat-{{$_line->_id}}">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="del" value="1" />
	      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	      <input type="hidden" name="prescription_line_medicament_id" value="{{$_line->_id}}" />
	      <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierPatient)">
	        {{tr}}delete{{/tr}}
	      </button>
	      {{if $_is_anesth && $sejour->_id && ($user->_is_praticien || $can->admin)}}
	      <button title="{{tr}}Add{{/tr}}" class="add notext" type="button" onclick="transfertLineTP('{{$_line->_id}}','{{$sejour->_id}}');">
	        {{tr}}Add{{/tr}}
	      </button>
	      {{/if}}       
	      
	      {{if $_line->fin}}
		      Du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->fin|date_format:"%d/%m/%Y"}} :
		    {{elseif $_line->debut}}
		      Depuis le {{$_line->debut|date_format:"%d/%m/%Y"}} :
		    {{/if}}
	      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
			    <a href=#1 onclick="Prescription.viewProduit(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');">
			      {{$_line->_ucd_view}} ({{$_line->_forme_galenique}})
			    </a>
			  </span>
		  </form>
		</li>
	{{/foreach}}
	</ul>
	{{if $dossier_medical->_ref_traitements|@count && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count}}
  <hr style="width: 50%;" />
  {{/if}}
{{/if}}


{{if is_array($dossier_medical->_ref_traitements)}}
<!-- Traitements -->
{{if !$dossier_medical->_ref_prescription}}
<strong>Traitements du patient</strong>
{{/if}}
<ul>
  {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
  <li>
    <form name="delTrmtFrm-{{$curr_trmt->_id}}" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_traitement_aed" />
    <input type="hidden" name="traitement_id" value="{{$curr_trmt->traitement_id}}" />
    <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierPatient)">
      {{tr}}delete{{/tr}}
    </button>
    {{if $_is_anesth && $sejour->_id}}
    <button class="add notext" type="button" onclick="copyTraitement({{$curr_trmt->traitement_id}})">
      {{tr}}add{{/tr}}
    </button>
    {{/if}}
    
    {{if $curr_trmt->fin}}
      Depuis {{mb_value object=$curr_trmt field=debut}} 
      jusqu'à {{mb_value object=$curr_trmt field=fin}} :
    {{elseif $curr_trmt->debut}}
      Depuis {{mb_value object=$curr_trmt field=debut}} :
    {{/if}}
    <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_trmt->_guid}}', 'objectViewHistory')">
     {{$curr_trmt->traitement}}
    </span>

    </form>
  </li>
  {{foreachelse}}
  {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
  <li><em>Pas de traitements</em></li>
  {{/if}}
  {{/foreach}}
</ul>
{{/if}}

<strong>Diagnostics du patient</strong>
<ul>
  {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
  <li>
    <button class="trash notext" type="button" onclick="oCimField.remove('{{$curr_code->code}}')">
      {{tr}}delete{{/tr}}
    </button>
    {{if $_is_anesth && $sejour->_id}}
    <button class="add notext" type="button" onclick="oCimAnesthField.add('{{$curr_code->code}}')">
      {{tr}}add{{/tr}}
    </button>
    {{/if}}
    {{$curr_code->code}}: {{$curr_code->libelle}}
  </li>
  {{foreachelse}}
  <li><em>Pas de diagnostic</em></li>
  {{/foreach}}
</ul>

<!-- Gestion des diagnostics pour le dossier medical du patient -->
<form name="editDiagFrm" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="tab" value="edit_consultation" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="object_id" value="{{$patient->_id}}" />
  <input type="hidden" name="object_class" value="CPatient" />
  <input type="hidden" name="codes_cim" value="{{$dossier_medical->codes_cim}}" />
</form>

<script type="text/javascript">
oCimField = new TokenField(document.editDiagFrm.codes_cim, { 
  confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
  onChange : updateTokenCim10
} ); 
</script>      