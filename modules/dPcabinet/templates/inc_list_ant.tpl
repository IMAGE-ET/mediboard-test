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
    $(list).select('.cancelled').invoke('toggle');
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

</script>

{{if $dossier_medical->_count_cancelled_antecedents}}
<button class="search" style="float: right" onclick="Antecedent.toggleCancelled('antecedents-{{$dossier_medical->_guid}}')">
  Afficher les {{$dossier_medical->_count_cancelled_antecedents}} annulés
</button>
{{/if}}

<strong>Antécédents du patient</strong>

<ul id="antecedents-{{$dossier_medical->_guid}}">
	{{if $dossier_medical->_count_antecedents || $dossier_medical->_count_cancelled_antecedents}}
	  {{foreach from=$dossier_medical->_all_antecedents item=_antecedent}}
	  <li {{if $_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
	    <form name="delAntFrm-{{$_antecedent->_id}}" action="?m=dPcabinet" method="post">
	      <input type="hidden" name="m" value="dPpatients" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="dosql" value="do_antecedent_aed" />
	      <input type="hidden" name="antecedent_id" value="{{$_antecedent->_id}}" />
	      <input type="hidden" name="annule" value="" />
	             
	      <!-- Seulement si l'utilisateur est le créateur -->
	      {{if $_antecedent->_ref_first_log && $_antecedent->_ref_first_log->user_id == $app->user_id}}
	      <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="Antecedent.remove(this.form, DossierMedical.reloadDossierPatient)">
	        {{tr}}Delete{{/tr}}
	      </button>
	      {{/if}}
	      
	      {{if $_is_anesth && $sejour->_id}}
	      <button title="{{tr}}Add{{/tr}}" class="add notext" type="button" onclick="copyAntecedent({{$_antecedent->_id}})">
	        {{tr}}Add{{/tr}}
	      </button>
	      {{/if}}         
		  </form>
	
	    <span onmouseover="ObjectTooltip.createEx(this, '{{$_antecedent->_guid}}')">
		    <strong>
		    	{{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
		    	{{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
		    </strong>
	      {{if $_antecedent->date}}
	        [{{mb_value object=$_antecedent field=date}}] : 
	      {{/if}}
	      {{$_antecedent->rques}}
	    </span>
	  </li>
	  {{/foreach}}
	{{else}}
		<li><em>{{tr}}CAntecedent.unknown{{/tr}}</em></li>
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
	        {{tr}}Delete{{/tr}}
	      </button>
	      
	      {{if $_line->fin}}
		      Du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->fin|date_format:"%d/%m/%Y"}} :
		    {{elseif $_line->debut}}
		      Depuis le {{$_line->debut|date_format:"%d/%m/%Y"}} :
		    {{/if}}
	      <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
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
  {{foreach from=$dossier_medical->_ref_traitements item=_traitement}}
  <li>
    <form name="delTrmtFrm-{{$_traitement->_id}}" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_traitement_aed" />
    {{mb_key object=$_traitement}}
		
    <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierPatient)">
      {{tr}}delete{{/tr}}
    </button>
    {{if $_is_anesth && $sejour->_id}}
    <button class="add notext" type="button" onclick="copyTraitement({{$_traitement->_id}})">
      {{tr}}Add{{/tr}}
    </button>
    {{/if}}
    
    {{if $_traitement->fin}}
      Depuis {{mb_value object=$_traitement field=debut}} 
      jusqu'à {{mb_value object=$_traitement field=fin}} :
    {{elseif $_traitement->debut}}
      Depuis {{mb_value object=$_traitement field=debut}} :
    {{/if}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$_traitement->_guid}}', 'objectViewHistory')">
      {{$_traitement->traitement}}
    </span>

    </form>
  </li>
  {{foreachelse}}
  {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
  <li><em>{{tr}}CTraitement.unknown{{/tr}}</em></li>
  {{/if}}
  {{/foreach}}
</ul>
{{/if}}

<strong>Diagnostics du patient</strong>
<ul>
  {{foreach from=$dossier_medical->_ext_codes_cim item=_code}}
  <li>
    <button class="trash notext" type="button" onclick="oCimField.remove('{{$_code->code}}')">
      {{tr}}Delete{{/tr}}
    </button>
    {{if $_is_anesth && $sejour->_id}}
    <button class="add notext" type="button" onclick="oCimAnesthField.add('{{$_code->code}}')">
      {{tr}}Add{{/tr}}
    </button>
    {{/if}}
    {{$_code->code}}: {{$_code->libelle}}
  </li>
  {{foreachelse}}
  <li><em>{{tr}}CDossierMedical-codes_cim.unknown{{/tr}}</em></li>
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
// FIXME : Modifier le tokenfield, car deux appels à onchange
oCimField = new TokenField(document.editDiagFrm.codes_cim, { 
  confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
  onChange : updateTokenCim10
} ); 
</script>      