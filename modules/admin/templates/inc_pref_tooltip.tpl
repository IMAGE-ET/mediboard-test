{{* $Id: edit_prefs.tpl 15799 2012-06-07 16:02:43Z charlyecho $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 15799 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 *}}

{{if $preference}} 
  <span onmouseover="ObjectTooltip.createEx(this, '{{$preference->_guid}}');">
    {{if $preference->value === ""}}
      <em>({{tr}}empty{{/tr}})</em> 
    {{elseif $preference->value === null}}
      <em>({{tr}}ditto{{/tr}})</em> 
    {{else}}
      {{$preference->value}}
    {{/if}}
  </span>
{{/if}}
