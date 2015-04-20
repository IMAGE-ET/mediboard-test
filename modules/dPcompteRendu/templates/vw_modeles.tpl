{{*
 * $Id$
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=compteRendu script=modele}}

<script>
  function sortBy(order_col, order_way) {
    var oForm = getForm("filterModeles");
    $V(oForm.order_col, order_col);
    $V(oForm.order_way, order_way);
    oForm.onsubmit();
  }

  function updateSelected(elt) {
    elt.up("table").select('tr').invoke("removeClassName", "selected");
    elt.addClassName("selected");
  }

  Main.add(function() {
    var form = getForm("filterModeles");
    var urlUsers = new Url("mediusers", "ajax_users_autocomplete");
    urlUsers.addParam("edit", "1");
    urlUsers.addParam("input_field", "user_id_view");
    urlUsers.autoComplete(form.user_id_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.user_id, id);
      }
    });

    var urlFunctions = new Url("mediusers", "ajax_functions_autocomplete");
    urlFunctions.addParam("edit", "1");
    urlFunctions.addParam("input_field", "function_id_view");
    urlFunctions.addParam("view_field", "text");
    urlFunctions.autoComplete(form.function_id_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.function_id, id);
      }
    });

    Modele.refresh();
  });
</script>

<a id="didac_button_create" class="button new" href="#1" onclick="Modele.edit(0)">
  {{tr}}CCompteRendu-title-create{{/tr}}
</a>

<fieldset>
  <legend>
    {{tr}}CCompteRendu-filter{{/tr}}
  </legend>

  <form name="filterModeles" method="get" onsubmit="return onSubmitFormAjax(this, null, 'modeles_area')">
    <input type="hidden" name="m" value="compteRendu" />
    <input type="hidden" name="a" value="ajax_list_modeles" />
    <input type="hidden" name="order_col" value="{{$order_col}}" />
    <input type="hidden" name="order_way" value="{{$order_way}}" />

    <table class="form">
      <tr>
        <th>{{mb_label object=$filtre field=user_id}}</th>
        <td>
          {{mb_field object=$filtre field=user_id hidden=1 onchange="\$V(this.form.function_id, '', false); \$V(this.form.function_id_view, '', false); this.form.onsubmit();"}}
          <input type="text" name="user_id_view" value="{{$filtre->_ref_user}}" />
        </td>
        <th>{{mb_label object=$filtre field=object_class}}</th>
        <td>
          {{assign var=_spec value=$filtre->_specs.object_class}}
          <select name="object_class" onchange="this.form.onsubmit()">
            <option value="">&mdash; {{tr}}CCompteRendu-object_class-all{{/tr}}</option>
            {{foreach from=$_spec->_locales item=_locale key=_object_class}}
              <option value="{{$_object_class}}" {{if $filtre->object_class == $_object_class}}selected{{/if}}>{{$_locale}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$filtre field=function_id}}</th>
        <td>
          {{mb_field object=$filtre field=function_id hidden=1 onchange="\$V(this.form.user_id, '', false); \$V(this.form.user_id_view, '', false); this.form.onsubmit();"}}
          <input type="text" name="function_id_view" value="{{$filtre->_ref_function}}" />
        </td>

        <th>{{mb_label object=$filtre field=type}}</th>
        <td>{{mb_field object=$filtre field=type onchange="this.form.onsubmit()" canNull=true emptyLabel="All"}}</td>
      </tr>
    </table>
  </form>
</fieldset>

<form name="deleteModele" method="post" class="prepared">
  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  <input type="hidden" name="callback" value="Modele.refresh" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="compte_rendu_id" />
</form>

<div id="modeles_area"></div>