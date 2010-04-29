/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

PlanningEquipement = {
  show: function(id) {
    new Url("ssr", "ajax_planning_equipement") .
      addParam("equipement_id", id) .
      requestUpdate("planning-equipement");
  },
  hide: function() {
    $("planning-equipement").update("");
  }
};
  
PlanningTechnicien = {
  show: function(id) {
    new Url("ssr", "ajax_planning_technicien") .
      addParam("kine_id", id || "11") .
      requestUpdate("planning-technicien");
  },
  hide: function() {
    $("planning-technicien").update("");
  }
};

Planification = { 
	refreshActivites: function(sejour_id) {
		new Url("ssr", "ajax_activites_sejour") .
		  addParam("sejour_id", sejour_id) .
		  requestUpdate("activites-sejour");
	},
	
	refreshSejour: function(sejour_id) {
	  new Url("ssr", "ajax_planning_sejour") .
	    addParam("sejour_id", sejour_id) .
	    requestUpdate("planning-sejour");
	},
	
	refresh: function(sejour_id) {
		Planification.refreshActivites(sejour_id);
    Planification.refreshSejour   (sejour_id);
	}
};
