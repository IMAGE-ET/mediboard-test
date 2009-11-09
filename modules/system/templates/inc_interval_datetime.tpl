{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=from_date value=$from|date_format:$dPconfig.date}}
{{assign var=to_date   value=$to|date_format:$dPconfig.date}}

{{if $from_date != $to_date}}
  Du {{$from|date_format:$dPconfig.datetime}}
  au {{$to|date_format:$dPconfig.datetime}}
{{else}}
  Le {{$from_date}}
  de {{$from|date_format:$dPconfig.time}} 
  à  {{$to|date_format:$dPconfig.time}}
{{/if}}