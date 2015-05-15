{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue appellée lors de la recherche dans le journal utilisateur avec aggrégation des résultats.-->

{{mb_script module=search script=search}}
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
      <th class="narrow"></th>
      <th class="narrow">Date</th>
      <th class="narrow">Utilisateur</th>
      <th>Détail des occurrences</th>
    </tr>
    <tr>
      <th colspan="6" class="section">Triés par nombre d'occurrences</th>
    </tr>
    {{foreach from=$objects_refs key=_key item=_object_ref}}
      <tr>
        <td class="button">
          <a href="#" class="button search notext" style="float:right"
             onclick="Search.searchByType('{{$_object_ref.date_log}}', '{{$_object_ref.object->user_id}}')">
          </a>
        </td>
        {{assign var=date_log value=$_object_ref.date_log|substr:0:10|date_format:'%d/%m/%Y'}}
        <td><span>{{$date_log}}</span></td>
        <td style="width:15%">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$_object_ref.object`}}
        </td>
        <td class="text">
          {{foreach from=$_object_ref.contexte key=_key item=_contexte}}
          <span>{{$_contexte.count}} recherches {{$_contexte.key}} trouvée(s)</span>
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
        <td class="text">
          <span> {{$_result._source.body}}</span>
          {{if $highlights.$_key}}
            <div class="compact">{{$highlights.$_key|purify|smarty:nodefaults}}</div>
          {{/if}}
        </td>
        {{if $_result._source.user_id}}
          {{assign var=user_id value=$_result._source.user_id}}
          <td>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$authors.$user_id`}}
          </td>
        {{else}}
          <td class="empty">Utilisateur inconnu</td>
        {{/if}}

        <td>
          {{assign var=score value=$_result._score*100}}
          <script>
            Main.add (function () {
              Search.progressBar('{{$_key}}', '{{$score}}');
            });
          </script>
        <span title="Score de pertinence : {{$score|round}}%">
          <div id="score_{{$_key}}" style="width: 25px; height: 25px; display: inline-block"></div>
        </span>
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