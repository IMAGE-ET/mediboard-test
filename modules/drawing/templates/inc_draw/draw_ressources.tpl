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

{{if $object}}
  <h2>Contexte</h2>
  <div id="context_ressources">
    {{mb_include module=drawing template=inc_list_files_for_category category=$object}}
  </div>
{{/if}}
{{if $conf.drawing.drawing_allow_external_ressource}}
  <input type="text" name="url" id="url_external_draw" value=""/><button onclick="insertFromInternet($V('url_external_draw'));">OK</button>
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