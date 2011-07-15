/**
 * JS function PMSI
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */


PMSI = {
  exportActes : function(object_id, object_class, oOptions, confirmCloture){
	if (confirmCloture && !confirm("L'envoi des actes cloturera définitivement le codage de cette intervention pour le chirurgien et l'anesthésiste." +
        "\nConfirmez-vous l'envoi en facturation ?")) {
      return;
	} 
	
	var oDefaultOptions = {
  	  onlySentFiles : false
    };
  
    Object.extend(oDefaultOptions, oOptions);
  
    var url = new Url("dPpmsi", "export_actes_pmsi");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
  
    var oRequestOptions = {
      waitingText: oDefaultOptions.onlySentFiles ? 
  	    "Chargement des fichers envoyés" : 
        "Export des actes..."
      };
  
    url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions); 
  },
  
  deverouilleDossier : function(object_id, object_class) {
    var url = new Url("dPpmsi", "ajax_refresh_export_actes_pmsi");
	url.addParam("object_id", object_id);
	url.addParam("object_class", object_class);
	url.requestUpdate("export_" + object_class + "_" + object_id, { waitingText : "Dévérouillage du dossier..." });     
  }
};