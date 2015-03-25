{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{mb_script module=search script=search}}
<script>
  Main.add(function () {
    var tab = Control.Tabs.create('tabs-favoris', true, {
      afterChange :function(container){
        switch(container.id){
          case "tab-General"    : var form = getForm("esSearch");
            form.words.focus();
            break;
          default : break;
        }
      }
    });
  });
</script>
<table class="main layout" id="table_main">
  <tr>
    <td class="button">
      <button type="button" class="favoris" onclick="Search.manageThesaurus('{{$sejour_id}}', '{{$contexte}}')">Gérer mes favoris</button>
    </td>
  </tr>
  <tr>
    <td class="narrow" style="vertical-align: top">
      <ul id="tabs-favoris" class="control_tabs_vertical" style="width: 15em">
        <li>
          <a href="#tab-General" style="line-height: 1em">{{tr}}CSearch classic search{{/tr}}</a>
        </li>
        {{foreach from=$results key=_search item=_result}}
          <li title="mots recherchés : {{$_result.entry}}">
            <a href="#tab-{{$_search}}" style="line-height: 1em" class="{{if $_result.nb_results == 0}}empty{{/if}}">
              {{if $_result.titre}}
                <span class="text">{{$_result.titre}} ({{$_result.nb_results}})</span>
              {{else}}
                <span>Sans-titre ({{$_result.nb_results}})</span>
              {{/if}}
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td style="vertical-align: top">
      <div id="tab-General" style="display: none;">
        {{mb_include module=search template=vw_search_manual}}
      </div>
      {{foreach from=$results key=_search item=_result}}
        <div id="tab-{{$_search}}" style="display: none;">
          <table class="tbl">
            <tr>
              <th class="category" colspan="2">Résultats ({{$_result.nb_results}} obtenus en {{$_result.time}}ms)
              </th>
            </tr>
            <tr>
              <th class="narrow">Date <br /> Type</th>
              <th>Document</th>
            </tr>
            <tr>
              <th class="section" colspan="2">Triés par pertinence - limités aux 30 premiers résultats</th>
            </tr>
            {{if isset($_result.results|smarty:nodefaults)}}
              {{foreach from=$_result.results item=__result}}
                <tr>
                  <td class="text">
                    <span>{{$__result._source.date|substr:0:10|date_format:'%d/%m/%Y'}}</span>

                    <div class="compact">{{tr}}{{$__result._type}}{{/tr}}</div>
                  </td>

                  {{if $__result._type == "CExObject"}}
                    {{assign var=guid value="`$__result._type`_`$__result._source.ex_class_id`-`$__result._source.id`"}}
                  {{else}}
                    {{assign var=guid value="`$__result._type`-`$__result._id`"}}
                  {{/if}}

                  <td class="text" onmouseover="ObjectTooltip.createEx(this, '{{$guid}}')">
                    {{if $__result._source.title != ""}}
                      <span>{{$__result._source.title|utf8_decode}}</span>
                    {{else}}
                      <span> ---- Titre non présent ---</span>
                    {{/if}}
                    {{if isset($__result.highlight|smarty:nodefaults)}}
                      <div class="compact">{{$__result.highlight.body.0|purify|smarty:nodefaults}}</div>
                    {{/if}}
                  </td>
                </tr>
                {{foreachelse}}
                <tr>
                  <td colspan="6" class="empty" style="text-align: center">
                    Aucun document ne correspond à la recherche
                  </td>
                </tr>
              {{/foreach}}
            {{else}}
              <tr>
                <td colspan="6" class="empty" style="text-align: center">
                  Aucun résultat pour ce favori
                </td>
              </tr>
            {{/if}}
          </table>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>