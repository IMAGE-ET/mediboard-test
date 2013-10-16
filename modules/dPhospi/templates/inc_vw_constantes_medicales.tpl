{{mb_default var=simple_view value=0}}

<script type="text/javascript">
const_ids = {{$const_ids|@json}};
keys_selection = {{$custom_selection|@array_keys|@json}};
paginate = {{$paginate|@json}};

window.previousPoint = null;
window.minXIndex = {{$min_x_index}};
window.minXValue = {{$min_x_value}};
window.xTicks = null;
window.drawnConstants = {{$drawn_constants|@json}};

newConstants = function(context_guid){
  var url = new Url('dPhospi', 'httpreq_vw_form_constantes_medicales');
  url.addParam("context_guid", context_guid);
  url.addParam("selection[]", keys_selection);
  url.addParam("patient_id", '{{$patient->_id}}');
  url.requestUpdate('constantes-medicales-form', {onComplete: initCheckboxes.curry()});
}

editConstants = function(const_id, context_guid, start){
  var url = new Url('dPhospi', 'httpreq_vw_form_constantes_medicales');
  url.addParam("const_id", const_id);
  url.addParam("context_guid", context_guid);
  url.addParam("readonly", '{{$readonly}}');
  url.addParam("start", start || 0);
  url.addParam("selection[]", keys_selection);
  url.addParam("patient_id", '{{$patient->_id}}');
  url.requestUpdate('constantes-medicales-form', {onComplete: initCheckboxes.curry()});
}

