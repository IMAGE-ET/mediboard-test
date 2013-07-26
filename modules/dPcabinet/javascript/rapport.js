Rapport = {
  refresh: function(plage_id) {
    var url = new Url('cabinet', 'print_rapport');
    url.addParam('plage_id', plage_id);
    url.requestUpdate('CPlageconsult-' + plage_id);
    Rapport.showObsolete();
  },

  showObsolete: function() {
    var style = {
      opacity: 0.9,
      position: 'absolute'
    };
    $('obsolete-totals').setStyle(style).clonePosition('totals').show();
  },

  editReglement: function(reglement_id, plage_id) {
    var url = new Url('cabinet', 'edit_reglement');
    url.addParam('reglement_id', reglement_id);
    url.requestModal(400);
    url.modalObject.observe('afterClose', Rapport.refresh.curry(plage_id));

  },
  
  addReglement: function(object_guid, emetteur, montant, mode, plage_id) {
    var url = new Url('cabinet', 'edit_reglement');
    url.addParam('reglement_id', '0');
    url.addParam('object_guid', object_guid);
    url.addParam('emetteur', emetteur);
    url.addParam('montant', montant);
    url.addParam('mode', mode);
    url.requestModal(400);
    url.modalObject.observe('afterClose', Rapport.refresh.curry(plage_id));
  }
};