SupervisionGraph = {
  currentGraphId: null,

  list: function(callback) {
    var url = new Url("dPpatients", "ajax_list_supervision");
    url.requestUpdate("supervision-list", {
      onComplete: callback ? callback : function(){}
    });
  },

  editGraph: function(id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_graph");
    url.addParam("supervision_graph_id", id);
    url.requestUpdate("supervision-graph-editor");
    return false;
  },

  callbackEditGraph: function(id) {
    SupervisionGraph.list(function(){
      SupervisionGraph.editGraph(id);
    });
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

  callbackEditTimedData: function(id) {
    SupervisionGraph.list(function(){
      SupervisionGraph.editTimedData(id);
    });
  },

  editTimedPicture: function(id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_timed_picture");
    url.addParam("supervision_timed_picture_id", id);
    url.requestUpdate("supervision-graph-editor");

    return false;
  },

  callbackEditTimedPicture: function(id) {
    SupervisionGraph.list(function(){
      SupervisionGraph.editTimedPicture(id);
    });
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
  },

  // Axis labels
  listAxisLabels: function(axis_id) {
    var url = new Url("dPpatients", "ajax_list_supervision_graph_axis_labels");
    url.addParam("supervision_graph_axis_id", axis_id);
    url.requestUpdate("supervision-graph-axis-labels-list");
  },
  editAxisLabel: function(id, axis_id) {
    var url = new Url("dPpatients", "ajax_edit_supervision_graph_axis_label");
    url.addParam("supervision_graph_axis_label_id", id);

    if (axis_id) {
      url.addParam("supervision_graph_axis_id", axis_id);
    }

    url.requestModal(400, 300);

    return false;
  },
  callbackAxisLabel: function(id, obj) {
    SupervisionGraph.listAxisLabels(obj.supervision_graph_axis_id);
    Control.Modal.close();
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
  callbackEditPack: function(id) {
    SupervisionGraph.list(function(){
      SupervisionGraph.editPack(id);
    });
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
    url.requestModal(400, 400);
    return false;
  },

  listGraphToPack: function(pack_id) {
    if (!pack_id) {
      return;
    }
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
        var width = 2;
        var offset = width / 2;

        ctx.beginPath();
        ctx.strokeStyle = color;
        ctx.lineWidth = width;
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
        ctx.moveTo(x, y1 - offset);
        ctx.lineTo(x, y1 + offset);
        ctx.moveTo(x, y2 - offset);
        ctx.lineTo(x, y2 + offset);
        ctx.stroke();
      }
    }
  },

  formatTrack: function(item) {
    var x = item.datapoint[0],
        y = item.datapoint[1];

    if (item.series.bandwidth && item.series.bandwidth.show) {
      y += " / " + item.series.data[item.dataIndex][2];
    }

    var date = new Date();
    date.setTime(x);

    var label = y+" "+item.series.unit;
    var point = item.series.data[item.dataIndex];
    if (point.label) {
      label = point.label;
    }

    var d = printf(
      "%02d/%02d %02d:%02d:%02d",
      date.getUTCDate(),
      date.getUTCMonth()+1,
      date.getUTCHours(),
      date.getUTCMinutes(),
      date.getUTCSeconds()
    );

    return  "<big style='font-weight:bold'>#{value}</big><hr />#{label}<br />#{date}<br />#{user}".interpolate({
      value: label,
      label: item.series.label,
      date: d,
      user: point.user
    });
  }
};
