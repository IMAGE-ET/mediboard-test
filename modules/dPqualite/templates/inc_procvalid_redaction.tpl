{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
  <tr>
    <th class="title" colspan="2">
      <input type="hidden" name="suivi[etat]" value="{{$docGed|const:'REDAC'}}" />
      {{tr}}_CDocGed_REDAC{{/tr}} ({{$docGed->_reference_doc}})
    </th>
  </tr>
  <tr>
    <td class="button text" colspan="2">
      {{tr}}_CDocGed_Redac{{/tr}}
    </td>
  </tr>
  <tr>
    <th>{{tr}}_CDocGed_validBy{{/tr}}</th>
    <td>{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  {{if $docGed->_lastentry->file_id}}
  <tr>
    <th>{{tr}}_CDocGed_lastfile{{/tr}}</th>
    <td>
      <a href="#" onclick="popFile('{{$docGed->_class_name}}','{{$docGed->_id}}','CFile','{{$docGed->_lastentry->file_id}}')" title="{{tr}}CFile-msg-viewfile{{/tr}}">
        <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
      </a>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th>
      <strong>{{tr}}_CDocGed_lastcomm{{/tr}} :</strong><br />
      {{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}
    </th>
    <td>
      {{$docGed->_lastentry->remarques|nl2br}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'{{tr escape="javascript"}}CDocGed.one{{/tr}}',objName:'{{$docGed->_reference_doc|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
  </tr>
</table>