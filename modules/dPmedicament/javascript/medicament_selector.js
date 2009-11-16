/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

var MedSelector = {
  sForm     : null,
  sView     : null,
  sCode     : null,
	sCodeUCD  : null,
	sCodeCIS  : null,
  sSearch   : null,
  sRechercheLivret : null,
  sOnglet   : null,
  sSearchByCIS : null,
	sGestionProduits: null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 700,
    height: 400
  },
  prepared : {
    code: null,
    nom:null
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    if(this.sSearch) {
      this.oUrl.addParam(this.sOnglet, this.sSearch);
    }
    this.oUrl.addParam("onglet_recherche", this.sOnglet);
    this.oUrl.addParam("_recherche_livret", this.sRechercheLivret?1:0);
    if(this.sSearchByCIS){
      this.oUrl.addParam("search_by_cis", this.sSearchByCIS);
    }
		if(this.sGestionProduits){
      this.oUrl.addParam("gestion_produits", this.sGestionProduits);
    }
    this.oUrl.setModuleAction("dPmedicament", "vw_idx_recherche");
    
    this.oUrl.popup(this.options.width, this.options.height, "Medicament Selector");
  },
  set: function(nom, code_cip, code_ucd, code_cis) {
    this.prepared.nom = nom;
    
		if (code_cip) {
	 	  this.prepared.code = code_cip;
	  }
		
		if(code_ucd){
			this.prepared.codeUCD = code_ucd;
		}
	  if(code_cis){
      this.prepared.codeCIS = code_cis;
    }
	
    // Lancement de l'execution du set
    window.setTimeout( window.MedSelector.doSet , 1);
  },
  
  doSet: function(){
    var oForm = document[MedSelector.sForm];
    $V(oForm[MedSelector.sView], MedSelector.prepared.nom);
    if (MedSelector.prepared.code) {
		  $V(oForm[MedSelector.sCode], MedSelector.prepared.code);
	  }
    if (MedSelector.prepared.codeUCD) {
      $V(oForm[MedSelector.sCodeUCD], MedSelector.prepared.codeUCD);
    }
		if (MedSelector.prepared.codeCIS) {
      $V(oForm[MedSelector.sCodeCIS], MedSelector.prepared.codeCIS);
    }
  },
      
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    MedSelector.oUrl.close();
  }
}
