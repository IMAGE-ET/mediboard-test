{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

completeSelect = function(oSelect, line_id, type_elt){
  var selectMoments = (type_elt == "mode_grille") ? 
                        window.opener.document.moment_unitaire.moment_unitaire_id :
                        document.moment_unitaire.moment_unitaire_id;
  
  oFormPrise = document.forms['addPrise'+type_elt+line_id].show();          
  if(oSelect.length == 1){
    /*oSelect.down().remove();
    $A(selectMoments.childNodes).each(function (optgroup) {
      oSelect.appendChild(optgroup.cloneNode(true));
    } );*/
    oSelect.innerHTML = selectMoments.innerHTML;
  }
}

refreshCheckbox = function(oForm){
  $V(oForm.matin, false);
  $V(oForm.midi, false);
  $V(oForm.soir, false);
}

changeUrgence = function(oForm, checked){
  if(checked == true){
   refreshCheckbox(oForm);
    oForm.matin.disable();
    oForm.midi.disable();
    oForm.soir.disable();
  } else {
   oForm.matin.enable();
   oForm.midi.enable();
   oForm.soir.enable();
  }
}

// Affichage des div d'ajout de posologies
selDivPoso = function(type, line_id, type_elt){
  if(!type){
    type = "foisPar"+type_elt;
  }
  
  oDivMoment = $('moment'+type_elt+line_id);
  oDivFoisPar = $('foisPar'+type_elt+line_id);
  oDivTousLes = $('tousLes'+type_elt+line_id);
  oDivDecalageIntervention = $('decalage_intervention'+type_elt+line_id);
  
  oFormPrise = document.forms['addPrise'+type_elt+line_id].show();
  
  var selectMoments = (type_elt == "mode_grille") ? 
                        window.opener.document.moment_unitaire.moment_unitaire_id :
                        document.moment_unitaire.moment_unitaire_id;
  
                        
  oFormPrise.getElements().each (function (e) {
    if ((e.type != "hidden") && (e.name != "unite_prise") && (e.name != "quantite")) {
      e.hide().writeAttribute("disabled", "disabled").value = "";
    }
  });
  switch (type) {
    case "tousLes"+type_elt: 
      oFormPrise.nb_tous_les.enable().show();
      oFormPrise.unite_tous_les.enable().show();
      oFormPrise.decalage_prise.enable().show();
      oDivMoment.hide();
      oDivFoisPar.hide();
      oDivTousLes.show();
      if(oDivDecalageIntervention){
        oDivDecalageIntervention.hide();
      }
      oFormPrise.moment_unitaire_id.setStyle( { float: null } );
      $("tous_les_"+type_elt+"_"+line_id).insert(oFormPrise.moment_unitaire_id);
      oFormPrise.moment_unitaire_id.onchange = null;
      oFormPrise.moment_unitaire_id.enable().show();
      break;
      
    case "moment"+type_elt: 
      oDivMoment.show();
      if(type_elt != "mode_grille"){
        oFormPrise.matin.enable().show();
        oFormPrise.midi.enable().show();
        oFormPrise.soir.enable().show();
        if(oFormPrise._urgent){
	        oFormPrise._urgent.enable().show();
	        changeUrgence(oFormPrise, oFormPrise._urgent.checked);
        }
        $("moment_"+type_elt+"_"+line_id).insert(oFormPrise.moment_unitaire_id);

      } else {
        $("tous_les_"+type_elt+"_"+line_id).insert(oFormPrise.moment_unitaire_id);
        oFormPrise.moment_unitaire_id.setStyle( { float: null } );
      }
      oFormPrise.moment_unitaire_id.enable().show();
      oFormPrise.moment_unitaire_id.onchange = oFormPrise.onsubmit.bind(oFormPrise);
    break;
    case "foisPar"+type_elt: 
      oFormPrise.nb_fois.enable().show();
      oFormPrise.unite_fois.enable().show();
      oDivFoisPar.show();
      oDivTousLes.hide();
      oDivMoment.hide();
      if(oDivDecalageIntervention){
        oDivDecalageIntervention.hide();
      }
    break;
    case "decalage_intervention"+type_elt: 
      oFormPrise.decalage_intervention.enable().show();
      oDivFoisPar.hide();
      oDivTousLes.hide();
      oDivMoment.hide();
      if(oDivDecalageIntervention){
        oDivDecalageIntervention.show();
      }
    break;
  }
  if (type == "moment"+type_elt) {
    oDivFoisPar.hide();
    oDivTousLes.hide();
    if(oDivDecalageIntervention){
      oDivDecalageIntervention.hide();
    }
  }
}

reloadPrises = function(prescription_line_id, chapitre){
  var url = new Url("dPprescription", "httpreq_vw_prises");
  url.addParam("prescription_line_id", prescription_line_id);
  url.addParam("chapitre", chapitre);
  url.requestUpdate('prises-'+chapitre+prescription_line_id);
}

onSubmitPrise = function(oForm, chapitre){
  if (!checkForm(oForm) || !oForm.object_id.value){
    return;
  }
  return onSubmitFormAjax(oForm, { onComplete:
    function(){
      reloadPrises(oForm.object_id.value, chapitre);
      oForm.quantite.value = 1;
      if(oForm.moment_unitaire_id){
        oForm.moment_unitaire_id.value = "";
      }
      if(oForm._urgent){
	      oForm._urgent.checked = false;
	      changeUrgence(oForm, false);
      }
  } });
}


// Calcul de la date de debut lors de la modification de la fin
syncDate = function(oForm, curr_line_id, fieldName, type, object_class, cat_id) {
  // Recuperation de la date actuelle
  var todayDate = new Date();
  var dToday = todayDate.toDATE();
  
  // Recuperation des dates des formulaires
  var sDebut = oForm.debut.value;
  var sFin = oForm._fin.value;
  var nDuree = parseInt(oForm.duree.value, 10);
  var sType = oForm.unite_duree.value;
  
  // Transformation des dates
  if(sDebut){
    var dDebut = Date.fromDATE(sDebut);  
  }
  if(sFin){
    var dFin = Date.fromDATE(sFin);  
  }
  
  // Modification de la fin en fonction du debut
  if(fieldName != "_fin" && sDebut && sType && nDuree) {
    dFin = dDebut;
    switch (sType) {
      case "jour":      dFin.addDays(nDuree-1); break;
      case "semaine":   dFin.addDays(nDuree*7-1); break;
      case "quinzaine": dFin.addDays(nDuree*14-1); break;
      case "mois":      dFin.addDays(nDuree*30-1); break;
      case "trimestre": dFin.addDays(nDuree*90-1); break;
      case "semestre":  dFin.addDays(nDuree*180-1); break;
      case "an":        dFin.addDays(nDuree*365-1); break;
    }

    oForm._fin.value = dFin.toDATE();
    oForm._fin_da.value = dFin.toLocaleDate();
    if(curr_line_id){
      testPharma(curr_line_id);
    }
  }
  
  //-- Lors de la modification de la fin --
  // Si debut, on modifie la duree
  if(sDebut && sFin && fieldName == "_fin"){
    var nDuree = parseInt((dFin - dDebut)/86400000,10);
    oForm.duree.value = nDuree+1;
    oForm.unite_duree.value = "jour";
    if(curr_line_id){
      testPharma(curr_line_id);
    }
  }
  
  // Si !debut et duree, on modifie le debut
  if(!sDebut && nDuree && sType && fieldName == "_fin"){
    dDebut = dFin;
    
    switch (sType) {
      case "jour":      dDebut.addDays(-nDuree); break;
      case "semaine":   dDebut.addDays(-nDuree*7); break;
      case "quinzaine": dDebut.addDays(-nDuree*14); break;
      case "mois":      dDebut.addDays(-nDuree*30); break;
      case "trimestre": dDebut.addDays(-nDuree*90); break;
      case "semestre":  dDebut.addDays(-nDuree*180); break;
      case "an":        dDebut.addDays(-nDuree*365); break;
    }

    oForm.debut.value = dDebut.toDATE();
    oForm.debut_da.value = dDebut.toLocaleDate();
    
    if(curr_line_id){
      testPharma(curr_line_id);
    } 
  }
  
  // Si !debut et !duree, on met le debut a aujourd'hui, et on modifie la duree
  if(!sDebut && !nDuree && fieldName == "_fin"){
    dDebut = todayDate;
    oForm.debut.value = todayDate.toDATE();
    oForm.debut_da.value = todayDate.toLocaleDate();
    var nDuree = parseInt((dFin - dDebut)/86400000,10);
    oForm.duree.value = nDuree;
    oForm.unite_duree.value = "jour";
    if(curr_line_id){
      testPharma(curr_line_id);
    }
  }
  
  if ($('line_medicament_'+curr_line_id) || $('line_element_'+curr_line_id)) {
    if(object_class == 'CPrescriptionLineMedicament'){
      var oTbody = $('line_medicament_'+curr_line_id);
    } else {
      var oTbody = $('line_element_'+curr_line_id);
    }
    
    // Classes du tbody avant la modification
    var classes_before = oTbody.className;
    
    // Ligne finie
    var oDiv = $('th_line_'+object_class+'_'+curr_line_id);
    if(oForm._fin.value != "" && oForm._fin.value < '{{$today}}'){
      oDiv.addClassName("arretee");
      oTbody.addClassName("line_stopped");
    } else {
      oDiv.removeClassName("arretee");
      oTbody.removeClassName("line_stopped");
    }
  
    
    // Classes du tbody apres la modification
    var classes_after = oTbody.className;
    
    // Si modif, deplacement du tbody
    if(classes_after != classes_before){
      if(object_class == 'CPrescriptionLineMedicament'){
        moveTbody(oTbody);
      } else {
        moveTbodyElt(oTbody, cat_id);
      }
    }
  }
}

addLineContigue = function(oForm){
  if(document.selPraticienLine){
    oForm.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  submitFormAjax(oForm, 'systemMsg'); 
}

// Fonction lancée lors de la modfication de la posologie
submitPoso = function(oForm, curr_line_id){
  // Suppression des prises de la ligne de prescription
  oForm._delete_prises.value = "1";
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){
      var url = new Url("dPprescription", "httpreq_prescription_prepare");
      url.addParam("prescription_line_id", curr_line_id);
      if(oForm.no_poso){
        url.addParam("no_poso", oForm.no_poso.value);
      }
      url.addParam("code_cip", oForm._code_cip.value);
      if(oForm._line_id_for_poso){
        url.addParam("line_id_for_poso", oForm._line_id_for_poso.value);
      }
      url.requestUpdate('prises-Med'+curr_line_id);
    } 
   }
  );
}

