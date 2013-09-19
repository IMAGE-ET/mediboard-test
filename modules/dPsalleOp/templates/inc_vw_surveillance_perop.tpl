
<script type="text/javascript">
window.previousPoint = null;
plothover = function (event, pos, item) {
  if (item) {
    var key = item.dataIndex+"-"+item.seriesIndex;
    if (previousPoint != key) {
      window.previousPoint = key;
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
    window.previousPoint = null;
  }
};

enChantier = function(){
  Modal.alert("Fonctionnalité en cours de développement");
};
  
Main.add(function(){
  
  (function ($){
    var ph, series, xaxes;
    
    {{foreach from=$graphs item=_graph key=i name=graphs}}
      {{if $_graph instanceof CSupervisionGraph}}
        {{assign var=_graph_data value=$_graph->_graph_data}}

        ph = $("#placeholder-{{$i}}");
        series = {{$_graph_data.series|@json}};
        xaxes  = {{$_graph_data.xaxes|@json}};
        xaxes[0].ticks = 10;

        {{if false && !$smarty.foreach.graphs.last}}
          xaxes[0].tickFormatter = function(){return " "};
          xaxes[0].labelHeight = 0;
          //xaxes[0].show = false;
        {{/if}}

    ph.bind("plothover", plothover);
    /*ph.bind("plotclick", function(event, pos, item){
      console.log(pos);
    });*/

        var plot = $.plot(ph, series, {
          grid: {
            hoverable: true,
            //clickable: true,
            markings: [
              // Debut op
              {xaxis: {from: 0, to: {{$time_debut_op}}}, color: "rgba(0,0,0,0.05)"},
              {xaxis: {from: {{$time_debut_op}}, to: {{$time_debut_op+1000}}}, color: "black"},

              // Fin op
              {xaxis: {from: {{$time_fin_op}}, to: Number.MAX_VALUE}, color: "rgba(0,0,0,0.05)"},
              {xaxis: {from: {{$time_fin_op}}, to: {{$time_fin_op+1000}}}, color: "black"}
            ]
          },
          series: SupervisionGraph.defaultSeries,
          xaxes: xaxes,
          yaxes: {{$_graph_data.yaxes|@json}}
        });
      {{/if}}
    {{/foreach}}
  })(jQuery);
});

editEvenementPerop = function(guid, operation_id, datetime) {
  var url = new Url("dPsalleOp", "ajax_edit_evenement_perop");
  url.addParam("evenement_guid", guid);
  url.addParam("operation_id", operation_id);
  url.addParam("datetime", datetime);
  url.requestModal(600, 400);
  
  return false;
}
</script>

{{assign var=images value="CPrescription"|static:"images"}}
{{assign var=width value=800}}

{{*
<div class="small-warning">
  Cette vue est en cours de développement et n'est qu'un aperçu. 
</div>
*}}

<form name="change-operation-graph-pack" method="post" action="?" onsubmit="return onSubmitFormAjax(this, reloadSurveillancePerop)">
  {{mb_class object=$interv}}
  {{mb_key object=$interv}}

  {{mb_label object=$interv field=graph_pack_id}}

  <select name="graph_pack_id" onchange="this.form.onsubmit()">
    <option value="">&ndash; {{tr}}CSupervisionGraphPack.none{{/tr}}</option>

    {{foreach from=$graph_packs item=_pack}}
      <option value="{{$_pack->_id}}" {{if $_pack->_id == $interv->graph_pack_id}}selected{{/if}}>
        {{$_pack}}
      </option>
    {{/foreach}}
  </select>
</form>

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
    <td>ASA: <strong>{{mb_value object=$interv field=ASA}}</strong> </td>
  </tr>
</table>
<hr />

{{if $can->admin && 0}}
<form name="generate-sample-observation-results" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadSurveillancePerop})">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_sample_observation_results_generate" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="context_class" value="{{$interv->_class}}" />
  <input type="hidden" name="context_id" value="{{$interv->_id}}" />
  <input type="hidden" name="patient_id" value="{{$interv->_ref_sejour->patient_id}}" />
  <input type="hidden" name="datetime_start" value="{{$time_debut_op_iso}}" />
  <input type="hidden" name="datetime_end" value="{{$time_fin_op_iso}}" />
  <!--<input type="hidden" name="period" value="1800" />-->
  <button class="change">Générer un jeu de données aléatoires</button>
</form>
{{/if}}

{{*
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
*}}

{{if !$interv->graph_pack_id}}
  {{mb_return}}
{{/if}}

<button class="new" onclick="createObservationResultSet('{{$interv->_guid}}', '{{$pack->_id}}')">
  {{tr}}CObservationResultSet-title-create{{/tr}}
</button>

