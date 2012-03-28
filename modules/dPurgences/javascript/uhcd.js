UHCD = {
  updater : null,
  frequency : null,
  
  init: function(frequency) {
    this.frequency = frequency || this.frequency;
    
    var url = new Url("dPurgences", "ajax_refresh_uhcd");
    UHCD.updater = url.periodicalUpdate('uhcd', { 
      frequency: this.frequency
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
  
  refreshUHCD: function() {    
    var url = new Url("dPurgences", "ajax_refresh_uhcd");
    url.requestUpdate('uhcd');
  },
  
  filter: function(input, indicator) {
    $$("#uhcd tr").invoke("show");
    indicator = $(indicator);
    
    var term = $V(input);
    if (!term) return;
    
    if (indicator) {
      indicator.show();
      this.stop();
    }
    
    $$("#uhcd .CPatient-view").each(function(p) {
      if (!p.innerHTML.like(term)) {
        p.up("tr").hide();
      }
    });
  } 
}