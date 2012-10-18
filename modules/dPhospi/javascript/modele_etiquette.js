ModeleEtiquette = {
  nb_printers: 0,
  print: function(object_class, object_id, modele_etiquette_id) {
    if (ModeleEtiquette.nb_printers > 0) {
      var url = new Url('compteRendu', 'ajax_choose_printer');
      
      if (modele_etiquette_id) {
        Control.Modal.close();
        url.addParam('modele_etiquette_id', modele_etiquette_id);
      }
      
      url.addParam('mode_etiquette', 1);
      url.addParam('object_class', object_class);
      url.addParam('object_id', object_id);
      url.requestModal(400);
    }
    else {
      var form = getForm('download_etiq_'+object_class+'_'+object_id);
      if (modele_etiquette_id) {
        Control.Modal.close();
        $V(form.modele_etiquette_id, modele_etiquette_id);
      }
      form.submit();
    }
  },
  chooseModele: function(object_class, object_id) {
    var url = new Url('hospi', 'ajax_choose_modele_etiquette');
    url.addParam('object_class', object_class);
    url.addParam('object_id', object_id);
    url.requestModal(400);
  }
}
