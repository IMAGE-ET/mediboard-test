{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}


{{math assign=max_score equation="x * 100" x=$results.0._score}}

<meter min="0" max="100" value="{{$max_score}}" low="50.0" optimum="101.0" high="70.0" style="width:100px; float:right;" title="Score de pertinence : {{$max_score}}%">
  <div class="progressBar compact text">
    <div class="bar normal" style="width:{{$max_score}}%;">
    </div>
    <div class="text">
      {{$max_score}}%
    </div>
  </div>
</meter>

<ul style="padding-top: 15px;">
  {{foreach from=$results key=_key item=_result}}
    {{if $highlights}}
      <li>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')" class="compact">
          {{$highlights.$_key|purify|smarty:nodefaults}}
        </span>
      </li>
      <hr/>
    {{else}}
      <li  onmouseover="ObjectTooltip.createEx(this, '{{$_result._type}}-{{$_result._id}}')" class="compact empty"> &mdash; Passez la souris pour visualiser le document &mdash;</li>
    {{/if}}
  {{/foreach}}
</ul>


