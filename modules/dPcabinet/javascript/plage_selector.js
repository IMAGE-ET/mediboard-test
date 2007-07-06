// $Id: $

var PlageSelector = {
  eHeure : null,
  ePlageconsult_id : null,
  eDate : null,
  eDuree : null,
  eChirid : null,
  options : {
    width : 800,
    height: 600
  },
    
  pop: function() {
    var url = new Url();
    url.setModuleAction("dPcabinet", "plage_selector");
    url.addParam("chir_id", this.eChirid.value);
    url.addParam("plageconsult_id", this.ePlageconsult_id.value);
    url.popup(this.options.width, this.options.height, "Plage");
  },
  
  set: function(heure, id, date, freq, chirid, chirname) {
    this.eHeure.value = heure;
    this.ePlageconsult_id.value = id;
    
    this.eDate.value = date;
     
    this.eDuree.value = freq;
    this.eChirid.value = chirid;
 
     if(this.ePlageconsult_id.onchange){
        this.ePlageconsult_id.onchange();
     }
         
  }
}
