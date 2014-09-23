{{*
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if $compte_rendu->_id}}
  <table class="form" id="layout" style="display: none;">
    <tr class="notice">
      <td>
        <div class="small-info">
          Ce modèle n'est pas un corps de texte.
        </div>
      </td>
    </tr>

    <tbody class="fields">
    {{if $pdf_thumbnails && $pdf_and_thumbs}}
      <tr>
        <th class="category" colspan="2">
          {{tr}}CCompteRendu-Pagelayout{{/tr}}
        </th>
      </tr>
      <tr id="page_layout" style="display: none;">
        <td colspan="2">
          {{mb_include template=inc_page_layout}}
        </td>
      </tr>
    {{/if}}
    <tr id="height"  style="display: none;">
      <th>{{mb_label object=$compte_rendu field=height}}</th>
      <td>
        {{if $droit}}
          <button id="button_addedit_modeles_generate_auto_height" type="button" class="change" onclick="Thumb.old(); Modele.generate_auto_height(); Modele.preview_layout();">{{tr}}CCompteRendu.auto_height{{/tr}}</button><br/>
          {{mb_field object=$compte_rendu field=height increment=true form=editFrm onchange="Thumb.old(); Modele.preview_layout();" step="10" onkeyup="Modele.preview_layout();"}}
        {{else}}
          {{mb_field object=$compte_rendu field=height readonly="readonly"}}
        {{/if}}
      </td>
    </tr>

    <tr id="layout_header_footer" style="display: none;">
      <th>{{tr}}CCompteRendu-preview-header-footer{{/tr}}</th>
      <td>
        <div id="preview_page" style="color: #000; height: 84px; padding: 7px; width: 58px; background: #fff; border: 1px solid #000; overflow: hidden;">
          <div id="header_footer_content" style="color: #000; white-space: normal; background: #fff; overflow: hidden; margin: -1px; height: 30px; width: 100%; font-size: 3px;">
            {{mb_include template=lorem_ipsum}}
          </div>
          <hr style="width: 100%; margin-top: 3px; margin-bottom: 3px;"/>
          <div id="body_content" style="margin: -1px; color: #999; height: 50px; width: 100%; font-size: 3px; white-space: normal; overflow: hidden;">
            {{mb_include template=lorem_ipsum}}
          </div>
        </div>
      </td>
    </tr>
    </tbody>
  </table>
{{else}}
  <div id="layout" style="display: none; " class="small-info">
    Aucune mise en page possible
  </div>
{{/if}}