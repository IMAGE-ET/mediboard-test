{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

toggleTypePerfusion = function(oForm){
  if(!oForm.type){
    return;
  }
	if(oForm.perfusion_id.value == ""){
	  oForm.type.show();
	} else {
	  oForm.type.hide();
	}
}

// Si la perfusion n'est pas de type PCA, on vide toutes les catarteristiques specifiques
resetBolus = function(oForm){
  $V(oForm.mode_bolus, 'sans_bolus');
  $V(oForm.dose_bolus, '');
  $V(oForm.periode_interdite, '');
}

// Modification de la perfusion en fonction du mode bolus
changeModeBolus = function(oForm){
  $("img_"+oForm.name+"_vitesse").show();

  // Reactivation de la vitesse
  oForm.vitesse.writeAttribute("disabled",null);
  oForm.vitesse.setOpacity(1);
  
  oForm.dose_bolus.writeAttribute("disabled",null);
  oForm.dose_bolus.setOpacity(1);
  
  oForm.periode_interdite.writeAttribute("disabled",null);
  oForm.periode_interdite.setOpacity(1);
  
  if(oForm.mode_bolus.value == 'sans_bolus'){
    // Désactivation des 2 champs de gestion du bolus
    $V(oForm.dose_bolus, '');
    $V(oForm.periode_interdite, '');
    
    oForm.dose_bolus.writeAttribute("disabled","disabled");
    oForm.dose_bolus.setOpacity(0.3);
    
    oForm.periode_interdite.writeAttribute("disabled","disabled");
    oForm.periode_interdite.setOpacity(0.3);
    
    return;
  }
  if(oForm.mode_bolus.value == 'bolus'){
    // Désactivation de la vitesse
    $V(oForm.vitesse, '');
    oForm.vitesse.writeAttribute("disabled","disabled");
    oForm.vitesse.setOpacity(0.3);
    
    $("img_"+oForm.name+"_vitesse").hide();
    return;
  }
}


	submitEditCommentaireSubst = function (object_id, commentaire) {
	  var oForm = getForm("editCommentaire");
	  prepareForm(oForm);
	  oForm.dosql.value = "do_prescription_line_medicament_aed";
	  oForm.prescription_line_medicament_id.value = object_id;
	  oForm.commentaire.value = commentaire;
	  submitFormAjax(oForm, 'systemMsg');
	}
	
	submitEditEmplacementSubst = function (object_id, emplacement) {
	  var oForm = getForm("editEmplacement");
	  prepareForm(oForm);
	  oForm.dosql.value = "do_prescription_line_medicament_aed";
	  oForm.prescription_line_medicament_id.value = object_id;
	  oForm.emplacement.value = emplacement;
	  submitFormAjax(oForm, 'systemMsg');
	}
	
	submitVoie = function(object_id, voie){
	  var oForm = getForm("editVoie");
	  prepareForm(oForm);
	  oForm.dosql.value = "do_prescription_line_medicament_aed";
	  oForm.prescription_line_medicament_id.value = object_id;
	  oForm.voie.value = voie;
	  submitFormAjax(oForm, 'systemMsg');
	}
	
	submitALD = function(object_class, object_id, ald){
	  var oForm = getForm("editALD");
	  prepareForm(oForm);
	  oForm.dosql.value = "do_prescription_line_medicament_aed";
	  oForm.prescription_line_medicament_id.value = object_id;
	  oForm.ald.value = ald ? "1" : "0";
	  submitFormAjax(oForm, 'systemMsg');
  }

	

modifFormDate = function(nb_prises, form_name, protocole,line_id){
  var oForm = document.forms[form_name];
  
  if(nb_prises > 0){
    $V(oForm.duree,"1",false); 
    $V(oForm.jour_decalage,"I",false); 
    $V(oForm.decalage_line,"+0",false);
    $V(oForm.unite_decalage,"jour",false)
    $V(oForm.time_debut,"",false);
    $V(oForm.time_debut_da,"",false);
    $V(oForm.jour_decalage_fin,"",false);
    $V(oForm.decalage_line_fin,"",false);
    $V(oForm.unite_decalage_fin,"",false);
    $V(oForm.time_fin,"",false);
    $V(oForm.time_fin_da,"",false);
    
    // the numericFields
    $('img_'+oForm.decalage_line_fin.id).hide();
    $('img_'+oForm.decalage_line.id).hide();
    $('img_'+oForm.duree.id).hide();
    
    // The time pickers
    $(oForm.time_debut).previousSiblings().first().select('img,div').invoke('hide');
    $(oForm.time_fin).previousSiblings().first().select('img,div').invoke('hide');
    
    onSubmitFormAjax(oForm);
    
    oForm.duree.disabled = "disabled";
    oForm.jour_decalage.disabled = "disabled";
    oForm.decalage_line.disabled = "disabled";
    oForm.unite_decalage.disabled = "disabled";
    oForm.time_debut.disabled = "disabled";
    oForm.jour_decalage_fin.disabled = "disabled";
    oForm.decalage_line_fin.disabled = "disabled";
    oForm.unite_decalage_fin.disabled = "disabled";
    oForm.time_fin.disabled = "disabled";
    
  } else {
    oForm.duree.disabled = false;
    oForm.jour_decalage.disabled = false;
    oForm.decalage_line.disabled = false;
    oForm.unite_decalage.disabled = false;
    oForm.time_debut.disabled = false;
    oForm.jour_decalage_fin.disabled = false;
    oForm.decalage_line_fin.disabled = false;
    oForm.unite_decalage_fin.disabled = false;
    oForm.time_fin.disabled = false;
      
    // the numericFields
    $('img_'+oForm.decalage_line_fin.id).show();
    $('img_'+oForm.decalage_line.id).show();
    $('img_'+oForm.duree.id).show();
    
    // The time pickers
    $(oForm.time_debut).previousSiblings().first().select('img,div').invoke('show');
    $(oForm.time_fin).previousSiblings().first().select('img,div').invoke('show'); 
  }
}

	
</script>

{{include file="../../dPprescription/templates/js_functions.tpl"}}

{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<form name="editCommentaire" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="commentaire" value="" />
</form>

<form name="editEmplacement" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="emplacement" value="" />
</form>

<form name="editVoie" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="voie" value="" />
</form>

<form name="editALD" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="ald" value="" />
</form>

<!-- Select de moments unitaire -->
<form name="moment_unitaire">
  <select name="moment_unitaire_id" style="width: 150px; display: none;">  
     <option value="">&mdash; Sélection du moment</option>
    {{foreach from=$moments key=type_moment item=_moments}}
    <optgroup label="{{$type_moment}}">
    {{foreach from=$_moments item=moment}}
    {{if $type_moment == "Complexes"}}
      <option value="complexe-{{$moment->code_moment_id}}">{{$moment->_view}}</option>
    {{else}}
      <option value="unitaire-{{$moment->_id}}">{{$moment->_view}}</option>
    {{/if}}
    {{/foreach}}
    </optgroup>
    {{/foreach}}
  </select>
</form>
	  
<form action="?" method="post" name="addLine" onsubmit="return checkForm(this);">  
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="prescription_line_medicament_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
  <input type="hidden" name="code_cip" value=""/>
  <input type="hidden" name="substitute_for_id" value="{{$line->_id}}" />
  <input type="hidden" name="substitute_for_class" value="{{$line->_class_name}}" />
  <input type="hidden" name="substitution_active" value="0" />
</form>

{{if $line->_class_name == "CPrescriptionLineMedicament"}}
  {{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{else}}
  {{assign var=dosql value="do_perfusion_aed"}}
{{/if}}

{{if !$mode_pack}}
<table class="tbl">
  <tr>
    <th class="title">Ajout d'une ligne de substitution</th>
  </tr>
  <tr>
    <td>
      <form action="?" method="get" name="searchProd" onsubmit="return false;">
			    <input type="text" name="produit" value="" size="12" />
			    <input type="checkbox" name="_recherche_livret" {{if $prescription->type=="sejour"}}checked="checked"{{/if}} />
			    Livret Thérap.
			    <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
			    <button type="button" class="search" onclick="MedSelector.init('produit');">Rechercher</button>
			    <input type="hidden" name="code_cip" onchange="addSubstitutionLine(this.value);"/>
			    <script type="text/javascript">
			      MedSelector.init = function(onglet){
			        this.sForm = "searchProd";
			        this.sView = "produit";
			        this.sCode = "code_cip";
			        this.sRechercheLivret = document.searchProd._recherche_livret.value;
			        this.sSearch = document.searchProd.produit.value;
			        this.sOnglet = onglet;
			        this.selfClose = false;
			        this.pop();
			      }
			  </script>
			  </form>
    </td>
  </tr>
  <tr>
    <td>
      Substitution possible dans le plan de soin
      <form name="editLine" action="?" method="post">
			  <input type="hidden" name="m" value="dPprescription" />
			  <input type="hidden" name="dosql" value="{{$dosql}}" />
				<input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
				{{mb_field object=$line field="substitution_plan_soin" onchange="submitFormAjax(this.form, 'systemMsg')"}}
			</form>
    </td>
  </tr>
  <tr>
    <th class="title">Affichage des lignes de substitutions</th>
  </tr>
</table>
{{/if}}
<table class="tbl">
  {{foreach from=$line->_ref_substitution_lines item=lines_chap}}
	  {{foreach from=$lines_chap item=curr_line}}
	    {{if $mode_pack}}
	    	{{if $curr_line->_class_name == "CPrescriptionLineMedicament"}}
	        {{include file="../../dPprescription/templates/../../dPprescription/templates/inc_vw_line_pack.tpl" line=$curr_line}}
	      {{else}}
	        {{include file="../../dPprescription/templates/../../dPprescription/templates/inc_vw_line_perf_pack.tpl" _perfusion=$curr_line}}
	      {{/if}}
	    {{else}}
	      {{if $curr_line->_class_name == "CPrescriptionLineMedicament"}}
	        {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" mode_pharma=0 mode_substitution=1}}
	      {{else}}
	        {{include file="../../dPprescription/templates/inc_vw_perfusion.tpl" mode_pharma=0 _perfusion=$curr_line mode_substitution=1}}
	      {{/if}}
	    {{/if}}
	  {{/foreach}}
  {{/foreach}}
</table>

<script type="text/javascript">

if(document.addLine && document.searchProd){
  // UpdateFields de l'autocomplete de medicaments
  updateFieldsMedicament = function(selected) {
    Element.cleanWhitespace(selected);
    dn = selected.childNodes;
    oFormAddLine.code_cip.value = dn[0].firstChild.nodeValue;
    submitFormAjax(document.addLine, 'systemMsg', { onComplete: function() { Prescription.viewSubstitutionLines('{{$line->_id}}', '{{$line->_class_name}}') } });
    $('searchProd_produit').value = "";
  }
  
  var oFormProduit = document.searchProd;
  var oFormAddLine = document.addLine;
  
  prepareForm(oFormAddLine);
  prepareForm(oFormProduit);
  
  // Autocomplete des medicaments
  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.addParam("produit_max", 40);
  
  urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsMedicament,
    callback: 
      function(input, queryString){
        return (queryString + "&inLivret="+($V(oFormProduit._recherche_livret)?'1':'0')); 
      }
  } );
}

// Ajout d'une ligne de substitution
addSubstitutionLine = function(code_cip){
  var oForm = document.addLine;
  oForm.code_cip.value = code_cip;
	submitFormAjax(document.addLine, 'systemMsg', { onComplete: function() { Prescription.viewSubstitutionLines('{{$line->_id}}','{{$line->_class_name}}') } });
}

// Suppression d'une ligne de substitution
Prescription.delLine =  function(line_id) {
	var oForm = document.addLine;
	oForm.prescription_line_medicament_id.value = line_id;
	oForm.del.value = 1;
	submitFormAjax(document.addLine, 'systemMsg', { onComplete: function() { Prescription.viewSubstitutionLines('{{$line->_id}}','{{$line->_class_name}}') } });
}

</script>