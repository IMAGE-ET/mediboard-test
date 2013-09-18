{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function(){
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
  url.addParam("patient_id", {{$operation->_ref_sejour->patient_id}});
  url.addParam("context_guid", "{{$operation->_ref_sejour->_guid}}");
  url.addParam("selection[]", ["pouls", "ta_gauche", "frequence_respiratoire", "score_sedation", "spo2", "diurese"]);
  url.addParam("date_min", "{{$operation->_datetime_reel}}");
  url.addParam("date_max", "{{$operation->_datetime_reel_fin}}");
  url.addParam("print", 1);
  url.requestUpdate("constantes");
  
  {{if "forms"|module_installed}}
    ExObject.loadExObjects("{{$operation->_class}}", "{{$operation->_id}}", "ex_objects_list", 3, null, {print: 1});
  {{/if}}
});

</script>

{{assign var=sejour value=$operation->_ref_sejour}}
{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=consult_anesth value=$operation->_ref_consult_anesth}}

<table class="print">
  <tr>
    <th class="title">
      <a href="#" onclick="window.print()" style="font-size: 1.3em;">
        Feuille de Bloc
      </a>
    </th>
  </tr>
</table>

<table class="print">
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
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
          <td>{{$operation->date_visite_anesth|date_format:$conf.date}}</td>
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
</table>

<table class="print"> 
  <tr>
    <th class="category" colspan="4">Evenements per-opératoires</th>
  </tr>   
  {{foreach from=$perops key=datetime item=_perops_by_datetime}}
    {{foreach from=$_perops_by_datetime item=_perop}}
      <tr>
          <td style="text-align: center;">{{mb_ditto name=date value=$datetime|date_format:$conf.date}}</td>
          <td style="text-align: center;">{{mb_ditto name=time value=$datetime|date_format:$conf.time}}</td>
        {{if $_perop instanceof CAnesthPerop}}
          <td style="font-weight: bold;" colspan="2" class="greedyPane">
            {{if $_perop->incident}}
              Incident :
            {{/if}}
            {{mb_value object=$_perop field=libelle}}
          </td>
        {{elseif $_perop instanceof CAdministration}}
          {{assign var=unite value=""}}
          {{if $_perop->_ref_object instanceof CPrescriptionLineMedicament || $_perop->_ref_object instanceof CPrescriptionLineMixItem}}
            {{assign var=unite value=$_perop->_ref_object->_ref_produit->libelle_unite_presentation}}
          {{/if}}

          <td colspan="2" class="greedyPane">
            {{if $_perop->_ref_object instanceof CPrescriptionLineElement}}
              {{$_perop->_ref_object->_view}}
            {{else}}
              {{$_perop->_ref_object->_ucd_view}}
            {{/if}}
            <strong>{{$_perop->quantite}} {{$unite}} </strong>
          </td>
        {{elseif $_perop instanceof CPrescriptionLineMix}}
          <td>
            {{if $datetime == $_perop->_pose}}
            Pose de la perfusion - 
            {{else}}
            Retrait de la perfusion - 
            {{/if}} 
            {{$_perop->_short_view}}
          </td>
        {{else}}
          <td colspan="2" class="greedyPane">
          {{foreach from=$_perop key=type item=_constante}}
            {{if $_constante}}
            <strong>{{tr}}CConstantesMedicales-{{$type}}{{/tr}}:</strong> {{$_constante}}<br />
            {{/if}}
          {{/foreach}}
          </td>
        {{/if}}
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>

{{if $conf.dPsalleOp.enable_surveillance_perop}}
<script type="text/javascript">
  Main.add(function(){
    (function ($){
      var ph, series, xaxes;

      {{foreach from=$perop_graphs item=_graph key=i name=graphs}}
        {{if $_graph instanceof CSupervisionGraph}}
          {{assign var=_graph_data value=$_graph->_graph_data}}

          ph = $("#placeholder-{{$i}}");
          series = {{$_graph_data.series|@json}};
          xaxes  = {{$_graph_data.xaxes|@json}};
          xaxes[0].ticks = 10;

          $.plot(ph, series, {
            grid: { hoverable: true, markings: [
              // Debut op
              {xaxis: {from: 0, to: {{$time_debut_op}}}, color: "rgba(0,0,0,0.05)"},
              {xaxis: {from: {{$time_debut_op}}, to: {{$time_debut_op+1000}}}, color: "black"},

              // Fin op
              {xaxis: {from: {{$time_fin_op}}, to: Number.MAX_VALUE}, color: "rgba(0,0,0,0.05)"},
              {xaxis: {from: {{$time_fin_op}}, to: {{$time_fin_op+1000}}}, color: "black"}
            ] },
            series: SupervisionGraph.defaultSeries,
            xaxes: xaxes,
            yaxes: {{$_graph_data.yaxes|@json}}
          });
        {{/if}}
      {{/foreach}}

    })(jQuery);
  });
</script>

{{assign var=width value=700}}

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
              {{foreach from=$_graph_data.yaxes|@array_reverse item=_yaxis}}
                <div style="position: relative;">
                  {{$_yaxis.label}}
                  <div class="symbol">{{$_yaxis.symbolChar|smarty:nodefaults}}&nbsp;</div>
                </div>
              {{/foreach}}
              {{*<span class="title">{{$_graph_data.title}}</span>*}}
            </div>
            <div id="placeholder-{{$i}}" style="width:{{$width}}px; height:{{$_graph->height}}px;"></div>

          {{elseif $_graph instanceof CSupervisionTimedData}}
            {{*
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
            *}}
            <table class="main tbl">
              <tr>
                <th colspan="2" class="category">
                  {{$_graph->title}}
                </th>
              </tr>

              {{foreach from=$_graph->_graph_data item=_evenement}}
              <tr>
                <td class="narrow" style="white-space: nowrap;">
                  {{$_evenement.datetime|date_format:$conf.datetime}}
                </td>
                <td>
                  {{$_evenement.value}}
                </td>
              </tr>
              {{/foreach}}
            </table>

          {{elseif $_graph instanceof CSupervisionTimedPicture}}
            <table class="main tbl">
              <tr>
                <th colspan="3" class="category">
                  {{$_graph->title}}
                </th>
              </tr>

              {{foreach from=$_graph->_graph_data item=_picture}}
                <tr>
                  <td class="narrow" style="white-space: nowrap;">
                    {{$_picture.datetime|date_format:$conf.datetime}}
                  </td>
                  <td class="narrow" style="white-space: nowrap;">
                    {{if array_key_exists("file", $_picture)}}
                      {{$_picture.file->_no_extension}}
                    {{/if}}
                  </td>
                  <td>
                    {{if $_picture.file_id}}
                      <img style="width: 50px;"
                           src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_picture.file_id}}&amp;phpThumb=1&amp;w=100&amp;q=95" />
                    {{/if}}
                  </td>
                </tr>
              {{/foreach}}
            </table>

            {{*
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
            *}}
          {{/if}}
        {{/foreach}}
      </div>
    </td>
  </tr>
</table>
{{/if}}

<div id="constantes"></div>

{{if "forms"|module_installed}}
  <table class="print"> 
    <tr>
      <th class="category">Formulaires</th>
    </tr> 
    <tr>
      <td id="ex_objects_list"></td>
    </tr>
  </table>
{{/if}}
