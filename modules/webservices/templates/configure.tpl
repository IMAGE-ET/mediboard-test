{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Configuration du module {{tr}}{{$m}}{{/tr}}</h1>
<hr />

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    <tr>
      <th class="category" colspan="2">Configuration {{tr}}{{$m}}{{/tr}}</th>
    </tr>
    
    <tr>
      {{assign var="var" value="connection_timeout"}}
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input class="num" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
      </td>
    </tr>
        
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
