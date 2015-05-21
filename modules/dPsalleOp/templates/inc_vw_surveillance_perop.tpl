{{assign var=right_margin value=5}}
{{assign var=yaxis_width value=75}}
{{assign var=dummy_yaxis_width value=12}}
{{math assign=left_col_width equation="$yaxes_count*$yaxis_width-$right_margin/2+$dummy_yaxis_width"}}

{{assign var=_readonly value=false}}
{{if "maternite"|module_active && $interv->_ref_sejour->grossesse_id}}
  {{assign var=_grossesse value=$interv->_ref_sejour->loadRefGrossesse()}}

  {{if "maternite CGrossesse lock_partogramme"|conf:"CGroups-$g" && $_grossesse->datetime_accouchement}}
    {{assign var=_readonly value=true}}
  {{/if}}
{{/if}}

<script>
SurveillancePerop = {
  previousPoint: null,
  lastDay: null,
  graphs: [],

  editPeropAdministration: function(operation_id) {
    var url = new Url('dPsalleOp', 'ajax_vw_surveillance_perop_administration');
    url.addParam('operation_id', operation_id);
    url.modal({
      width: 1000,
      height: 600,
      onClose: reloadSurveillancePerop
    });
  },

  xTickFormatter: function (val, axis) {
    var date = new Date(val);
    var day = date.getUTCDate();
    var formatted;

    if (val < axis.min || val > axis.max) {
      return;
    }

    if (!SurveillancePerop.lastDay || SurveillancePerop.lastDay != day) {
      formatted = printf(
        "<strong>%02d:%02d</strong><br /> %02d/%02d",
        date.getUTCHours(),
        date.getUTCMinutes(),
        date.getUTCDate(),
        date.getUTCMonth()+1
      );
    }
    else {
      formatted = printf(
        "<strong>%02d:%02d</strong>",
        date.getUTCHours(),
        date.getUTCMinutes()
      );
    }

    SurveillancePerop.lastDay = day;

    return formatted;
  },

  plothover: function (event, pos, item) {
    if (item) {
      var key = item.dataIndex+"-"+item.seriesIndex;

      if (SurveillancePerop.previousPoint != key) {
        SurveillancePerop.previousPoint = key;
        jQuery("#flot-tooltip").remove();

        var yaxis = item.series.yaxis.n;
        event.target.select(".flot-y"+yaxis+"-axis, .flot-y"+yaxis+"-axis .flot-tick-label").invoke("addClassName", "axis-onhover");

        var contents = SupervisionGraph.formatTrack(item);

        $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, contents).setStyle({
          position: 'absolute',
          top:  item.pageY + 5 + "px",
          left: item.pageX + 5 + "px"
        }));
      }
    }
    else {
      $$(".axis-onhover").invoke("removeClassName", "axis-onhover");

      jQuery("#flot-tooltip").remove();
      SurveillancePerop.previousPoint = null;
    }
  },

  plotclick: function(event, pos, item){
    {{if !$_readonly}}
      if (!item) {
        return;
      }

      var data = item.series.data[item.dataIndex];
      editObservationResultSet(data.set_id, '{{$pack->_id}}', data.result_id);
    {{/if}}
  }
};

enChantier = function(){
  Modal.alert("Fonctionnalité en cours de développement");
};
  
