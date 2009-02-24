<script type="text/javascript">
var g = [];
var data = {{$data|@json}};
var dates = {{$dates|@json}};
var hours = {{$hours|@json}};
var const_ids = {{$const_ids|@json}};
var last_date = null;

submitConstantesMedicales = function(oForm) {
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function () {
      refreshConstantesMedicales($V(oForm.context_id));
    }
  });
  return false;
}

editConstantes = function (const_id){
  var url = new Url;
  url.setModuleAction('dPhospi', 'httpreq_vw_form_constantes_medicales');
  url.addParam('const_id', const_id);
  url.requestUpdate('constantes-medicales-form', { 
    waitingText: null,
    onComplete: function () {
      prepareForm('edit-constantes-medicales');
    }
  } );
}

insertGraph = function (container, data, id, width, height) {
  container.insert('<b id="title-'+id+'"><br />'+data.title+(data.unit?(' ('+data.unit+')'):'')+'</b>');
  container.insert('<div id="'+id+'" style="width: '+width+'; height: '+height+';"></div>');
  last_date = null;
  return Flotr.draw($(id), data.series, data.options);
}

tickFormatter = function (n) {
  n = parseInt(n);
  
  var s = '<a href="#1" onclick="editConstantes('+const_ids[n]+')">';
  if (dates[n] && dates[n] == last_date) {
    s += hours[n];
  } else if (dates[n] && hours[n]) {
    s += hours[n]+'<br />'+dates[n];
    last_date = dates[n];
  }
  return s+'</a>';
};

trackFormatter = function (obj) {
  return dates[parseInt(obj.x)] + ' : ' + obj.y;
};

function initializeGraph(src, data) {
  src.options = {
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
  if (src.standard) {
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
    trackDecimals: 1,
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
  }
};

// We initalize the graphs with the default options
{{foreach from=$data key=name item=field}}
initializeGraph(data.{{$name}}, options);
{{/foreach}}

// And we put the the specific options
data.ta.options.colors = ['silver', '#00A8F0', '#C0D800'];

data.pouls.options.colors = ['silver', 'black'];
data.pouls.options.mouse.trackDecimals = 0;

data.temperature.options.colors = ['silver', 'orange'];

drawGraph = function() {
  var c = $('constantes-medicales-graph');
  if (c) {
    g[0] = insertGraph(c, data.ta, 'constantes-medicales-ta', '500px', '120px');
    g[1] = insertGraph(c, data.poids, 'constantes-medicales-poids', '500px', '120px');
    g[2] = insertGraph(c, data.taille, 'constantes-medicales-taille', '500px', '120px');
    g[3] = insertGraph(c, data.pouls, 'constantes-medicales-pouls', '500px', '120px');
    g[4] = insertGraph(c, data.temperature, 'constantes-medicales-temperature', '500px', '120px');
    g[5] = insertGraph(c, data.spo2, 'constantes-medicales-spo2', '500px', '120px');
    g[6] = insertGraph(c, data.score_sensibilite, 'constantes-medicales-score_sensibilite', '500px', '120px');
    g[7] = insertGraph(c, data.score_motricite, 'constantes-medicales-score_motricite', '500px', '120px');
    g[8] = insertGraph(c, data.score_sedation, 'constantes-medicales-score_sedation', '500px', '120px');
    g[9] = insertGraph(c, data.frequence_respiratoire, 'constantes-medicales-frequence_respiratoire', '500px', '120px');
    g[10] = insertGraph(c, data.EVA, 'constantes-medicales-EVA', '500px', '120px');
  }
};


hideGraph = function(id) {
  $(id).hide();
  $('title-'+id).hide();
}

showGraph = function(id){
  $(id).show();
  $('title-'+id).show();
}

toggleGraph = function(id){
  if($(id).visible()){
    hideGraph(id);
  } else {
    showGraph(id);
  }
  checkbox = document.forms['edit-constantes-medicales'].elements['checkbox-'+id];
  var cookie = new CookieJar();
  cookie.setValue('graphsToShow', id, checkbox.checked);
}


Main.add(function () {
  var oForm = document.forms['edit-constantes-medicales'];

  prepareForm(oForm);
  drawGraph();
  
  var cookie = new CookieJar();

  // Si le cookie n'existe pas, on affiche seulement les 4 principaux graphs
  if(!cookie.get('graphsToShow')){
    cookie.setValue('graphsToShow', 'constantes-medicales-ta', true);
    cookie.setValue('graphsToShow', 'constantes-medicales-pouls', true);
    cookie.setValue('graphsToShow', 'constantes-medicales-temperature', true);
    cookie.setValue('graphsToShow', 'constantes-medicales-spo2', true);
  }
  
  // Recuperation de la valeur du cookie, on masque les graphs qui ne sont pas selectionnés
  {{foreach from=$graphs item=graph_name}}
    oForm["checkbox-{{$graph_name}}"].checked = cookie.getValue('graphsToShow', '{{$graph_name}}');
    if(oForm["checkbox-{{$graph_name}}"].checked == false){ hideGraph('{{$graph_name}}'); }
  {{/foreach}}

});
</script>

{{assign var=patient value=$sejour->_ref_patient}}
<table class="tbl">
  <tr>
    <th colspan="10" class="title">
       <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}"'>
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=42}}
       </a>
    {{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
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

<table>
  <tr>
    <td colspan="2">
     <button class="hslip" title="Afficher/cacher" onclick="$('constantes-medicales-form').toggle();" type="button">
     Formulaire
    </button>
  </tr>
  <tr>
    <td>
      <div id="constantes-medicales-form">
			  {{include file="inc_form_edit_constantes_medicales.tpl"}}
			</div>
    </td>
    <td>
      <div id="constantes-medicales-graph" style="margin-left: 5px; margin-top: -15px;"></div>
    </td>
  </tr>
</table>