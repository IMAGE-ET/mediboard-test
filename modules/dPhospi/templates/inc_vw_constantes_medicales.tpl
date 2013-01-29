{{mb_default var=simple_view value=0}}

<script type="text/javascript">  
g = [];
data = {{$data|@json}};
dates = {{$dates|@json}};
hours = {{$hours|@json}};
comments = {{$comments|@json}};
const_ids = {{$const_ids|@json}};
keys_selection = {{$selection|@array_keys|@json}};
last_date = null;
paginate = {{$paginate|@json}};

var xRange = {min: -0.5, max: Math.max(dates.length, 14)-0.5};
var xOffset = ((xRange.max+0.5) - 14);
window.globalXOffset = 0;

if (xOffset > 0) {
  xRange.min += xOffset;
}

submitConstantesMedicales = function(oForm) {
  return onSubmitFormAjax(oForm, {
    onComplete: function () {
      refreshConstantesMedicales($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
    }
  });
}

editConstantes = function (const_id, context_guid, start){
  var url = new Url('dPhospi', 'httpreq_vw_form_constantes_medicales');
  url.addParam("const_id", const_id);
  url.addParam("context_guid", context_guid);
  url.addParam("readonly", '{{$readonly}}');
  url.addParam("start", start || 0);
  url.addParam("selection[]", keys_selection);
  url.requestUpdate('constantes-medicales-form');
}

insertGraph = function (container, data, id, width, height) {
  var element = $(id);
  
  if (!element) {
    container.insert('<div id="'+id+'" style="width:'+width+';height:'+height+';margin:auto;" class="constantes-graph"></div>');
    element = $(id);
  }
  
  last_date = null;
  
  var o = Flotr.clone(data.options);
  o.xaxis.min += window.globalXOffset;
  o.xaxis.max += window.globalXOffset;
  
  return Flotr.draw(element, data.series, o);
}

tickFormatter = function (n) {
  n = parseInt(n);
  if (!const_ids[n]) {
    return "";
  }
  
  var comment = comments[n];
  
  var s = '<a href="#1" '+(comment ? (' title="'+comment+'" style="color:red;font-weight:bold;" ') : '')+
          ' onclick="editConstantes('+const_ids[n]+', \'{{$context_guid}}\')">';
  
  if (dates[n] && dates[n] == last_date) {
    s += '<strong>'+hours[n]+'</strong>';
  } else if (dates[n] && hours[n]) {
    s += '<strong>'+hours[n]+'</strong><br />'+dates[n].substr(0, 5)+(new Array(50).join(" ")); // argh, flotr checks the length of the longest string, not the biggest element
    last_date = dates[n];
  }
  
  return s+'</a>';
};

yTickFormatter = function (n) {
  return "<span style='display: inline-block; width: 3em;'>"+n+"</span>";
};

trackFormatter = function (obj) {
  var n = parseInt(obj.x);
  var data = obj.series.data[obj.index];
  var unit = (data[7] || "");
  
  if ("cumul" in obj.series) {
    return obj.series.cumul + "<br /><strong>" + data[1] + " " + unit + "</strong>";
  }
  
  var str = dates[n] + ' ' + hours[n] + "<br /><strong>" + data[1] + " " + unit + "</strong><br />" + data[5];
         
  if (data[6]) {
    str += '<hr />' + data[6];
  }
  
  str += '<hr /><button class="edit" onclick="editConstantes('+const_ids[n]+', \'{{$context_guid}}\')">{{tr}}Edit{{/tr}}</button>';
  
  return str;
};

shiftGraphs = function(side){
  var offset = 5;
  window.globalXOffset += {before: -offset, after: +offset}[side]; 
  last_date = null; 
  drawGraph(); 
  toggleAllGraphs();
}

initializeGraph = function (src, data) {
  src.options = {
    title: src.options.title || '',
    xaxis: src.options.xaxis || {},
    yaxis: src.options.yaxis || {},
    mouse: src.options.mouse || {},
    points: src.options.points || {},
    lines: src.options.lines || {},
    grid: src.options.grid || {},
    legend: src.options.legend || {},
    colors: src.options.colors || {},
    selection: src.options.selection || {}
  };

  Flotr.merge(data, src.options);
  
  // Suppression des valeurs Y nulles
  src.series.each(function(serie) {
    serie.data = serie.data.reject(
      function(v) {return v[1] == null;}
    );
  });
  
  // Ajout de la ligne de niveau standard
  if (src.standard) {
    src.series.unshift({
      data: [[-1000, src.standard], [1000, src.standard]], 
      points: {show: false},
      markers: {show: false},
      mouse: {track: false},
      lines: {lineWidth: 1},
      color: "silver"
    });
  }
}

// Default options for the graphs
options = {
  mouse: {
    track: true,
    trackFormatter: trackFormatter,
    trackDecimals: 2,
    position: 'nw',
    relative: true
  },
  xaxis: {
    noTicks: 12,
    tickDecimals: 1,
    ticks: false,
    tickFormatter: tickFormatter,
    min: xRange.min,
    max: xRange.max
  },
  yaxis: {
    tickFormatter: yTickFormatter
  },
  points: {
    show: true
  },
  lines: {
    show: true
  },
  candles: {
    upFillColor: '#ff8c00',
    downFillColor: '#ff8c00',
    candleWidth: 0.2
  },
  grid: {
    outlineWidth: 1,
    backgroundColor: '#fff'
  },
  legend: {
    position: 'nw',
    backgroundOpacity: 0
  }
};

if (Prototype.Browser.IE) {
  options.shadowSize = 0;
}

{{if $print}}
  options.resolution = 2;
  options.mouse.track = false;
  options.markers = {show: true};
  options.grid.outlineWidth = 1;
  options.xaxis.min = -0.2;
{{/if}}

// We initalize the graphs with the default options
{{foreach from=$data key=name item=field}}
initializeGraph(data.{{$name}}, options);
{{/foreach}}

drawGraph = function() {
  var c = $('constantes-medicales-graph');
  if (!c) return;
  
  var width, height;
  
  {{if $print}}
    width = "700px";
    height = "160px";
  {{else}}
    width = "550px";
    height = "140px";
  {{/if}}
  
  $H(data).each(function(pair){
    var id = 'constantes-medicales-'+pair.key;
    
    data[pair.key].draw = function(){(function(c, d, id, width, height){
      var graph = insertGraph(c, d, id, width, height);
      last_date = null;
    })(c, pair.value, id, width, height)};
    
    {{if $print}}
      data[pair.key].draw();
      data[pair.key].draw = null;
    {{/if}}
  });
};

toggleGraph = function (key, b) {
  if (b && data[key].draw) {
    data[key].draw();
    data[key].draw = null;
  }
  
  var c = $('constantes-medicales-'+key);
  if (c) {
    c.setVisible(b);
  }
}

toggleAllGraphs = function() {
  var oForm = getForm('edit-constantes-medicales');
  var checkbox;
  
  {{if $print}}
    {{foreach from=$data item=curr_data key=key}}
      $('constantes-medicales-{{$key}}').setVisible(
        data.{{$key}}.series.last().data.length > 0
      );
    {{/foreach}}
  {{else}}
    {{foreach from=$data item=curr_data key=key}}
      checkbox = oForm["checkbox-constantes-medicales-{{$key}}"];
      checkbox.checked = keys_selection.indexOf("{{$key}}") > -1 || (data.{{$key}}.series.last().data.length > 0);
      toggleGraph("{{$key}}", checkbox.checked);
    {{/foreach}}
  {{/if}}
}

hideMouseTrack = function(event){
  var element = Event.element(event);
  var container = element.up(".constantes-graph");
  
  if (!container) return;
  
  //"constantes-graph"
  $$(".flotr-mouse-value").each(function(track){
    if (track.up(".constantes-graph") != container) {
      track.hide();
    }
  });
}

Main.add(function () {
  drawGraph();
  toggleAllGraphs();
  document.observe("mouseover", hideMouseTrack);
});

loadConstantesMedicales  = function(context_guid) {
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales"),
      container = $("constantes-medicales") || $("constantes") || $("Constantes"); // case sensitive ?

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
          
          <div id="constantes-medicales-graph" style="text-align: center; clear: both;"></div>
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