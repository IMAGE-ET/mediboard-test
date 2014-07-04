{{*
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="files" script="file" ajax=true}}
<script>
  Main.add (function () {
    Search.words_request = '{{$words}}';
  });
</script>
{{if $objects_refs}}
  <table class="tbl form" id="results" style="height: 70%">
    <tbody>
      <tr>
      <th class="title" colspan="6">Résultats ({{$nbresult}} obtenus en {{$time}}ms)</th>
    </tr>
      <tr>
      <th>
        <span>Contexte</span>
        <input type="text" id="filter-contexte" size="20" onkeyup="Search.filter(this, 'contextes', 'results')" />
        <label for="filter-contexte"></label>
      </th>
      <th class="narrow">Praticien</th>
      <th>Détail des occurrences</th>
    </tr>
      <tr>
      <th colspan="6" class="section">Triés par nombre d'occurrences</th>
    </tr>
      {{foreach from=$objects_refs key=_key item=_object_ref}}
      <tr>
        <td class="text contextes" style="width:40%">
          {{if $_object_ref.object->_class == "CSejour"}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_object_ref.object->_guid}}')">{{$_object_ref.object->_view}}</span>
          {{elseif $_object_ref.object->_class == "CConsultAnesth"}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_object_ref.object->_guid}}')">
              {{$_object_ref.object->_ref_consultation->_ref_patient->_civilite}} {{$_object_ref.object->_ref_consultation->_ref_patient->_p_last_name}}
              {{$_object_ref.object->_ref_consultation->_ref_patient->_p_first_name}} - {{$_object_ref.object->_view}}  du  {{$_object_ref.object->_ref_consultation->_ref_plageconsult->date|date_format:'%d/%m/%Y'}}
            </span>
          {{elseif $_object_ref.object->_class == "CConsultation"}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_object_ref.object->_guid}}')">
              {{$_object_ref.object->_ref_patient->_civilite}} {{$_object_ref.object->_ref_patient->_p_last_name}}
              {{$_object_ref.object->_ref_patient->_p_first_name}} - {{$_object_ref.object->_view}}  du  {{$_object_ref.object->_ref_plageconsult->date|date_format:'%d/%m/%Y'}}
            </span>
          {{else}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_object_ref.object->_guid}}')">
              {{$_object_ref.object->_ref_patient->_civilite}} {{$_object_ref.object->_ref_patient->_p_last_name}}
              {{$_object_ref.object->_ref_patient->_p_first_name}} - {{$_object_ref.object->_view}} du {{$_object_ref.object->date|date_format:'%d/%m/%Y'}}
            </span>
          {{/if}}
        </td>
        <td style="width:15%">
          {{if $_object_ref.object->_class == "CConsultAnesth"}}
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$_object_ref.object->_ref_consultation->_ref_praticien`}}
           {{else}}
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$_object_ref.object->_ref_praticien`}}
          {{/if}}
        </td>
        <td class="text">
          {{foreach from=$_object_ref.type key=_key item=_sejour_type}}
            <span>{{$_sejour_type.count}} {{tr}}{{$_sejour_type.key}}{{/tr}} trouvée(s)</span>
            <a href="#" class="button search notext" style="float:right"
               onclick="Search.searchMoreDetails('{{$_object_ref.object->_id}}', '{{$_object_ref.object->_class}}', '{{$_sejour_type.key}}')">
            </a>
            <div id="details-{{$_sejour_type.key}}-{{$_object_ref.object->_id}}"></div>
            <hr/>
          {{/foreach}}
        </td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="4" class="empty" style="text-align: center">
          Aucun document ne correspond à la recherche
        </td>
      </tr>
      {{/foreach}}
    </tbody>
  </table>
{{else}}
  {{mb_include module=system template=inc_pagination change_page="changePage" total=$nbresult current=$start step=30}}
  <table class="tbl form" style="height: 70%">
    <tbody>
    <tr>
      <th class="title" colspan="6">Résultats ({{$nbresult}} obtenus en {{$time}}ms)</th>
    </tr>
    <tr>
      <th class="narrow">Date <br /> Type</th>
      <th>Document</th>
      <th class="narrow">Auteur</th>
      <th class="narrow">Patient</th>
      <th class="narrow">Pertinence</th>
    </tr>
    <tr>
      <th colspan="6" class="section">Triés par pertinence</th>
    </tr>
    {{foreach from=$results key=_key item=_result}}
      <tr>
        <td class="text">
          <span>{{$_result._source.date|substr:0:10|date_format:'%d/%m/%Y'}}</span>

          <div class="compact">{{tr}}{{$_result._type}}{{/tr}}</div>
        </td>
        <td class="text" onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')">
          {{if $_result._source.title != ""}}
            <span>{{$_result._source.title|utf8_decode}}</span>
          {{else}}
            <span> ---- Titre non présent ---</span>
          {{/if}}
          {{if $highlights}}
            <div class="compact">{{$highlights.$_key|purify|smarty:nodefaults}}</div>
          {{/if}}
        </td>
        {{if $_result._source.author_id}}
          {{assign var=author_id value=$_result._source.author_id}}
          <td>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$authors.$author_id`}}
          </td>
        {{else}}
          <td class="empty">Utilisateur inconnu</td>
        {{/if}}


        {{if $_result._source.patient_id}}
          <td class="compact">
            {{assign var=patient_id value=$_result._source.patient_id}}
            <span onmouseover="ObjectTooltip.createEx(this, 'CPatient-{{$patient_id}}')">{{$patients.$patient_id}}</span>
          </td>
        {{else}}
          <td class="empty">Patient inconnu</td>
        {{/if}}

        <td>
          {{assign var=score value=$_result._score*100}}
          <meter min="0" max="100" value="{{$score}}" low="50.0" optimum="101.0" high="70.0" style="width:100px;" title="{{$score}}%">
            <div class="progressBar compact text">
              <div class="bar normal" style="width:{{$score}}%;"></div>
              <div class="text">{{$score}}%</div>
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
{{/if}}