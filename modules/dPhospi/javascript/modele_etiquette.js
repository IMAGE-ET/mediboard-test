ModeleEtiquette = {
  nb_printers: 0,
  print: function(object_class, object_id) {
    if (ModeleEtiquette.nb_printers > 0) {
      var url = new Url('dPcompteRendu', 'ajax_choose_printer');
      url.addParam('mode_etiquette', 1);
      url.addParam('object_class', object_class);
      url.addParam('object_id', object_id);
      url.requestModal(400);
    }
    else {
      getForm('download_etiq_'+object_class+'_'+object_id).submit();
    }
  }
}
