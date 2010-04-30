/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

PlanningEquipement = {
  show: function(equipement_id, sejour_id) {
    new Url("ssr", "ajax_planning_equipement") .
      addParam("equipement_id", equipement_id) .
			addParam("sejour_id", sejour_id) .
      requestUpdate("planning-equipement");
  },
	
  hide: function() {
    $("planning-equipement").update("");
  }
};
  
PlanningTechnicien = {
	surveillance: 0,
	kine_id     : null,
	sejour_id   : null,
	
  show: function(kine_id, surveillance, sejour_id) {
		this.kine_id = kine_id;
		this.sejour_id = sejour_id; 
    var url = new Url("ssr", "ajax_planning_technicien");
		url.addParam("kine_id", kine_id);
	  url.addParam("surveillance", surveillance);
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("planning-technicien");
  },
	
  hide: function() {
    $("planning-technicien").update("");
  },

	toggle: function() {
		Console.debug(this);
		this.surveillance = this.surveillance == 1 ? 0 : 1;
	  PlanningTechnicien.show(this.kine_id, this.surveillance, this.sejour_id);
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
