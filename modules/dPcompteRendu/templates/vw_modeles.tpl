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

  Main.add(Modele.refresh);
</script>

<a id="didac_button_create" class="button new" href="#1" onclick="Modele.edit(0)">
  {{tr}}CCompteRendu-title-create{{/tr}}
</a>

<form name="deleteModele" method="post" class="prepared">
  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  <input type="hidden" name="callback" value="Modele.refresh" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="compte_rendu_id" />
</form>

<form name="filterModeles" method="get" onsubmit="return onSubmitFormAjax(this, null, 'modeles_area')">
  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="a" value="ajax_list_modeles" />
  <input type="hidden" name="order_col" value="{{$order_col}}" />
  <input type="hidden" name="order_way" value="{{$order_way}}" />

  <table class="form">
    <tr>
      <th class="category" colspan="10">{{tr}}CCompteRendu-filter{{/tr}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$filtre field=user_id}}</th>
      <td>
        <select name="user_id" onchange="$V(this.form.function_id, '', false); this.form.onsubmit()">
          <option value="" {{if !$filtre->user_id}}selected{{/if}}>&mdash; Choisissez un utilisateur</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$filtre->user_id}}
        </select>
      </td>
      <th>{{mb_label object=$filtre field=function_id}}</th>
      <td>
        <select name="function_id" onchange="$V(this.form.user_id, '', false); this.form.onsubmit()">
          <option value="" {{if !$filtre->function_id}}selected{{/if}}>&mdash; Choisissez une fonction</option>
          {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filtre->function_id}}
        </select>
      </td>
      <th>{{mb_label object=$filtre field=object_class}}</th>
      <td>
       {{assign var=_spec value=$filtre->_specs.object_class}}
        <select name="object_class" onchange="this.form.onsubmit()">
          <option value="">&mdash; {{tr}}CCompteRendu-type-all{{/tr}}</option>
          {{foreach from=$_spec->_locales item=_locale key=_object_class}}
            <option value="{{$_object_class}}" {{if $filtre->object_class == $_object_class}}selected{{/if}}>{{$_locale}}</option>
          {{/foreach}}
        </select>
      </td>

      <th>{{mb_label object=$filtre field=type}}</th>
      <td>{{mb_field object=$filtre field=type onchange="this.form.onsubmit()" canNull=true emptyLabel="All"}}</td>
    </tr>
  </table>
</form>

<div id="modeles_area"></div>