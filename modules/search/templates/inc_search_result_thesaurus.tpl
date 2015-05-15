{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue appellée lors de la recherche dans le journal de recherche sans agrégation (utilisée aussi dans les favoris)-->

{{mb_default var=show_score value=true}}

<table class="tbl form">
  <tr>
    <th class="title" colspan="4">Liste des Résultats <br /> ({{$nbresult}} obtenus en {{$time}}ms)</th>
  </tr>
  {{mb_include module=system template=inc_pagination change_page="changePage" total=$nbresult current=$start step=20}}
  <tr>
    <th class="narrow">Date <br /> Type</th>
    <th>Document</th>
    <th class="narrow"></th>
    {{if $show_score}}
      <th class="text narrow"></th>
    {{/if}}
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
      <td class="text">
        <span> {{$_result._source.body}}</span>
      </td>
      {{if $show_score}}
        <td class="button">
          {{assign var=score value=$_result._score*100}}
          <script>
            Main.add(function () {
              Search.progressBar('{{$_key}}', '{{$score}}');
            });
          </script>
        <span title="Score de pertinence : {{$score|round}}%">
          <div id="score_{{$_key}}" style="width: 25px; height: 25px; display: inline-block"></div>
        </span>
        </td>
      {{/if}}

      <td class="button">
        <button class="favoris notext" title="Ajouter aux favoris"
                onclick="Thesaurus.addeditThesaurusEntryManual('{{$_result._source.aggregation}}', '{{$_result._source.body}}', '{{$_result._source.user_id}}', '{{$_result._source.types}}', '{{$_result._type}}', null)"></button>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="6" class="empty" style="text-align: center">
        Aucun document ne correspond à la recherche
      </td>
    </tr>
  {{/foreach}}
</table>
