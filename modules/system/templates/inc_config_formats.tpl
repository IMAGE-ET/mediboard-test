{{* $Id: configure.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-formats" action="?m=system&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{mb_include module=system template=inc_config_date_format var=date}}
    
    {{mb_include module=system template=inc_config_date_format var=time}}
    
    {{mb_include module=system template=inc_config_date_format var=longdate}}
    
    {{mb_include module=system template=inc_config_date_format var=longtime}}
    
    {{mb_include module=system template=inc_config_date_format var=datetime}}
  
    {{assign var="var" value="timezone"}}
    <tr>
      <th>
        <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">{{tr}}config-{{$var}}{{/tr}}</label>
      </th>
      <td>
        <select name="{{$var}}">
          {{foreach from=$timezones item=timezone_group key=title_group}}
            <optgroup label="{{$title_group}}">
              {{foreach from=$timezone_group item=title key=timezone}}
                <option value="{{$timezone}}" {{if $timezone==$dPconfig.$var}}selected="selected"{{/if}}>
                  {{$title}}
                </option>
              {{/foreach}}
            </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="m" value="system"}}
    {{mb_include module=system template=inc_config_str var=phone_number_format maxlength=14 suffix="Le format ne doit contenir que des '9' et des espaces. Il doit y avoir au maximum 10 fois '9'. Un '9' correspond à un numéro de 0 à 9."}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>