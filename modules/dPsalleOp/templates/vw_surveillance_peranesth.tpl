
<script type="text/javascript">
var previousPoint = null;
var plothover = function (event, pos, item) {
  if (item) {
    var key = item.dataIndex+"-"+item.seriesIndex;
    if (previousPoint != key) {
      previousPoint = key;
      jQuery("#flot-tooltip").remove();
      
      var x = item.datapoint[0],
          y = item.datapoint[1];
      
      var date = new Date();
      date.setTime(x);
      
      var contents = 
      "<big style='font-weight:bold'>"+y+" "+item.series.unit+"</big>"+
      "<hr />"+item.series.label+"<br />" + printf("%02d:%02d:%02d", date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());
      
      $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, contents).setStyle({
        position: 'absolute',
        top:  item.pageY + 5 + "px",
        left: item.pageX + 5 + "px"
      }));
    }
  }
  else {
    jQuery("#flot-tooltip").remove();
    previousPoint = null;            
  }
}
  
Main.add(function(){
  
  (function ($){
    var ph, series, xaxes;
    
    {{foreach from=$graphs item=_graph key=i name=graphs}}
      ph = $("#placeholder-{{$i}}");
      series = {{$_graph.series|@json}};
      xaxes  = {{$_graph.xaxes|@json}};
      
      {{if !$smarty.foreach.graphs.last}}
        xaxes[0].tickFormatter = function(){return " "};
      {{/if}}
      
      ph.bind("plothover", plothover);
      
      $.plot(ph, series, {
        grid: { hoverable: true, markings: [
          // Debut op
          {xaxis: {from: 0, to: {{$time_debut_op}}}, color: "rgba(0,0,0,0.05)"},
          {xaxis: {from: {{$time_debut_op}}, to: {{$time_debut_op+1000}}}, color: "black"},
          
          // Fin op
          {xaxis: {from: {{$time_fin_op}}, to: Number.MAX_VALUE}, color: "rgba(0,0,0,0.05)"},
          {xaxis: {from: {{$time_fin_op}}, to: {{$time_fin_op+1000}}}, color: "black"}
        ] },
        series: { points: { show: true, radius: 3 } },
        xaxes: xaxes,
        yaxes: {{$_graph.yaxes|@json}}
      });
    {{/foreach}}
    
  })(jQuery);
});
</script>

{{assign var=images value="CPrescription"|static:"images"}}

<div style="font-size: 1.5em; table-layout: fixed;">
  <span style="font-weight: bold;">{{$interv->_ref_sejour->_ref_patient}}</span> &mdash;
  {{$interv->_ref_sejour->_ref_patient->_age}} ans &mdash;
  {{$interv->_ref_sejour->_ref_patient->_ref_constantes_medicales->poids}} Kg &mdash;
  Groupe sang. / Rh. : {{$consult_anesth->groupe}} / {{$consult_anesth->rhesus}}
</div>

<hr />

<button>Incision</button>
<button>Fermeture</button>
<button>Intubation</button>
<button>Extubation</button>
<hr />

<div style="position: relative;" class="supervision">
  {{foreach from=$graphs item=_graph key=i}}
    <div class="yaxis-labels">
      {{foreach from=$_graph.yaxes|@array_reverse item=_yaxis}}
        <div>
          {{$_yaxis.label}}
          <div class="symbol">{{$_yaxis.symbolChar|smarty:nodefaults}}</div>
        </div>
      {{/foreach}}
      <span class="title">{{$_graph.title}}</span>
    </div>
    <div id="placeholder-{{$i}}" style="width:900px;height:200px;"></div>
    <br />
  {{/foreach}}
  
  <table class="main gestes" style="table-layout: fixed; width: 900px;">
    <col style="width: {{$yaxes_count*38-14}}px;" />
    
    {{foreach from=$gestes key=_label item=_gestes}}
      <tr>
        <th>{{tr}}{{$_label}}{{/tr}}</th>
        <td>
        {{foreach from=$_gestes item=_geste}}
          <div style="padding-left: {{$_geste.position}}%; {{if $_geste.alert}} color: red; {{/if}}" class="geste">
            <div  onmouseover="ObjectTooltip.createEx(this, '{{$_geste.object->_guid}}');">
              <div class="marking">
                <!--<span>{{$_geste.datetime|date_format:$conf.datetime}}</span>-->
              </div>
              <div class="label" title="{{$_geste.datetime|date_format:$conf.datetime}}">
                {{if $_geste.icon}}
                  {{assign var=_icon value=$_geste.icon}}
                  <img src="{{$images.$_icon}}" />
                {{/if}}
                {{if $_geste.unit}}
                  {{$_geste.unit}} <strong>{{$_geste.label}}</strong>
                {{else}}
                  {{$_geste.label}}
                {{/if}}
              </div>
            </div>
          </div>
        {{/foreach}}
        </td>
      </tr>
    {{/foreach}}
    
    <tr>
      <th></th>
      <td style="padding: 1px;">
        <div class="now opacity-50" style="padding-left: {{$now}}%;">
          <div class="marking"></div>
        </div>
      </td>
    </tr>
  </table>
</div>