moveTbody = function(oTbody){
  var oTableMed = $('med');
  var oTableMedArt = $('med_art');
  
  if(oTbody.hasClassName('med')){
    if(oTbody.hasClassName('line_stopped')){
      oTableMedArt.insert(oTbody);      
    } else {
      oTableMed.insert(oTbody);     
    } 
  }
}

moveTbodyElt = function(oTbody, cat_id){
  var oTableElt = $('elt_'+cat_id);
  var oTableEltArt = $('elt_art_'+cat_id);
  
  if(oTbody.hasClassName('elt')){
    if(oTbody.hasClassName('line_stopped')){
      oTableEltArt.insert(oTbody);      
    } else {
      oTableElt.insert(oTbody);     
    } 
  }
}

changePraticienMed = function(praticien_id){
  var oFormAddLine = document.forms.addLine;
	var oFormAddAerosol = document.forms.add_aerosol;
  var oFormAddLineCommentMed = document.forms.addLineCommentMed;
	var oFormTransfert = document.forms.transfert_line_TP;

  $V(oFormAddLine.praticien_id, praticien_id);
  $V(oFormAddAerosol.praticien_id, praticien_id);
	$V(oFormTransfert.praticien_id, praticien_id);
  $V(oFormAddLineCommentMed.praticien_id, praticien_id);
}

