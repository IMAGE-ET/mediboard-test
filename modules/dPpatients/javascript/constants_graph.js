/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

ConstantsGraph = Class.create({
  displayMode: null,
  displayTime: null,
  previousPoint: null,
  contextGuid: null,
  minXIndex: null,
  minXValue: null,
  maxXValue: null,
  hiddenGraphs: null,
  xTicks: null,
  graphsStructure: null,
  graphsData: null,
  widget: null,

  initialize: function(graphs_data, min_x_index, min_x_value, widget, context_guid, display_mode, display_time, hidden_graphs, graphs_structure) {
    this.graphsData = graphs_data;
    this.widget = widget;
    if (context_guid) {
      this.contextGuid = context_guid;
    }
    if (display_mode) {
      this.displayMode = display_mode;
    }
    if (display_time) {
      this.displayTime = display_time;
    }
    if (min_x_index) {
      this.minXIndex = min_x_index;
    }
    if (min_x_value) {
      this.minXValue = min_x_value;
    }
    if (hidden_graphs) {
      this.hiddenGraphs = hidden_graphs;
    }
    if (graphs_structure) {
      this.graphsStructure = graphs_structure;
    }

    if (!this.widget) {
      this.initCheckboxes();
    }
  },

  initCheckboxes: function() {
    for (var rank in this.graphsStructure) {
      var graphs = this.graphsStructure[rank];
      if (graphs) {
        for (var id = 0; id < graphs.length; id++) {
          var oForm = getForm('edit-constantes-medicales');
          var graph = graphs[id];
          if (graph) {
            for (var i = 0; i < graph.length; i++) {
              var constant = graph[i];
              var checkbox = oForm['checkbox-constantes-medicales-' + constant];
              if (checkbox) {
                checkbox.setAttribute('data-graph_id', rank + '_' + id);
                checkbox.addClassName('checkbox-drawn-graph');
                if (this.hiddenGraphs) {
                  if (this.hiddenGraphs.indexOf(rank + '_' + id) == -1) {
                    checkbox.checked = true;
                  }
                }
                else {
                  checkbox.checked = true;
                }
                if (i > 0) {
                  checkbox.setAttribute('readonly', 1);
                }
              }
            }
          }
        }
      }
    }
  },

  plotHover: function(event, pos, item) {
    if (item) {
      var key = item.dataIndex+"-"+item.seriesIndex;
      if (window.oGraphs.previousPoint != key && item.datapoint[0] >= window.oGraphs.minXValue && item.datapoint[0] <= window.oGraphs.maxXValue) {
        var axis_labels = $$('.axis-onhover');

        axis_labels.each(function(item) {
          item.removeClassName('axis-onhover');
        });

        var legend_labels = $$('.legend-onhover');
        legend_labels.each(function(item) {
          item.removeClassName('legend-onhover');
        });

        this.previousPoint = key;
        jQuery("#flot-tooltip").remove();
        var oPh = $(event.target.id);
        var top = item.pageY;
        var left;
        if (item.pageX < oPh.offsetLeft) {
          left = oPh.offsetLeft + 30;
        }
        else {
          left = item.pageX - 15;
        }

        var content = item.series.data[item.dataIndex].date;
        if (item.series.data[item.dataIndex].hour != null) {
          content = content + ' ' + item.series.data[item.dataIndex].hour;
        }

        content = content + "<br /><strong>" + item.datapoint[1];
        if (item.series.bandwidth.show) {
          content = content + "/" + item.series.data[item.dataIndex][2];
        }
        content = content + " " + item.series.unit;

        if (item.series.data[item.dataIndex].users != null) {
          content = content + "</strong>";
          item.series.data[item.dataIndex].users.each(function(user) {
            content = content + "<br />" + user;
          });
        }

        if (item.series.data[item.dataIndex].context != null) {
          content = content + '<br/>' + item.series.data[item.dataIndex].context;
        }

        if (item.series.data[item.dataIndex].comment != null) {
          content = content + '<hr/>' + item.series.data[item.dataIndex].comment;
        }

        $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, content).setStyle({
          position: 'absolute',
          top:  top + "px",
          left: left + "px",
          opacity: 0.8,
          backgroundColor: '#000000',
          color: '#FFFFFF',
          borderRadius: '4px',
          textAlign: 'center',
          maxWidth: '300px',
          whiteSpace: 'normal'
        }));

        var yaxis_labels = $$('#' + event.target.id + ' .flot-text .y' + item.series.yaxis.n + 'Axis .tickLabel');
        yaxis_labels.each(function (item) {
          item.addClassName('axis-onhover');
        });

        var legend_labels = $$('#legend' + event.target.id.substring(11) + ' td.legendLabel');
        var i = item.seriesIndex;

        if (item.series.bars.show) {
          i = 0;
        }
        if (i >= legend_labels.length) {
          i = legend_labels.length - 1;
        }
        legend_labels[i].addClassName('legend-onhover');
      }
    }
    else {
      var axis_labels = $$('.axis-onhover');
      axis_labels.each(function(item){
        item.removeClassName('axis-onhover');
      });

      $$('.legend-onhover').invoke('removeClassName', 'legend-onhover');


      jQuery("#flot-tooltip").remove();
      window.oGraphs.previousPoint = null;
    }
  },

  plotClick: function(event, pos, item) {

    if (item) {
      var context_guid = window.oGraphs.contextGuid;
      if (item.series.data[item.dataIndex].context_guid) {
        context_guid = item.series.data[item.dataIndex].context_guid;
      }
      editConstants(item.series.data[item.dataIndex].id, context_guid);
    }
  },

  shift: function(direction) {
    if (this.displayMode == 'time') {
      this.shiftTimeMode(direction);
    }
    else {
      this.shiftClassicMode(direction);
    }
    this.draw();
  },

  shiftClassicMode: function(direction) {
    var offset = 5;
    this.minXIndex += {before: -offset, after: +offset}[direction];

    if (this.minXIndex < 0) {
      this.minXIndex = 0;
    }
    var actualLength = this.xTicks.length - this.minXIndex;
    if (this.xTicks.length > 15 && actualLength < 15) {
      this.minXIndex -= 15 - actualLength;
    }
    this.minXValue = this.xTicks[this.minXIndex][0] - 0.5;
    this.maxXValue = this.minXValue + 15;
  },

  shiftTimeMode: function(direction) {
    var step = this.displayTime * 3600000;
    this.minXValue += {before: -step, after: +step}[direction];
    this.maxXValue += {before: -step, after: +step}[direction];
  },

  toggle: function(checkbox) {
    var graph_id = checkbox.get('graph_id');
    if (graph_id) {
      var checkboxes = $$('form[name=edit-constantes-medicales] input[data-graph_id=' + graph_id + '].checkbox-drawn-graph');
      checkboxes.each(function(cb) {
        cb.checked = checkbox.checked;
      });
      var row = $('graph_row_' + graph_id);
      row.setVisible(checkbox.checked);
    }
  },

  draw: function() {
    var graphs_data = JSON.parse(Object.toJSON(this.graphsData));
    for (var rank in graphs_data) {
      var graphs = graphs_data[rank];
      if (graphs) {
        if (!this.widget) {
          for (var id = 0; id < graphs.length; id++) {
            var graph = graphs[id];
            if (graph) {
              this.drawGraph(graph, rank, id);
              if (this.hiddenGraphs && this.hiddenGraphs.indexOf(rank + '_' + id) != -1) {
                $('graph_row_' + rank + '_' + id).hide();
              }
            }
          }
        }
        else {
          if (graphs.datas) {
            this.drawGraph(graphs, rank);
          }
        }
      }
    }
  },

  drawGraph: function(graph, rank, id) {
    var oDatas = graph.datas;
    var oOptions = graph.options;

    if (this.xTicks == null) {
      this.xTicks = oOptions.xaxis.ticks;
    }
    if (this.maxXValue == null) {
      this.maxXValue = oOptions.xaxis.max;
    }
    else {
      oOptions.xaxis.min = this.minXValue;
      oOptions.xaxis.max = this.maxXValue;
    }

    if (this.displayMode == 'classic') {
      oOptions.xaxis.ticks = this.xTicks.slice(this.minXIndex, this.minXIndex + 15);
    }

    // Deleting the datas of the bandwidth series who are not displayed, because they can appear in the yaxis space
    oDatas.each(function(serie, key) {
      if (serie.bandwidth) {
        var data = new Array();
        serie.data.each(function(point) {
          if (point[0] >= this.minXValue && point[0] <= this.maxXValue) {
            data.push(point);
          }
        }.bind(this));
        serie.data = data;
      }
    }.bind(this));

    var oPh;
    if (!this.widget) {
      oOptions.legend = {container: jQuery('#legend_' + rank + '_' + id)};
      oPh = jQuery('#placeholder_' + rank + '_' + id);
    }
    else {
      oOptions.legend = {container: jQuery('#legend_' + rank)};
      oPh = jQuery('#placeholder_' + rank);
    }

    oPh.bind('plothover', window.oGraphs.plotHover);
    oPh.bind('plotclick', window.oGraphs.plotClick);
    var plot = jQuery.plot(oPh, oDatas, oOptions);

    oDatas.each(function (serie) {
      if (serie.bars) {
        serie.data.each(function (data) {
          if (data[1] > 0) {
            if (data[0] >= this.minXValue && data[0] < this.maxXValue) {
              var oPoint = plot.pointOffset({x: data[0], y: data[1]});
              oPh.append('<div style="position: absolute; left:' + (oPoint.left + 5) + 'px; top: ' + (oPoint.top + 5) + 'px; font-size: smaller">' + data[1] + '</div>');
            }
            if (data[0] < this.minXValue && (data[0] + data.barWidth) >= this.minXValue && (data[0] + data.barWidth) < this.maxXValue) {
              var oPoint = plot.pointOffset({x: data[0] + data.barWidth, y: data[1]});
              oPh.append('<div style="position: absolute; left:' + (oPoint.left - 15) + 'px; top: ' + (oPoint.top + 5) + 'px; font-size: smaller">' + data[1] + '</div>');
            }
            if (data[0] < this.minXValue && (data[0] + data.barWidth) >= this.maxXValue) {
              var xPos;
              if (this.displayMode == 'time') {
                xPos = this.minXValue + this.displayTime * 3600000;
              }
              else {
                xPos = this.minXValue + 7.5;
              }
              var oPoint = plot.pointOffset({x: xPos, y: data[1]});
              oPh.append('<div style="position: absolute; left:' + (oPoint.left) + 'px; top: ' + (oPoint.top + 5) + 'px; font-size: smaller">' + data[1] + '</div>');
            }
          }
        }.bind(this));
      }
      else if(serie.bandwidth) {
        serie.data.each(function (data) {
          var max = Math.max(data[1], data[2]);
          var min = Math.min(data[1], data[2]);
          var oPointMax = plot.pointOffset({x: data[0], y: max, yaxis: serie.yaxis});
          var oPointMin = plot.pointOffset({x: data[0], y: min, yaxis: serie.yaxis});

          oPh.append('<div style="position: absolute; left: ' + (oPointMax.left - 8) + 'px; top: ' + (oPointMax.top - 15) + 'px; font-size: smaller">' + max + '</div>');
          oPh.append('<div style="position: absolute; left: ' + (oPointMin.left - 8) + 'px; top: ' + (oPointMin.top + 5) + 'px; font-size: smaller">' + min + '</div>');
        }.bind(this));
      }
    }.bind(this));

    // Make the labels of the xaxis clickable
    $$('#placeholder_' + rank + '_' + id + ' .x1Axis .tickLabel').each(function(item) {
      item.style.zIndex = 10;
    });
  },

  getHiddenGraphs: function() {
    var checkboxes = $$('form[name=edit-constantes-medicales] input[type=checkbox].checkbox-drawn-graph:not(:checked)');
    this.hiddenGraphs = [];
    for (var i = 0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      if (this.hiddenGraphs.indexOf(checkbox.get('graph_id')) == -1) {
        this.hiddenGraphs.push(checkbox.get('graph_id'));
      }
    }

    return this.hiddenGraphs;
  }
});