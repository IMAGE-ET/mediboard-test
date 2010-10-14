{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $type == 'web_service'}}
  <option value="">&mdash; Liste des web services</option>
  {{foreach from=$web_services item=_web_service}}
    <option value="{{$_web_service}}" {{if $web_service == $_web_service}}selected="selected"{{/if}}>
      {{$_web_service}}
    </option>
  {{/foreach}}
{{else}}
  <option value="">&mdash; Liste des fonctions</option>
  {{foreach from=$fonctions item=_fonction}}
    <option value="{{$_fonction}}" {{if $fonction == $_fonction}}selected="selected"{{/if}}>
      {{$_fonction}}
    </option>
  {{/foreach}}
{{/if}}
