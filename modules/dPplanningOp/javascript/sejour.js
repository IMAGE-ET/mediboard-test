// $Id: plage_selector.js 6447 2009-06-22 08:11:48Z phenxdesign $

Sejour = {
  edit: function(sejour_id) {
    new Url("dPplanningOp", "vw_edit_sejour", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  },
  admission: function(date) {
    new Url("dPadmissions", "vw_idx_admission", "tab").
      addParam("date", date).
      redirectOpener();
  },
  showSSR: function(sejour_id) {
    new Url("ssr", "vw_aed_sejour_ssr", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  },
  showUrgences: function(sejour_id) {
    new Url("dPurgences", "vw_aed_rpu", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  }
};
