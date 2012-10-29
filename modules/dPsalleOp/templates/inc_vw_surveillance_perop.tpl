
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

editEvenementPerop = function(guid, operation_id, datetime) {
  var url = new Url("dPsalleOp", "ajax_edit_evenement_perop");
  url.addParam("evenement_guid", guid);
  url.addParam("operation", operation_id);
  url.addParam("datetime", datetime);
  url.requestModal(600, 400);
  
  return false;
}
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
      <button onclick="enChantier()" class="door-in">Entrée</button><br />
      <button onclick="enChantier()" class="door-out">Sortie</button>
    </td>
    <td class="narrow">
      <button onclick="enChantier()" class="intubation">Intubation</button><br />
      <button onclick="enChantier()" class="extubation">Extubation</button>
    </td>
    <td class="narrow">
      <button onclick="enChantier()" class="bistouri">Incision</button><br />
      <button onclick="enChantier()" class="fermeture-plaie">Fermeture</button>
    </td>
    <td>
      {{if $can->admin}}
        <form name="generate-sample-observation-results" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadSurveillancePerop})" style="float: right;">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="dosql" value="do_sample_observation_results_generate" />
          <input type="hidden" name="suppressHeaders" value="1" />
          <input type="hidden" name="context_class" value="{{$interv->_class}}" />
          <input type="hidden" name="context_id" value="{{$interv->_id}}" />
          <input type="hidden" name="patient_id" value="{{$interv->_ref_sejour->patient_id}}" />
          <input type="hidden" name="datetime_start" value="{{$time_debut_op_iso}}" />
          <input type="hidden" name="datetime_end" value="{{$time_fin_op_iso}}" />
          <button class="change">Générer un jeu de données aléatoires</button>
        </form>
      {{/if}}

      <button onclick="enChantier()" class="injection">Injection</button>
      <button onclick="editEvenementPerop('CAnesthPerop-0', '{{$interv->_id}}')" class="warning" style="border-color: red;">Incident</button>
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
  
  <table class="main evenements" style="table-layout: fixed; width: {{$width}}px;">
    <col style="width: {{$yaxes_count*38-14}}px;" />
    
    {{foreach from=$evenements key=_label item=_evenements}}
      <tr>
        <th>{{tr}}{{$_label}}{{/tr}}</th>
        <td>
        {{foreach from=$_evenements item=_evenement}}
          {{if $_evenement.position <= 100}}
          {{assign var=evenement_width value=""}}
          {{if array_key_exists('width', $_evenement)}} 
            {{assign var=evenement_width value="width: `$_evenement.width`%;"}}
          {{/if}}
          
          <div style="padding-left: {{$_evenement.position}}%; {{if $_evenement.alert}} color: red; {{/if}} {{if array_key_exists('width', $_evenement)}} margin-bottom: 2px; {{/if}}" class="evenement">
            <div onmouseover="ObjectTooltip.createEx(this, '{{$_evenement.object->_guid}}');" style="{{$evenement_width}}">
              <div class="marking">
                <!--<span>{{$_evenement.datetime|date_format:$conf.datetime}}</span>-->
              </div>
              <div class="label" title="{{$_evenement.datetime|date_format:$conf.datetime}} - {{if $_evenement.unit}}{{$_evenement.unit}}{{/if}} {{$_evenement.label}}">
                {{if $_evenement.editable}} 
                  <a href="#{{$_evenement.object->_guid}}" onclick="return editEvenementPerop('{{$_evenement.object->_guid}}', '{{$interv->_id}}')">
                {{/if}}
                
                  {{if $_evenement.icon}}
                    {{assign var=_icon value=$_evenement.icon}}
                    <img src="{{$images.$_icon}}" />
                  {{/if}}
                  {{if $_evenement.unit}}
                    {{$_evenement.unit}} <strong>{{$_evenement.label|truncate:30}}</strong>
                  {{else}}
                    {{$_evenement.label|truncate:30}}
                  {{/if}}
                
                {{if $_evenement.editable}} 
                  </a>
                {{/if}}
              </div>
            </div>
          </div>
          {{/if}}
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
