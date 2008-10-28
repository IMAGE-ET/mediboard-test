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
    lineColor: 'red',
    sensibility: 6,
    trackDecimals: 1,
    radius: 4
  },
  /*bars: {
    outlineWidth: 1
  },*/
  xaxis: {
    noTicks: 12,
    tickDecimals: 1,
    ticks: false,
    lineWidth: 1,
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
  /*selection: {
    mode: 'x',
    fps: 30
  },*/
  legend: {
    position: 'nw',
    backgroundOpacity: 0
  }
};

// We initalize the graphs with the default options
initializeGraph(data.ta, options);
initializeGraph(data.poids, options);
initializeGraph(data.taille, options);
initializeGraph(data.pouls, options);
initializeGraph(data.temperature, options);
initializeGraph(data.spo2, options);
initializeGraph(data.score_sensibilite, options);
initializeGraph(data.score_motricite, options);
initializeGraph(data.score_sedation, options);
initializeGraph(data.frequence_respiratoire, options);
initializeGraph(data.EVA, options);

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