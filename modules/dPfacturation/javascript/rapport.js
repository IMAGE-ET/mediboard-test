Rapport = {
  refresh: function(date) {
    var url = new Url('facturation', 'print_actes');
    url.addParam('date', date);
    url.requestUpdate(date);
    Rapport.showObsolete();
  },

  showObsolete: function() {
    var style = {
      opacity: 0.9,
      position: 'absolute'
    };
    $('obsolete-totals').setStyle(style).clonePosition('totals').show();
  },

  editReglement: function(reglement_id, date) {
    var url = new Url('cabinet', 'edit_reglement');
    url.addParam('reglement_id', reglement_id);
    url.requestModal(400);
    url.modalObject.observe('afterClose', Rapport.refresh.curry(date));

  },
  
  addReglement: function(object_guid, emetteur, montant, mode, date) {
    var url = new Url('cabinet', 'edit_reglement');
    url.addParam('reglement_id', '0');
    url.addParam('object_guid', object_guid);
    url.addParam('emetteur', emetteur);
    url.addParam('montant', montant);
    url.addParam('mode', mode);
    url.requestModal(400);
    url.modalObject.observe('afterClose', Rapport.refresh.curry(date));
  }
}