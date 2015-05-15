{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue appellée lors du de la recherche classique sans aggrégation-->

{{mb_script module=search script=search}}

<table class="tbl form" style="height: 70%" id="results_without_aggreg">
  <tbody>
  <tr>
    <th class="title" colspan="7">Résultats ({{$nbresult}} obtenus en {{$time}}ms)
      <button class="print notext not-printable" type="button" onclick="$('results_without_aggreg').print();">
        {{tr}}Print{{/tr}}
      </button>
      <button class="download notext not-printable" type="button" onclick="Search.downloadCSV();">
        {{tr}}CSV{{/tr}}
      </button>
    </th>
  </tr>
  <tr>
    <th class="narrow">Date <br /> Type</th>
    <th>Document</th>
    <th class="narrow">Auteur</th>
    <th class="narrow">Patient</th>
    <th class="narrow not-printable"></th>
    {{if $contexte == "pmsi" && "atih"|module_active}}
      <th colspan="2">Marq.</th>
    {{/if}}
  </tr>
  <tr>
    <th colspan="7" class="section">Triés par pertinence</th>
  </tr>
  {{foreach from=$results key=_key item=_result}}
    <tr>
      <td class="text">
        <span>{{$_result._source.date|substr:0:10|date_format:'%d/%m/%Y'}}</span>

        <div class="compact">{{tr}}{{$_result._type}}{{/tr}}</div>
      </td>
      {{if $_result._type == "CExObject"}}
        {{assign var=guid value="`$_result._type`_`$_result._source.ex_class_id`-`$_result._source.id`"}}
      {{else}}
        {{assign var=guid value="`$_result._type`-`$_result._id`"}}
      {{/if}}

      <td class="text" onmouseover="ObjectTooltip.createEx(this, '{{$guid}}')">
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

      <td class="not-printable">
        {{assign var=score value=$_result._score*100}}
        <script>
          Main.add(function () {
            Search.progressBar('{{$_result._source.id}}', '{{$score}}');
          });
        </script>
        <span title="Score de pertinence : {{$score|round}}%">
          <div id="score_{{$_result._source.id}}" style="width: 25px; height: 25px; display: inline-block"></div>
        </span>
      </td>
      {{if $contexte == "pmsi" && "atih"|module_active}}
        <td class="narrow not-printable">
          {{if !in_array("`$_result._type`-`$_result._source.id`", $items)}}
            <button class="add notext"
                    onclick="Search.addItemToRss(null, '{{$sejour_id}}', '{{$_result._type}}', '{{$_result._source.id}}', null)"></button>
          {{/if}}
          {{foreach from=$rss_items key=_key item=_item}}
            {{if $_result._type == $_item->search_class && $_result._source.id == $_item->search_id}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_item->_guid}}')">
                <i class="fa fa-check fa-2x" style="color:green"></i>
              </span>
            {{/if}}
          {{/foreach}}
        </td>
      {{/if}}
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="6" class="empty" style="text-align: center">
        {{tr}}CSearch-result.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
  </tbody>
</table>