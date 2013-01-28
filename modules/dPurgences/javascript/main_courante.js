MainCourante = {
  updater : null,
  frequency : null,
	
  init: function(frequency) {
    this.frequency = frequency || this.frequency;
    
    var url = new Url("dPurgences", "httpreq_vw_main_courante");
    MainCourante.updater = url.periodicalUpdate('main_courante', { 
      frequency: this.frequency,
      onSuccess: function() {
        Veille.toggle.defer($('admission_ant').checked);
      }
    });
  },
  
  start: function(delay, frequency) {
    this.stop();
    this.init.delay(delay, frequency);
  },

  stop: function() {
    if (this.updater) {
      this.updater.stop();
    }
  },

  resume: function() {
    if (this.updater) {
      this.updater.resume();
    }
  },
	
	print: function(date) {
	  var url = new Url("dPurgences", "print_main_courante");
	  url.addParam("date", date);
	  url.popup(900, 700, "Impression main courante");
  },
  
  printSortie: function(date, aff_sortie) {
    var url = new Url("dPurgences", "print_sortie_patients");
    url.addParam("date", date);
    url.addParam("aff_sortie", aff_sortie);
    url.popup(900, 700, "Impression sortie patients");
  },
	
	legend: function() {
	  var url = new Url("dPurgences", "vw_legende");
	  url.popup(300, 320, "Legende");
	},
	
  filter: function(input, indicator) {
	  $$("#main_courante tr").invoke("show");
	  indicator = $(indicator);
    
	  var term = $V(input);
	  if (!term) return;
    
	  if (indicator) {
	    indicator.show();
	    this.stop();
	  }
    
	  $$("#main_courante .CPatient-view").each(function(p) {
	    if (!p.innerHTML.like(term)) {
	      p.up("tr").hide();
	    }
	  });
	}
}
