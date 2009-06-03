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

<table class="tbl">
  <tr>
    <th colspan="9">
      <a href="?m=dPadmissions&tab=vw_idx_admission&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$dPconfig.longdate}}
      <form name="changeDateAdmissions" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_admission" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=dPadmissions&tab=vw_idx_admission&date={{$demain}}" style="display: inline">>>></a>
      <br /> 
      <em>
      {{if $selAdmis == "n"}}Admissions non effectuées
      {{elseif $selSaisis == "n"}}Dossiers non préparés
      {{else}}Toutes les admissions
      {{/if}}
      {{if $selTri == "nom"}}triées par nom
      {{elseif $selTri == "heure"}}triées par heure
      {{/if}}
      </em>
    </th>
  </tr>
  <tr>
    <th>
    {{mb_colonne class="CSejour" field="_nomPatient" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_idx_admission&selAdmis=$selAdmis&selSaisis=$selSaisis"}}
    </th>
    
    <th>
    {{mb_colonne class="CSejour" field="_nomPraticien" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_idx_admission&selAdmis=$selAdmis&selSaisis=$selSaisis"}}
    </th>
    
    <th>
    {{mb_colonne class="CSejour" field="entree_prevue" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_idx_admission&selAdmis=$selAdmis&selSaisis=$selSaisis"}}
    </th>
    
    <th>Chambre</th>
    <th>Admis</th>
    <th>
      <form name="editAllAdmFrm" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_edit_admis" />
      <input type="hidden" name="id" value="{{$date}}" />
      <input type="hidden" name="mode" value="allsaisie" />
      <input type="hidden" name="value" value="1" />
      <button class="tick" type="submit">
        {{tr}}CSejour-saisi_SHS-tous{{/tr}}
      </button>
      </form>
    </th>
    <th>Anesth</th>
    <th>CMU</th>
    <th>DH</th>
  </tr>
  {{foreach from=$today item=curr_adm}}
  <tr id="admission{{$curr_adm->sejour_id}}">
  {{include file="inc_vw_admission_line.tpl" nodebug=true}}
  </tr>
  {{/foreach}}
</table>