{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  window.menuTabs = Control.Tabs.create('prescription_tab_group', false);
} );

toggleFieldComment = function(button, tr_comment, libelle){
  tr_comment.toggle();
  if(tr_comment.visible()){
    button.update("Masquer "+libelle);
    button.className = "cancel";
  } else {
    button.update("Ajouter "+libelle);
    button.className = "new";
  }
}

// Initialisation des alertes
if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}

updateFavoris = function (praticien_id, chapitre, select){
  // Suppression du onclick du select
	select.onclick="";
  var url = new Url("dPprescription", "httpreq_vw_favoris_prescription");
  url.addParam("praticien_id", praticien_id);
  url.addParam("chapitre", chapitre);
  url.requestUpdate(select, { waitingText: null });
}

// Si la perfusion n'est pas de type PCA, on vide toutes les catarteristiques specifiques
resetBolus = function(oForm){
  $V(oForm.mode_bolus, 'sans_bolus');
  $V(oForm.dose_bolus, '');
  $V(oForm.periode_interdite, '');
}

// Modification de la perfusion en fonction du mode bolus
changeModeBolus = function(oForm){
  // Reactivation de la vitesse
  oForm.vitesse.writeAttribute("disabled",null);
  oForm.vitesse.up('table').select('td.arrows').invoke('show');
  oForm.vitesse.setOpacity(1);
  
  oForm.nb_tous_les.writeAttribute("disabled",null);
  oForm.nb_tous_les.up('table').select('td.arrows').invoke('show');
  oForm.nb_tous_les.setOpacity(1);
  
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
    oForm.vitesse.up('table').select('td.arrows').invoke('hide');
    oForm.vitesse.setOpacity(0.3);

    $V(oForm.nb_tous_les, '');
    oForm.nb_tous_les.writeAttribute("disabled","disabled");
    oForm.nb_tous_les.up('table').select('td.arrows').invoke('hide');
    oForm.nb_tous_les.setOpacity(0.3);
        
    return;
  }
}

// Lancement du mode de saisie popup
viewEasyMode = function(mode_protocole, mode_pharma, chapitre){
  var url = new Url();
  url.setModuleAction("dPprescription","vw_easy_mode");
  url.addParam("prescription_id", '{{$prescription->_id}}');
  url.addParam("mode_protocole", mode_protocole);
  url.addParam("mode_pharma", mode_pharma);
  url.addParam("chapitre", chapitre);
  url.popup(900,500,"Mode grille");
}

refreshElementPrescription = function(chapitre, mode_protocole, mode_pharma, readonly, lite) {
  if (!window[chapitre+'Loaded']) {
    WaitingMessage.cover("div_"+chapitre);
    Prescription.reload('{{$prescription->_id}}', null, chapitre, mode_protocole, mode_pharma, null, readonly, lite);
    window[chapitre+'Loaded'] = true;
  }
}

setPrimaryKeyDosql = function (form, object_class, object_id) {
  var field, dosql;
  switch (object_class) {
    case "CPrescriptionLineMedicament": 
      field = "prescription_line_medicament_id";
      dosql = "do_prescription_line_medicament_aed";
      break;
    case "CPrescriptionLineElement": 
      field = "prescription_line_element_id";
      dosql = "do_prescription_line_element_aed";
      break;
    case "CPrescriptionLineComment": 
      field = "prescription_line_comment_id";
      dosql = "do_prescription_line_comment_aed";
      break;
    case "CPerfusion": 
      field = "perfusion_id";
      dosql = "do_perfusion_aed";
      break;  
  }
  form[field].value = object_id;
  form.dosql.value = dosql;
}

