{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Calendar.regField(getForm("changeDateAdmissions").date, null, {noView: true});
</script>

<table class="tbl" id="admissions" >
  <tr>
    <th class="title" colspan="10">
      <a href="?m=dPadmissions&tab=vw_idx_admission&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$dPconfig.longdate}}
      <form name="changeDateAdmissions" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_admission" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
			</form>
			<a href="?m=dPadmissions&tab=vw_idx_admission&date={{$demain}}" style="display: inline">>>></a>
			<br />
			
      <em style="float: left; font-weight: normal;">
			{{$today|@count}}
      {{if $selAdmis == "n"}}admissions non effectuées
      {{elseif $selSaisis == "n"}}dossiers non préparés
      {{else}}admissions ce jour
      {{/if}}
      </em>
	
			<select style="float: right" name="filterFunction" style="width: 16em;" onchange="reloadAdmission($V(getForm('selType')._type_admission), this.value);">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions_filter item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
    </th>
  </tr>
	
	{{assign var=url value="?m=$m&tab=vw_idx_admission&selAdmis=$selAdmis&selSaisis=$selSaisis"}}
  <tr>
    <th>
    {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    <th style="width: 1%;">
      <input type="text" size="3" onkeyup="Admissions.filter(this)" id="filter-patient-name" />
    </th>
  
    <th>
    {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th>
    {{mb_colonne class="CSejour" field="entree_prevue" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th>Chambre</th>
    <th>Admis</th>
    <th>
      {{if $canAdmissions->edit}}
      <form name="editAllAdmFrm" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_edit_admis" />
      <input type="hidden" name="id" value="{{$date}}" />
      <input type="hidden" name="mode" value="allsaisie" />
      <input type="hidden" name="value" value="1" />
			<input type="hidden" name="filterFunction" value="{{$filterFunction}}" />
			<button class="tick" type="submit">
        {{tr}}CSejour-saisi_SHS-tous{{/tr}}
      </button>
      </form>
      {{else}}
        {{tr}}CSejour-saisi_SHS-tous{{/tr}}
      {{/if}}
    </th>
    <th>Anesth</th>
    <th>CMU</th>
    {{if $dPconfig.dPadmissions.show_dh}}
    <th>DH</th>
    {{/if}}
  </tr>

  {{foreach from=$today item=curr_adm}}
  <tr id="admission{{$curr_adm->sejour_id}}">
    {{mb_include module=dPadmissions template="inc_vw_admission_line" nodebug=true}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10"><em>{{tr}}None{{/tr}}</em></td>
  </tr>
  {{/foreach}}
</table>