<script type="text/javascript">  
g = [];
data = {{$data|@json}};
dates = {{$dates|@json}};
hours = {{$hours|@json}};
const_ids = {{$const_ids|@json}};
keys_selection = {{$selection|@array_keys|@json}};
last_date = null;

submitConstantesMedicales = function(oForm) {
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function () {
      refreshConstantesMedicales($V(oForm.context_class)+'-'+$V(oForm.context_id));
    }
  });
  return false;
}

editConstantes = function (const_id, context_guid){
  var url = new Url('dPhospi', 'httpreq_vw_form_constantes_medicales');
  url.addParam('const_id', const_id);
  url.addParam('context_guid', context_guid);
  url.addParam('readonly', '{{$readonly}}');
  url.addParam("selection[]", keys_selection);
  url.requestUpdate('constantes-medicales-form');
}

insertGraph = function (container, data, id, width, height) {
  container.insert('<div id="'+id+'" style="width:'+width+';height:'+height+';margin:auto;"></div>');
  last_date = null;
  return Flotr.draw($(id), data.series, data.options);
}

tickFormatter = function (n) {
  n = parseInt(n);
  
  var s = '<a href="#1" onclick="editConstantes('+const_ids[n]+', \'{{$context_guid}}\')">';
  if (dates[n] && dates[n] == last_date) {
    s += hours[n];
  } else if (dates[n] && hours[n]) {
    s += hours[n]+'<br />'+dates[n];
    last_date = dates[n];
  }
  return s+'</a>';
};

tickFormatterSpreadsheet = function (n) {
  n = parseInt(n);
	if (!dates[n] || !hours[n]) return " ";
  return dates[n]+' '+hours[n];
};

trackFormatter = function (obj) {
  return dates[parseInt(obj.x)] + ' : ' + 
         obj.series.data[obj.index][1] + '<br />' + 
         obj.series.data[obj.index][2];
};

initializeGraph = function (src, data) {
  src.options = {
    title: src.options.title || '',
    xaxis: src.options.xaxis || {},
    yaxis: src.options.yaxis || {},
    mouse: src.options.mouse || {},
    points: src.options.points || {},
    lines: src.options.lines || {},
    grids: src.options.grids || {},
    legend: src.options.legend || {},
    colors: src.options.colors || {},
    selection: src.options.selection || {}
  };

  Object.extend(src.options,        data);
  Object.extend(src.options.xaxis,  data.xaxis);
  Object.extend(src.options.yaxis,  data.yaxis);
  Object.extend(src.options.mouse,  data.mouse);
  Object.extend(src.options.points, data.points);
  Object.extend(src.options.lines,  data.lines);
  Object.extend(src.options.grids,  data.grids);
  Object.extend(src.options.legend, data.legend);
  Object.extend(src.options.colors, data.colors);
  Object.extend(src.options.selection, data.selection);
  
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
    min: 0,
    max: Math.max(dates.length-1, 12)
  },
  points: {
    show: true
  },
  lines: {
    show: true
  },
  grid: {
    backgroundColor: '#fff'
  },
  legend: {
    position: 'nw',
    backgroundOpacity: 0
  },
  spreadsheet: {
    show: true,
    tabGraphLabel: 'Graphique',
    tabDataLabel: 'Données',
    toolbarDownload: 'Télécharger les données (CSV)',
    toolbarSelectAll: 'Tout sélectionner',
    tickFormatter: tickFormatterSpreadsheet
  }
};

{{if $print}}
  options.resolution = 2;
  options.spreadsheet.show = false;
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
    width = "500px";
    height = "130px";
  {{/if}}
  
  $H(data).each(function(pair){
	  var id = 'constantes-medicales-'+pair.key;
		
		data[pair.key].draw = function(){(function(c, d, id, width, height){
	    var graph = insertGraph(c, d, id, width, height);
	    
	    {{if !$print}}
	      var tabs = graph.spreadsheet.tabs;
	      
	      tabs.data.observe('click', function(e){
	        g.each(function(graph2){
	          graph2.spreadsheet.showTab('data');
	        });
	      });
	      
	      tabs.graph.observe('click', function(e){
	        g.each(function(graph2){
	          graph2.spreadsheet.showTab('graph');
	        });
	      });
	    {{/if}}
	    
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

Main.add(function () {
  var oForm = document.forms['edit-constantes-medicales'];
  var checkbox;
  
  drawGraph();
  
  {{if $print}}
    {{foreach from=$data item=curr_data key=key}}
      $('constantes-medicales-{{$key}}').setVisible(
        data.{{$key}}.series.last().data.length > 0
      );
    {{/foreach}}
  {{else}}
    {{foreach from=$data item=curr_data key=key}}
      checkbox = oForm["checkbox-constantes-medicales-{{$key}}"];
      checkbox.checked = (data.{{$key}}.series.last().data.length > 1);
			toggleGraph("{{$key}}", checkbox.checked);
    {{/foreach}}
  {{/if}}
});

loadConstantesMedicales  = function(context_guid) {
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales"),
      container = $("constantes-medicales") || $("constantes") || $("Constantes"); // case sensitive ?

  url.addParam("context_guid", '{{$context_guid}}');
  url.addParam("patient_id", '{{$patient->_id}}');
  url.addParam("readonly", '{{$readonly}}');
  url.addParam("selection[]", keys_selection);
  url.addParam("selected_context_guid", context_guid);
  url.requestUpdate(container);
};
</script>

<table class="tbl" {{if $print}}style="display: none;"{{/if}}>
  <tr>
    <th colspan="10" class="title">
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
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
      {{mb_value object=$patient field=naissance}} ({{$patient->_age}} ans)
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
  <div id="constantes-medicales-graph" style="margin-left: 5px; text-align: center;"></div>
{{else}}
  {{if "forms"|module_active && $curr_context instanceof CSejour}}
	  <script type="text/javascript">
	  	Main.add(function(){
			  Control.Tabs.create("surveillance-tab");
			});
	  </script>
		
	  <ul class="control_tabs" id="surveillance-tab">
	  	<li>
	  		<a href="#tab-constantes-medicales">Constantes</a>
	  	</li>
	    <li>
	      <a href="#tab-ex_class-list" onmousedown="this.onmousedown=null; ExObject.loadExObjects('{{$curr_context->_class_name}}', '{{$curr_context->_id}}', 'tab-ex_class-list', 0)">
	      	Formulaires
				</a>
	    </li>
	  </ul>
		<hr class="control_tabs" />
	{{/if}}
	
  <table class="main" id="tab-constantes-medicales">
    <tr>
      <td class="narrow" id="constantes-medicales-form">
  			{{include file="inc_form_edit_constantes_medicales.tpl" context_guid=$context_guid}}
      </td>
      <td>
        <button class="hslip notext" title="Afficher/Cacher le formulaire" onclick="$('constantes-medicales-form').toggle();" type="button">
          Formulaire constantes
        </button>
        <div id="constantes-medicales-graph" style="margin-left: 5px; text-align: center;"></div>
      </td>
    </tr>
  </table>
	
	<div id="tab-ex_class-list" style="display: none;"></div>
{{/if}}