Main.add(function(){
  var width = $("surveillance_cell").getWidth();

  $$(".graph-placeholder").invoke("setStyle", {width: width+"px"});

  $$(".supervision .evenements").invoke("setStyle", {width: (width-{{$right_margin}})+"px"});
  
  (function ($){
    var ph, series, options, xaxes;
    
    {{foreach from=$graphs item=_graph key=i name=graphs}}
      {{if $_graph instanceof CSupervisionGraph}}
        {{assign var=_graph_data value=$_graph->_graph_data}}

        ph = $("#placeholder-{{$i}}");
        series = {{$_graph_data.series|@json}};
        xaxes  = {{$_graph_data.xaxes|@json}};
        xaxes[0].ticks = 10;
        xaxes[0].tickFormatter = SurveillancePerop.xTickFormatter;

        ph.bind("plothover", SurveillancePerop.plothover);
        ph.bind("plotclick", SurveillancePerop.plotclick);

        options = {
          grid: {
            hoverable: true,
              {{if !$_readonly}}
              clickable: true,
              {{/if}}
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
        };

        SurveillancePerop.graphs.push({
          holder: ph,
          series: series,
          options: options
        });
      {{/if}}
    {{/foreach}}

    SurveillancePerop.graphs.each(function(graph){
      $.plot(graph.holder, graph.series, graph.options);
    });
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

quickEvenementPerop = function(operation_id) {
  var url = new Url("dPsalleOp", "ajax_quick_evenement_perop");
  url.addParam("operation_id", operation_id);
  url.requestModal(700, 400, {onClose: reloadSurveillancePerop});

  return false;
}

printSurveillance = function(operation_id) {
  var url = new Url("dPsalleOp", "vw_partogramme");
  url.addParam("operation_id", operation_id);
  url.pop(750, 700, "Impression surveillance");
}
</script>

{{assign var=images value="CPrescription"|static:"images"}}

<table class="main tbl">
  <tr>
    <td class="narrow">
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

      {{if $interv->graph_pack_id && $concentrators !== null}}
        {{mb_include module=patientMonitoring template=inc_concentrator_js ajax=true}}

        <select name="concentrator_id" onchange="Concentrator.selectConcentrator(this)">
          <option value="">&ndash; {{tr}}CMonitoringConcentrator.none{{/tr}}</option>

          {{foreach from=$concentrators item=_concentrator}}
            <option value="{{$_concentrator->_id}}"
                    data-host="{{$_concentrator->host_address}}"
                    data-port="{{$_concentrator->host_port}}">
              {{$_concentrator}}
            </option>
          {{/foreach}}
        </select>

        <span>
          -
        </span>

        <button class="change notext" onclick="Concentrator.selectConcentrator(this.previous('select'))"></button>
      {{/if}}
    </td>
    <td>
      <strong>
        {{$interv->_ref_sejour->_ref_patient->_ref_constantes_medicales->poids}} Kg &ndash;
        {{$interv->_ref_sejour->_ref_patient->_ref_constantes_medicales->taille}} cm
      </strong>
    </td>
    {{assign var=dossier_medical value=$interv->_ref_sejour->_ref_patient->_ref_dossier_medical}}
    <td>Gr. sang. / Rh: <strong>{{mb_value object=$dossier_medical field=groupe_sanguin}} {{mb_value object=$dossier_medical field=rhesus}}</strong></td>
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

{{if "maternite"|module_active && $interv->_ref_sejour->grossesse_id}}
  {{assign var=_grossesse value=$interv->_ref_sejour->loadRefGrossesse()}}

  <table class="main layout">
    <tr>
      <td class="narrow">
        <form name="edit-grossesse-accouchement-{{$_grossesse->_id}}-datetime_debut_travail" method="post" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="maternite"/>
          {{mb_class object=$_grossesse}}
          {{mb_key   object=$_grossesse}}
          <input type="hidden" name="callback" value="reloadSurveillancePerop" />

          {{if $_grossesse->datetime_debut_travail}}
            {{mb_label object=$_grossesse field=datetime_debut_travail}}
            {{mb_field object=$_grossesse field=datetime_debut_travail register=true
                       form="edit-grossesse-accouchement-`$_grossesse->_id`-datetime_debut_travail" onchange="this.form.onsubmit()"}}
          {{else}}
            <input type="hidden" name="datetime_debut_travail" value="now" />
            <button type="submit" class="save">{{tr}}CGrossesse-datetime_debut_travail{{/tr}}</button>
          {{/if}}
        </form>

        <form name="edit-grossesse-accouchement-{{$_grossesse->_id}}-datetime_accouchement" method="post" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="maternite"/>
          {{mb_class object=$_grossesse}}
          {{mb_key   object=$_grossesse}}
          <input type="hidden" name="callback" value="reloadSurveillancePerop" />

          {{if $_grossesse->datetime_accouchement}}
            {{mb_label object=$_grossesse field=datetime_accouchement}}
            {{mb_field object=$_grossesse field=datetime_accouchement register=true
                       form="edit-grossesse-accouchement-`$_grossesse->_id`-datetime_accouchement" onchange="this.form.onsubmit()"}}
          {{else}}
            <input type="hidden" name="datetime_accouchement" value="now" />
            <button type="submit" class="save">{{tr}}CGrossesse-datetime_accouchement{{/tr}}</button>
          {{/if}}
        </form>
      </td>
      {{*
      <td class="narrow">
        <button onclick="enChantier()" class="save">Entrée salle</button>
        <button onclick="enChantier()" class="save">Sortie salle</button>
      </td>
      *}}
    </tr>
  </table>
  <hr />
{{/if}}

{{if !$interv->graph_pack_id}}
  {{mb_return}}
{{/if}}

<button class="new" onclick="createObservationResultSet('{{$interv->_guid}}', '{{$pack->_id}}')" {{if $_readonly}} disabled {{/if}}>
  {{tr}}CObservationResultSet-title-create{{/tr}}
</button>

<button class="injection" onclick="SurveillancePerop.editPeropAdministration('{{$interv->_id}}')" {{if $_readonly}} disabled {{/if}}>Administrer</button>

<button class="print" onclick="printSurveillance({{$interv->_id}})">Imprimer surveillance</button>

{{assign var=require_right_col value=false}}
{{foreach from=$graphs item=_graph key=i}}
  {{if $_graph instanceof CSupervisionInstantData}}
    {{assign var=require_right_col value=true}}
  {{/if}}
{{/foreach}}

<div style="position: relative;" class="supervision">
  <table class="main layout">
    <tr>
      <td id="surveillance_cell">
  {{foreach from=$graphs item=_graph key=i}}
    {{if $_graph instanceof CSupervisionGraph}}
      {{assign var=_graph_data value=$_graph->_graph_data}}
      <div class="yaxis-labels" style="height:{{$_graph->height}}px;">
        {{foreach from=$_graph_data.yaxes|@array_reverse item=_yaxis name=_yaxis}}
          {{if !$smarty.foreach._yaxis.last}}
          <div style="position: relative; color: {{$_yaxis.color}};">
            {{$_yaxis.label}}
            <div class="symbol">{{$_yaxis.symbolChar|smarty:nodefaults}}&nbsp;</div>
          </div>
          {{/if}}
        {{/foreach}}
        {{*<span class="title">{{$_graph_data.title}}</span>*}}
      </div>
      <div id="placeholder-{{$i}}" style="height:{{$_graph->height}}px;" class="graph-placeholder"></div>
    {{elseif $_graph instanceof CSupervisionTimedData}}
      <table class="main evenements" style="table-layout: fixed; margin-bottom: -1px;">
        <col style="width: {{$left_col_width}}px;" />

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
                  <div class="label" title="{{$_evenement.datetime|date_format:$conf.datetime}}
{{$_evenement.value|JSAttribute|replace:"\\n":"\n"|replace:"\\r":""}}">
                    <a href="#" {{if !$_readonly}} onclick="editObservationResultSet('{{$_evenement.set_id}}', '{{$pack->_id}}', '{{$_evenement.result_id}}')" {{/if}}>
                      {{$_evenement.value|truncate:60|nl2br}}
                    </a>
                  </div>
                </div>
              </div>
              {{/if}}
            {{/foreach}}
          </td>
        </tr>
      </table>
    {{elseif $_graph instanceof CSupervisionTimedPicture}}
      <table class="main evenements" style="table-layout: fixed; margin-bottom: -1px; height: 70px;">
        <col style="width: {{$left_col_width}}px;" />

        <tr>
          <th style="word-wrap: break-word;">
            {{$_graph->title}}
          </th>
          <td>
            <div style="position: relative;">
              {{foreach from=$_graph->_graph_data item=_picture}}
                {{if $_picture.file_id && $_picture.position <= 100}}
                  <div style="position: absolute; left: {{$_picture.position}}%; margin-left: -25px; text-align: center; padding-top: 5px; cursor: pointer;"
                       title="{{$_picture.datetime|date_format:$conf.datetime}}"
                       {{if !$_readonly}} onclick="editObservationResultSet('{{$_picture.set_id}}', '{{$pack->_id}}', '{{$_picture.result_id}}')" {{/if}}
                    >
                    <span style="position: absolute; left: 20px; top: -2px; width: 10px;">^</span>
                    <img style="height: 50px;"
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
  
  <table class="main evenements" style="table-layout: fixed;">
    <col style="width: {{$left_col_width}}px;" />
    
    {{foreach from=$evenements key=_label item=_evenements}}
      {{if @$_evenements.subitems}}
        <tr>
          <th colspan="2">
            <strong>
              {{tr}}{{$_label}}{{/tr}}
            </strong>

            {{if $_evenements.icon}}
              {{assign var=_icon value=$_evenements.icon}}
              <img src="{{$images.$_icon}}" />
            {{/if}}

            {{if $_label == "CPrescription._chapitres.med" && !$_readonly}}
              <button class="add notext compact" onclick="SurveillancePerop.editPeropAdministration('{{$interv->_id}}')"></button>
            {{/if}}
          </th>
        </tr>
        {{foreach from=$_evenements.subitems key=_subkey item=_subitem}}
          <tr>
            <th style="text-align: right; padding: 2px;">
              <strong onmouseover="ObjectTooltip.createEx(this, '{{$_subitem.line->_guid}}');">
                {{$_subitem.label|truncate:50}}
              </strong>
  {{*
              <button class="add notext compact" style="float: right;" disabled
                      onclick="alert('Fonctionnalité pas encore implémentée')">
                Administrer
              </button>
*}}
            </th>
            <td>
              {{foreach from=$_subitem.items item=_evenement}}
                {{if $_evenement.position <= 100}}
                  {{assign var=evenement_width value=""}}
                  {{if array_key_exists('width', $_evenement)}}
                    {{assign var=evenement_width value="width: `$_evenement.width`%;"}}
                  {{/if}}

                  <div style="padding-left: {{$_evenement.position}}%; margin-left: -1px; {{if $_evenement.alert}} color: red; {{/if}} {{if array_key_exists('width', $_evenement)}} margin-bottom: 2px; {{/if}}" class="evenement">
                    <div onmouseover="ObjectTooltip.createEx(this, '{{$_evenement.object->_guid}}');" style="{{$evenement_width}}; {{if $_evenement.alert}} background: red; {{/if}}">
                      <div class="marking"></div>
                      <div class="label" title="{{$_evenement.datetime|date_format:$conf.datetime}} - {{if $_evenement.unit}}{{$_evenement.unit}}{{/if}} {{$_evenement.label}}">
                        {{if $_evenement.unit}}
                          {{$_evenement.unit}} <strong>{{$_evenement.label|truncate:40}}</strong>
                        {{else}}
                          {{$_evenement.label|truncate:40}}
                        {{/if}}
                      </div>
                    </div>
                  </div>
                {{/if}}
              {{/foreach}}
            </td>
          </tr>
        {{/foreach}}
      {{else}}
      <tr>
        <th>
          {{tr}}{{$_label}}{{/tr}}

          {{if $_label == "CAnesthPerop" && !$_readonly}}
            <div style="float: right;">
              <button class="new notext compact"
                      onclick="return editEvenementPerop('CAnesthPerop-0', '{{$interv->_id}}')"></button>

              <button class="new-lightning notext compact"
                      onclick="return quickEvenementPerop('{{$interv->_id}}')"></button>
            </div>
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
                {{if $_evenement.editable && !$_readonly}}
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
      {{/if}}
    {{/foreach}}
    
    {{if $now <= 100}}
    <tr>
      <th style="padding: 0; border: none;"></th>
      <td style="padding: 0; border: none;">
        <div class="now opacity-50" style="padding-left: {{$now}}%;">
          <div class="marking"></div>
        </div>
      </td>
    </tr>
    {{/if}}
  </table>
      </td>

      {{if $require_right_col}}
      <td style="{{if $require_right_col}}width: 200px;{{/if}}">
        {{foreach from=$graphs item=_graph key=i}}
          {{if $_graph instanceof CSupervisionInstantData}}
            <div id="container-{{$_graph->_guid}}">
              {{$_graph->_ref_value_type}}

              <div style="line-height: 1; color: #{{$_graph->color}};">
                <span style="font-size: {{$_graph->size}}px;" class="supervision-instant-data"
                      data-value_type_id="{{$_graph->value_type_id}}"
                      data-value_unit_id="{{$_graph->value_unit_id}}">
                  -
                </span>
                <span style="font-size: 1.2em">{{$_graph->_ref_value_unit}}</span>
              </div>
            </div>
            <hr />
          {{/if}}
        {{/foreach}}
      </td>
      {{/if}}
    </tr>
  </table>
</div>
