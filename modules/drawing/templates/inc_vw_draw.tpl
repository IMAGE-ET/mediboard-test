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

{{mb_script module=drawing script=DrawObject ajax=1}}
{{mb_script module="mediusers" script="color_selector" ajax=1}}

<style>
  #target_files img {
    max-width: 150px;
  }

  #target_files {
    text-align: center;
  }

  #target_files li {
    display: inline-block;
    list-style: none;
    padding:0;
    margin: 0;
  }

  fieldset.active{
    border-left:solid 3px green;
  }

  .basic_color {
    border:solid 1px black;
    width: 1.35em;
    height: 2em;
    display: inline-block;
    line-height: 1.5em;
  }
</style>

<script>

  saveDraw = function(form, is_export) {
    if (is_export) {
      var sdata = DrawObject.getSvgStr();
      $V(form.export, 1);
    }
    else {
      var sdata = DrawObject.getJsonStr();
    }
    $V(form.svg_content, sdata);
    return onSubmitFormAjax(form, {method:'post', onComplete:Control.Modal.close});
  };

  toggleMode = function() {
    var mode = DrawObject.toggleMode();
    $$('#draw_tools_0, #draw_tools_1').each(function(elt) {
      elt.removeClassName('active');
    });
    if (mode) {
      $('draw_tools_1').addUniqueClassName('active');
    }
    else {

      $('draw_tools_0').addUniqueClassName('active');
    }
  };

  colorSelect = function(value) {
    DrawObject.changeDrawColor(value);
    $('current_color').setStyle({backgroundColor : value});
  };

  debug = function() {
    console.log(DrawObject.getSvgStr());
  };

  selectThisElement = function(element) {
    var file_id = element.get('file_id');
    var is_svg = element.get('file_type').indexOf('svg') != -1;
    if (is_svg) {
      //old
      DrawObject.insertImg("?m=files&a=fileviewer&file_id="+file_id+"&phpThumb=1");

    }
    else {
      //new
      var url = new Url("drawing", "ajax_get_content");
      url.addParam('format', 'uri');
      url.addParam("file_id", file_id);
      url.requestJSON(DrawObject.insertImg);
    }
  };



  keyBoardEvent = function(ev) {
    var key = ev.keyCode;

    if (key == 32) {  // spacebar = toggle
      //ev.preventDefault();
      toggleMode();
      return false;
    }

    if (key == 46) { // suppr
      ev.preventDefault();
      DrawObject.removeActiveObject();
      return false;
    }

    /*
    Later
    if (key == 65) { // a = annuler
      ev.preventDefault();
      DrawObject.undo();
      return false;
    }
    */
    return false;
  };

  rightclicEvent = function(ev) {
    var type = ev.target.tagName.toLowerCase();
    if (type == "canvas") {
      ev.preventDefault();
      return false;
    }
  };

  Main.add(function() {
    var tabs = Control.Tabs.create('tabs_draw', false);

    // right click to toggle modes
    document.observe('contextmenu', rightclicEvent);

    // keyboard down
    document.observe('keydown', keyBoardEvent);

    // init
    DrawObject.init('canvas');
    {{if $draw->_id}}
      DrawObject.loadDraw('{{$draw->_binary_content|smarty:nodefaults}}');
    {{else}}
      DrawObject.canvas.backgroundColor = '#'+Preferences.drawing_background;
      DrawObject.canvas.renderAll();
    {{/if}}
    DrawObject.refresh();
});
</script>

<table class="main">
  <tr>
    <td style="text-align: center; width: 600px;">
      <canvas id="canvas" width="800" height="600" style="border: solid 1px black;" >
        {{tr}}Canvas.unsupported{{/tr}}
      </canvas>
    </td>
    <td style="text-align: center;">
      {{if $draw->_id}}Modification{{else}}Création{{/if}}
      <ul class="control_tabs" id="tabs_draw">
        <li><a href="#draw_tools"><img src="modules/drawing/images/icon.png" alt="Dessin" style="width: 16px" /></a></li>
        <li><a href="#notes"><img src="style/mediboard/images/buttons/comment.png" alt="{{tr}}Help{{/tr}}" /></a></li>
        <li><a href="#ressources"><img src="modules/dPfiles/images/icon.png" alt="Fichiers" style="width: 16px" /></a></li>
        <li><a href="#draw_help"><img src="style/mediboard/images/buttons/help.png" alt="{{tr}}Help{{/tr}}" /></a></li>

        {{if $admin}}
          <li><a href="#debug_canvas">Debug</a></li>
        {{/if}}
      </ul>
      <div id="draw_tools">
        {{mb_include module=drawing template=inc_draw/draw_tools}}
      </div>

      <div id="notes">
        {{foreach from=$draw->_ref_notes item=_note}}

        {{/foreach}}
      </div>

      <div id="ressources" style="height: 500px; overflow-y: auto; display: none;">
        {{if $object}}
          <h2>Contexte</h2>
          {{foreach from=$object->_ref_files item=_file}}
            <img src="?m=files&a=fileviewer&file_id={{$_file->_id}}&phpThumb=1" alt=""/>
          {{foreachelse}}
            {{tr}}CFile.none{{/tr}}
          {{/foreach}}
        {{/if}}
        <h2>Ressources</h2>
        <form method="get" name="filter_files_draw" onsubmit="return onSubmitFormAjax(this, null, 'target_files')">
          <input type="hidden" name="m" value="drawing"/>
          <input type="hidden" name="a" value="ajax_list_files_for_category"/>
          <select name="category_id" style="width:15em;" onchange="this.form.onsubmit();">
            <option value="">&mdash; {{tr}}Select{{/tr}}</option>
            {{foreach from=$categories item=_cat}}
              <option value="{{$_cat->_id}}">{{$_cat}} ({{$_cat->_nb_files}})</option>
            {{/foreach}}
          </select>
        </form>
        <div id="target_files">
        </div>
      </div>

      {{if $admin}}
        <div id="debug_canvas" style="display: none;">
          <button class="" onclick="debug();">Debug</button>
        </div>
      {{/if}}

      <div id="draw_help" style="display: none;">
        <h2>Raccourcis clavier</h2>
        <table class="tbl">
          <tr>
            <th>a</th>
            <td>Annuler la dernière action</td>
          </tr>
          <tr>
            <th>Barre<br/>d'espace</th>
            <td>Mode Dessin / edition</td>
          </tr>
          <tr>
            <th>Suppr</th>
            <td>Supprimer l'objet actif</td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>

{{* lets work *}}
