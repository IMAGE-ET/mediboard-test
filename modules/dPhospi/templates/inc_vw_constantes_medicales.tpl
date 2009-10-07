<script type="text/javascript">  
g = [];
data = {{$data|@json}};
dates = {{$dates|@json}};
hours = {{$hours|@json}};
const_ids = {{$const_ids|@json}};
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
  var url = new Url;
  url.setModuleAction('dPhospi', 'httpreq_vw_form_constantes_medicales');
  url.addParam('const_id', const_id);
  url.addParam('context_guid', context_guid);
  url.addParam('readonly', '{{$readonly}}');
  url.requestUpdate('constantes-medicales-form', { 
    waitingText: null,
    onComplete: function () {
      prepareForm('edit-constantes-medicales');
    }
  } );
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
  return dates[n]+' '+hours[n];
};

trackFormatter = function (obj) {
  return dates[parseInt(obj.x)] + ' : ' + obj.series.data[obj.index][1];
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
  Object.extend(src.options.selection, data.selection);
  
  // Suppression des valeurs Y nulles
  src.series.each(function(serie) {
    serie.data = serie.data.reject(
      function(v) {return v[1] == null;}
    );
  });
  
  // Ajout de la ligne de niveau standard
  if (0 && src.standard) {
    src.series.unshift({
      data: [[0, src.standard], [1000, src.standard]], 
      points: {show: false},
      mouse: {track: false}
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

// We initalize the graphs with the default options
{{foreach from=$data key=name item=field}}
initializeGraph(data.{{$name}}, options);
{{/foreach}}

// And we put the the specific options
data.ta.options.colors = [/*'silver', */'#00A8F0', '#C0D800'];

data.pouls.options.colors = ['silver', 'black'];
data.pouls.options.mouse.trackDecimals = 0;

data.temperature.options.colors = ['silver', 'orange'];

drawGraph = function() {
  var c = $('constantes-medicales-graph');
  if (c) {
    $H(data).each(function(pair){
      g.push(insertGraph(c, pair.value, 'constantes-medicales-'+pair.key, '500px', '130px'));
      last_date = null;
    });
    
    g.each(function(graph){
      graph.spreadsheet.tabs.data.observe('click', function(e){
        g.each(function(graph2){
          graph2.spreadsheet.showTab('data');
        });
      });
      
      graph.spreadsheet.tabs.graph.observe('click', function(e){
        g.each(function(graph2){
          graph2.spreadsheet.showTab('graph');
        });
      });
    });
  }
};

toggleGraph = function(id){
  $(id).toggle();
};

Main.add(function () {
  var oForm = document.forms['edit-constantes-medicales'];

  prepareForm(oForm);
  drawGraph();
  
  {{foreach from=$data item=curr_data key=key}}
    oForm["checkbox-constantes-medicales-{{$key}}"].checked = !!data.{{$key}}.series.first().data.length;
    $('constantes-medicales-{{$key}}').setVisible(oForm["checkbox-constantes-medicales-{{$key}}"].checked);
  {{/foreach}}
});

loadConstantesMedicales  = function(context_guid) {
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales"),
      container = $("constantes-medicales") || $("constantes") || $("Constantes"); // case sensitive ?

  url.addParam("context_guid", '{{$context_guid}}');
  url.addParam("patient_id", '{{$patient->_id}}');
  url.addParam("readonly", '{{$readonly}}');
  url.addParam("selected_context_guid", context_guid);
  url.requestUpdate(container, { waitingText: null } );
};
</script>

<table class="tbl">
  <tr>
    <th colspan="10" class="title">
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
      </a>
      Constantes médicales dans le cadre de: 
      {{if $readonly}}
        {{$context->_view}}
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

<table class="main">
  <tr>
    <td colspan="2">
      <button class="hslip" title="Afficher/Cacher" onclick="$('constantes-medicales-form').toggle();" type="button">
        Formulaire
      </button>
    </td>
  </tr>
  <tr>
    <td style="width: 0.1%;">
      <div id="constantes-medicales-form">
			  {{include file="inc_form_edit_constantes_medicales.tpl" context_guid=$context_guid}}
			</div>
    </td>
    <td>
      <div id="constantes-medicales-graph" style="margin-left: 5px; text-align: center;"></div>
    </td>
  </tr>
</table>