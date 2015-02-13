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

{{mb_script module=drawing script=DrawingCategory}}
{{mb_script module=files script=file}}

<style>
  .drawing_file_list img{
    max-width: 120px;
    max-height: 120px;
  }
</style>

<script>
  Main.add(function() {
    refreshList();
  });

  refreshList = function() {
    var oform = getForm('filter_ressources');
    oform.onsubmit();
  };
</script>

<button class="new" type="button" onclick="DrawingCategory.editModal('', reloadPage);">{{tr}}CDrawingCategory.new{{/tr}}</button>

<fieldset>
  <legend>Filtrer les ressources</legend>
  <form method="get" name="filter_ressources" onsubmit="return onSubmitFormAjax(this, {}, 'result_ressouces')">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="a" value="ajax_list_ressources"/>
    <table class="form">
      <tr>
        <th>Utilisateur</th>
        <td>
          <select name="user_id" onchange="$V(this.form.function_id, '', false); this.form.onsubmit();">
            <option value="">&mdash; {{tr}}Select{{/tr}}</option>
            {{foreach from=$users item=_user}}
              <option value="{{$_user->_id}}">{{$_user}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>Fonction</th>
        <td>
          <select name="function_id" onchange="$V(this.form.user_id, '', false); this.form.onsubmit();">
            <option value="">&mdash; {{tr}}Select{{/tr}}</option>
            {{foreach from=$functions item=_function}}
              <option value="{{$_function->_id}}">{{$_function}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    </table>
  </form>
</fieldset>
<div id="result_ressouces"></div>
