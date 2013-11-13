{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage dPsante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<table class="form">
  {{foreach from=$hypertext_links item=_hypertext_link}}
    <tr>
      <td class="narrow"><a href="#" onclick="HyperTextLink.edit('{{$object_id}}', '{{$object_class}}', '{{$_hypertext_link->_id}}');">{{$_hypertext_link->name}}</a></td>
      <td class="narrow"><button type="button" class="glob notext" title="{{tr}}Access{{/tr}}" onclick="HyperTextLink.accessLink('{{$_hypertext_link->name}}', '{{$_hypertext_link->link}}')"/></td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2">{{tr}}CHyperTextLink.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <td colspan="2"><button type="button" class="new" onclick="HyperTextLink.edit('{{$object_id}}', '{{$object_class}}')">{{tr}}New{{/tr}}</button></td>
  </tr>
</table>