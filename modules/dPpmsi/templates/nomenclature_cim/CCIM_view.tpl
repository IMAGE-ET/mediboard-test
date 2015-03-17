{{*
 * $Id$
 *  
 * @category atih
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

<table class="tbl">
  <tr>
    <th colspan="3">{{$object->code}}</th>
  </tr>
  <tr>
    <td> <strong>{{mb_label object=$object field=short_name}}</strong> : {{$object->short_name}}</td>
  </tr>
  <tr>
    <td> <strong>{{mb_label object=$object field=complete_name}}</strong> : {{$object->complete_name}}</td>
  </tr>
  <tr>
    <td> <strong>{{mb_label object=$object field=type}}</strong> : {{tr}}CCIM10.type.{{$object->type}}{{/tr}}</td>
    </tr>
</table>