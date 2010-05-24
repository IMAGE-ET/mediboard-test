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
  {{if $from_date}}
		{{if !$to_date}}A partir{{/if}}
	  du {{$from_date}}
	{{/if}}
	
  {{if $to_date}}
	  {{if !$from_date}}Jusqu'au{{/if}}
	  au {{$to_date}}
  {{/if}}
{{elseif $from_date}}
  Le {{$from_date}}
{{/if}}
