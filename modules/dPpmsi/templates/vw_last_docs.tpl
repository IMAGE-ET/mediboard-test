{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
<script type="text/javascript">
  Main.add(function() {
    var form = getForm('filterDocs');
    Calendar.regField(form.entree_min);
    Calendar.regField(form.entree_max);
    Calendar.regField(form.sortie_min);
    Calendar.regField(form.sortie_max);
    Calendar.regField(form.intervention_min);
    Calendar.regField(form.intervention_max);
    
    togglePanel("{{$section_search}}");
  });
  
  changePage = function(page) {
    $V(getForm('filterDocs').page,page);
  };
  
  togglePanel = function(section) {
    var form = getForm("filterDocs");
    $V(form.section_search, section);
    var tab_sejour = $("filter_sejour");
    var tab_operation = $("filter_interv");
    
    switch (section) {
      case "sejour":
        tab_sejour.removeClassName("opacity-20");
        tab_operation.addClassName("opacity-20");
        tab_sejour.select("input", "select").invoke("enable");
        tab_operation.select("select").invoke("disable");
        break;
      case "intervention":
        tab_operation.removeClassName("opacity-20");
        tab_sejour.addClassName("opacity-20");
        tab_operation.select("input", "select").invoke("enable");
        tab_sejour.select("input", "select").invoke("disable");
    }
    
    form.select("input[type=radio]").invoke("enable");
    form.onsubmit();
  }  
</script>

<form name="filterDocs" method="get" onsubmit="return onSubmitFormAjax(this, null, 'result_docs')">
  <input type='hidden' name="m" value="dPpmsi" />
  <input type="hidden" name="a" value="ajax_refresh_last_docs" />
  <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit();"/>
  <input type="hidden" name="section_search" value="{{$section_search}}">
  <table class="form">
    <tr>
      <th class="title" colspan="8">
        Filtre
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label class="CCompteRendu" field="file_category_id"}}
      </th>
      <td>
        <select name="cat_docs" onchange="this.form.onsubmit();">
           <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
           {{foreach from=$categories item=_cat}}
             <option value="{{$_cat->_id}}" {{if $_cat->_id == $cat_docs}}selected="selected"{{/if}}>{{$_cat}}</option>
           {{/foreach}}
        </select>
      </td>
      <th>
        {{mb_label class="CMediusers" field="function_id"}}
      </th>
      <td>
        <select name="specialite_docs" onchange="$V(this.form.prat_docs, '', false); this.form.onsubmit();">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_function list=$specialites selected=$specialite_docs}}
        </select>
      </td>
      <th>
        Utilisateur
      </th>
      <td>
        <select name="prat_docs" onchange="$V(this.form.specialite_docs, '', false); this.form.onsubmit();">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$prat_docs}}
        </select>
      </td>
      <td>
        Du
        <input type="hidden" name="date_docs_min" onchange="this.form.onsubmit();" class="notNull" value="{{$date_docs_min}}"/>
        au
        <input type="hidden" name="date_docs_max" onchange="this.form.onsubmit();" class="notNull" value="{{$date_docs_max}}"/>
        <script type="text/javascript">
          Main.add(function() {
            var form = getForm('filterDocs');
            Calendar.regField(form.date_docs_min);
            Calendar.regField(form.date_docs_max);
          });
        </script>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <table class="form opacity-20" id="filter_sejour">
          <tr>
            <th class="category" colspan="2">
              <input type="radio" {{if $section_search == "sejour"}}checked{{/if}} name="_section_search_view"
              value="sejour" onclick="togglePanel(this.value)" style="float: left;" />
              Séjour
            </th>
          </tr>
          <tr>
            <th>
              {{mb_label class=CSejour field=type}}
            </th>
            <td>
              {{mb_field object=$sejour field=type onchange="this.form.onsubmit();"}}
            </td>
          </tr>
          <tr>
            <th>
              {{mb_label class=CSejour field=entree}}
            </th>
            <td>
              du <input type="hidden" name="entree_min" value="{{$entree_min}}" onchange="this.form.onsubmit();"/>
              au <input type="hidden" name="entree_max" value="{{$entree_max}}" onchange="this.form.onsubmit();"/>
            </td>
          </tr>
          <tr>
            <th>
              {{mb_label class=CSejour field=sortie}}
            </th>
            <td>
              du <input type="hidden" name="sortie_min" value="{{$sortie_min}}" onchange="this.form.onsubmit();"/>
              au <input type="hidden" name="sortie_max" value="{{$sortie_max}}" onchange="this.form.onsubmit();"/>
            </td>
          </tr>
        </table>
      </td>
      <td colspan="4">
        <table class="form opacity-20" id="filter_interv">
          <tr>
            <th class="category" colspan="2">
              <input type="radio" {{if $section_search == "intervention"}}checked{{/if}} name="_section_search_view"
              value="intervention" onclick="togglePanel(this.value)" style="float: left;" />
              Intervention
            </th>
          </tr>
          <tr>
            <th>
              {{mb_label class=COperation field=date}}
            </th>
            <td>
              du <input type="hidden" name="intervention_min" value="{{$intervention_min}}" onchange="this.form.onsubmit();"/>
              au <input type="hidden" name="intervention_max" value="{{$intervention_max}}" onchange="this.form.onsubmit();"/>
            </td>
          </tr>
          <tr>
            <th>
              Praticien
            </th>
            <td colspan="3">
              <select name="prat_interv" onchange="this.form.onsubmit();">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$prat_docs}}
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
<div style="width: 100%" id="result_docs"></div>