{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $from_field}}
  {{assign var=from_date value=`$object->$from_field`}}
{{else}}
  {{assign var=from_date value=""}}
{{/if}}
{{if $to_field}}
  {{assign var=to_date value=`$object->$to_field`}}
{{else}}
  {{assign var=to_date value=""}}
{{/if}}

{{if $from_field && $from_date}} 
  {{if $to_field && $to_date}}
    {{if $from_date != $to_date}}
      De {{mb_value object=$object field=$from_field}} 
      à  {{mb_value object=$object field=$to_field  }}
    {{else}}
      En {{mb_value object=$object field=$from_field}} 
    {{/if}}
  {{else}}
    Depuis {{mb_value object=$object field=$from_field}}
  {{/if}}
    
{{else}}
  {{if $to_field && $to_date}}
    Jusque {{mb_value object=$object field=$to_field}} 
  {{/if}}
{{/if}}


