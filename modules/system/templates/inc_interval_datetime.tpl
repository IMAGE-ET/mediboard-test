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

{{if $from_date != $to_date}}
  Du {{$from|date_format:$conf.datetime}}
  au {{$to|date_format:$conf.datetime}}
{{elseif $from == $to}}
  Le {{$from_date}}
  à  {{$to|date_format:$conf.time}}
{{else}}
  Le {{$from_date}}
  de {{$from|date_format:$conf.time}} 
  à  {{$to|date_format:$conf.time}}
{{/if}}