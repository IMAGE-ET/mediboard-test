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

<script>
  Modele = {
    showUtilisation: function(compte_rendu_id) {
      var url = new Url('compteRendu', 'ajax_show_utilisation');
      url.addParam('compte_rendu_id', compte_rendu_id);
      url.requestModal(640, 480);
    },

    refresh: function() {
      var url = new Url("compteRendu", "ajax_list_modeles");
      url.addFormData(getForm("filterModeles"));
      url.requestUpdate("modeles_area");
    },

    edit: function(compte_rendu_id) {
      var url = new Url("compteRendu", "addedit_modeles");
      url.addParam("compte_rendu_id", compte_rendu_id);
      url.modal({width: "95%", height: "90%", onClose: Modele.refresh, closeOnEscape: false, waitingText: true});
    }
  }

  function sortBy(order_col, order_way) {
    var oForm = getForm("filterModeles");
    $V(oForm.order_col, order_col);
    $V(oForm.order_way, order_way);
    oForm.onsubmit();
  }

  function updateSelected(elt) {
     elt.up('table').select('tr').each(function(elt) {
       elt.removeClassName('selected');
     });
    elt.addClassName('selected');
  }

  Main.add(Modele.refresh);
</script>

<a class="button new" href="#1" onclick="Modele.edit(0)">
  {{tr}}CCompteRendu-title-create{{/tr}}
</a>

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
        <select name="user_id" onchange="this.form.onsubmit()">
          {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$filtre->user_id}}
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