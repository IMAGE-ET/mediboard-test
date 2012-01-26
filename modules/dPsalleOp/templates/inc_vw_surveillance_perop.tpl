
<script type="text/javascript">
previousPoint = null;
plothover = function (event, pos, item) {
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

enChantier = function(){
  Modal.alert("Fonctionnalité en cours de développement");
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
{{assign var=width value=800}}

<div class="small-warning">
  Cette vue est en cours de développement et n'est qu'un aperçu. 
</div>

<table class="main tbl">
  <tr>
    <td>
      <strong>
        {{$interv->_ref_sejour->_ref_patient->_ref_constantes_medicales->poids}} Kg &ndash;
        {{$interv->_ref_sejour->_ref_patient->_ref_constantes_medicales->taille}} cm
      </strong>
    </td>
    <td>Gr. sang. / Rh: <strong>{{mb_value object=$consult_anesth field=groupe}} {{mb_value object=$consult_anesth field=rhesus}}</strong></td>
    <td>Mallampati: <strong>{{mb_value object=$consult_anesth field=mallampati}}</strong></td>
    <td>ASA: <strong>{{mb_value object=$consult_anesth field=ASA}}</strong></td>
  </tr>
</table>
<hr />

<table class="main layout">
  <tr>
    <td class="narrow">
      <button onclick="enChantier()" class="submit">Entrée</button><br />
      <button onclick="enChantier()" class="submit">Sortie</button>
    </td>
    <td class="narrow">
      <button onclick="enChantier()" class="submit">Intubation</button><br />
      <button onclick="enChantier()" class="submit">Extubation</button>
    </td>
    <td class="narrow">
      <button onclick="enChantier()" class="submit">Incision</button><br />
      <button onclick="enChantier()" class="submit">Fermeture</button>
    </td>
    <td>
      <button onclick="enChantier()" class="submit">Injection</button>
      <button onclick="enChantier()" class="warning" style="border-color: red;">Incident</button>
    </td>
  </tr>
</table>
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
    <div id="placeholder-{{$i}}" style="width:{{$width}}px;height:200px;"></div>
    <br />
  {{/foreach}}
  
  <table class="main gestes" style="table-layout: fixed; width: {{$width}}px;">
    <col style="width: {{$yaxes_count*38-14}}px;" />
    
    {{foreach from=$gestes key=_label item=_gestes}}
      <tr>
        <th>{{tr}}{{$_label}}{{/tr}}</th>
        <td>
        {{foreach from=$_gestes item=_geste}}
          {{assign var=geste_width value=""}}
          {{if array_key_exists('width', $_geste)}} 
            {{assign var=geste_width value="width: `$_geste.width`%;"}}
          {{/if}}
          
          <div style="padding-left: {{$_geste.position}}%; {{if $_geste.alert}} color: red; {{/if}} {{if array_key_exists('width', $_geste)}} margin-bottom: 0; {{/if}}" class="geste">
            <div onmouseover="ObjectTooltip.createEx(this, '{{$_geste.object->_guid}}');" style="{{$geste_width}}">
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
    
    {{if $now <= 100}}
    <tr>
      <th></th>
      <td style="padding: 1px;">
        <div class="now opacity-50" style="padding-left: {{$now}}%;">
          <div class="marking"></div>
        </div>
      </td>
    </tr>
    {{/if}}
  </table>
</div>

<table class="main tbl">
  <tr>
    <th class="title" colspan="3">Historique</th>
  </tr>
</table>