<div style="position: relative;" class="supervision">
  {{foreach from=$graphs item=_graph key=i}}
    {{if $_graph instanceof CSupervisionGraph}}
      {{assign var=_graph_data value=$_graph->_graph_data}}
      <div class="yaxis-labels" style="height:{{$_graph->height}}px;">
        {{foreach from=$_graph_data.yaxes|@array_reverse item=_yaxis}}
          <div style="position: relative;">
            {{$_yaxis.label}}
            <div class="symbol">{{$_yaxis.symbolChar|smarty:nodefaults}}&nbsp;</div>
          </div>
        {{/foreach}}
        {{*<span class="title">{{$_graph_data.title}}</span>*}}
      </div>
      <div id="placeholder-{{$i}}" style="width:{{$width}}px; height:{{$_graph->height}}px;" class="graph-placeholder"></div>
    {{elseif $_graph instanceof CSupervisionTimedData}}
      <table class="main evenements" style="table-layout: fixed; width: {{$width-12}}px; margin-bottom: -1px;">
        <col style="width: {{$yaxes_count*78-12}}px;" />

        <tr>
          <th style="word-wrap: break-word;">
            {{$_graph->title}}
          </th>
          <td>
            {{foreach from=$_graph->_graph_data item=_evenement}}
              {{if $_evenement.position <= 100}}
              <div style="padding-left: {{$_evenement.position}}%; margin-left: -1px;" class="evenement">
                <div>
                  <div class="marking"></div>
                  <div class="label" title="{{$_evenement.datetime|date_format:$conf.datetime}}">
                    {{$_evenement.value|truncate:40}}
                  </div>
                </div>
              </div>
              {{/if}}
            {{/foreach}}
          </td>
        </tr>
      </table>
    {{elseif $_graph instanceof CSupervisionTimedPicture}}
      <table class="main evenements" style="table-layout: fixed; width: {{$width-12}}px; margin-bottom: -1px; height: 66px;">
        <col style="width: {{$yaxes_count*78-12}}px;" />

        <tr>
          <th style="word-wrap: break-word;">
            {{$_graph->title}}
          </th>
          <td>
            <div style="position: relative;">
              {{foreach from=$_graph->_graph_data item=_picture}}
                {{if $_picture.file_id && $_picture.position <= 100}}
                  <div style="position: absolute; left: {{$_picture.position}}%; margin-left: -25px; text-align: center; padding-top: 5px;" title="{{$_picture.datetime|date_format:$conf.datetime}}">
                    <span style="position: absolute; left: 20px; top: -2px; width: 10px;">^</span>
                    <img style="width: 50px;"
                         src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_picture.file_id}}&amp;phpThumb=1&amp;w=100&amp;q=95" />
                    <br />
                    {{$_picture.file->_no_extension}}
                  </div>
                {{/if}}
              {{/foreach}}
            </div>
          </td>
        </tr>
      </table>
    {{/if}}

  {{/foreach}}
  
  <table class="main evenements" style="table-layout: fixed; width: {{$width-12}}px;">
    <col style="width: {{$yaxes_count*78-12}}px;" />
    
    {{foreach from=$evenements key=_label item=_evenements}}
      <tr>
        <th>
          {{tr}}{{$_label}}{{/tr}}

          {{if $_label == "CAnesthPerop"}}
            <button class="new notext compact" style="float: right;"
                    onclick="return editEvenementPerop('CAnesthPerop-0', '{{$interv->_id}}')"></button>
          {{/if}}
        </th>
        <td>
        {{foreach from=$_evenements item=_evenement}}
          {{if $_evenement.position <= 100}}
          {{assign var=evenement_width value=""}}
          {{if array_key_exists('width', $_evenement)}} 
            {{assign var=evenement_width value="width: `$_evenement.width`%;"}}
          {{/if}}
          
          <div style="padding-left: {{$_evenement.position}}%; margin-left: -1px; {{if $_evenement.alert}} color: red; {{/if}} {{if array_key_exists('width', $_evenement)}} margin-bottom: 2px; {{/if}}" class="evenement">
            <div onmouseover="ObjectTooltip.createEx(this, '{{$_evenement.object->_guid}}');" style="{{$evenement_width}}; {{if $_evenement.alert}} background: red; {{/if}}">
              <div class="marking"></div>
              <div class="label" title="{{$_evenement.datetime|date_format:$conf.datetime}} - {{if $_evenement.unit}}{{$_evenement.unit}}{{/if}} {{$_evenement.label}}">
                {{if $_evenement.editable}} 
                  <a href="#{{$_evenement.object->_guid}}"
                     onclick="return editEvenementPerop('{{$_evenement.object->_guid}}', '{{$interv->_id}}')"
                    {{if $_evenement.alert}} style="color: red;" {{/if}}>
                {{/if}}
                
                  {{if $_evenement.icon}}
                    {{assign var=_icon value=$_evenement.icon}}
                    <img src="{{$images.$_icon}}" />
                  {{/if}}
                  {{if $_evenement.unit}}
                    {{$_evenement.unit}} <strong>{{$_evenement.label|truncate:40}}</strong>
                  {{else}}
                    {{$_evenement.label|truncate:40}}
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
