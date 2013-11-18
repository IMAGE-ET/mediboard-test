{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=objectType value=CPlageConsult}}

{{if $object->_ref_disponibility|@count}}
  {{assign var=count_dispo value=$object->_ref_disponibility|@array_count_values}}
  <div class="progressBar_dispo" title="{{foreach from=$count_dispo key=type item=_dispo name=loop}}{{tr}}{{$objectType}}_planning_disponibility_{{$type}}{{/tr}} : {{$_dispo}}{{if !$smarty.foreach.loop.last}}, {{/if}}{{/foreach}}">
    {{foreach from=$object->_ref_disponibility item=_dispo}}
      <div class="disponibility_bar disponibility_planning_{{$_dispo}}"></div>
    {{/foreach}}
  </div>
{{/if}}