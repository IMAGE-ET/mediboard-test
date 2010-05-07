/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

PlanningEquipement = {
	equipement_id : null,
	sejour_id : null,
	
  show: function(equipement_id, sejour_id) {
		this.equipement_id = equipement_id || this.equipement_id;
    this.sejour_id = sejour_id || this.sejour_id; 
    if(!this.equipement_id){
			return;
		}
		new Url("ssr", "ajax_planning_equipement") .
      addParam("equipement_id", this.equipement_id) .
			addParam("sejour_id", this.sejour_id) .
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
	height      : null,
	selectable  : null,
	large        : null,
	show: function(kine_id, surveillance, sejour_id, height, selectable, large) {
		this.kine_id = kine_id || this.kine_id;
		this.sejour_id = sejour_id || this.sejour_id ; 
		this.surveillance = surveillance || this.surveillance ; 
		this.height = height || this.height ; 
    this.selectable = selectable || this.selectable;
		this.large = large || this.large;
    
		if(!this.kine_id){
      return;
    }
    var url = new Url("ssr", "ajax_planning_technicien");
		url.addParam("kine_id", this.kine_id);
	  url.addParam("surveillance", this.surveillance);
    url.addParam("sejour_id",  this.sejour_id);
		url.addParam("height", this.height);
		url.addParam("selectable", this.selectable);
		url.addParam("large", this.large);
		url.requestUpdate("planning-technicien");
  },
	
  hide: function() {
    $("planning-technicien").update("");
  },

	toggle: function() {
		this.surveillance = this.surveillance == 1 ? 0 : 1;
	  PlanningTechnicien.show(this.kine_id, this.surveillance, this.sejour_id);
	}
};

Planification = { 
  sejour_id : null,
  selectable : null,
  scroll: function() {
    $("planification").scrollTo();
  },

	refreshActivites: function(sejour_id) {
		 this.sejour_id = sejour_id || this.sejour_id;
		new Url("ssr", "ajax_activites_sejour") .
		  addParam("sejour_id", this.sejour_id) .
		  requestUpdate("activites-sejour");
	},
	
	refreshSejour: function(sejour_id, selectable) {
    this.sejour_id = sejour_id || this.sejour_id;
		this.selectable = selectable || this.selectable;
	  new Url("ssr", "ajax_planning_sejour") .
	    addParam("sejour_id", this.sejour_id) .
			addParam("selectable", this.selectable) .
	    requestUpdate("planning-sejour");
	},
	
  refresh: function(sejour_id) {
    this.sejour_id = sejour_id || this.sejour_id;
    Planification.refreshActivites(this.sejour_id);
    Planification.refreshSejour   (this.sejour_id, true);
  },
  
  showWeek: function(date) {
    var url = new Url("ssr", "ajax_week_changer");
    if (date) {
      url.addParam("date", date);
    }
    url.requestUpdate("week-changer", { onComplete: function(){
			if(date){
				onCompleteShowWeek();
      }
		}});
  }
};
