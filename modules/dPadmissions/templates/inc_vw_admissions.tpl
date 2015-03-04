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
    Prestations.callback = reloadAdmission;
    Calendar.regField(getForm("changeDateAdmissions").date, null, {noView: true});
    Admissions.restoreSelection();
  });
</script>

{{mb_include module=admissions template=inc_refresh_page_message}}

{{if $period}}
  <div class="small-info">
    Vue partielle limitée au <strong>{{$period}}</strong>. Veuillez changer le filtre pour afficher toute la journée.
  </div>
{{/if}}

<table class="tbl" id="admissions">
  <tr>
    <th class="title" colspan="10">
      <a href="#1" style="display: inline" onclick="$V(getForm('selType').date, '{{$hier}}'); reloadFullAdmissions()">&lt;&lt;&lt;</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDateAdmissions" action="?" method="get">
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('selType').date, this.value); reloadFullAdmissions()" />
      </form>
      <a href="#1" style="display: inline" onclick="$V(getForm('selType').date, '{{$demain}}'); reloadFullAdmissions()">&gt;&gt;&gt;</a>
      <br />

      <em style="float: left; font-weight: normal;">
      {{$sejours|@count}}
      {{if $selAdmis == "n"}}admissions non effectuées
      {{elseif $selSaisis == "n"}}dossiers non préparés
      {{else}}admissions ce jour
      {{/if}}
      </em>

      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="$V(getForm('selType').filterFunction, this.value); reloadAdmission();">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filterFunction}}
      </select>
    </th>
  </tr>

  <tr>
    <th style="width: 10%">{{tr}}CSejour-admit{{/tr}}</th>
    <th class="narrow">
      <input type="checkbox" style="float: left;" onclick="Admissions.togglePrint(this.checked)"/>
    </th>
    <th style="width: 40%;">
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
    </th>

    <th style="width: 20%;">
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>

    <th>
      {{mb_colonne class="CSejour" field="entree_prevue" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>

    <th class="narrow">Chambre</th>
    <th class="narrow">
      {{if $canAdmissions->edit && $sejours|@count}}
        <form name="Multiple-CSejour" action="?" method="post" onsubmit="return submitMultiple(this);">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_sejour_aed" />
          <input type="hidden" name="sejour_ids" value="{{$sejours|@array_keys|@join:"-"}}" />
          <input type="hidden" name="entree_preparee" value="1" />
          <button class="tick oneclick" type="submit">
            {{tr}}CSejour-entree_preparee-all{{/tr}}
          </button>
        </form>
      {{else}}
        {{tr}}CSejour-entree_preparee-all{{/tr}}
      {{/if}}
    </th>
    <th style="width: 20%;"> Anesth.</th>
    <th class="narrow">Couv.</th>
    {{if $conf.dPadmissions.show_dh}}
    <th class="narrow">DH</th>
    {{/if}}
  </tr>

  {{foreach from=$sejours item=_sejour}}
  <tr class="sejour sejour-type-default sejour-type-{{$_sejour->type}} {{if !$_sejour->facturable}} non-facturable {{/if}}" id="{{$_sejour->_guid}}">
    {{mb_include module=admissions template="inc_vw_admission_line" nodebug=true}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}None{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>