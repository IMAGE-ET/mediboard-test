{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=sejour value=$operation->_ref_sejour}}
{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=consult_anesth value=$operation->_ref_consult_anesth}}

{{mb_script module=patients script=supervision_graph}}

<table class="print">
  <tr>
    <th class="title">
      <a href="#" onclick="window.print()" style="font-size: 1.3em;">
        Surveillance
      </a>
    </th>
  </tr>
</table>

<table class="print">
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Informations sur la patiente</th>
        </tr>
        <tr>
          <td colspan="2">{{$patient->_view}}</td>
        </tr>
        <tr>
          <td colspan="2">
            Né{{if $patient->sexe != "m"}}e{{/if}} le {{mb_value object=$patient field=naissance}}
            ({{$patient->_age}})
            - sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}
          </td>
        </tr>
      </table>
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>

        <tr>
          <th>{{mb_label object=$operation->_ref_plageop field=date}}</th>
          <td class="greedyPane">
            {{mb_value object=$operation->_ref_plageop field=date}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=salle_id}}</th>
          <td class="text">
            {{$operation->_ref_salle->_view}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=libelle}}</th>
          <td class="text">
            {{if $operation->libelle}}
              {{mb_value object=$operation field=libelle}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=ASA}}</th>
          <td>
            {{if $operation->ASA}}
              {{mb_value object=$operation field=ASA}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=cote}}</th>
          <td>{{mb_value object=$operation field=cote}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=position}}</th>
          <td>
            {{if $operation->position}}
              {{mb_value object=$operation field=position}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=chir_id}}</th>
          <td class="text">Dr {{$operation->_ref_chir->_view}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=anesth_id}}</th>
          <td class="text">
            {{if $operation->_ref_anesth->user_id}}
              Dr {{$operation->_ref_anesth->_view}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=type_anesth}}</th>
          <td class="text">
            {{if $operation->type_anesth}}
              {{mb_value object=$operation field=type_anesth}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Personnel</th>
          <td class="text">
            Non Disponible
          </td>
        </tr>
        <tr>
          <th>Matériel</th>
          <td class="text">
            {{if $operation->materiel}}
              {{mb_value object=$operation field=materiel}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$object field=exam_per_op}}</th>
          <td class="text">
            {{if $operation->exam_per_op}}
              {{mb_value object=$operation field=exam_per_op}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Remarques</th>
          <td class="text">
            {{if $operation->rques}}
              {{mb_value object=$operation field=rques}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Visite de pré-anesthésie</th>
        </tr>
        {{if $operation->prat_visite_anesth_id}}
        <tr>
          <th>{{mb_label object=$operation field=prat_visite_anesth_id}}</th>
          <td>Dr {{$operation->_ref_anesth_visite->_view}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=date_visite_anesth}}</th>
          <td>
            {{$operation->date_visite_anesth|date_format:$conf.date}}
            {{if "dPsalleOp COperation use_time_vpa"|conf:"CGroups-$g" && $operation->time_visite_anesth}}
              à {{$operation->time_visite_anesth|date_format:$conf.time}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=rques_visite_anesth}}</th>
          <td>{{mb_value object=$operation field=rques_visite_anesth}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=autorisation_anesth}}</th>
          <td>{{mb_value object=$operation field=autorisation_anesth}}</td>
        </tr>
        {{else}}
        <tr>
          <td colspan="2">Non saisie</td>
        </tr>
        {{/if}}
      </table>

    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Horaires</th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=debut_prepa_preop}}</th>
          <td class="halfPane">{{mb_value object=$operation field=debut_prepa_preop}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=fin_prepa_preop}}</th>
          <td class="halfPane">{{mb_value object=$operation field=fin_prepa_preop}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$operation field=entree_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=entree_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=induction_debut}}</th>
          <td class="halfPane">{{mb_value object=$operation field=induction_debut}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=induction_fin}}</th>
          <td class="halfPane">{{mb_value object=$operation field=induction_fin}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=pose_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=pose_garrot}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=debut_op}}</th>
          <td class="halfPane">{{mb_value object=$operation field=debut_op}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=fin_op}}</th>
          <td class="halfPane">{{mb_value object=$operation field=fin_op}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=retrait_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=retrait_garrot}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=sortie_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=sortie_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=entree_reveil}}</th>
          <td class="halfPane">{{mb_value object=$operation field=entree_reveil}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=sortie_reveil_possible}}</th>
          <td class="halfPane">{{mb_value object=$operation field=sortie_reveil_possible}}</td>
        </tr>
      </table>    
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Durées</th>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=_presence_salle}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_presence_salle}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$operation field=_duree_interv}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_interv}}</td>
        </tr>
        {{if $operation->_duree_garrot}}
        <tr>
          <th>{{mb_label object=$operation field=_duree_garrot}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_garrot}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$operation field=_duree_sspi}}</th>
          <td class="halfPane">{{mb_value object=$operation field=_duree_sspi}}</td>
        </tr>
      </table>      
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <table width="100%" style="border-spacing: 0px;font-size: 100%;">
        <tr>
          <th class="category" colspan="5">Actes CCAM</th>
        </tr>
        {{assign var="styleBorder" value="border: solid #aaa 1px;"}}
        <tr>
          <th style="{{$styleBorder}}text-align:left;">Code</th>
          <th style="{{$styleBorder}}text-align:left;">Exécutant</th>
          <th style="{{$styleBorder}}text-align:left;">Activité</th>
          <th style="{{$styleBorder}}text-align:left;">Phase &mdash; Modifs.</th>
          <th style="{{$styleBorder}}text-align:left;">Association</th>
        </tr>
        {{foreach from=$operation->_ref_actes_ccam item=currActe}}
        <tr>
          <td class="text" style="{{$styleBorder}}">
            <strong>{{$currActe->code_acte}}</strong><br />
            {{$currActe->_ref_code_ccam->libelleLong}}
          </td>
          <td class="text" style="{{$styleBorder}}">{{$currActe->_ref_executant->_view}}</td>
          <td style="{{$styleBorder}}">{{$currActe->code_activite}}</td>
          <td style="{{$styleBorder}}">
            {{$currActe->code_phase}}
            {{if $currActe->modificateurs}}
            &mdash; {{$currActe->modificateurs}}
            {{/if}}
          </td>
          <td style="{{$styleBorder}}">{{$currActe->_guess_association}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>

  {{if $sejour->grossesse_id}}
    <tr>
      <th colspan="2" class="category">
        {{tr}}CGrossesse{{/tr}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour->_ref_grossesse field=terme_prevu}}</th>
      <td>{{mb_value object=$sejour->_ref_grossesse field=terme_prevu}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour->_ref_grossesse field=rques}}</th>
      <td>{{mb_value object=$sejour->_ref_grossesse field=rques}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour->_ref_grossesse field=datetime_debut_travail}}</th>
      <td>{{mb_value object=$sejour->_ref_grossesse field=datetime_debut_travail}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour->_ref_grossesse field=datetime_accouchement}}</th>
      <td>{{mb_value object=$sejour->_ref_grossesse field=datetime_accouchement}}</td>
    </tr>
  {{/if}}
