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
  if (container.visible()){
  container.insert('<br /><b>'+data.title+(data.unit?(' ('+data.unit+')'):'')+'</b>');
  container.insert('<div id="'+id+'" style="width: '+width+'; height: '+height+';"></div>');
  
  last_date = null;
  return Flotr.draw($(id), data.series, data.options);
  }
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
initializeGraph(data.pouls, options);
initializeGraph(data.temperature, options);
initializeGraph(data.spo2, options);

// And we put the the specific options
data.ta.options.colors = ['silver', '#00A8F0', '#C0D800'];

data.pouls.options.colors = ['silver', 'black'];
data.pouls.options.mouse.trackDecimals = 0;

data.temperature.options.colors = ['silver', 'orange'];

drawGraph = function() {
  var c = $('constantes-medicales-graph');
  if (c) {
    g[0] = insertGraph(c, data.ta, 'constantes-medicales-ta', '500px', '120px');
    g[1] = insertGraph(c, data.pouls, 'constantes-medicales-pouls', '500px', '120px');
    g[2] = insertGraph(c, data.temperature, 'constantes-medicales-temperature', '500px', '120px');
    g[3] = insertGraph(c, data.spo2, 'constantes-medicales-spo2', '500px', '120px');
  }
};

Main.add(drawGraph);
Main.add(function () {
  prepareForm(document.forms['edit-constantes-medicales']);
  //Calendar.regField('edit-constantes-medicales', 'datetime', true);
});
</script>

<div id="constantes-medicales-form">
{{include file="inc_form_edit_constantes_medicales.tpl"}}
</div>

<div id="constantes-medicales-graph"></div>
