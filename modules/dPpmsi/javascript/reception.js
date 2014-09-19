
Reception = {
  form: null,

  showLegend: function() {
    new Url("pmsi", "vw_legende").requestModal();
  },

  toggleMultipleServices: function(elt) {
    var status = elt.checked;
    var form = elt.form;
    var elt_service_id = form.service_id;
    elt_service_id.multiple = status;
    elt_service_id.size = status ? 5 : 1;
  },

  reloadAll: function() {
    Reception.reloadMonthSejours();
    Reception.reloadListSejours();
  },


  reloadMonthSejours: function() {
    var form = getForm(Reception.form);
    var url = new Url("pmsi" , "vw_recept_month_sejour");
    url.addParam("date"      , $V(form.date));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("prat_id"   , $V(form.prat_id));
    url.addParam("filterFunction" , $V(form.filterFunction));
    url.addParam("order_col" , $V(form.order_col));
    url.addParam("order_way" , $V(form.order_way));
    url.addParam("tri_recept"  , $V(form.tri_recept));
    url.addParam("tri_complet" , $V(form.tri_complet));
    url.addParam("period"    , $V(form.period));
    url.requestUpdate('allSejours');
  },

  reloadListSejours: function() {
    var form = getForm(Reception.form);
    var url = new Url("pmsi", "vw_recept_list_sejour");
    url.addParam("date"      , $V(form.date));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("prat_id"   , $V(form.prat_id));
    url.addParam("filterFunction" , $V(form.filterFunction));
    url.addParam("order_col" , $V(form.order_col));
    url.addParam("order_way" , $V(form.order_way));
    url.addParam("tri_recept" , $V(form.tri_recept));
    url.addParam("tri_complet", $V(form.tri_complet));
    url.addParam("period"    , $V(form.period));
    url.requestUpdate('listSejours');
  },

  filterSortie: function(tri_recept, tri_complet) {
    var form = getForm(Reception.form);
    $V(form.tri_recept  , tri_recept);
    $V(form.tri_complet , tri_complet);
    Reception.reloadAll();
  },

  filter: function(input, table) {
    table = $(table);
    table.select("tr").invoke("show");

    var term = $V(input);
    if (!term) return;

    table.select(".CPatient-view").each(function(e) {
      if (!e.innerHTML.like(term)) {
        e.up("tr").hide();
      }
    });
  },

  reloadSortieDate: function(elt, date) {
    var form = getForm(Reception.form);
    $V(form.date, date);
    var old_selected = elt.up("table").down("tr.selected");
    old_selected.removeClassName("selected");
    var elt_tr = elt.up("tr");
    elt_tr.addClassName("selected");
    Reception.reloadListSejours();
  },

  subitEtatPmsi: function(form, sejour_id) {
    return onSubmitFormAjax(form, function() {
      var url = new Url("pmsi", "ajax_recept_sejour");
      url.addParam("sejour_id", sejour_id);
      url.requestUpdate('CSejour-'+sejour_id);
      Reception.reloadMonthSejours();
    });
  }
};