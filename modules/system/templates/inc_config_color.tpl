{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if @$m}}
  {{if @$class}}
    {{assign var=field  value="$m[$class][$var]"}}
    {{assign var=value  value=$conf.$m.$class.$var}}
    {{assign var=locale value=config-$m-$class-$var}}
  {{else}}
    {{assign var=field  value="$m[$var]"}}
    {{assign var=value  value=$conf.$m.$var}}
    {{assign var=locale value=config-$m-$var}}
  {{/if}}
{{else}}
  {{assign var=field  value="$var"}}
  {{assign var=value  value=$conf.$var}}
  {{assign var=locale value=config-$var}}
{{/if}}

<tr>
  <th {{if @$thcolspan}}colspan="{{$thcolspan}}"{{else}}style="width: 50%"{{/if}}>
    <label for="{{$field}}" title="{{tr}}{{$locale}}-desc{{/tr}}">
      {{tr}}{{$locale}}{{/tr}}
    </label>  
  </th>
  
  <td {{if @$tdcolspan}}colspan="{{$tdcolspan}}"{{/if}}>
    {{unique_id var=uid}}

		<input name="{{$field}}" value="{{$value}}" type="text" class="color_picker" />
    <button type="button" class="cancel notext" onclick="$V(getForm('{{$form}}').elements['{{$field}}'], '', true);"></button>

    <script>
      Main.add(function(){
        var _e = getForm('{{$form}}').elements['{{$field}}'];
        new jscolor.color(_e, {required:false});
      });
    </script>
  </td>
</tr>
