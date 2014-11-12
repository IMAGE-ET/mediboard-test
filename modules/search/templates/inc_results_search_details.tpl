{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}


{{math assign=max_score equation="x * 100" x=$results.0._score}}
<table class="layout">
  <tr>
    <td class="text">
      <table>
        {{foreach from=$results key=_key item=_result}}
          <tr>
            <td class="text compact" style="width=50px;">
              {{if $highlights}}
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')" class="text compact">
                  {{$highlights.$_key|purify|smarty:nodefaults}}
                </span>
              {{else}}
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')" class="compact empty"> &mdash; Passez la souris pour visualiser le document &mdash;</span>
              {{/if}}
            </td>
            {{if $m != "dPpmsi"}}
              <td class="button">
                <button class="add notext" onclick="Search.addItemToRss(null,'{{$_result._type}}','{{$_result._id}}', null)"></button>
              </td>
            {{/if}}
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="narrow">
      <meter min="0" max="100" value="{{$max_score}}" low="50.0" optimum="101.0" high="70.0" style="width:100px;" title="Score de pertinence : {{$max_score}}%">
        <div class="progressBar compact text">
          <div class="bar normal" style="width:{{$max_score}}%;">
          </div>
          <div class="text">
            {{$max_score}}%
          </div>
        </div>
      </meter>
    </td>
  </tr>
</table>