plotHover = function(event, pos, item) {
  if (item) {
    var key = item.dataIndex+"-"+item.seriesIndex;
    if (previousPoint != key && item.datapoint[0] >= window.minXValue && item.datapoint[0] <= window.minXValue + 10) {
      var axis_labels = $$('.axis-onhover');
      axis_labels.each(function(item){
        item.removeClassName('axis-onhover');
      });

      var legend_labels = $$('.legend-onhover');
      legend_labels.each(function(item) {
        item.removeClassName('legend-onhover');
      });

      window.previousPoint = key;
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

      if (item.series.data[item.dataIndex].comment != null) {
        content = content + '<hr/>' + item.series.data[item.dataIndex].comment;
      }

      $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, content).setStyle({
        position: 'absolute',
        top:  top + "px",
        left: left + "px",
        opacity: 0.7,
        backgroundColor: '#000000',
        color: '#FFFFFF',
        borderRadius: '4px',
        textAlign: 'center'
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
    window.previousPoint = null;
  }
};

plotClick = function(event, pos, item) {
  if (item) {
    editConstants(item.series.data[item.dataIndex].id, '{{$context_guid}}');
  }
};

initCheckboxes = function() {
  {{foreach from=$graphs_datas key=_rank item=_graphs_for_rank}}
    {{foreach from=$_graphs_for_rank key=_graph_id item=_graph}}
      var oForm = getForm('edit-constantes-medicales');
      var checkbox;
      {{foreach from=$graphs_structure[$_rank][$_graph_id] key=_key item=_constant}}
        checkbox = oForm['checkbox-constantes-medicales-{{$_constant}}'];
        if (checkbox) {
          checkbox.addClassName('checkbox-graph-{{$_rank}}_{{$_graph_id}}');
          checkbox.checked = true;
          {{if $_key > 0}}
            checkbox.setAttribute('readonly', 1);
          {{/if}}
        }
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
}

drawGraphs = function() {
  var oPh,oDatas, oOptions, sTitle;
  {{foreach from=$graphs_datas key=_rank item=_graphs_for_rank}}
    {{foreach from=$_graphs_for_rank key=_graph_id item=_graph}}
      // Modification of the checkboxes
      var oForm = getForm('edit-constantes-medicales');
      var checkbox;
      {{foreach from=$graphs_structure[$_rank][$_graph_id] key=_key item=_constant}}
        checkbox = oForm['checkbox-constantes-medicales-{{$_constant}}'];
        if (checkbox) {
          checkbox.addClassName('checkbox-graph-{{$_rank}}_{{$_graph_id}}');
          {{if $_key > 0}}
            checkbox.setAttribute('readonly', 1);
          {{/if}}
        }
      {{/foreach}}

      var oDatas = {{$_graph.datas|@json}};
      var oOptions = {{$_graph.options|@json}};
      var cumul = {{$_graph.cumul}};
      if (window.xTicks == null) {
        window.xTicks = oOptions.xaxis.ticks;
      }

      oOptions.legend = {container: jQuery('#legend_{{$_rank}}_{{$_graph_id}}')};
      oOptions.xaxis.ticks = window.xTicks.slice(window.minXIndex, window.minXIndex + 10);
      oOptions.xaxis.min = window.minXValue - 0.5;
      oOptions.xaxis.max = window.minXValue + 9.5;

      // Deleting the datas of the bandwidth series who are not displayed, because they can appear in the yaxis space
      oDatas.each(function(serie) {
        if (serie.bandwidth) {
          var data = new Array();
          serie.data.each(function(point) {
            if (point[0] >= window.minXValue && point[0] <= window.minXValue + 10) {
              data.push(point);
            }
          });
          serie.data = data;
        }
      });

      var oPh = jQuery('#placeholder_{{$_rank}}_{{$_graph_id}}');
      oPh.bind('plothover', plotHover);
      oPh.bind('plotclick', plotClick);
      var plot = jQuery.plot(oPh, oDatas, oOptions);

      // Display the value for the cumul graphs
      if (cumul) {
        oDatas.each(function (serie) {
          if (serie.bars) {
            serie.data.each(function (data) {
              var oPoint = plot.pointOffset({x: data[0], y: data[1]});
              if (data[0] >= window.minXValue && data[0] <= window.minXValue + 10) {
                oPh.append('<div style="position: absolute; left:' + (oPoint.left + 5) + 'px; top: ' + (oPoint.top + 5) + 'px; font-size: smaller">' + data[1] + '</div>');
              }
            });
          }
        });
      }

      // Make the labels of the xaxis clickable
      $$('#placeholder_{{$_rank}}_{{$_graph_id}} .x1Axis .tickLabel').each(function(item) {
        item.style.zIndex = 1000;
      });
    {{/foreach}}
  {{/foreach}}
};

toggleGraph = function(checkbox) {
  var className = $w(checkbox.className)[1];

  var checkboxes = $$('.' + className);
  checkboxes.each(function(cb) {
    cb.checked = checkbox.checked;
  });
  var id_graph = className.substring(15);
  var row = $('graph_row_' + id_graph);
  row.setVisible(checkbox.checked);
};

shiftGraphs = function(direction) {
  var offset = 5;
  window.minXIndex += {before: -offset, after: +offset}[direction];

  if (window.minXIndex < 0) {
    window.minXIndex = 0;
  }
  var actualLength = window.xTicks.length - window.minXIndex;
  if (window.xTicks.length > 10 && actualLength < 10) {
    window.minXIndex -= 10 - actualLength;
  }

  window.minXValue = window.xTicks[window.minXIndex][0];
  drawGraphs();
};

Main.add(function () {
  var oForm = getForm('edit-constantes-medicales');
  var checkbox;

  drawnConstants.each(function(constant) {
    checkbox = oForm['checkbox-constantes-medicales-' + constant];
    if (checkbox) {
      checkbox.checked = true;
    }
  });

  drawGraphs();
  ViewPort.SetAvlHeight('graphs', 1.0);
});

loadConstantesMedicales  = function(context_guid) {
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales"),
      container = $("constantes-medicales") || $("constantes") || $("Constantes")|| $("constantes-tri"); // case sensitive ?

  url.addParam("context_guid", '{{$context_guid}}');
  url.addParam("patient_id", '{{$patient->_id}}');
  url.addParam("readonly", '{{$readonly}}');
  url.addParam("selection[]", keys_selection);
  url.addParam("selected_context_guid", context_guid);
  url.addParam("paginate", window.paginate || 0);
  url.requestUpdate(container);
};

refreshFiches = function(sejour_id){
  var url = new Url("soins", "ajax_vw_fiches");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("tab-fiches");
}
</script>

<table class="tbl" {{if $print || $simple_view}}style="display: none;"{{/if}}>
  <tr>
    <th colspan="10" class="title">
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
      </a>
      Constantes médicales dans le cadre de: 
      <br />
      {{if $readonly}}
        {{$context}}
      {{else}}
        <select name="context" onchange="loadConstantesMedicales($V(this));">
          <option value="all" {{if $all_contexts}}selected="selected"{{/if}}>Tous les contextes</option> 
          {{foreach from=$list_contexts item=curr_context}}
            <option value="{{$curr_context->_guid}}" 
            {{if !$all_contexts && $curr_context->_guid == $context->_guid}}selected="selected"{{/if}}
            {{if !$all_contexts && $curr_context->_guid == $context_guid}}style="font-weight:bold;"{{/if}}
            >{{$curr_context}}</option>
          {{/foreach}}
        </select>
      {{/if}}
    </th>
  </tr>
  <tr>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=poids}}:
      {{if $patient->_ref_constantes_medicales->poids}}
        {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
      {{else}}??{{/if}}
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient field=naissance}}: 
      {{mb_value object=$patient field=naissance}} ({{$patient->_age}})
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=taille}}:
      {{if $patient->_ref_constantes_medicales->taille}}
        {{mb_value object=$patient->_ref_constantes_medicales field=taille}} cm
      {{else}}??{{/if}}
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=_imc}}:
      {{if $patient->_ref_constantes_medicales->_imc}}
        {{mb_value object=$patient->_ref_constantes_medicales field=_imc}}
      {{else}}??{{/if}}
    </td>
  </tr>
