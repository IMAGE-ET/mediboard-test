{{*
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

<table class="tbl">
  <tr>
    <th colspan="2">{{tr}}{{$object->search_class}}{{/tr}}</th>
  </tr>
  <tr>
    <td><strong>{{mb_label object=$object field=rmq}}</strong> : {{$object->rmq}}</td>
  </tr>
  <tr>
    <td><strong>{{mb_label object=$object field=user_id}}</strong> : {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_mediuser}}
    </td>
  </tr>
</table>