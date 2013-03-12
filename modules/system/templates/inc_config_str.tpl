{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=textarea value=0}}

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
    {{assign var=uid value=""|uniqid}}
    {{assign var=uid value="uid-$uid"}}
    
    {{if @$numeric}}
    <script type="text/javascript">
      Main.add(function(){
        $$(".{{$uid}}")[0].addSpinner({min: 0});
      });
    </script>
    {{/if}}
    
    {{if $textarea}}
      <textarea class="{{if @$cssClass}}{{$cssClass}}{{else}}str{{/if}} {{$uid}}" name="{{$field}}">{{$value|smarty:nodefaults}}</textarea>
    {{else}}
      <input class="{{if @$cssClass}}{{$cssClass}}{{else}}str{{/if}} {{$uid}}" {{if @$password}} type="password" {{/if}} name="{{$field}}" 
             value="{{$value|smarty:nodefaults}}" {{if @$size}}size="{{$size}}"{{/if}}
             {{if @$maxlength}}maxlength="{{$maxlength}}"{{/if}}/> 
      {{if @$suffix}}{{$suffix}}{{/if}}
    {{/if}} 
  </td>
</tr>
