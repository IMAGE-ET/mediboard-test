
PlanningSejour = {
  callback: null,
  url: null,
  modal: null,

  view: function(sejour_id, date) {
    date = date || '';
    var url = new Url('planningOp', 'ajax_vw_planning_sejour');
    url.addParam('sejour_id', sejour_id);
    url.addParam('debut'    , date);
    PlanningSejour.url = url.requestModal("80%", "100%");
  },

  changeDate: function(form) {
    var url = new Url('planningOp', 'ajax_vw_planning_sejour');
    url.addParam('sejour_id', $V(form.sejour_id));
    url.addParam('debut'    , $V(form.debut));
    url.requestUpdate("planning-CSejour-"+$V(form.sejour_id));
    return false;
  }
};