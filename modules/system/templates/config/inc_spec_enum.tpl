{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage system
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $is_last}}
  {{assign var=_list value='|'|explode:$_prop.list}}
  <select class="{{$_prop.string}}" name="c[{{$_feature}}]" {{if $is_inherited}} disabled {{/if}}>
    {{foreach from=$_list item=_item}}
      <option value="{{$_item}}" {{if $_item == $value}} selected {{/if}}>
        {{assign var=config value="CConfiguration::getConfigSpec"|static_call:$_feature}}
        {{if "localize"|array_key_exists:$config}}
          {{tr}}config-{{$_feature|replace:' ':'-'}}.{{$_item}}{{/tr}}
        {{else}}
          {{$_item}}
        {{/if}}
      </option>
    {{/foreach}}
  </select>
{{else}}
  {{$value}}
{{/if}}