// Test permettant de pré-selectionner la case à cocher 
testPharma = function(line_id){
  // si on est pas en mode pharmacie, on sort de la fonction
  {{if !$mode_pharma}}return;{{/if}} 
  var oFormAccordPraticien = document.forms["editLineAccordPraticien-"+line_id];
  if(oFormAccordPraticien.accord_praticien.value == 0){
    if(confirm("Modifiez vous cette ligne en accord avec le praticien ?")){
      oFormAccordPraticien.__accord_praticien.checked = true;
      $V(oFormAccordPraticien.accord_praticien,"1");
    }
  }
}

// Preselection des executants
preselectExecutant = function(executant_id, category_id){
 $$('select.executant-'+category_id).each( function(select) {
   select.value = executant_id;
   select.onchange();
 })
}

changePraticienElt = function(praticien_id, element){
  var oFormAddLineElement = document.addLineElement;
  var oFormAddLineCommentElement = document.forms['addLineComment'+element];
  
  oFormAddLineElement.praticien_id.value = praticien_id;
  if(oFormAddLineCommentElement){
    oFormAddLineCommentElement.praticien_id.value = praticien_id;
  }
}

// UpdateFields de l'autocomplete des elements
updateFieldsElement = function(selected, formElement, element) {
  Element.cleanWhitespace(selected);
  var dn = selected.childNodes;
  Prescription.addLineElement(dn[0].firstChild.nodeValue, dn[1].firstChild.nodeValue);
  $(formElement+'_'+element).value = "";
}

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
	Element.cleanWhitespace(selected);
  var dn = selected.childNodes;
	if(dn[0].className != 'informal'){
    Prescription.addLine(dn[0].firstChild.nodeValue);
	}
  $('searchProd_produit').value = "";
}

