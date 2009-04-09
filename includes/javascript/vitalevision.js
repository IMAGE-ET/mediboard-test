/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var VitaleVision = {
	xmlText: '',
	xmlDocument: null,
	applet: null,
	modalWindow: null,
	
	// Lecture du contenu de la carte et lancement d'une fonction après la lecture terminée
	getContent: function(callback){
		if (VitaleVision.applet && VitaleVision.applet.performRead() == "OK") {
			setTimeout(function(){
				VitaleVision.xmlText = VitaleVision.applet.getContent() + '';
				if (callback) callback();
			}, 500);
		}
	},
	
	// Lecture du contenu XML et création du document XML
	parseContent: function(){
		VitaleVision.xmlText = VitaleVision.xmlText.strip();

	  // Firefox, Mozilla, Opera, etc.
	  try {
	    VitaleVision.xmlDocument = new DOMParser().parseFromString(VitaleVision.xmlText, "text/xml");
	  }
	  catch(e) {
	    // IE
	    try {
	      VitaleVision.xmlDocument = new ActiveXObject("Microsoft.XMLDOM");
	      VitaleVision.xmlDocument.async = false;
	      VitaleVision.xmlDocument.loadXML(VitaleVision.xmlText);
	    } catch(e) {
	      Console.trace(e.message);
	      return;
	    }
	  }
	  
	  cleanWhitespace = function(node) {
	    var i, notWhitespace = /\S/;
	    for (i = 0; i < node.childNodes.length; i++) {
	      var childNode = node.childNodes[i];
	      if ((childNode.nodeType == 3) && (!notWhitespace.test(childNode.nodeValue))) {
	        // that is, if it's a whitespace text node
	        node.removeChild(node.childNodes[i]);
	        i--;
	      }
	      if ( childNode.nodeType == 1) {
	        // elements can have text child nodes of their own
	        cleanWhitespace(childNode);
	      }
	    }
	  }
	  cleanWhitespace(VitaleVision.xmlDocument);
	},
	
	// Lancement de la lecture de la carte vitale
	read: function() {
    VitaleVision.getContent(VitaleVision.parseContent);
		
    var i, beneficiaireSelect = $('modal-beneficiaire-select'), listBeneficiaires;
    
		setTimeout(function(){
		try {
			listBeneficiaires = VitaleVision.xmlDocument.getElementsByTagName("listeBenef")[0].childNodes;
			if (listBeneficiaires.length > 0) {
				beneficiaireSelect.update();
				
				for (i = 0; i < listBeneficiaires.length; i++) {
					var ident = listBeneficiaires[i].getElementsByTagName("ident")[0], nom = getNodeValue("nomUsuel", ident), prenom = getNodeValue("prenomUsuel", ident);
					
					beneficiaireSelect.insert(new Element('option', {
						value: i
					}).update(nom + " " + prenom));
				}
        if (listBeneficiaires.length == 1) {
          $('msg-multiple-benef').hide();
          beneficiaireSelect.hide();
          $('msg-confirm-benef').show();
          $$('#benef-nom span')[0].update(beneficiaireSelect.options[0].innerHTML);
        }
				VitaleVision.modalWindow = modal($('modal-beneficiaire'), {
					overlayOpacity: 0.75,
					className: 'modal'
				});
			}
		} 
		catch (e) {
			alert('Erreur lors de la lecture de la carte vitale, veuillez la ré-insérer.');
			return;
		}}, 700);
	},
	
	// Remplissage du formulaire en fonction du bénéficiaire sélectionné dans la fenetre modale
	fillForm: function(form, id) {
	  form = form.elements;
		
	  var benef = VitaleVision.xmlDocument.getElementsByTagName("listeBenef")[0].childNodes[id],
	      ident = benef.getElementsByTagName("ident")[0],
	      amo = benef.getElementsByTagName("amo")[0];
	
	  $V(form.nom, getNodeValue("nomUsuel", ident));  
	  $V(form.prenom, getNodeValue("prenomUsuel", ident));  
	  
	  if(getNodeValue("nomUsuel", ident) != getNodeValue("nomPatronymique", ident)) {
	    $V(form.nom_jeune_fille, getNodeValue("nomPatronymique", ident));
	  }
	  
	  var dateNaissance = getNodeValue("naissance dateEnCarte", ident),
	      jour  = dateNaissance.substring(4, 6),
	      mois  = dateNaissance.substring(2, 4),
	      annee = dateNaissance.substring(0, 2);
	      
	  var an = new Date().getFullYear();
	  annee = (("20"+annee > an) ? "19" : "20")+annee;
	
	  $V(form.naissance, jour + "/" + mois + "/" + annee);
	  
	  $V(form.matricule, getNodeValue("nir", ident));
	  tabs.setActiveTab('identite');
	  $(form.matricule).focus(); // Application du mask
	  
	  $V(form.adresse, getNodeValue("adresse ligne1", ident) + "\r\n" + 
	                   getNodeValue("adresse ligne2", ident) + "\r\n" + 
	                   getNodeValue("adresse ligne3", ident) + "\r\n" + 
	                   getNodeValue("adresse ligne4", ident));
	  
	  var ville = getNodeValue("adresse ligne5", ident);
	  $V(form.cp, ville.substring(0, 5));
	  $V(form.ville, ville.substring(6, ville.length));
	  
	  $V(form.rang_naissance, getNodeValue("rangDeNaissance", ident));
	  //$V(form.rang_beneficiaire, getNodeValue("qualBenef", amo));
	  
	  $V(form.code_regime, getNodeValue("codeRegime", amo));
	  $V(form.caisse_gest, getNodeValue("caisse", amo));
	  $V(form.centre_gest, getNodeValue("centreGestion", amo));
	  
	  var periodeDroits = getNodeValue("listePeriodesDroits element debut", amo);
	  jour  = periodeDroits.substring(0, 2);
	  mois  = periodeDroits.substring(2, 4);
	  annee = periodeDroits.substring(4, 8);
	  if(jour != ""){
	    $(form.deb_amo.form.name+'_deb_amo_da').update(jour + "/" + mois + "/" + annee);
	    $V(form.deb_amo, annee + "-" + mois + "-" + jour);
	  }
	
	  periodeDroits = getNodeValue("listePeriodesDroits element fin", amo);
	  jour  = periodeDroits.substring(0, 2);
	  mois  = periodeDroits.substring(2, 4);
	  annee = periodeDroits.substring(4, 8);
	  if(jour != ""){
	    $(form.fin_amo.form.name+'_fin_amo_da').update(jour + "/" + mois + "/" + annee);
	    $V(form.fin_amo, annee + "-" + mois + "-" + jour);
	  }
	  
	  var libelleExo = getNodeValue("libelleExo", amo).replace(/\\r\\n/g, "\n");
	  if(libelleExo.match(/affection/i)){
	    $V(form.code_exo, 4);
	  } else if(libelleExo.match(/rente AT/i) || 
	            libelleExo.match(/pension d'invalidité/i) || 
	            libelleExo.match(/pension militaire/i)){
	    $V(form.code_exo, 5);
	  } else if(libelleExo.match(/FSV/i)) {
	    $V(form.code_exo, 9);
	  } else {
	    $V(form.code_exo, 0);
	  }
	
	  $V(form.libelle_exo, libelleExo);
	  $V(form.medecin_traitant_declare, (getNodeValue("medecinTraitant", amo) == "Oui") ? 1 : 0);
	  $V(form.cmu, (getNodeValue("cmu typeCMU", amo) != "") ? 1 : 0);
	  //calculFinAmo(); ?
	  
	  var i, benefList = VitaleVision.xmlDocument.getElementsByTagName("listeBenef")[0].childNodes,
	      ident,
	      amo = benefList[id].getElementsByTagName("amo")[0];
	      
	  if(getNodeValue("qualBenef", amo) != 0) {
	    for(i = 0; i < VitaleVision.xmlDocument.getElementsByTagName("listeBenef")[0].length; i++){
	      if(getNodeValue("qualBenef", benefList[id].getElementsByTagName("amo")[0]) == 0){
	        id = i;
	      }
	    }
	  }
	  benef = benefList[id],
	  ident = benef.getElementsByTagName("ident")[0],
	  amo = benef.getElementsByTagName("amo")[0];
	  
	  $V(form.assure_nom, getNodeValue("nomUsuel", ident));
	  $V(form.assure_prenom, getNodeValue("prenomUsuel", ident));
	  if(getNodeValue("nomUsuel", ident) != getNodeValue("nomPatronymique", ident)) {
	    $V(form.assure_nom_jeune_fille, getNodeValue("nomPatronymique", ident));
	  }
	
	  var dateNaissance = getNodeValue("naissance dateEnCarte", ident),
	      jour  = dateNaissance.substring(4, 6),
	      mois  = dateNaissance.substring(2, 4),
	      annee = dateNaissance.substring(0, 2);
	      
	  var an = new Date().getFullYear();
	  annee = (("20"+annee > an) ? "19" : "20")+annee;
	
	  $V(form.assure_naissance, jour + "/" + mois + "/" + annee);
	  
	  $V(form.assure_matricule, getNodeValue("nir", ident));
	  tabs.changeTabAndFocus('assure', form.assure_nom);
	
	  $V(form.assure_adresse, getNodeValue("adresse ligne1", ident) + "\r\n" + 
	                          getNodeValue("adresse ligne2", ident) + "\r\n" + 
	                          getNodeValue("adresse ligne3", ident) + "\r\n" + 
	                          getNodeValue("adresse ligne4", ident));
	  
	  var ville = getNodeValue("adresse ligne5", ident);
	  $V(form.assure_cp, ville.substring(0, 5));
	  $V(form.assure_ville, ville.substring(6, ville.length));
	  
	  tabs.setActiveTab('assure');
	  $(form.assure_matricule).focus(); // Application du mask
	  $(form.assure_nom).focus();
	}
}

// Mapping de l'applet à l'objet VitaleVision
VitaleVision.applet = document.resultVitaleVision;

// Fonction de récupération de données avec syntax pseudo XPath ultra simplifié, avec noeud de base
function getNodeValue(path, node) {
  var i, parts = path.split(' ');
  
  for (i = 0; i < parts.length && node; i++){
    node = node.getElementsByTagName(parts[i])[0];
  }
	if (!node) return '';
  return ((node.textContent || node.text)+'').strip();
}