var DisplayGraph = {
  filterForm : null,
  lastUrl : null,

  getFilterForm : function() {
    DisplayGraph.filterForm = getForm("stats_params")
  },

  launchStats : function(type_graph) {
    DisplayGraph.getFilterForm();
    var url = new Url("stats", "vw_graph_std");
    url.addParam("type_graph", type_graph);
    this.addFiltersParam(url);
    url.requestModal();
    DisplayGraph.lastUrl =  url;
  },

  addFiltersParam: function(url) {
  }

};