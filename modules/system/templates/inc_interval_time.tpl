{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $from != $to}}
  De {{$from|date_format:$conf.time}}
  à  {{$to|date_format:$conf.time}}
{{else}}
  à  {{$to|date_format:$conf.time}}
{{/if}}
