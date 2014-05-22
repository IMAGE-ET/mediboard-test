{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_include module=system template=inc_pagination change_page="changePage" total=$nbresult current=$start step=30}}
{{mb_script module="dPfiles" script="file" ajax=true}}
<table class="tbl form" style="height: 70%">
  <tbody>
    <tr>
      <th class="title" colspan="6">Résultats ({{$nbresult}} obtenus en {{$time}}ms)</th>
    </tr>
    <tr>
      <th class="narrow">Date </th>
      <th class="narrow">Type </th>
      <th>Titre du document</th>
      <th class="narrow">Auteur</th>
      <th class="narrow">Patient</th>
      <th class="narrow">Pertinence</th>
    </tr>
    <tr>
      <th colspan="6" class="section">Triés par pertinence</th>
    </tr>
    <tr>
      {{foreach from=$results item=_result}}
          <tr>
            <td class="compact"> {{$_result._source.date|substr:0:10}} </td>
            <td class="compact"> {{tr}}{{$_result._type}}{{/tr}} </td>
            {{if $_result._source.title != ""}}
              <td class="text">
               <span onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')">{{$_result._source.title|utf8_decode}}</span>
              </td>
            {{else}}
              <td  class="empty">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')"> ---- Titre non présent---</span>
              </td>
            {{/if}}
            {{if $_result._source.author_id}}
              {{assign var=author_id value=$_result._source.author_id}}
              <td>
                {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$authors.$author_id`}}
              </td>
            {{else}}
              <td  class="empty">Utilisateur inconnu</td>
            {{/if}}


            {{if $_result._source.patient_id}}
              <td>
                {{assign var=patient_id value=$_result._source.patient_id}}
                <span onmouseover="ObjectTooltip.createEx(this, 'CPatient-{{$patient_id}}')">{{$patients.$patient_id}}</span>
              </td>
            {{else}}
              <td  class="empty">Patient inconnu</td>
            {{/if}}

            <td>
              {{assign var=score value=$_result._score*100}}
              <meter min="0" max="100" value="{{$score}}" low="50.0" optimum="101.0" high="70.0" style="width:150px;" title="{{$score}}%">
                <div class="progressBar compact text">
                  <div class="bar normal" style="width:{{$score}}%;">
                  </div>
                  <div class="text">
                    {{$score}}%
                  </div>
                </div>
              </meter>
            </td>
          </tr>
     {{foreachelse}}
      <tr>
        <td colspan="6" class="empty" style="text-align: center">
            Aucun document ne correspond à la recherche
        </td>
      </tr>
    {{/foreach}}
  </tbody>
</table>

