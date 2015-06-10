{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    Admissions.restoreSelection();
    Calendar.regField(getForm("changeDateSorties").date, null, {noView: true});
    Prestations.callback = reloadSorties;
  });
</script>

{{mb_include module=admissions template=inc_refresh_page_message}}

{{if $period}}
  <div class="small-info">
    Vue partielle limitée au <strong>{{$period}}</strong>. Veuillez changer le filtre pour afficher toute la journée.
  </div>
{{/if}}

<table class="tbl" id="sortie">
  <tr>
    <th class="title" colspan="{{if "dPplanningOp CSejour use_phone"|conf:"CGroups-$g"}}11{{else}}10{{/if}}">
      <a href="#1" onclick="$V(getForm('selType').date, '{{$hier}}'); reloadFullSorties()" style="display: inline">&lt;&lt;&lt;</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDateSorties" action="?" method="get">
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('selType').date, this.value); reloadFullSorties()" />
      </form>
      <a href="#1" onclick="$V(getForm('selType').date, '{{$demain}}'); reloadFullSorties()"  style="display: inline">&gt;&gt;&gt;</a>
      
      <br />
      
      <em style="float: left; font-weight: normal;">
      {{$sejours|@count}}
      {{if $selSortis == "n"}}sorties non effectuées
      {{else}}sorties ce jour
      {{/if}}
      </em>
  
      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="$V(getForm('selType').filterFunction, this.value); reloadSorties();">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{mb_include module="mediusers" template="inc_options_function" list=$functions selected=$filterFunction}}
      </select>
      
      {{if $type == "ambu" || $type == "exte" }}
      <button class="print" type="button" onclick="printAmbu('{{$type}}')">{{tr}}Print{{/tr}} {{tr}}CSejour.type.{{$type}}{{/tr}}</button>
      {{/if}}
    </th>
  </tr>

  <tr>
    <th class="narrow">Effectuer la sortie</th>
    <th class="narrow">
      <input type="checkbox" style="float: left;" onclick="Admissions.togglePrint(this.checked)"/>
    </th>
    {{if "dPplanningOp CSejour use_phone"|conf:"CGroups-$g"}}
      <th class="narrow">{{tr}}CSejour-appel{{/tr}}</th>
    {{/if}}
    <th>
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'sortie')" id="filter-patient-name" />
    </th>
    <th>
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th>
      {{mb_colonne class="CSejour" field="sortie_prevue" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th>Chambre</th>
    <th class="narrow">
      {{if $canAdmissions->edit && $sejours|@count}}
        <form name="Multiple-CSejour-sortie_preparee" action="?" method="post" onsubmit="return submitMultiple(this);">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_sejour_aed" />
          <input type="hidden" name="sejour_ids" value="{{$sejours|@array_keys|@join:"-"}}" />
          <input type="hidden" name="sortie_preparee" value="1" />
          <button class="tick oneclick" type="submit">
            {{tr}}CSejour-sortie_preparee-all{{/tr}}
          </button>
        </form>
      {{else}}
        {{tr}}CSejour-sortie_preparee-all{{/tr}}
      {{/if}}
    </th>
    {{if $conf.dPadmissions.show_dh}}
      <th>DH</th>
    {{/if}}
  </tr>
  
  {{foreach from=$sejours item=_sejour}}
    <tr class="sejour-type-default sejour-type-{{$_sejour->type}} {{if !$_sejour->facturable}} non-facturable {{/if}}" id="{{$_sejour->_guid}}">
      {{mb_include module="admissions" template="inc_vw_sortie_line" nodebug=true}}
    </tr>
  {{/foreach}}
</table>