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
  Du {{$from_date}}
  au {{$to_date}}
{{else}}
  Le {{$from_date}}
{{/if}}