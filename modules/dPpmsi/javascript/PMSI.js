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
  confirmCloture: 0,
  
  loadExportActes: function(object_id, object_class, confirmCloture, module) {
    var url = new Url("dPpmsi", "ajax_view_export_actes");
    url.addParam("object_id"   , object_id);
    url.addParam("object_class", object_class);
    url.addParam("module"      , module);
    if(confirmCloture == 1) {
      PMSI.confirmCloture = 1;
      url.addParam("confirmCloture", confirmCloture);
    }
    
    url.requestUpdate("export_" + object_class + "_" + object_id); 
  },
  
  exportActes: function(object_id, object_class, oOptions, module){
    if ((PMSI.confirmCloture == 1) && !confirm("L'envoi des actes cloturera définitivement le codage de cette intervention pour le chirurgien et l'anesthésiste." +
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
    url.addParam("module"      , module);

    var oRequestOptions = {
      waitingText: oDefaultOptions.onlySentFiles ? 
        "Chargement des fichers envoyés" : 
        "Export des actes..."
    };
    
    if (PMSI.confirmCloture == 1) {
      oRequestOptions.onComplete = function() {
        PMSI.reloadActes(object_id, module);
      }
    }

    url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions); 
  },
  
  deverouilleDossier: function(object_id, object_class, module) {
    var url = new Url("dPpmsi", "export_actes_pmsi");
    url.addParam("object_id"     , object_id);
    url.addParam("object_class"  , object_class);
    url.addParam("unlock_dossier", 1);
  
    var oRequestOptions = {
      waitingText: "Dévérouillage du dossier..."
    };
    if (PMSI.confirmCloture == 1) {
      oRequestOptions.onComplete = function() {
        PMSI.reloadActes(object_id, module);
      }
    }
  
    url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions);     
  },
  
  reloadActes: function(operation_id, module) {
    var url = new Url("dPsalleOp", "ajax_refresh_actes");
    url.addParam("operation_id", operation_id);
    url.addParam("module", module);
    url.requestUpdate("codage_actes");
  },

  checkActivites: function(object_id, object_class, oOptions, module) {
    var url = new Url("dPsalleOp", "ajax_check_activites_cloture");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("suppressHeaders", 1);
    url.addParam("dialog", 1);
    url.requestJSON(function(completed_activite) {
      if (completed_activite.activite_1 == 0 && !confirm($T('CActeCCAM-_no_activite_1_cloture'))) {
        return;
      }
      if (completed_activite.activite_4 == 0 && !confirm($T('CActeCCAM-_no_activite_4_cloture'))) {
        return;
      }
      PMSI.exportActes(object_id, object_class, oOptions, module);
    });
  }
};