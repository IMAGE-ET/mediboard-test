{{*
 * $Id$
 *
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}


<script>
  Main.add (function () {
    Search.words_request = '{{$words}}';
    Search.export_csv = '{{$results|@json|JSAttribute}}';
  });
</script>
{{if $objects_refs}}
  <table class="tbl form" id="results" style="height: 70%">
    <tbody>
      <tr>
      <th class="title" colspan="6">Résultats ({{$nbresult}} obtenus en {{$time}}ms)
        <button class="print notext not-printable" type="button" onclick="$('results').print();">
          {{tr}}Print{{/tr}}
        </button>
        <button class="download notext not-printable" type="button" onclick="Search.downloadCSV();">
          {{tr}}CSV{{/tr}}
        </button>
      </th>
    </tr>
      <tr>
        <th></th>
        <th class="narrow">{{tr}}NDA{{/tr}}</th>
        <th class="narrow">Durée séjour</th>
        <th>
          <span>Contexte</span>
          <input class="not-printable" type="text" id="filter-contexte" size="20" onkeyup="Search.filter(this, 'contextes', 'results')" />
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
        <!-- Recherche plus détaillée -->
        <td class="narrow">
          <button class="search notext not-printable" type="button" style="float:right"
                  onclick='Search.searchByType(null, null, "{{$_object_ref.object->_id}}", "{{$_object_ref.object->_class}}", "{{$fuzzy_search}}", {{$_object_ref.type|@json}})'>
          </button>
        </td>
        <!-- NDA -->
        <td>
          {{if $_object_ref.object->_class == "CSejour"}}
            {{$_object_ref.object->_NDA}}
          {{elseif $_object_ref.object->_ref_sejour->_id}}
            {{$_object_ref.object->_ref_sejour->_NDA}}
          {{else}}
            <span class="text compact empty">{{tr}}NDA.none{{/tr}}</span>
          {{/if}}
        </td>

        <!-- Durée du séjour -->
        <td>
          {{if $_object_ref.object->_class == "CSejour"}}
            {{$_object_ref.object->_duree}} jours
          {{elseif $_object_ref.object->_ref_sejour->_id}}
            {{$_object_ref.object->_ref_sejour->_duree}} jours
          {{else}}
            <span class="text compact empty">{{tr}}Duree.none{{/tr}}</span>
          {{/if}}
        </td>

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
  {{mb_include module=search template=inc_results_search_details}}
{{/if}}