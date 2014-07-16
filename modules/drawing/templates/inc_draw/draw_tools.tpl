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

<fieldset class="active" id="draw_tools_1">
  <legend>Dessin au crayon</legend>
  <p><label>Largeur du trait<input type="range" min="1" max="10" value="3" onchange="DrawObject.changeDrawWidth($V(this));"/></label></p>
  <p><label>Couleur
      <div class="basic_color" style="background-color: black;" onclick="colorSelect('black');"></div>
      <div class="basic_color" style="background-color: gray;" onclick="colorSelect('gray');"></div>
      <div class="basic_color" style="background-color: white;" onclick="colorSelect('white');"></div>
      <div class="basic_color" style="background-color: blue;" onclick="colorSelect('blue');"></div>
      <div class="basic_color" style="background-color: green;" onclick="colorSelect('green');"></div>
      <div class="basic_color" style="background-color: yellow;" onclick="colorSelect('yellow');"></div>
      <div class="basic_color" style="background-color: red;" onclick="colorSelect('red');"></div>
      <div class="basic_color" style="background-color: fuchsia;" onclick="colorSelect('fuchsia');"></div>
      {{if $app->user_prefs.drawing_advanced_mode}}
        <form name="osef" method="get" onsubmit="return false;">
          <input type="hidden" name="color_inout" value="#fff" onchange="colorSelect(this.value);"/>
          <script>
            ColorSelector.init = function(form_name) {
              this.sForm  = form_name;
              this.sColor = "color_inout";
              this.sColorView = 'current_color';
              this.pop();
            };
          </script>
          <button style="border:none; display: inline-block; width:1.35em; height: 2em; text-align: center;" onclick="ColorSelector.init(this.form);">P</button>
        </form>
      {{/if}}
      <span style="display:block; width: 100%; height:1.5em; background-color: black;" id="current_color"></span>

    </label>
  </p>
</fieldset>

<button class="switch" onclick="toggleMode();"></button>

<fieldset id="draw_tools_0">
  <legend>Controles</legend>
  <button onclick="DrawObject.removeActiveObject();" class="cancel notext">{{tr}}drawobject.delete{{/tr}}</button>
  <button onclick="DrawObject.zoomInObject()" class="zoom-in notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>
  <button onclick="DrawObject.zoomOutObject()" class="zoom-out notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>

  {{if $app->user_prefs.drawing_advanced_mode}}
    <button onclick="DrawObject.flipXObject()" class="hslip notext">{{tr}}drawobject.flipx-desc{{/tr}}</button>
    <button onclick="DrawObject.flipYObject()" class="vslip notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>

    {{*<button onclick="DrawObject.sendToBack()"     class="down notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>*}}
    <button onclick="DrawObject.sendBackwards()"  class="down notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>
    <button onclick="DrawObject.bringForward()"   class="up notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>
    {{*<button onclick="DrawObject.bringToFront()"   class="up notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>*}}

    <p><label>Opacité<input type="range" min="1" max="100" value="100" onchange="DrawObject.changeOpacty($V(this));"/></label></p>
  {{/if}}

</fieldset>
<hr/>
<fieldset>
  <legend>Historique</legend>
  <button onclick="DrawObject.undo()" class="undo">Annuler le dernier ajout</button>
  <button onclick="DrawObject.clearCanvas();" class="trash">Tout effacer</button>
</fieldset>
<hr/>
<fieldset>
  <legend>Enregistrement du fichier</legend>
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
  <legend>Export du fichier</legend>
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