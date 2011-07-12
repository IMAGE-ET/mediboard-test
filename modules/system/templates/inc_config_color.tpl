{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="mediusers" script="color_selector"}}

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
    
		<script type="text/javascript">
			ColorSelector.init{{$uid}} = function(form_name, color_view){
			  this.sForm  = form_name;
			  this.sColor = "{{$field}}";
			  this.sColorView = color_view;
			  this.pop();
			}
    </script>

    <span class="color-view" id="color-{{$uid}}" style="background: #{{$value}};">
      {{tr}}Choose{{/tr}}
    </span>
		
    <button type="button" class="search notext" onclick="ColorSelector.init{{$uid}}(this.form,'color-{{$uid}}')">
      {{tr}}Choose{{/tr}}
    </button>
		
		<input name="{{$field}}" value="{{$value}}" type="hidden" /> 
  </td>
</tr>