modifUniteDecal = function(oFieldJour, oFieldUnite){
  if(oFieldJour.value != "I"){
    $V(oFieldUnite,"jour");
    oFieldUnite.disabled = "disabled";
  } else {
    oFieldUnite.disabled = "";
  }
}

togglePerfDecalage = function(oForm){
  if($V(oForm.jour_decalage) == 'N'){
    $V(oForm.decalage_interv, '0');
    $('decalage_interv-'+$V(oForm.prescription_line_mix_id)).hide();
  } else {
    $('decalage_interv-'+$V(oForm.prescription_line_mix_id)).show();
  }
}

toggleContinuiteLineMix = function(radioButton, prescription_line_mix_id){
  if($V(radioButton) == "continue"){
    $('discontinue-'+prescription_line_mix_id).hide();
  }
  if($V(radioButton) == "discontinue"){
    $('continue-'+prescription_line_mix_id).hide();
  }
  $($V(radioButton)+'-'+prescription_line_mix_id).show();
}

removeSolvant = function(checkbox){
  if (!checkbox.checked) return;

  $(checkbox).up('table.group').select('input[name=__solvant]').each(
    function(e){
      if(e != checkbox){
        e.checked=false;
        e.onclick();
      }
    }
  );
}

updateSolvant = function(prescription_line_mix_id, line_id){
  var checked = $('lines-'+prescription_line_mix_id).select('input[name=__solvant]:checked')[0];
  if(!checked){
    return;
  }

  // Ligne de solvant
  var prescription_line_mix_item_id = $V(checked.up('form').prescription_line_mix_item_id);
  
  var modif_qte_totale = 0;
  if(line_id && prescription_line_mix_item_id == line_id){
    modif_qte_totale = 1;
  }
  
  var url = new Url("dPprescription", "httpreq_update_solvant");
  url.addParam("prescription_line_mix_item_id", prescription_line_mix_item_id);
  url.addParam("modif_qte_totale", modif_qte_totale);
  
  url.requestJSON(function(e){
    if(!e){
      return;
    }
    if(modif_qte_totale){
      var oFormPerfusion = getForm("editPerf-"+prescription_line_mix_id);
      $V(oFormPerfusion.quantite_totale, e, false);
    } else {
      var oForm = getForm("editLinePerf-"+prescription_line_mix_item_id);
      $V(oForm.quantite, e);
      $V(oForm.unite, 'ml');
    }
  });
}


</script>