</table>

{{if $print}}
  <div id="constantes-medicales-graph" style="text-align: center;"></div>
{{else}}
   <script type="text/javascript">
     Main.add(function(){
      Control.Tabs.create("surveillance-tab");
     });
   </script>

    <ul class="control_tabs" id="surveillance-tab">
      <li>
        <a href="#tab-constantes-medicales">Constantes</a>
      </li>
      
      {{if $context instanceof CSejour && !$simple_view}}
        {{if "forms"|module_active}}
          <li>
            <a href="#tab-ex_class-list" onmousedown="this.onmousedown=null; ExObject.loadExObjects('{{$context->_class}}', '{{$context->_id}}', 'tab-ex_class-list', 0)">
              Formulaires
            </a>
          </li>
        {{/if}}
        
        <li>
          <a href="#tab-fiches" onmousedown="refreshFiches('{{$context->_id}}');">Fiches</a>
        </li>
      {{/if}}
    </ul>
    <hr class="control_tabs" />
      
  <table class="main" id="tab-constantes-medicales">
    <tr>
      <td class="narrow" id="constantes-medicales-form">
        {{include file="inc_form_edit_constantes_medicales.tpl" context_guid=$context_guid}}
      </td>
      <td>
        {{unique_id var=uniq_id_constantes}}
         
        <script type="text/javascript">
          Main.add(function(){
            Control.Tabs.create("tabs-constantes-graph-table", true);
          });
        </script>
        
        {{* {{if $paginate}}
          {{mb_include module=system template=inc_pagination total=$total_constantes current=$start change_page="changePage$uniq_id_constantes" step=$count}}
        {{/if}} *}}
        
        <ul class="control_tabs small" id="tabs-constantes-graph-table">
          <li><a href="#constantes-graph">Graphiques</a></li>
          <li><a href="#constantes-table">Tableau</a></li>
          {{if $paginate}}
            <li>
              <label>
                Afficher les 
                <select name="count_constantes" onchange="refreshConstantesMedicales('{{$context_guid}}', 1, $V(this))" style="margin: 0;">
                  {{assign var=_counts value=","|@explode:"50,100,200,500"}}
                  {{foreach from=$_counts item=_count}}
                    <option value="{{$_count}}" {{if $count == $_count}} selected {{/if}}>{{$_count}}</option>
                  {{/foreach}}
                </select>
                derniers ({{$total_constantes}} au total)
              </label>
            </li>
          {{/if}}
        </ul>
        <hr class="control_tabs" />
        
        {{if $const_ids|@count == $count}}
          <div class="small-warning">
            Le nombre de constantes affichées est limité à {{$count}}.
          </div>
        {{/if}}
          
        <div id="constantes-graph">
          <button class="hslip notext" style="float: left;" title="Afficher/Cacher le formulaire" onclick="$('constantes-medicales-form').toggle();" type="button">
            Formulaire constantes
          </button>
          
          <button id="constantes-medicales-graph-before" class="left" style="float: left;"       onclick="shiftGraphs('before');">Avant</button>
          <button id="constantes-medicales-graph-after"  class="right rtl" style="float: right;" onclick="shiftGraphs('after');">Après</button>
          <br/>

          <div id="graphs">
            <table class="layout">
              {{foreach from=$graphs_datas key=_rank item=_graphs_for_rank}}
                {{foreach from=$_graphs_for_rank key=_graph_id item=_graph}}
                  <tr id="graph_row_{{$_rank}}_{{$_graph_id}}">
                    <td>
                      <p style="text-align: center"><strong>{{$_graph.title}}</strong></p>
                      <div id="placeholder_{{$_rank}}_{{$_graph_id}}" style="width: {{$_graph.width}}px; height: 200px; margin-top: 10px; margin-bottom: 10px; margin-left: {{$_graph.margin_left}}px"></div>
                    </td>
                    <td>
                      <div id="legend_{{$_rank}}_{{$_graph_id}}" style="margin-top: 30px; width: 15em"></div>
                    </td>
                  </tr>
                {{/foreach}}
              {{/foreach}}
            </table>
          </div>
        </div>
        
        <div id="constantes-table" style="display: none; text-align: left;">
          <button class="change" onclick="$('constantes-table').down('.contantes-horizontal').toggle(); $('constantes-table').down('.contantes-vertical').toggle()">
            Changer l'orientation
          </button>
          
          <div class="contantes-horizontal" style="display: none;">
          {{mb_include module=patients template=print_constantes}}
          </div>
          <div class="contantes-vertical">
          {{mb_include module=patients template=print_constantes_vert}}
          </div>
        </div>
      </td>
    </tr>
  </table>
  
  <div id="tab-ex_class-list" style="display: none;"></div>
  <div id="tab-fiches" style="display: none;"></div>
{{/if}}