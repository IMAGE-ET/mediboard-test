// $Id: $

Mouvements = {
  retry: function(klass, rec) {
    var url = new Url("dPsante400", "synchro_sante400");
    url.addParam("class", klass);
    url.addParam("rec", rec); 
    url.addParam("verbose", 1);
    url.popup(900, 700);
  },
  
  relaunch: function () {
    if (document.typeFilter.relaunch.checked) {
      document.typeFilter.submit();
    } 
  }
}
