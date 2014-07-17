{{*
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>Enregistrement du brouillon</legend>
  <form name="save_file_{{$draw->_id}}" method="post">
    <input type="hidden" name="m" value="drawing" />
    <input type="hidden" name="dosql" value="do_drawfile_aed"/>
    <input type="hidden" name="svg_content" value=""/>
    <input type="hidden" name="export" value="0"/>
    {{mb_field object=$draw field=author_id hidden=1}}
    {{mb_field object=$draw field=file_type hidden=1}}
    {{mb_field object=$draw field=object_class hidden=1}}
    {{mb_field object=$draw field=object_id hidden=1}}
    {{mb_key object=$draw}}
    <table class="form">
      <tr>
        <th class="narrow">{{mb_label object=$draw field=author_id}}</th>
        <td>{{$draw->_ref_author}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$draw field=file_name}}</th>
        <td>{{mb_field object=$draw field=file_name style="width:10em;"}}</td>
      </tr>

      <tr>
        <td class="button" colspan="2">
          <button class="save" onclick="return saveDraw(this.form, 0)">{{tr}}Save{{/tr}}</button>
          {{if $draw->_id}}
            <button class="trash" type="button" onclick="return confirmDeletion(this.form, {ajax:1}, {onComplete:Control.Modal.close});">{{tr}}Delete{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
  </form>
</fieldset>

<fieldset>
  <legend>Export</legend>
  <form name="export_file_{{$draw->_id}}" method="post">
    <input type="hidden" name="m" value="drawing" />
    <input type="hidden" name="dosql" value="do_drawfile_aed"/>
    <input type="hidden" name="svg_content" value=""/>
    <input type="hidden" name="export" value="1"/>
    {{mb_field object=$draw field=author_id hidden=1}}
    {{mb_field object=$draw field=file_type hidden=1}}
    {{mb_field object=$draw field=object_class hidden=1}}
    {{mb_field object=$draw field=object_id hidden=1}}
    {{mb_key object=$draw}}

    <table class="form">
      <tr>
        <th class="narrow">{{mb_label object=$draw field=author_id}}</th>
        <td>{{$draw->_ref_author}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$draw field=file_name}}</th>
        <td>{{mb_field object=$draw field=file_name style="width:10em;"}}</td>
      </tr>
      <tr>
        <th>Catégorie</th>
        <td>
          <select name="file_category_id">
            <option value="">{{tr}}All{{/tr}}</option>
            {{foreach from=$file_categories item=_cat}}
              <option value="{{$_cat->_id}}" {{if $draw->file_category_id == $_cat->_id}}selected="selected" {{/if}}>{{$_cat}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    </table>
    <button class="upload" onclick="return saveDraw(this.form, 1)">{{tr}}Export{{/tr}} {{tr}}and{{/tr}} {{tr}}Close{{/tr}}</button>
  </form>
</fieldset>