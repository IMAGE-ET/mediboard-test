{{*
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  filterResults = function(keywords) {
    var area_docs = $("area_docs");
    var items = area_docs.select(".item_name");

    if (!keywords) {
      items.each(function(elt) {
        {{if $display == 'icon'}}
        elt.up("table").setStyle({display: "inline-table"});
        {{else}}
        elt.up("tr").setStyle({display: "table-row"});
        {{/if}}
      });
      return;
    }

    items.each(function(elt) {
      {{if $display == 'icon'}}
      elt.up("table").setStyle({display: "none"});
      {{else}}
      elt.up("tr").setStyle({display: "none"});
      {{/if}}
    });

    keywords = keywords.split(" ");

    area_docs.select(".item_name").each(function(item) {
      keywords.each(function(keyword) {
        if (item.getText().like(keyword)) {
          {{if $display == 'icon'}}
          item.up("table").setStyle({display: "inline-table"});
          {{else}}
          item.up("tr").setStyle({display: "table-row"});
          {{/if}}
        }
      });
    });
  };
</script>

{{if $display == "list"}}
  <table class="tbl">
{{/if}}

{{foreach from=$context->_all_docs item=_docs_by_context key=context}}
  {{if $tri != "date"}}
    {{if $display == "list"}}
      <tr>
        <th colspan="5">
          {{$context}}
        </th>
      </tr>
    {{else}}
      <table class="tbl">
        <tr>
          <th>
            {{$context}}
          </th>
        </tr>
      </table>
    {{/if}}
  {{/if}}
  {{if $display == "list"}}
    <tr>
      <th class="section narrow"></th>
      <th class="section">Nom</th>
      <th class="section" style="width: 25%;">Catégorie</th>
      <th class="section">Contexte</th>
      <th class="section narrow">Date</th>
    </tr>
  {{/if}}

  {{foreach from=$_docs_by_context item=_doc}}
    {{if $_doc instanceof CCompteRendu}}
      {{mb_include module=compteRendu template=CCompteRendu_fileviewer doc=$_doc}}
    {{elseif $_doc instanceof CFile}}
      {{mb_include module=files template=CFile_fileviewer file=$_doc}}
    {{elseif $_doc instanceof CExLink}}
      {{mb_include module=forms template=CExLink_fileviewer link=$_doc}}
    {{/if}}
  {{/foreach}}
{{foreachelse}}
  {{if $display == "list"}}
    <tr>
      <td colspan="5">
  {{/if}}
    <div class="small-info">
      Aucun document
    </div>
  {{if $display == "list"}}
    </td>
  </tr>
  {{/if}}
{{/foreach}}

{{if $display == "list"}}
  </table>
{{/if}}