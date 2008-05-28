<script type="text/javascript">
var g = [];
var data = {{$data|@json}};
var dates = {{$dates|@json}};
var hours = {{$hours|@json}};
var last_date = null;

submitConstantesMedicales = function(oForm) {
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function () {
      refreshConstantesMedicales($V(oForm.context_id));
    }
  });
};

insertGraph = function (container, data, id, width, height) {
  container.insert('<br /><b>'+data.title+(data.unit?(' ('+data.unit+')'):'')+'</b>');
  container.insert('<div id="'+id+'" style="width: '+width+'; height: '+height+';"></div>');
  last_date = null;
  return Flotr.draw($(id), data.series, data.options);
}

tickFormatter = function (n) {
  n = parseInt(n);
  if (dates[n] == last_date) {
    return hours[n];
  } else if (dates[n] && hours[n]) {
    last_date = dates[n];
    return hours[n]+'<br />'+dates[n];
  } else {
    return '';
  }
};

trackFormatter = function (obj) {
  return dates[parseInt(obj.x)] + ' : ' + obj.y;
};

function initializeGraph(src, data) {
  src.options = {
    xaxis: {},
    yaxis: {},
    mouse: {},
    points: {},
    lines: {},
    grids: {},
    legend: {}
  };
  
  Object.extend(src.options.xaxis,  data.xaxis);
  Object.extend(src.options.yaxis,  data.yaxis);
  Object.extend(src.options.mouse,  data.mouse);
  Object.extend(src.options.points, data.points);
  Object.extend(src.options.lines,  data.lines);
  Object.extend(src.options.grids,  data.grids);
  Object.extend(src.options.legend,  data.legend);
  
  src.series.each(function(serie) {
    serie.data = serie.data.reject(
      function(v) {return v[1] == null;}
    );
  });
}

// Default options for the graphs
options = {
  mouse: {
    track:true,
    trackFormatter: trackFormatter,
    lineColor: "red",
    sensibility: 2,
    trackDecimals: 1,
    radius: 2
  },
  yaxis: {},
  xaxis: {
    noTicks: Math.min(20, dates.length),
    tickDecimals: 1,
    ticks: false,
    lineWidth: 1,
    tickFormatter: tickFormatter,
    max: Math.max(dates.length-1, 10)
  },
  points: {
    show: true
  },
  lines: {
    show: true
  },
  grid: {
    backgroundColor: 'white'
  },
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
data.ta.options.yaxis.min = 0;
data.ta.options.yaxis.max = 30;

data.pouls.options.colors = ['silver', 'black'];
data.pouls.options.mouse.trackDecimals = 0;
data.pouls.options.yaxis.min = 40;
data.pouls.options.yaxis.max = 160;

data.temperature.options.colors = ['silver', 'orange'];
data.temperature.options.yaxis.min = 35;
data.temperature.options.yaxis.max = 45;

data.spo2.options.yaxis.min = 0;
data.spo2.options.yaxis.max = 100;

drawGraph = function() {
  var c = $('constantes-medicales-graph');
  if (c) {
    g[0] = insertGraph(c, data.ta, 'constantes-medicales-ta', '500px', '120px');
    g[1] = insertGraph(c, data.pouls, 'constantes-medicales-pouls', '500px', '120px');
    g[2] = insertGraph(c, data.temperature, 'constantes-medicales-temperature', '500px', '120px');
    g[2] = insertGraph(c, data.spo2, 'constantes-medicales-spo2', '500px', '120px');
  }
};

Main.add(drawGraph);
</script>

    <!--<tr>
      <th>{{mb_label object=$new_constantes field=poids}}</th>
      <td>{{mb_field object=$new_constantes field=poids tabindex="1" size="4"}} kg</td>
    </tr>
    <tr>
      <th>{{mb_label object=$new_constantes field=taille}}</th>
      <td>{{mb_field object=$new_constantes field=taille tabindex="2" size="4"}} cm</td>
    </tr>
    <tr>
      <th>{{mb_label object=$new_constantes field=_vst}}</th>
      <td class="readonly">{{mb_field object=$new_constantes field=_vst size="4" readonly="readonly"}} ml</td>
    </tr>
    <tr>
      <th>{{mb_label object=$new_constantes field=_imc}}</th>
      <td class="readonly">{{mb_field object=$new_constantes field=_imc size="4" readonly="readonly"}}</td>
    </tr>-->

<form name="edit-constantes-medicales" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
  <input type="hidden" name="datetime" value="now" />
  <input type="hidden" name="_new_constantes_medicales" value="1" />
  {{mb_field object=$new_constantes field=context_class hidden=1}}
  {{mb_field object=$new_constantes field=context_id hidden=1}}
  {{mb_field object=$new_constantes field=patient_id hidden=1}}
  
  <table class="tbl" style="width: 1%;">
    <tr>
      <th>{{mb_label object=$new_constantes field=ta}}</th>
      <th>{{mb_label object=$new_constantes field=pouls}}</th>
      <th>{{mb_label object=$new_constantes field=spo2}}</th>
      <th>{{mb_label object=$new_constantes field=temperature}}</th>
      <th></th>
    </tr>
    <tr>
      <td>
        {{mb_field object=$new_constantes field=_ta_systole tabindex="3" size="1"}} /
        {{mb_field object=$new_constantes field=_ta_diastole tabindex="4" size="1"}} cm Hg
      </td>
      <td>{{mb_field object=$new_constantes field=pouls tabindex="5" size="4"}} /min</td>
      <td>{{mb_field object=$new_constantes field=spo2 tabindex="6" size="4"}} %</td>
      <td>{{mb_field object=$new_constantes field=temperature tabindex="7" size="4"}} °C</td>
      <td><button type="button" class="new" onclick="return submitConstantesMedicales(this.form);">{{tr}}Save{{/tr}}</button></td>
    </tr>
  </table>
</form>

<div id="constantes-medicales-graph"></div>