submitALD = function(object_class, object_id, ald){
  var oForm = getForm("editLineALD-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  oForm.ald.value = ald ? "1" : "0";
  onSubmitFormAjax(oForm);
}

submitConditionnel = function(object_class, object_id, conditionnel){
  var oForm = getForm("editLineConditionnel-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  oForm.conditionnel.value = conditionnel ? "1" : "0";
  return onSubmitFormAjax(oForm);
}

submitValidationInfirmiere = function(object_class, object_id, prescription_id, div_refresh, mode_pharma) {
  var oForm = getForm("validation_infirmiere-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  return onSubmitFormAjax(oForm, { onComplete: 
    function() { 
      Prescription.reload(prescription_id, '', div_refresh, '', mode_pharma); 
    }
  });
}

submitValidationPharmacien = function(prescription_id, object_id, valide_pharma, mode_pharma) {
  var oForm = getForm("validation_pharma");
  prepareForm(oForm);
  oForm.valide_pharma.value = valide_pharma;
  oForm.prescription_line_medicament_id.value = object_id;
  onSubmitFormAjax(oForm, { onComplete: function() {
    Prescription.reload(prescription_id, '', 'medicament', '', mode_pharma); }
  });
}

submitValideAllLines = function (prescription_id, chapitre, mode_pharma) {
  var oForm = getForm("valideAllLines");
  prepareForm(oForm);
  oForm.prescription_id.value = prescription_id;
  oForm.chapitre.value = chapitre;
  if (mode_pharma) {
    oForm.mode_pharma.value = mode_pharma;
  }
  return onSubmitFormAjax(oForm);
}

submitAddComment = function (object_class, object_id, commentaire) {
  var oForm = getForm("addComment-"+object_class);
  prepareForm(oForm);
  setPrimaryKeyDosql(oForm, object_class, object_id);
  oForm.commentaire.value = commentaire;
  return onSubmitFormAjax(oForm);
}


submitEmplacement = function(object_class, object_id, emplacement){
  var oForm = getForm("emplacement-"+object_class);
  prepareForm(oForm);
  setPrimaryKeyDosql(oForm, object_class, object_id);  
  oForm.emplacement.value = emplacement;
  return onSubmitFormAjax(oForm);
}

submitVoie = function(line_medicament_id, libelle_voie){
  var oForm = getForm("voie");
  prepareForm(oForm);
  oForm.prescription_line_medicament_id.value = line_medicament_id;
  oForm.voie.value = libelle_voie;
  return onSubmitFormAjax(oForm);
}


submitSignaturePraticien = function(perfusion_id, prescription_id, signature_praticien){
  var oForm = getForm("perf_signature_prat");
  prepareForm(oForm);
  oForm.perfusion_id.value = perfusion_id;
  oForm.signature_prat.value = signature_praticien;
  return onSubmitFormAjax(oForm, { onComplete: function(){
  	Prescription.reload(prescription_id, '', 'medicament');	
  } } );
}

submitSignaturePharmacien = function(perfusion_id, prescription_id, signature_pharmacien){
  var oForm = getForm("perf_signature_pharma");
  prepareForm(oForm);
  oForm.perfusion_id.value = perfusion_id;
  oForm.signature_pharma.value = signature_pharmacien;
  return onSubmitFormAjax(oForm, { onComplete: function(){
    Prescription.reload(prescription_id, '', 'medicament','', '1');
  } } );
}

submitValidationInfir = function(perfusion_id, prescription_id, validation_infir){
  var oForm = getForm("perf_validation_infir");
  prepareForm(oForm);
  oForm.perfusion_id.value = perfusion_id;
  oForm.validation_infir.value = validation_infir;
  return onSubmitFormAjax(oForm, { onComplete: function(){
  	Prescription.reloadPrescSejour(prescription_id);	
  } } );
}


// Permet de changer la couleur de la ligne lorsqu'on stoppe la ligne
changeColor = function(object_id, object_class, oForm, traitement, cat_id){   
  if(oForm.date_arret){
    var date_arret = oForm.date_arret.value;
    var date_fin = date_arret;
  }
  
  if(oForm._heure_arret && oForm._min_arret){
    var heure_arret = oForm._heure_arret.value;
    var min_arret = oForm._min_arret.value;
    var date_fin = date_fin+" "+heure_arret+":"+min_arret+":00";
  }
    
  // Entete de la ligne
  var oDiv = $('th_line_'+object_class+'_'+object_id);
  if(object_class == 'CPrescriptionLineMedicament'){
    var oTbody = $('line_medicament_'+object_id);
  } else {
    var oTbody = $('line_element_'+object_id);
  }
  var classes_before = oTbody.className;
  if(date_fin != "" && date_fin <= '{{$now}}'){
    oDiv.addClassName("arretee");
    oTbody.addClassName("line_stopped");
  } else {
    oDiv.removeClassName("arretee");
    oTbody.removeClassName("line_stopped");
  }
  var classes_after = oTbody.className;
  
  // Deplacement de la ligne
  if(classes_before != classes_after){
    if(object_class == 'CPrescriptionLineMedicament'){
      moveTbody(oTbody);
    } else {
      moveTbodyElt(oTbody, cat_id);
    }
  }
}

changeColorPerf = function(perf_id, oForm){
  if(oForm.date_arret && oForm.date_arret.value != ''){
    var _fin = oForm.date_arret.value; 
  } else {
    var _fin = oForm.date_debut.value;
  }
  var oTbody = $('perfusion-'+perf_id);
  var oTh = $('th-perf-'+perf_id);
  if(_fin <= '{{$now}}'){
    oTh.addClassName("arretee");
    oTbody.addClassName("line_stopped");
  } else {
    oTh.removeClassName("arretee");
    oTbody.removeClassName("line_stopped");
  }
}

modifFormDate = function(nb_prises, form_name, protocole,line_id){
  var oForm = document.forms[form_name];
 
  if(protocole == 0){
    oDiv = $('info_date_'+line_id);
    if(nb_prises > 0){
	    oForm.hide();
	    oDiv.show();
    } else {
      oForm.show();
      oDiv.hide();
    }
  }

  if(protocole == 1){
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
      
      // The time pickers
      $(oForm.time_debut).previousSiblings().first().select('img,div').invoke('show');
      $(oForm.time_fin).previousSiblings().first().select('img,div').invoke('show'); 
	  }
  }
}

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
		  		  
</script>

{{include file="../../dPprescription/templates/js_functions.tpl"}}

