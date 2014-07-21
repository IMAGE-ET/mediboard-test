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
      <input type="text" class="color_picker" name="color" value="000000" onchange="colorSelect(this.value);" id="color_picker_draw"/>
    </label>
    <script>
      Main.add(function(){new jscolor.color($('color_picker_draw'), {})})
    </script>
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
  <button onclick="DrawObject.clearCanvas();" class="cleanup">Tout effacer</button>
</fieldset>

<hr/>

<form method="get" name="titi" >
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
        <input type="color" value="#000000" name="color_text_cv" id="color_text_cv"/>
      </td>
    </tr>
    <tr>
      <th>{{if $app->user_prefs.drawing_advanced_mode}}
          Ombre<br/>du texte {{/if}}

      </th>
      <td>
        <input type="text" value="#ffffff 0 0 10px" name="bgcolor_text_cv" id="bgcolor_text_cv"   {{if !$app->user_prefs.drawing_advanced_mode}}
style="display: none;" {{/if}}            />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" onclick="DrawObject.addEditText( $V('content_text_cv'), $V('color_text_cv'), $V('bgcolor_text_cv') );">Valider</button>
        <button type="button" onclick="Control.Modal.close();">Annuler</button>
      </td>
    </tr>
  </table>
</form>