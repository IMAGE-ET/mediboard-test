{{*
  * View the plage datetime in readable format
  *  
  * @category Astreintes
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{mb_default var=object value=""}}

{{if is_object($object)}}

  {{if $object->start|date_format:"%Y-%m-%d" == $today}}
    {{tr}}Today{{/tr}}
    {{if $object->end|date_format:"%Y-%m-%d" == $object->start|date_format:"%Y-%m-%d"}}
      {{tr}}from{{/tr}}
    {{else}}
      au
    {{/if}}
    {{$object->start|date_format:"%H:%M"}}
  {{else}}
   Du {{mb_value object=$object field=start}}
  {{/if}}

  {{if $object->end|date_format:"%Y-%m-%d" == $today}}
    {{if $object->end|date_format:"%Y-%m-%d" == $object->start|date_format:"%Y-%m-%d"}}
      {{tr}}to{{/tr}}
    {{/if}}
     {{$object->end|date_format:"%H:%M"}}
  {{else}}
   au {{mb_value object=$object field=end}}
  {{/if}}

{{else}}
  <div class="small-warning">{{tr}}CPlageCalendaire-no-object-for-template{{/tr}}</div>
{{/if}}