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
	
	showMany: function(equipement_ids) {
		equipement_ids.each(function(equipement_id) {
      $("planning-equipement-"+equipement_id).update("");
	    new Url("ssr", "ajax_planning_equipement") .
	      addParam("equipement_id", equipement_id) .
	      requestUpdate("planning-equipement-"+equipement_id, {coverIE: false});
		} );
	},
	
  show: function(equipement_id, sejour_id) {
		this.equipement_id = equipement_id || this.equipement_id;
    this.sejour_id = sejour_id || this.sejour_id; 
    if(!this.equipement_id){
			return;
		}
    $("planning-equipement").update("");
		new Url("ssr", "ajax_planning_equipement") .
      addParam("equipement_id", this.equipement_id) .
			addParam("sejour_id", this.sejour_id) .
      requestUpdate("planning-equipement", {coverIE: false});
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
	show: function(kine_id, surveillance, sejour_id, height, selectable, large, print) {
		this.kine_id = kine_id || this.kine_id;
		this.sejour_id = sejour_id || this.sejour_id ; 
		this.surveillance = surveillance || this.surveillance ; 
		this.height = height || this.height ; 
    this.selectable = selectable || this.selectable;
		this.large = large || this.large;
    
		if(!this.kine_id){
      return;
    }
    
    $("planning-technicien").update("");
    
    var url = new Url("ssr", "ajax_planning_technicien");
		url.addParam("kine_id", this.kine_id);
	  url.addParam("surveillance", this.surveillance);
    url.addParam("sejour_id",  this.sejour_id);
		url.addParam("height", this.height);
		url.addParam("selectable", this.selectable);
		url.addParam("large", this.large);
		url.addParam("print", print);
		url.requestUpdate("planning-technicien", {
		  onComplete: function(){
				if (print) {
					$('planning-technicien').select(".planning col")[2].style.width = 0;
					$('planning-technicien').select(".week-container")[0].style.overflowY = "visible";
					
					for (var i = 0; i < 7; i++) {
						$$('.hour-0' + i)[0].hide();
					}
					for (var i = 19; i < 24; i++) {
						$$('.hour-' + i)[0].hide();
					}
			
					}
				}, 
        coverIE: false
	    }
		);
  },
	
  hide: function() {
    $("planning-technicien").update("");
  },

	toggle: function() {
		this.surveillance = this.surveillance == 1 ? 0 : 1;
	  PlanningTechnicien.show(this.kine_id, this.surveillance, this.sejour_id);
	},
	
	print: function(){
	  var url = new Url("ssr", "print_planning_technicien");
	  url.addParam("kine_id", this.kine_id);
	  url.popup("700","700","Planning du rééducateur");
	}
};

Planification = { 
  sejour_id : null,
  selectable : null,
	height: null,
  scroll: function() {
    $("planification").scrollTo();
  },

	refreshActivites: function(sejour_id) {
		if (!$("activites-sejour")) return;
    this.sejour_id = sejour_id || this.sejour_id;
		new Url("ssr", "ajax_activites_sejour") .
		  addParam("sejour_id", this.sejour_id) .
		  requestUpdate("activites-sejour", {coverIE: false});
	},
	
	refreshSejour: function(sejour_id, selectable, height, print, large) {
    if (!$("planning-sejour")) return;
    this.sejour_id = sejour_id || this.sejour_id;
		this.selectable = selectable || this.selectable;
		this.height = height || this.height;
    
    $("planning-sejour").update("");
    
	  new Url("ssr", "ajax_planning_sejour") .
	    addParam("sejour_id", this.sejour_id) .
			addParam("selectable", this.selectable) .
			addParam("height", this.height) .
			addParam("print", print).
			addParam("large", large).
      requestUpdate("planning-sejour", { 
			  onComplete: function(){
					if(print){
						$('planning-sejour').select(".planning col")[2].style.width = 0;
						$('planning-sejour').select(".week-container")[0].style.overflowY = "visible";
						
						for(var i=0; i<7; i++){
							$$('.hour-0'+i)[0].hide();
            }
						for(var i=19; i<24; i++){
              $$('.hour-'+i)[0].hide();
            }
					}
				}, 
        coverIE: false
			});
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
			if (date){
				Planification.onCompleteShowWeek();
      }
		}, coverIE: false});
  },
	
	onCompleteShowWeek: Prototype.emptyFunction
};

// Warning: planning.js has to be included first
PlanningEvent.onMouseOver = function(event) {
  var matches = event.className.match(/CEvenementSSR-([0-9]+)/);
  if (matches) {
    ObjectTooltip.createEx(event, matches[0]);
  }
}

