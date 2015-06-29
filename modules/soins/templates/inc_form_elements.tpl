{{*
 * $Id$
 *
 * @category Dossier de soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=with_med value=false}}

<script>
  // Selection ou deselection de tous les elements d'une catégorie
  selectCategory = function(oCheckboxCat) {
    var checked = oCheckboxCat.checked;
    var checkboxes = $('categories').select('input.' + oCheckboxCat.value);
    var count_cat   = oCheckboxCat.value == "med" ? 1 : checkboxes.length;

    checkboxes.invoke("writeAttribute", "checked", checked);

    var counter = $("countSelected_" + oCheckboxCat.value);
    counter.update(checked ? count_cat : 0);
    selectTr(counter);
  };

  toggleCheckbox = function(elt) {
    elt.toggleClassName("cancel");
    elt.toggleClassName("tick");
    var checked = elt.hasClassName("cancel");
    var categories = $('categories');
    categories.select('input[type=checkbox][name!=premedication]').invoke("writeAttribute", "checked", checked);
    categories.select('tr').invoke(checked ? "addClassName" : "removeClassName", "selected");
    if (checked) {
      categories.select('.counter').each(function(counter) {
        var split = counter.id.split("_");
        counter.update($$(".category_"+split[1]).length);
      });
    }
    else {
      categories.select('.counter').invoke("update", "0");
    }
  };

  // Affichage des elements au sein des catégories
  toggleElements = function(category_guid) {
    $('categories').select('.category_'+category_guid).invoke('toggle');
  };

  // Mise a jour du compteur lors de la selection d'un element
  updateCountCategory = function(checkbox, category_guid) {
    var counter = $('countSelected_'+category_guid);
    var count = parseInt(counter.innerHTML);
    count = checkbox.checked ? count+1 : count-1;
    counter.update(count);
    selectTr(counter);
    var all_checked = $$("."+category_guid).all(function(elt) { return elt.checked });
    var input_category = $("categories").select("input[value="+category_guid+"]")[0];
    input_category.checked = all_checked;
  };

  selectTr = function(counter) {
    var count = parseInt(counter.innerHTML);
    count ? counter.up("tr").addClassName("selected") : counter.up("tr").removeClassName("selected");
  };

  fillCategories = function() {
    var table_categories = $("categories");
    if (table_categories) {
      {{foreach from=$categories_id item=_category_id}}
      var elts = table_categories.select("input[value={{$_category_id}}]");
      if (elts.length > 0) {
        elts[0].click();
      }
      {{/foreach}}
    }
  };
</script>

<form name="selectElts" method="get">
  <!-- Checkbox vide permettant d'eviter que le $V considere qu'il faut retourner true ou false s'il n'y a qu'une seule checkbox -->
  <input type="checkbox" name="elts" value="" style="display: none;"/>

  <table class="tbl">
    <tr>
      <th class="title">
        <small style="float: right">
          <input type="checkbox" name="premedication" /> {{mb_label class="CPrescriptionLineElement" field="premedication"}}
        </small>
        <button type="button" class="cancel notext" style="float: left" onclick="toggleCheckbox(this);">{{tr}}Reset{{/tr}}</button>
        Activités
      </th>
    </tr>
    {{if $with_med}}
      <tr>
        <th>
          Médicaments
        </th>
      </tr>
      <tr>
        <td>
          <span style="float: right;"><strong>(<span id="countSelected_med" class="counter">0</span>/1)</strong></span>
          <input type="checkbox" name="elts" value="med" onclick="selectCategory(this);" />
          <strong><a href="#1" style="display: inline;">Médicaments</a></strong>
        </td>
      </tr>
    {{/if}}
    {{foreach from=$categories key=_chapitre item=_cats_by_chap}}
      <tr>
        <th>{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</th>
      </tr>
      {{foreach from=$_cats_by_chap item=_elements}}
        {{foreach from=$_elements item=_element name=elts}}
          {{if $smarty.foreach.elts.first}}
            {{assign var=category value=$_element->_ref_category_prescription}}
            <tr>
              <td>
                <span style="float: right;"><strong>(<span id="countSelected_{{$category->_guid}}" class="counter">0</span>/{{$_elements|@count}})</strong></span>
                <input type="checkbox" onclick="selectCategory(this);" value="{{$category->_guid}}" />
                <strong onclick="toggleElements('{{$category->_guid}}');">
                  <a href="#" style="display: inline;">{{$category}}</a>
                </strong>
              </td>
            </tr>
          {{/if}}
          <tr class="category_{{$category->_guid}}" style="display: none;">
            <td style="text-indent: 2em;">
              <label>
                <input type="checkbox" name="elts" value="{{$_element->_id}}" class="{{$category->_guid}}" onclick="updateCountCategory(this, '{{$category->_guid}}');" />
                {{$_element}}
              </label>
            </td>
          </tr>
        {{/foreach}}
      {{/foreach}}
      {{foreachelse}}
        {{if !$with_med}}
        <tr>
          <td class="empty">Aucune activité</td>
        </tr>
        {{/if}}
    {{/foreach}}
  </table>
</form>