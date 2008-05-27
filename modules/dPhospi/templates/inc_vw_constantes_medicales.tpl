<script type="text/javascript">
var g = [];
var data = {{$data|@json}};
var dates = {{$dates|@json}};

submitConstantesMedicales = function(oForm) {
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function () {
      refreshConstantesMedicales($V(oForm.context_id));
    }
  });
};

tickFormatter = function (n) {
  return dates[parseInt(n)];
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
    sensibility: 4,
    trackDecimals: 1,
    radius: 10
  },
  yaxis: {},
  xaxis: {
    noTicks: 4,
    tickDecimals: 1,
    ticks: false,
    lineWidth: 1,
    tickFormatter: tickFormatter,
    max: dates.length-1
  },
  points: {
    show: true
  },
  lines: {
    show: true
  },
  grid: {
    backgroundColor: "white"
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

// And we put the the specific options
data.ta.options.colors = ['silver', '#00A8F0', '#C0D800'];
data.ta.options.yaxis.min = 0;
data.ta.options.yaxis.max = 300;

data.pouls.options.colors = ['silver', 'black'];
data.pouls.options.yaxis.min = 40;
data.pouls.options.yaxis.max = 160;

data.temperature.options.colors = ['silver', 'orange'];
data.temperature.options.yaxis.min = 35;
data.temperature.options.yaxis.max = 45;

drawGraph = function() {
  g[0] = Flotr.draw($('constantes-medicales-ta'), data.ta.series, data.ta.options);
  g[1] = Flotr.draw($('constantes-medicales-pouls'), data.pouls.series, data.pouls.options);
  g[2] = Flotr.draw($('constantes-medicales-temperature'), data.temperature.series, data.temperature.options);
};

Main.add(drawGraph);
</script>

<table class="form">
  <tr>
    <td style="width: 1%;">
      <form name="edit-constantes-medicales" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
        <input type="hidden" name="datetime" value="now" />
        <input type="hidden" name="_new_constantes_medicales" value="1" />
        {{mb_field object=$new_constantes field=context_class hidden=1}}
        {{mb_field object=$new_constantes field=context_id hidden=1}}
        {{mb_field object=$new_constantes field=patient_id hidden=1}}
        
        <table class="form">
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
          <tr>
            <th>{{mb_label object=$new_constantes field=ta}}</th>
            <td>
              {{mb_field object=$new_constantes field=_ta_systole tabindex="3" size="1"}} /
              {{mb_field object=$new_constantes field=_ta_diastole tabindex="4" size="1"}} cm Hg
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$new_constantes field=pouls}}</th>
            <td>{{mb_field object=$new_constantes field=pouls tabindex="5" size="4"}} /min</td>
          </tr>
          <tr>
            <th>{{mb_label object=$new_constantes field=spo2}}</th>
            <td>{{mb_field object=$new_constantes field=spo2 tabindex="6" size="4"}} %</td>
          </tr>
          <tr>
            <th>{{mb_label object=$new_constantes field=temperature}}</th>
            <td>{{mb_field object=$new_constantes field=temperature tabindex="7" size="4"}} °C</td>
          </tr>
          <tr>
            <td></td>
            <td><button type="button" class="new" onclick="return submitConstantesMedicales(this.form);">{{tr}}Save{{/tr}}</button></td>
          </tr>
        </table>
      </form>
    </td>
    <td>
    {{if $sejour->_list_constantes_medicales|@count > 0}}
    Tension artérielle (mmHg)
    <div id="constantes-medicales-ta" style="width: 400px; height: 100px;"></div>
    
    Pouls (/min)
    <div id="constantes-medicales-pouls" style="width: 400px; height: 140px;"></div>
    
    Température (°C)
    <div id="constantes-medicales-temperature" style="width: 400px; height: 140px;"></div>
    {{else}}
      Aucune constante n'est encore enregistrée
    {{/if}}
    </td>
  </tr>
</table>