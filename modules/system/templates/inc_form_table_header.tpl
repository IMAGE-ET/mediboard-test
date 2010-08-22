{{* $Id: $ *}}

{{*
  * @package Mediboard
  * @subpackage system
  * @version $Revision: $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{mb_default var=colspan value=2}}
<tr>
  {{if $object->_id}}
  <th class="title modify" colspan="{{$colspan}}">
    {{mb_include module=system template=inc_object_notes     }}
    {{mb_include module=system template=inc_object_idsante400}}
    {{mb_include module=system template=inc_object_history   }}
    {{tr}}{{$object->_class_name}}-title-modify{{/tr}} 
    '{{$object}}'
  </th>
  {{else}}
  <th class="title" colspan="{{$colspan}}">
    {{tr}}{{$object->_class_name}}-title-create{{/tr}} 
  </th>
  {{/if}}
</tr>
