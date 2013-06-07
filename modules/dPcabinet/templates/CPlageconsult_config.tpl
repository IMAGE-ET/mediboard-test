{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CPlageconsult" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  
  <table class="form">

    {{assign var="class" value="CPlageconsult"}}
        
    <tr>
      <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    
    {{assign var="var" value="hours_start"}}
    <tr>
      <th class="halfPane">
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="var" value="hours_stop"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="var" value="minutes_interval"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$intervals item=_interval}}
          <option value="{{$_interval}}" {{if $_interval == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_interval}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="var" value="hour_limit_matin"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<script type="text/javascript">
  cleanPlages = function() {
    var url = new Url("cabinet", "controllers/do_clean_plages");
    url.addParam("praticien_id", $V("clean_plage_praticien_id"));
    url.addParam("date"        , $V("clean_plage_date"));
    url.addParam("limit"       , $V("clean_plage_limit"));

    // Give some rest to server
    var onComplete = $('clean_plage_auto').checked ? cleanPlages : Prototype.emptyFunction;
    url.requestUpdate("resultCleanPlages", function () { onComplete.delay(2) } );
  };
</script>

<form name="clean-CPlageConsult" method="get">
  <table class="form">
    <tr>
      <th colspan="3" class="category">Outil de nettoyage</th>
    </tr>
    <tr>
      <th>Date de départ</th>
      <td>
        <script type="text/javascript">
          Main.add(function() {
            Calendar.regField(getForm("clean-CPlageConsult").debut);
          });
        </script>
        <input id="clean_plage_date" type="hidden" name="debut" value="{{$debut}}" />
      </td>
      <td rowspan="5" class="greedyPane" id="resultCleanPlages">
      </td>
    </tr>
    <tr>
      <th>Praticien</th>
      <td>
        <select id="clean_plage_praticien_id" name="praticien_id" style="width: 14em;">
          <option value="">&mdash; Tous les praticiens</option>
          {{foreach from=$praticiens item=_prat}}
            <option value="{{$_prat->_id}}">{{$_prat}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>Nombre maximum de plages à traiter</th>
      <td>
        <input id="clean_plage_limit" type="text" name="limit" value="{{$limit}}" style="width: 14em;" />
      </td>
    </tr>

    <tr>
      <th>
        <label for="clean_plage_auto">Auto</label>
      </th>
      <td>
        <input id="clean_plage_auto" type="checkbox" name="auto" value="1"  />
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        <button type="button" class="trash" onclick="cleanPlages()">{{tr}}Clean{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>