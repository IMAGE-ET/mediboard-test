{{* $Id: $ *}}

{{*
  * @package Mediboard
  * @subpackage system
  * @version $Revision: $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{mb_default var=colspan value=2}}
{{mb_default var=css_class value=""}}
{{mb_default var=duplicate value=false}}

<tr>
  {{if $object->_id}}
  <th class="title modify {{$css_class}}" colspan="{{$colspan}}">
    {{mb_include module=system template=inc_object_notes     }}
    {{mb_include module=system template=inc_object_idsante400}}
    {{mb_include module=system template=inc_object_history   }}
    {{tr}}{{$object->_class}}-title-modify{{/tr}} 
    <br />
    '{{$object}}'
  </th>
  {{elseif $duplicate}}
  <th class="title duplicate" colspan="{{$colspan}}">
    {{tr}}{{$object->_class}}-title-duplicate{{/tr}}
  </th>
  {{else}}
  <th class="title" colspan="{{$colspan}}">
    {{tr}}{{$object->_class}}-title-create{{/tr}} 
  </th>
  {{/if}}
</tr>
