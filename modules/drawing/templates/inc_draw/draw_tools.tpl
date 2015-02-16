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

<fieldset id="draw_tools_1">
  <legend><label><input type="radio" name="toggle_type" onclick="changeMode('draw');" checked="checked"/>Dessin au crayon</label></legend>
  <p><label>Largeur du trait<input type="range" min="1" max="10" value="3" onchange="DrawObject.changeDrawWidth($V(this));"/></label></p>
  <p><label>Couleur
      <input type="text" class="color_picker" name="color" value="000000" onchange="colorSelect(this.value);" id="color_picker_draw"/>
    </label>
    <script>
      Main.add(function(){
        new jscolor.color($('color_picker_draw'), {});
        new jscolor.color($('color_text_cv'), {});
      });
    </script>
  </p>
</fieldset>

<fieldset id="draw_tools_0">
  <legend><label><input type="radio" name="toggle_type" onclick="changeMode('edit');" />Controles</label></legend>
  <div id="draw_object_tool">
    <button onclick="DrawObject.removeActiveObject();" class="cancel notext">{{tr}}drawobject.delete{{/tr}}</button>
    <button onclick="DrawObject.zoomInObject()" class="zoom-in notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>
    <button onclick="DrawObject.zoomOutObject()" class="zoom-out notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>

    <button onclick="DrawObject.flipXObject()" class="hslip notext">{{tr}}drawobject.flipx-desc{{/tr}}</button>
    <button onclick="DrawObject.flipYObject()" class="vslip notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>

    {{*<button onclick="DrawObject.sendToBack()"     class="down notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>*}}
    <button onclick="DrawObject.sendBackwards()"  class="down notext">{{tr}}drawobject.sendBackwards-desc{{/tr}}</button>
    <button onclick="DrawObject.bringForward()"   class="up notext">{{tr}}drawobject.sendForward-desc{{/tr}}</button>
    {{*<button onclick="DrawObject.bringToFront()"   class="up notext">{{tr}}drawobject.flipy-desc{{/tr}}</button>*}}

    <p><label>Opacité<input type="range" min="1" max="100" value="100" onchange="DrawObject.changeOpacty($V(this));"/></label></p>
  </div>
  <button onclick="DrawObject.clearCanvas();" class="cleanup">Tout effacer</button>

</fieldset>

<hr/>

<fieldset>
  <legend>Texte</legend>
  <form method="get" name="text_edit_canvas" >
    <table class="form">
      <tr>
        <th>Texte</th>
        <td>
          <textarea id="content_text_cv" name="content_text_cv"></textarea>
        </td>
      </tr>
      <tr>
        <th>Couleur</th>
        <td>
          <input type="text" class="color_picker" value="#000000" name="color_text_cv" id="color_text_cv"/>
        </td>
      </tr>
      <tr>
        <th>
            Ombre<br/>du texte
        </th>
        <td>
          <input type="text" value="#000000 0 0 10px" name="bgcolor_text_cv" id="bgcolor_text_cv" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="button">
          <button type="button" onclick="DrawObject.addEditText( $V('content_text_cv'), $V('color_text_cv'), $V('bgcolor_text_cv') );">Valider</button>
        </td>
      </tr>
    </table>
  </form>
</fieldset>