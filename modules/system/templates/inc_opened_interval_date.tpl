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
  {{if $from_date}}
		{{if !$to_date}} A partir du {{else}} du {{/if}}
	  {{$from_date}}
	{{/if}}
	
  {{if $to_date}}
	  {{if !$from_date}} Jusqu'au {{else}} au {{/if}}
	  {{$to_date}}
  {{/if}}
{{elseif $from_date}}
  Le {{$from_date}}
{{/if}}