<form name="addPriseElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prise_posologie_id" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="CPrescriptionLineElement" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="nb_fois" value="" />
  <input type="hidden" name="unite_fois" value="" />
  <input type="hidden" name="moment_unitaire_id" value="" />
  <input type="hidden" name="nb_tous_les" value="" />
  <input type="hidden" name="unite_tous_les" value="" />
  <input type="hidden" name="chapitre" value="" />
</form>
	    
<!-- Formulaire d'ajout de ligne d'element dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLineElement" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />  
  <input type="hidden" name="debut" value="{{$today}}" />
  <input type="hidden" name="time_debut" value="" />
  <input type="hidden" name="duree" value="" />
  <input type="hidden" name="unite_duree" value="" />
  <input type="hidden" name="callback" value="" />
  <input type="hidden" name="element_prescription_id" value=""/>
  <input type="hidden" name="_chapitre" value="" />
</form>

<!-- Tabulations -->
<ul id="prescription_tab_group" class="control_tabs">
  <li><a href="#div_medicament">Médicaments</a></li>

{{if $app->user_prefs.mode_readonly}}
  {{assign var=_lite value=false}}
{{else}}
  {{assign var=_lite value=true}}
{{/if}}
{{if !$mode_pharma}}
  {{assign var=specs_chapitre value=$class_category->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
  
  {{if !($mode_protocole && $_chapitre == "dmi")}}
  <li><a href="#div_{{$_chapitre}}" {{if !$mode_pack}}onmouseup="refreshElementPrescription('{{$_chapitre}}', null, null, true,'{{$_lite}}');"{{/if}}>{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
  {{/if}}
  
  {{/foreach}}
{{/if}}
</ul>

<hr class="control_tabs" />

<!-- Declaration des divs -->
<div id="div_medicament" style="display:none;">
  {{if $mode_pack}}
    {{include file="../../dPprescription/templates/inc_div_medicament_short.tpl"}}
  {{else}}
    {{include file="../../dPprescription/templates/inc_div_medicament.tpl"}}
  {{/if}}
</div>

{{if !$mode_pharma}}
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
    {{if !($mode_protocole && $_chapitre == "dmi")}}
	    <script type="text/javascript">
	    window['{{$_chapitre}}Loaded'] = false;
	    Main.add( function(){
	      Prescription.refreshTabHeader('div_{{$_chapitre}}','{{$prescription->_counts_by_chapitre.$_chapitre}}','{{if $prescription->object_id}}{{$prescription->_counts_by_chapitre_non_signee.$_chapitre}}{{else}}0{{/if}}');
	    });
	    </script>
	    <div id="div_{{$_chapitre}}" style="display:none;">
	    {{if $mode_pack}}
	      {{include file="../../dPprescription/templates/inc_div_element_short.tpl" element=$_chapitre}}
	    {{/if}}
	    </div>
    {{/if}}
  {{/foreach}}
{{/if}}

<!-- Formulaires regroupés -->
<form name="editLineALD-CPrescriptionLineMedicament" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ald" value="" />
</form>

<form name="editLineALD-CPrescriptionLineElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ald" value="" />
</form>

<form name="editLineALD-CPrescriptionLineComment" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_comment_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ald" value="" />
</form>

<form name="editLineConditionnel-CPrescriptionLineMedicament" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="conditionnel" value="" />
</form>

<form name="editLineConditionnel-CPrescriptionLineElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="conditionnel" value="" />
</form>

<form name="validation_infirmiere-CPrescriptionLineMedicament" action="?" method="post">
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="valide_infirmiere" value="1" />
</form>

<form name="validation_infirmiere-CPrescriptionLineElement" action="?" method="post">
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="valide_infirmiere" value="1" />
</form>

<form name="validation_pharma" action="" method="post">
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="valide_pharma" value="" />
</form>

<form name="valideAllLines" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
  <input type="hidden" name="prescription_id" value="" />
  <input type="hidden" name="chapitre" value="" />
  <input type="hidden" name="mode_pharma" value="" />
</form>

<form name="addComment-CPrescriptionLineMedicament" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="commentaire" value="" />
</form>

<form name="addComment-CPerfusion" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="perfusion_id" value="" />
  <input type="hidden" name="commentaire" value="" />
</form>


<form name="addComment-CPrescriptionLineElement" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="commentaire" value="" />
</form>

<form name="emplacement-CPrescriptionLineMedicament" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="emplacement" value="" />
</form>

<form name="emplacement-CPrescriptionLineElement" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="emplacement" value="" />
</form>

<form name="voie" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="voie" value="" />
</form>

<form name="perf_signature_prat" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_perfusion_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="perfusion_id" value="" />
  <input type="hidden" name="signature_prat" value="" />
</form>

<form name="perf_signature_pharma" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_perfusion_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="perfusion_id" value="" />
  <input type="hidden" name="signature_pharma" value="" />
</form>

<form name="perf_validation_infir" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_perfusion_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="perfusion_id" value="" />
  <input type="hidden" name="validation_infir" value="" />
</form>