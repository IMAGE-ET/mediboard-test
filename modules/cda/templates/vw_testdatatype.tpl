{{*
 * test
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=cda script=ccda}}

<div id="resultAction">
    <table class="tbl">
    <tr>
      <th colspan="4">
        {{tr}}Action{{/tr}}
      </th>
    </tr>
    <tr>
      <td>
        <button type="button" class="button" onclick="Ccda.actionTest('voc')">
          {{tr}}TestVoc{{/tr}}
        </button>
      </td>
      <td>
        <button type="button" class="button" onclick="Ccda.actionTest('base')">
          {{tr}}TestBase{{/tr}}
        </button>
      </td>
      <td>
        <button type="button" class="button" onclick="Ccda.actionTest('datatype')">
          {{tr}}TestComp{{/tr}}
        </button>
      </td>
    </tr>
  </table>
  <br>
  {{if $action == "null"}}
    {{mb_return}}
  {{/if}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="3">Synthèse</th>
    </tr>
    <tr>
      <th>
        {{tr}}testCount{{/tr}}
      </th>
      <th>
        {{tr}}numberSuccess{{/tr}}
      </th>
      <th>
        {{tr}}numbererror{{/tr}}
      </th>
    </tr>
    <tr>
      <td>
        {{$resultsynth.total}}
      </td>
      <td>
        {{$resultsynth.succes}}
      </td>
      <td>
        {{foreach from=$resultsynth.erreur item=_classerror}}
          <a href="#{{$_classerror}}">{{$_classerror}}</a>
        {{/foreach}}
      </td>
    </tr>
  </table>
  <br/>
  <br/>

  {{mb_include template="inc_testdatatype"}}

</div>