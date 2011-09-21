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
                        window.opener.document.moment_unitaire.moment_unitaire_id.cloneNode(true) :
                        document.moment_unitaire.moment_unitaire_id;
  
  document.forms['addPrise'+type_elt+line_id].show();

  if(oSelect.options.length == 1){
    if (Prototype.Browser.IE) {
      oSelect.down().remove();
      $A(selectMoments.childNodes).each(function (child) {
        oSelect.insert(child.outerHTML);
      });
    }
    else {
      oSelect.innerHTML = selectMoments.innerHTML;
    }
  }
}

refreshCheckbox = function(oForm){
  oForm.select('input.moment_poso_lite').each(function(e){
    $V(e, false);
  });
}

changeUrgence = function(oForm, checked){
  if(checked == true){
   refreshCheckbox(oForm);
	
	 oForm.select('input.moment_poso_lite').each(function(e){
     e.disable();
   });
  } else {
	  oForm.select('input.moment_poso_lite').each(function(e){
     e.enable();
    });
	}
}

// Affichage des div d'ajout de posologies
selDivPoso = function(type, line_id, type_elt){
  if(!type){
    type = "foisPar"+type_elt;
  }
  
	if(line_id){
	  $("view_quantity_"+line_id).show();
	  $("add_button_"+line_id).show();
  }
  
  oDivMoment = $('moment'+type_elt+line_id);
  oDivFoisPar = $('foisPar'+type_elt+line_id);
  oDivTousLes = $('tousLes'+type_elt+line_id);
	oDivEvenement = $('evenement'+type_elt+line_id);
  
	oDivStats = $('stats'+type_elt+line_id);
  oDivDecalageIntervention = $('decalage_intervention'+type_elt+line_id);
  
  var oFormPrise = document.forms['addPrise'+type_elt+line_id].show();
  var oFormEvenement = document.forms['addPrise'+type_elt+line_id].show();
  
  var selectMoments = (type_elt == "mode_grille") ? 
                        window.opener.document.moment_unitaire.moment_unitaire_id :
                        document.moment_unitaire.moment_unitaire_id;
  
                        
  oFormPrise.getElements().each (function (e) {
    if ((e.type != "hidden") && (e.name != "unite_prise") && (e.name != "quantite")) {
      e.hide().writeAttribute("disabled", "disabled").value = "";
    }
  });
	
	// hide de toutes les div
	oDivMoment.hide();
  oDivFoisPar.hide();
  oDivEvenement.hide();
  oDivStats.hide();
  oDivTousLes.hide();
	if(oDivDecalageIntervention){
    oDivDecalageIntervention.hide();
  }
				
  switch (type) {
    case "tousLes"+type_elt: 
		oDivTousLes.show();
      oFormPrise.nb_tous_les.enable().show();
      oFormPrise.unite_tous_les.enable().show();
      oFormPrise.decalage_prise.enable().show();
      
      oFormPrise.moment_unitaire_id.setStyle( { float: "none" } );
      $("tous_les_"+type_elt+"_"+line_id).insert(oFormPrise.moment_unitaire_id);
      oFormPrise.moment_unitaire_id.onchange = null;
      oFormPrise.moment_unitaire_id.enable().show();
      break;
      
    case "moment"+type_elt: 
      oDivMoment.show();
      if(type_elt != "mode_grille"){
			
				$$('input.moment_poso_lite').each(function(e){
				  e.enable().show();
				});
      
        if(oFormPrise._urgent){
	        oFormPrise._urgent.enable().show();
	        changeUrgence(oFormPrise, oFormPrise._urgent.checked);
        }
        $("moment_"+type_elt+"_"+line_id).insert(oFormPrise.moment_unitaire_id);

      } else {
        $("tous_les_"+type_elt+"_"+line_id).insert(oFormPrise.moment_unitaire_id);
        oFormPrise.moment_unitaire_id.setStyle( { float: "none" } );
      }
      oFormPrise.moment_unitaire_id.enable().show();
      oFormPrise.moment_unitaire_id.onchange = oFormPrise.onsubmit.bind(oFormPrise);
    break;
		
    case "foisPar"+type_elt: 
      oFormPrise.nb_fois.enable().show();
      oFormPrise.unite_fois.enable().show();
      oDivFoisPar.show();
    break;
		
    case "decalage_intervention"+type_elt: 
      oFormPrise.type_decalage.enable().show();
      oFormPrise.decalage_intervention.enable().show();
			oFormPrise.unite_decalage_intervention.enable().show();
      if(oDivDecalageIntervention){
        oDivDecalageIntervention.show();
      }
    break;
		
	  case "stats"+type_elt: 
			$("view_quantity_"+line_id).hide();
			$("add_button_"+line_id).hide();
	    oDivStats.show();
    break;
		
		case "evenement"+type_elt: 
      oDivEvenement.show();
			oFormEvenement.condition.enable().show();
    break;
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
  
  // Si c'est une prise en urgences et que la ligne n'a pas de durée,
  // passage de cette durée à 1 jour.
  if (oForm._urgent && oForm._urgent.checked) {
    var oFormDate = getForm("editDates-"+chapitre+"-"+$V(oForm.object_id));
    if (oFormDate && !$V(oFormDate.duree)) {
      $V(oFormDate.duree, 1);
      $V(oFormDate._fin_da, '');
    }
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
}

addLineContigue = function(oForm){
  if(document.forms.selPraticienLine){
    oForm.praticien_id.value = document.forms.selPraticienLine.praticien_id.value;
  }
  onSubmitFormAjax(oForm);
}

// Fonction lancée lors de la modfication de la posologie
submitPoso = function(oForm, curr_line_id, curr_line_class, chapitre){
  // Suppression des prises de la ligne de prescription
  oForm._delete_prises.value = "1";

  return onSubmitFormAjax(oForm, { onComplete: function(){
      var url = new Url("dPprescription", "httpreq_prescription_prepare");
      url.addParam("prescription_line_guid", curr_line_class+"-"+curr_line_id);
			
			if (oForm._code_cip){
			  url.addParam("code_cip", oForm._code_cip.value);
      }
      if (oForm._element_prescription_id){
			  url.addParam("element_prescription_id", oForm._element_prescription_id.value);
      }
			
      if(oForm._line_id_for_poso){
        url.addParam("line_id_for_poso", oForm._line_id_for_poso.value);
      }
			url.requestUpdate('prises-'+chapitre+curr_line_id);
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
  //$(formElement+'_'+element).value = "";
}

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
	Element.cleanWhitespace(selected);
  var dn = selected.childNodes;

	if(dn[0].className == 'protocole'){
	  {{if $prescription->object_id}}
		  // Application d'un protocole par acces rapide
			oFormProtocole = getForm("applyProtocole");
			$V(oFormProtocole.pack_protocole_id, "prot-"+dn[0].getText()); 
      $V(oFormProtocole.protocole_id, selected.get("id"));
			submitProtocole();
		{{else}}
		  Protocole.duplicate(dn[0].getText(), "{{$prescription->_id}}");
		{{/if}}
	} else {
		if(dn[0].className != 'informal'){
	    Prescription.addLine(dn[0].firstChild.nodeValue);
		}
	}
  $('searchProd_produit').value = "";
}

modifUniteDecal = function(oFieldJour, oFieldUnite){
  if(oFieldJour.value != "I" && oFieldJour.value != "A"){
    $V(oFieldUnite,"jour");
    oFieldUnite.disabled = "disabled";
  } else {
    oFieldUnite.disabled = "";
  }
}

toggleContinuiteLineMix = function(radioButton, prescription_line_mix_id){
  var sValueContinuite = $V(radioButton);
  $$(".debit_produit").each(function(debit) {
    if(sValueContinuite == "discontinue"){
      debit.down().innerHTML = '';
      debit.hide();
    }
    else if (debit.style.display && debit.down().innerHTML != '') {
      debit.show();
    } 
  });
  if(sValueContinuite == "continue"){
    $('lines-'+prescription_line_mix_id).select('.calcul_debit').invoke('show');
    $('discontinue-'+prescription_line_mix_id).hide();
  }
  if(sValueContinuite == "discontinue"){
    $('lines-'+prescription_line_mix_id).select('.calcul_debit').invoke('hide');
    $('continue-'+prescription_line_mix_id).hide();
  }
  $(sValueContinuite+'-'+prescription_line_mix_id).show();
}

removeSolvant = function(checkbox){
  if (!checkbox.checked) return;

  $(checkbox).up('table.line_mix_items').select('input[name=__solvant]').each(
    function(e){
      if(e != checkbox){
        e.checked=false;
        e.onclick();
      }
    }
  );
}

calculDebit = function(line_id, line_item_id){
  var url = new Url("soins", "ajax_calcul_debit");
  url.addParam("line_id", line_id);
  url.addParam("line_item_id", line_item_id);
  url.pop(600, 200, "calculDebit");
}

updateVolumeTotal = function(prescription_line_mix_id, modif_qte_totale, quantite_totale, solvant, prescription_line_mix_item_id) {
  var checked = $('lines-'+prescription_line_mix_id).select('input[name=__solvant]:checked')[0];
  if(!checked){
    if (modif_qte_totale) {
      $("volume_total_"+prescription_line_mix_id).hide();
    }
    return;
  }
  
  var url = new Url("dPprescription", "httpreq_update_solvant");
  url.addParam("prescription_line_mix_id", prescription_line_mix_id);
  url.addParam("modif_qte_totale", modif_qte_totale);
  if (solvant != null) {
    url.addParam("solvant", solvant);
  }
  if (quantite_totale) {
    url.addParam("quantite_totale", quantite_totale);
  }
  if (prescription_line_mix_item_id) {
    url.addParam("prescription_line_mix_item_id", prescription_line_mix_item_id);
  }
  url.requestJSON(function(e){
    if(e == null){
      return;
    } 
    if(modif_qte_totale && (solvant == null || solvant == 1)){
      var volume_total = $("volume_total_"+prescription_line_mix_id);
      volume_total.show();
      volume_total.select("input")[0].value = e; 
    } else {
      var td = $$("input[name=__solvant]:checked")[0].up("td");
      $V(td.select("input[name=quantite]")[0], e);
      $V(td.select("input[name=unite]")[0], 'ml');
    }
  });
}

updateSolvant = function(prescription_line_mix_id, quantite_totale){
  var checked = $('lines-'+prescription_line_mix_id).select('input[name=__solvant]:checked')[0];
  if (!checked) {
    return;
  }
  // Ligne de solvant
  var prescription_line_mix_item_id = $V(checked.up('form').prescription_line_mix_item_id);
  updateVolumeTotal(prescription_line_mix_id, 1, quantite_totale, 0, prescription_line_mix_item_id);
}

updateDebitProduit = function(prescription_line_mix_item_id) {
  var url = new Url("dPprescription", "ajax_update_debit_produit");
  url.addParam("prescription_line_mix_item_id", prescription_line_mix_item_id);
  url.requestJSON(function(e) {
    var debit = $("debit_"+prescription_line_mix_item_id);
    if (e.debit == '-') {
      debit.down().innerHTML = "";
      debit.hide();
      return;
    }
    if (!getForm("editLinePerf-"+prescription_line_mix_item_id).__solvant.checked) {
      debit.show();
    }
    debit.down().innerHTML = e.debit;
  });
}

showHideDebitProduit = function(elt, prescription_line_mix_item_id) {
  if (!elt) {
    return;
  }
  var debit = $("debit_" + prescription_line_mix_item_id);
  
  if (!elt.checked && debit.down().innerHTML != "") {
    debit.show();
  }
  else {
    debit.hide();
  }
}

</script>