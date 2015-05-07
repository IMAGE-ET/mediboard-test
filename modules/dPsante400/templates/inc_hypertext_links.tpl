{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage sante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_script module=sante400 script=hyperTextLink ajax=true}}

{{if isset($object->_back.hypertext_links|smarty:nodefaults)}}
  {{foreach from=$object->_back.hypertext_links item=_hypertext_link}}
    <button class="glob notext" type="button" title="{{$_hypertext_link->name}}"
            onclick="HyperTextLink.accessLink('{{$_hypertext_link->name}}', '{{$_hypertext_link->link}}')">
      {{$_hypertext_link->name}}
    </button>
  {{/foreach}}
{{/if}}