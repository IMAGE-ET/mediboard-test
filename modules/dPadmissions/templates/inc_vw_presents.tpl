{{* $Id: inc_vw_admissions.tpl 15057 2012-03-29 08:07:50Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 15057 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Calendar.regField(getForm("changeDatePresents").date, null, {noView: true});
</script>

<table class="tbl" id="admissions">
  <tr>
    <th class="title" colspan="10">
      <a href="?m=dPadmissions&tab=vw_idx_present&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDatePresents" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_present" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=dPadmissions&tab=vw_idx_present&date={{$demain}}" style="display: inline">>>></a>
      <br />
      
      <em style="float: left; font-weight: normal;">
        {{$sejours|@count}} présents ce jour
      </em>
  
      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="reloadPresent(this.value);">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
    </th>
  </tr>
  
  {{assign var=url value="?m=$m&tab=vw_idx_present"}}
  <tr>
    <th>
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
    </th>
  
    <th>
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th class="narrow">
      {{mb_colonne class="CSejour" field="entree" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th class="narrow">
      {{mb_colonne class="CSejour" field="sortie" order_col=$order_col order_way=$order_way url=$url}}
    </th>
    
    <th>Chambre</th>
  </tr>

  {{foreach from=$sejours item=_sejour}}
  <tr class="sejour" id="{{$_sejour->_guid}}">
    {{mb_include module=admissions template="inc_vw_present_line" nodebug=true}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}None{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>