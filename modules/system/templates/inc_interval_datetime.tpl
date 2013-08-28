{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=from_date value=$from|date_format:$conf.date}}
{{assign var=to_date   value=$to|date_format:$conf.date}}
{{assign var=today value=$smarty.now|date_format:"%Y-%m-%d"}}
{{assign var=from_dateD value=$from|date_format:"%Y-%m-%d"}}
{{assign var=to_dateD value=$to|date_format:"%Y-%m-%d"}}


{{if $from_date != $to_date}}   <!-- from date != to date -->

  <!-- FROM -->
  {{if $today == $from_dateD}}
    {{tr}}From_Today{{/tr}} {{$from|date_format:$conf.time}}
  {{else}}
    Du {{$from|date_format:$conf.datetime}}
  {{/if}}
  <!-- TO -->
  {{if $today == $to_dateD}}
    à {{tr}}Today{{/tr}} {{$to|date_format:$conf.time}}
  {{else}}
    au {{$to|date_format:$conf.datetime}}
  {{/if}}

{{elseif $from == $to}}       <!-- from dateTime == to dateTime -->
  {{if $today == $from_dateD}}
    {{tr}}From_Today{{/tr}}
  {{else}}
    Le {{$from_date}}
  {{/if}}
  à  {{$to|date_format:$conf.time}}

{{else}}
  {{if $today == $from_dateD}}
    {{tr}}Today{{/tr}}
  {{else}}
    Le {{$from_date}}
  {{/if}}
  de {{$from|date_format:$conf.time}} 
  à  {{$to|date_format:$conf.time}}
{{/if}}