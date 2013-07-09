{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  {{assign var="_source" value=$source}}
  {{if isset($source->_allowed_instances.$class|smarty:nodefaults)}}
    {{assign var="_source" value=$source->_allowed_instances.$class}}
  {{/if}}
  
  {{if !$_source->_id}}
  <tr>
    <td>
      <a class="button new" onclick="$('config-source-{{$class}}-{{$sourcename}}').show(); Control.Modal.position();">
        {{tr}}{{$class}}-title-create{{/tr}}
      </a>
   </td>
  </tr>
  {{/if}}
  
  <tr>
    <td id="config-source-{{$class}}-{{$sourcename}}" {{if !$_source->_id}}style="display:none"{{/if}}>
      {{if $_source->_class == $class}}
        {{mb_include module=$mod template="`$class`_inc_config" source=$_source}}  
      {{/if}}     
    </td>
  </tr>
</table> 