</table>

<script type="text/javascript">
  Main.add(function(){
    var lastDay;
    var xTickFormatter = function (val, axis) {
      var date = new Date(val);
      var day = date.getUTCDate();
      var formatted;

      if (val < axis.min || val > axis.max) {
        return;
      }

      if (!lastDay || lastDay != day) {
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

      lastDay = day;

      return formatted;
    };

    (function ($){
      var ph, series, xaxes;

      {{foreach from=$perop_graphs item=_graph key=i name=graphs}}
        {{if $_graph instanceof CSupervisionGraph}}
          {{assign var=_graph_data value=$_graph->_graph_data}}

          ph = $("#placeholder-{{$i}}");
          series = {{$_graph_data.series|@json}};
          xaxes  = {{$_graph_data.xaxes|@json}};
          xaxes[0].ticks = 10;
          xaxes[0].tickFormatter = xTickFormatter;

          $.plot(ph, series, {
            grid: {
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
</script>

{{assign var=images value="CPrescription"|static:"images"}}
{{assign var=width value=750}}
{{assign var=right_margin value=5}}
{{assign var=yaxis_width value=75}}
{{assign var=dummy_yaxis_width value=12}}
{{math assign=left_col_width equation="$yaxes_count*$yaxis_width+$dummy_yaxis_width"}}

<table class="main print">
  <tr>
    <th class="category" colspan="4">Surveillance</th>
  </tr>
  <tr>
    <td>
      <div style="position: relative;" class="supervision">
        {{foreach from=$perop_graphs item=_graph key=i}}
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
            <div id="placeholder-{{$i}}" style="width:{{$width}}px; height:{{$_graph->height}}px;"></div>

          {{elseif $_graph instanceof CSupervisionTimedData}}
            <table class="main evenements" style="table-layout: fixed; width: {{$width-$right_margin}}px; margin-bottom: -1px;">
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
            <table class="main evenements" style="table-layout: fixed; width: {{$width-$right_margin}}px; margin-bottom: -1px; height: 90px;">
              <col style="width: {{$left_col_width}}px;" />

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
                          <img style="height: 50px;"
                               src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_picture.file_id}}&amp;phpThumb=1&amp;w=100&amp;q=95" />
                          <br />
                          {{$_picture.file->_no_extension}}
                          <br />
                          {{$_picture.datetime|date_format:$conf.time}}
                        </div>
                      {{/if}}
                    {{/foreach}}
                  </div>
                </td>
              </tr>
            </table>
          {{/if}}
        {{/foreach}}

        <table class="main evenements" style="table-layout: fixed; width: {{$width-$right_margin}}px;">
          <col style="width: {{$left_col_width}}px;" />

          {{foreach from=$evenements key=_label item=_evenements}}
            {{if @$_evenements.subitems}}
            <tr>
              <th colspan="2" style="text-align: center;">
                <strong>
                  {{tr}}{{$_label}}{{/tr}}
                </strong>

                {{if $_evenements.icon}}
                  {{assign var=_icon value=$_evenements.icon}}
                  <img src="{{$images.$_icon}}" />
                {{/if}}
              </th>
            </tr>
            {{foreach from=$_evenements.subitems key=_subkey item=_subitem}}
              <tr>
                <th style="text-align: right; padding: 2px;">
                  {{$_subitem.label}}
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
              </th>
              <td>
                {{foreach from=$_evenements item=_evenement}}
                  {{if $_evenement.position <= 100}}
                    {{assign var=evenement_width value=""}}
                    {{if array_key_exists('width', $_evenement)}}
                      {{assign var=evenement_width value="width: `$_evenement.width`%;"}}
                    {{/if}}

                    <div style="padding-left: {{$_evenement.position}}%; margin-left: -1px; {{if $_evenement.alert}} color: red; {{/if}} {{if array_key_exists('width', $_evenement)}} margin-bottom: 2px; {{/if}}" class="evenement">
                      <div onmouseover="ObjectTooltip.createEx(this, '{{$_evenement.object->_guid}}');" style="{{$evenement_width}}">
                        <div class="label" title="{{$_evenement.datetime|date_format:$conf.datetime}} - {{if $_evenement.unit}}{{$_evenement.unit}}{{/if}} {{$_evenement.label}}">
                          {{if $_evenement.icon}}
                            {{assign var=_icon value=$_evenement.icon}}
                            <img src="{{$images.$_icon}}" />
                          {{/if}}
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
            {{/if}}
          {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
</table>