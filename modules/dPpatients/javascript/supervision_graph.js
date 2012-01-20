SupervisionGraph = {
  currentGraphId: null,
  
  // Axes
  listAxes: function(graph_id) {
    SupervisionGraph.currentGraphId = graph_id;
      
    var url = new Url("dPpatients", "ajax_list_supervision_graph_axes");
    url.addParam("supervision_graph_id", graph_id);
    url.requestUpdate("supervision-graph-axes-list");
    
    SupervisionGraph.preview(SupervisionGraph.currentGraphId);
  },
  editAxis: function(id, graph_id) {
    
    var url = new Url("dPpatients", "ajax_edit_supervision_graph_axis");
    url.addParam("supervision_graph_axis_id", id);
    
    if (graph_id) {
      SupervisionGraph.currentGraphId = graph_id;
      url.addParam("supervision_graph_id", graph_id);
    }
    
    url.requestUpdate("supervision-graph-axis-editor");
    
    return false;
  },
  callbackEditAxis: function(id, obj) {
    SupervisionGraph.listAxes(obj.supervision_graph_id);
    SupervisionGraph.editAxis(id, obj.supervision_graph_id);
    SupervisionGraph.preview(SupervisionGraph.currentGraphId);
  },
  
  // Series
  listSeries: function(axis_id) {
    var url = new Url("dPpatients", "ajax_list_supervision_graph_series");
    url.addParam("supervision_graph_axis_id", axis_id);
    url.requestUpdate("supervision-graph-series-list");
  },
  editSeries: function(id, axis_id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_graph_series");
    url.addParam("supervision_graph_series_id", id);
    
    if (axis_id) {
      url.addParam("supervision_graph_axis_id", axis_id);
    }
    
    url.requestModal(400, 300);
    
    return false;
  },
  callbackEditSeries: function(id, obj) {
    SupervisionGraph.listSeries(obj.supervision_graph_axis_id);
    SupervisionGraph.listAxes(SupervisionGraph.currentGraphId);
    Control.Modal.close();
    SupervisionGraph.editSeries(id, obj.supervision_graph_axis_id);
  }, 
  
  preview: function(graph_id) {
    if (!graph_id) {
      return;
    }
    
    var url = new Url("dPpatients", "ajax_preview_supervision_graph");
    url.addParam("supervision_graph_id", graph_id);
    url.requestUpdate("supervision-graph-preview");
  }
};
