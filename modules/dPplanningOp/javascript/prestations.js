// $Id: $

Prestations = {
  callback: null,
  urlPresta: null,

  edit: function(sejour_id, context) {
    var url = new Url('planningOp', 'ajax_vw_prestations');
    url.addParam('sejour_id', sejour_id);
    if (context) {
      url.addParam('context', context);
    }
    Prestations.urlPresta = url.requestModal(800, 600, {
      onClose: Prestations.refreshAfterEdit,
      showReload: true
    });

  },

  refreshAfterEdit : function() {
    if (window.refreshMouvements) {
      refreshMouvements();
    }
    if (window.Placement && window.Placement.loadTableau) {
      Placement.loadTableau();
    }
  }
};