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

{{mb_script module=drawing script=DrawingItem}}
{{mb_include module=drawing template=fabricjs}}

<script>
  confirmDel = function(form) {
    if (confirm('test')) {

    }
    return false;
  };
</script>

{{if !$gallery}}
  <button class="new" onclick="DrawingItem.editModal()">{{tr}}CDrawingItem.new{{/tr}}</button>

  <a href="?m={{$m}}&tab={{$tab}}&gallery=1" class="button thumbnails">{{tr}}vw_list_gallery{{/tr}}</a>

  <table class="main">
    <tr>
      <th class="title">Brouillons</th>
      <th class="title">Exports</th>
    </tr>
    <tr>
      <td style="width: 50%;">
        <table class="tbl">
          <tr>
            <th class="narrow"></th>
            <th>titre</th>
            <th>Lié à</th>
            <th>Créateur</th>
            <th>Taille</th>
            <th>date</th>
          </tr>
          {{foreach from=$files item=_file}}
            <tr>
              <td>
                {{if $conf.drawing.edit_svg}}
                  <button onclick="DrawingItem.editModal('{{$_file->_id}}');" class="edit notext">{{tr}}Edit{{/tr}}</button>
                {{/if}}
                <form method="post" name="delFile_{{$_file->_id}}">
                  <input type="hidden" name="m" value="dPfiles"/>
                  <input type="hidden" name="del" value="1"/>
                  {{mb_key object=$_file}}
                  {{mb_class object=$_file}}
                  <button class="trash notext" type="button" onclick="return confirmDeletion(this.form, {ajax: true}, {onComplete : window.location.reload});">{{tr}}Delete{{/tr}}</button>
                </form>
              </td>
              <td>
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}');">
                {{$_file}}
                </span>
              </td>
              <td>
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_file->_ref_object->_guid}}');">{{$_file->_ref_object}}</span>
              </td>
              <td>
                {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_file->_ref_author}}
              </td>
              <td>{{mb_value object=$_file field=_file_size}}</td>
              <td><span title="{{mb_value object=$_file field=file_date}}">{{mb_value object=$_file field=file_date format=relative}}</span></td>
            </tr>
            {{foreachelse}}
            <tr>
              <td colspan="5" class="empty">{{tr}}CFile.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </td>
      <td>
        <table class="tbl">
          <tr>
            <th class="narrow"></th>
            <th>titre</th>
            <th>Lié à</th>
            <th>Taille</th>
            <th>date</th>
          </tr>
          {{foreach from=$files_svg item=_file}}
            <tr>
              <td>
                <button class="search notext" onclick="new Url().ViewFilePopup('{{$_file->object_class}}', '{{$_file->object_id}}', '{{$_file->_class}}', '{{$_file->_id}}')">Voir</button>
                <form method="post" name="delFile_{{$_file->_id}}">
                  <input type="hidden" name="m" value="dPfiles"/>
                  <input type="hidden" name="del" value="1"/>
                  {{mb_key object=$_file}}
                  {{mb_class object=$_file}}
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {ajax: true}, {onComplete : document.location.reload});">{{tr}}Delete{{/tr}}</button>
                </form>
              </td>
              <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}');">
          {{$_file->_view}}
          </span>
              </td>
              <td>
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_file->_ref_object->_guid}}');">{{$_file->_ref_object}}</span>
                <button>Lier à</button>
              </td>
              <td>{{mb_value object=$_file field=_file_size}}</td>
              <td><span title="{{mb_value object=$_file field=file_date}}">{{mb_value object=$_file field=file_date format=relative}}</span></td>
            </tr>
            {{foreachelse}}
            <tr>
              <td colspan="5" class="empty">{{tr}}CFile.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </td>
    </tr>
  </table>


{{else}}
  <style>
    .img_container {
      width: 200px;
      height: 150px;
      border:solid 1px #484848;
      border-radius: 4px;
      box-shadow: 2px 2px 5px black;
      margin: 10px;
      float:left;
    }

    .img_container img {
      max-width: 200px;
      max-height: 150px;
    }
  </style>
  <p>
    <a href="?m={{$m}}&tab={{$tab}}&gallery=0" class="button list">{{tr}}vw_list_file{{/tr}}</a>
  </p>
  {{foreach from=$files_svg item=_file}}
    <div class="img_container" onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}');">
      <img src="?m=files&a=fileviewer&file_id={{$_file->_id}}" alt="" />
    </div>
  {{foreachelse}}
    <p>{{tr}}CFile.none{{/tr}}</p>
  {{/foreach}}
{{/if}}
