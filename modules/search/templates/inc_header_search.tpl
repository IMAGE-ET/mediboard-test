{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_default var=aggreg value=true}}
{{mb_default var=check_aggreg value=true}}
{{mb_default var=fuzzy value=true}}
{{mb_default var=tooltip_help value=true}}

<table class="main layout" id="search_print">
  <tr>
    <td id="td_container_search">
      <input type="search" id="words" name="words" class="autocomplete" placeholder="Saisissez les termes de votre recherche ici..." style="width:50em; font-size:medium;" onchange="$V(this.form.start, '0'); $V(this.form.words_favoris, this.value)" autofocus/>
      <input type="hidden" name="words_favoris"/>
      <button class="favoris notext" type="button"
              onclick="
                  if($V(this.form.words)){
                  Thesaurus.addeditThesaurusEntry(this.form, null, function(){});
                  }
                  else {
                  Modal.alert('Pour ajouter un favoris il faut que votre recherche contienne au moins un mot.');
                  }
                  " title="{{tr}}CSearch-addToThesaurus{{/tr}}"></button>
      {{mb_include module=search template=inc_tooltip_help display=$tooltip_help}}
      <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
    </td>
  </tr>
  <tr>
    <td>
      {{if $aggreg}}
        <span class="circled">
          <input type="checkbox" name="aggregate" id="aggregate" value="1" {{if $check_aggreg}}checked{{/if}}/>
          <label for="aggregate"> Agrégation des résultats</label>
        </span>
      {{/if}}
      {{if $fuzzy}}
        <span class="circled">
          <input type="checkbox" name="fuzzy" id="fuzzy" value="1"/>
          <label for="fuzzy">{{tr}}CSearch-Fuzzy Search{{/tr}}</label>
        </span>
      {{/if}}
    </td>
  </tr>
</table>