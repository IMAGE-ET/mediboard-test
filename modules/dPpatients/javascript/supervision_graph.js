SupervisionGraph = {
  currentGraphId: null,

  editGraph: function(id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_graph");
    url.addParam("supervision_graph_id", id);
    url.requestUpdate("supervision-graph-editor");
    return false;
  },

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
  editTimedData: function(id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_timed_data");
    url.addParam("supervision_timed_data_id", id);
    url.requestUpdate("supervision-graph-editor");

    return false;
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
  },

  editPack: function(id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_graph_pack");
    url.addParam("supervision_graph_pack_id", id);
    url.requestUpdate("supervision-graph-editor");
    return false;
  },

  editGraphToPack: function(id, pack_id, graph_class) {
    var url = new Url("dPpatients", "ajax_edit_supervision_graph_to_pack");
    url.addParam("graph_class", graph_class);

    if (id) {
      url.addParam("supervision_graph_to_pack_id", id);
    }

    if (pack_id) {
      url.addParam("supervision_graph_pack_id", pack_id);
    }
    url.requestModal(600, 600);
    return false;
  },

  listGraphToPack: function(pack_id) {
    var url = new Url("dPpatients", "ajax_list_supervision_graph_to_pack");
    url.addParam("supervision_graph_pack_id", pack_id);
    url.requestUpdate("graph-to-pack-list");
    return false;
  },

  graphToPackCallback: function(id, obj) {
    Control.Modal.close();
    SupervisionGraph.listGraphToPack(obj.pack_id);
  },

  /**
   * Default series
   */
  defaultSeries: {
    points: {
      show: true,
      radius: 3
    },
    bandwidth: {
      active: true,
      drawBandwidth: function(ctx,bandwidth, x,y1,y2,color,isOverlay) {
        ctx.beginPath();
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.moveTo(x, y1);
        ctx.lineTo(x, y2);
        ctx.stroke();
        ctx.beginPath();
        if (isOverlay) {
          ctx.strokeStyle = "rgba(255,255,255," + bandwidth.highlight.opacity + ")";
        }
        else {
          ctx.strokeStyle = color;
        }
        ctx.lineWidth = 10;
        ctx.moveTo(x, y1 - 1);
        ctx.lineTo(x, y1 + 1);
        ctx.moveTo(x, y2 - 1);
        ctx.lineTo(x, y2 + 1);
        ctx.stroke();
      }
    }
  }
};
