MainCourante = {
  updater : null,
	
  init: function(frequency) {
    var url = new Url("dPurgences", "httpreq_vw_main_courante");
    MainCourante.updater = url.periodicalUpdate('main_courante', { frequency: frequency } );
  },
  
  start: function(delay, frequency) {
    this.stop()
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
  
	legend: function() {
	  var url = new Url("dPurgences", "vw_legende");
	  url.popup(300, 320, "Legende");
	},
	
  filter: function(input) {
	  $$("#main_courante tr").invoke("show");
	  
	  var term = $V(input);
	  if (!term) return;
	  
	  $$("#main_courante .CPatient-view").each(function(p) {
	    if (!p.innerHTML.like(term)) {
	      p.up("tr").hide();
	    }
	  });
	}
}
