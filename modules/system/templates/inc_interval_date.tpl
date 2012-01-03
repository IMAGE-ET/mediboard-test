{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=from value=""}}
{{mb_default var=to   value=""}}
{{assign var=from_date value=$from|date_format:$conf.date}}
{{assign var=to_date   value=$to|date_format:$conf.date}}

{{if $from}} 
  {{if $to}} 
    {{if $from_date != $to_date}}
      Du {{$from_date}}
      au {{$to_date}}
    {{else}}
      Le {{$from_date}}
    {{/if}}
  {{else}}
	Depuis le {{$from_date}}
  {{/if}}
    
{{else}}
  {{if $to}} 
    Jusqu'au {{$to_date}}
  {{/if}}
{{/if}}


