{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CService" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">

  <tr>
    <th class="title" colspan="2">Choix d'un service par défaut selon le type d'admission</th>
  </tr>
  
  {{foreach from=$types_admission key=_type_admission item=_locale_type_admission}}
    {{assign var=var value="default_service_types_sejour"}}
    <tr> 
      <th>
        <label for="{{$_locale_type_admission}}" title="{{$_locale_type_admission}}">
          {{$_locale_type_admission}}
        </label>
      </th>  
      <td>
        <select class="num" name="{{$m}}[{{$var}}][{{$_type_admission}}]">
          <option value="">&mdash; Choix du service </option>
          {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $_service->_id == $conf.$m.$var.$_type_admission}}selected="selected"{{/if}}>
            {{$_service}}
          </option>
          {{/foreach}}
        </select>
      </td>             
    </tr>
  {{/foreach}}
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>