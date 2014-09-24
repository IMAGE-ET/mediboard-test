{{*
 * $Id$
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=ihe script=ihe}}

<script>
  Main.add(function () {
    var form       = getForm("editiheConfig");
    var tokenfield = new TokenField(form.elements["ihe[function_ids]"]);
    tokenfield.getValues().each(function (value) {
      var list = $("listFunctions");
      var select = $("selectihe_functions");
      $A(select.options).detect(function (option) {
        if (option.value == value) {
          IHE.createTag(option.text, value, option.get("color"));
        }
      });
    });
  });
</script>

<form name="editiheConfig" method="post" action="?m={{$m}}&amp;{{$actionType}}=configure" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="ihe[function_ids]" value="{{$conf.$m.function_ids}}">
  <table class="form">
    <tr><th colspan="2" class="title">{{tr}}Config{{/tr}}</th></tr>
    <tr>
      <th><label title="{{tr}}config-ihe-function_ids-desc{{/tr}}">{{tr}}config-ihe-function_ids{{/tr}}</label></th>
      <td>
        <select id="selectihe_functions" onchange="IHE.addFunction(this)">
          <option value="" selected>{{tr}}Choose{{/tr}}</option>
          {{foreach from=$functions item=_function}}
            <option value="{{$_function->_id}}" data-color="{{$_function->color}}">{{$_function->_view}}</option>
          {{/foreach}}
        </select>
        <ul id="listFunctions" class="tags">
        </ul>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>