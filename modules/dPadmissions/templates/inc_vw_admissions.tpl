{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=planningOp script=prestations ajax=1}}

<script type="text/javascript">
Main.add(function() {
  Prestations.callback = reloadAdmission;
  Calendar.regField(getForm("changeDateAdmissions").date, null, {noView: true});
  Admissions.restoreSelection('listAdmissions');
});
</script>

{{mb_include module=admissions template=inc_refresh_page_message}}

<table class="tbl" id="admissions">
  <tr>
    <th class="title" colspan="10">
      <a href="?m=dPadmissions&tab=vw_idx_admission&date={{$hier}}" style="display: inline">&lt;&lt;&lt;</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDateAdmissions" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_admission" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=admissions&tab=vw_idx_admission&date={{$demain}}" style="display: inline">&gt;&gt;&gt;</a>
      <br />
      
      <em style="float: left; font-weight: normal;">
      {{$sejours|@count}}
      {{if $selAdmis == "n"}}admissions non effectuées
      {{elseif $selSaisis == "n"}}dossiers non préparés
      {{else}}admissions ce jour
      {{/if}}
      </em>
  
      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="reloadAdmission(this.value);">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
    </th>
  </tr>
  
  {{assign var=url value="?m=$m&tab=vw_idx_admission&selAdmis=$selAdmis&selSaisis=$selSaisis"}}
  <tr>
    <th class="narrow">{{tr}}CSejour-admit{{/tr}}</th>
    <th>
      <input type="checkbox" style="float: left;" onclick="Admissions.togglePrint('admissions', this.checked)"/>
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
    </th>
  
    <th>
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th>
      {{mb_colonne class="CSejour" field="entree_prevue" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th class="narrow">Chambre</th>
    <th class="narrow">
      {{if $canAdmissions->edit}}
      <form name="Multiple-CSejour" action="?" method="post" onsubmit="return submitMultiple(this);">

      <input type="hidden" name="m" value="planningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_ids" value="{{$sejours|@array_keys|@join:"-"}}" />

      <input type="hidden" name="entree_preparee" value="1" />

      <button class="tick" type="submit">
        {{tr}}CSejour-entree_preparee-tous{{/tr}}
      </button>

      </form>
      {{else}}
        {{tr}}CSejour-entree_preparee-tous{{/tr}}
      {{/if}}
    </th>
    <th>Anesth.</th>
    <th>Couv.</th>
    {{if $conf.dPadmissions.show_dh}}
    <th>DH</th>
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