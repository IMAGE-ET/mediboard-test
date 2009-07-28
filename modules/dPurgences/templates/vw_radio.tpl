{{* $Id: vw_idx_rpu.tpl 6473 2009-06-24 15:18:19Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

</script>

<table style="width:100%">
  <tr>
    <th>
     le
     {{$date|date_format:$dPconfig.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CRPU field="_patient_id"}}</th>
    <th>{{mb_title class=CRPU field="_responsable_id"}}</th>
    <th>{{mb_title class=CRPU field="radio_debut"}}</th>
    <th>{{mb_title class=CRPU field="radio_fin"}}</th>
    <th>{{mb_title class=CRPU field="bio_depart"}}</th>
    <th>{{mb_title class=CRPU field="bio_retour"}}</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    {{assign var=rpu value=$_sejour->_ref_rpu}}
    {{assign var=rpu_id value=$rpu->_id}}
    {{assign var=patient value=$_sejour->_ref_patient}}
  
    {{* Param to create/edit a RPU *}}
    {{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$_sejour->_id"}}
    {{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}
    <tr>
      <td>
	      <a style="float: right;" title="Voir le dossier" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}&amp;sejour_id={{$_sejour->_id}}">
	        <img src="images/icons/search.png" alt="Dossier patient"/>
	      </a>
	      <a href="{{$rpu_link}}">
	        <strong>
	        {{$patient->_view}}
	        </strong>
	        {{if $patient->_IPP}}
	        <br />[{{$patient->_IPP}}]
	        {{/if}}
	      </a>
      </td>
      
      <td>
	      <a href="{{$rpu_link}}">
	        {{$_sejour->_ref_praticien->_view}}
	      </a>
      </td>
      
      <td>{{mb_value object=$rpu field="radio_debut"}}</td>
      <td>{{mb_value object=$rpu field="radio_fin"}}</td>
      <td>{{mb_value object=$rpu field="bio_depart"}}</td>
      <td>{{mb_value object=$rpu field="bio_retour"}}</td>
    </tr>
  {{/foreach}}
</table>
