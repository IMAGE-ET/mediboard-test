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

<script>
  Main.add(function() {
    Control.Tabs.create("tabs_ressources");
  });
</script>

<ul id="tabs_ressources" class="control_tabs">
  <li><a href="#context_tab">Contexte</a></li>
  <li><a href="#upload_tab">Envoi</a></li>
  <li><a href="#ressource_tab">Ressources</a></li>
</ul>

<div id="upload_tab" style="display: none;">
  <h2>Téléversement</h2>
  <input type="file" name="path" onchange="DrawObject.insertFromUpload(this);"/>
  {{if $conf.drawing.drawing_allow_external_ressource}}
    <h2>Lien internet</h2>
    <input type="text" name="url" id="url_external_draw" value="" placeholder="Lien direct web"/><button onclick="insertFromInternet($V('url_external_draw'));">OK</button>
  {{/if}}
</div>
<div id="context_tab" style="display: none;">
  {{if $object}}
    <h2>Contexte</h2>
    <div id="context_ressources">
      {{mb_include module=drawing template=inc_list_files_for_category category=$object}}
    </div>
  {{/if}}
</div>
<div id="ressource_tab" style="display: none">
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