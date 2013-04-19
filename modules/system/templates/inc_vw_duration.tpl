{{*
  * View duration in human readable way
  *  
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{foreach from=$duration key=_unit item=_value}}
  {{if $_value != 0 && $_unit != "second"}}
    {{$_value}} {{tr}}{{$_unit}}{{if $_value>1}}s{{/if}}{{/tr}}
  {{/if}}

{{/foreach}}