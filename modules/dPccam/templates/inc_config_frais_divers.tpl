{{* $Id: configure.tpl 9306 2010-06-28 08:29:45Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 9306 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-fraisDivers" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">
    {{assign var=class value=CCodable}}
    {{assign var=var value=use_frais_divers}}
    {{foreach from=$childs_codable_class item=_child_codable_class}}
    {{assign var=type value=$_child_codable_class}}
    {{if $type != "CFacturable" && $type != 'CDevisCodage'}}
      <tr>
        <th style="width: 50%">
          <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}">
            {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}
          </label>     
        </th>
        <td>
          <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]_1">{{tr}}bool.1{{/tr}}</label>
          <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" value="1" {{if $conf.$m.$class.$var.$type == "1"}}checked="checked"{{/if}}/>
          <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]_0">{{tr}}bool.0{{/tr}}</label>
          <input type="radio" name="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" value="0" {{if $conf.$m.$class.$var.$type == "0" || $conf.$m.$class.$var.$type == ""}}checked="checked"{{/if}} />
        </td>
      </tr>
    {{/if}}
    {{/foreach}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
 </table>
</form>