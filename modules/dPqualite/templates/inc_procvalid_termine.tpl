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
      <input type="hidden" name="suivi[etat]" value="{{$docGed|const:'VALID'}}" />
      {{tr}}_CDocGed_TERMINE{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-doc_ged_suivi_id-court{{/tr}}</th>
    <td>
      {{if $docGed->_lastactif->doc_ged_suivi_id}}
      {{$docGed->_reference_doc}}<br />
      {{tr}}CDocGed-version-court{{/tr}} : {{$docGed->version}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{tr}}CDocGed-doc_theme_id{{/tr}}</th>
    <td class="text">
      {{$docGed->_ref_theme->nom}}
    </td>
  </tr>
  <tr>
    <th>{{tr}}CDocGed-group_id{{/tr}}</th>
    <td class="text">
      {{$docGed->_ref_group->text}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <a href="#" onclick="popFile('{{$docGed->_class_name}}','{{$docGed->_id}}','CFile','{{$docGed->_lastactif->file_id}}')" title="{{tr}}CFile-msg-viewfile{{/tr}}">
        <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastactif->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
      </a>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      {{if $docGed->annule}}
      <button class="change" type="button" onclick="annuleDoc(this.form,0);">
        {{tr}}button-CDocGed-retablir{{/tr}}
      </button>
      {{else}}
        <button class="cancel" type="button" onclick="annuleDoc(this.form,1);">
          {{tr}}Cancel{{/tr}}
        </button>
      {{/if}}
    </td>
  </tr>
</table>