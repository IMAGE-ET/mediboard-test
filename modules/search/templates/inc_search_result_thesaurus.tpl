{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="tbl form">
  <tr>
    <th class="title" colspan="4">Liste des Résultats  <br/> ({{$nbresult}} obtenus en {{$time}}ms)</th>
  </tr>
  {{mb_include module=system template=inc_pagination change_page="changePage" total=$nbresult current=$start step=30}}
  <tr>
    <th class="narrow">Date <br /> Type</th>
    <th>Document</th>
    <th class="narrow">Pertinence</th>
    <th class="narrow"></th>
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
      <td>
        {{assign var=score value=$_result._score*100}}
        <meter min="0" max="100" value="{{$score}}" low="50.0" optimum="101.0" high="70.0" style="width:100px;" title="{{$score}}%">
          <div class="progressBar compact text">
            <div class="bar normal" style="width:{{$score}}%;"></div>
            <div class="text">{{$score}}%</div>
          </div>
        </meter>
      </td>
      <td class="button">
        <button class="add notext" title="Ajouter aux favoris"
                onclick="Search.addeditThesaurusEntry('{{$_result._source.aggregation}}', '{{$_result._source.body}}', '{{$_result._source.user_id}}', '{{$_result._source.types}}', '{{$_result._type}}', null)"></button>
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
