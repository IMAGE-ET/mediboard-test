{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
IdentitoVigilance.guesses = {{$guesses|@json}};
Main.add(function() {
  var tab = $$("a[href=#identito_vigilance]")[0];
	tab.down("small").update("({{$mergeables_count}})");
	{{if $mergeables_count}}
	tab.addClassName("wrong");
	{{/if}}
})
</script>
	
<form name="Merger" action="?">

<table class="tbl">
  <tr>
    <th colspan="6" class="title">{{tr}}CPatient{{/tr}}</th>
    <th colspan="3" class="title">{{tr}}CSejour{{/tr}}</th>
    <th rowspan="2" class="title">RPU</th>
  </tr>

  <tr>
    <th colspan="2">{{mb_label class=CPatient field=nom}}</th>
    <th>{{mb_title class=CPatient field=_IPP}}</th>
    <th>{{mb_title class=CPatient field=naissance}}</th>
    <th>{{mb_title class=CPatient field=_age}}</th>
    <th>{{mb_label class=CPatient field=adresse}}</th>
    <th colspan="2">{{mb_label class=CSejour field=_entree}}</th>
    <th>{{mb_title class=CSejour field=_num_dossier}}</th>
  </tr>

  {{foreach from=$patients item=_patient}}
	{{assign var=patient_id value=$_patient->_id}}
  {{assign var=phonings value=$guesses.$patient_id.phonings}}
  {{assign var=siblings value=$guesses.$patient_id.siblings}}
  {{assign var=mergeable value=$guesses.$patient_id.mergeable}}
	
	{{if $mergeable}}
	<tbody class="hoverable" class="CPatient">

    {{foreach from=$_patient->_ref_sejours item=_sejour name=sejour}}
    {{assign var=count_sejour value=$_patient->_ref_sejours|@count}}
    <tr>

    {{if $smarty.foreach.sejour.first}} 
	    <td rowspan="{{$count_sejour}}" style="width: 1%;">
	      <input name="{{$_patient->_class_name}}-first" type="checkbox" value="{{$_patient->_id}}" onclick="IdentitoVigilance.highlite(this);" />
	      <input name="{{$_patient->_class_name}}-second" type="radio" value="{{$_patient->_id}}" style="visibility: hidden;" onclick="IdentitoVigilance.merge(this);"/>
	    </td>
	    <td rowspan="{{$count_sejour}}">
	      <div class="text" id="{{$_patient->_guid}}">
	        <strong><big onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">{{$_patient}}</big></strong>
	      </div>
	    </td>
	    <td rowspan="{{$count_sejour}}" style="text-align: center">
	      <strong>{{mb_include module=dPpatients template=inc_vw_ipp ipp=$_patient->_IPP}}</strong>
	    </td>
	    <td rowspan="{{$count_sejour}}">
	      <big>{{mb_value object=$_patient field=naissance}}</big> 
	    </td>
	    <td rowspan="{{$count_sejour}}">
	      {{mb_value object=$_patient field=_age}} ans
	    </td>
	    <td rowspan="{{$count_sejour}}">
	      {{mb_value object=$_patient field=adresse}}
	      {{mb_value object=$_patient field=cp}}
	      {{mb_value object=$_patient field=ville}}
	    </td>
    {{/if}}
       
    <td style="width: 1%;">
      {{if $count_sejour > 1}}
      <input name="{{$_sejour->_class_name}}-first" type="checkbox" value="{{$_sejour->_id}}" onclick="IdentitoVigilance.highlite(this);" />
      <input name="{{$_sejour->_class_name}}-second" type="radio" value="{{$_sejour->_id}}" style="visibility: hidden;" onclick="IdentitoVigilance.merge(this);"/>
      {{/if}}
    </td>
    <td id="{{$_sejour->_guid}}">
    	<big onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
    		{{mb_value object=$_sejour field=_entree date=$date}}
			</big>
		</td>
    <td>
    	{{if !$_sejour->_num_dossier}} 
       <div class="warning">
          {{tr}}None{{/tr}}
       </div>
    	{{else}}
      <strong>{{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}</strong>
    	{{/if}}
		</td>

    <td>
	    {{foreach from=$_sejour->_back.rpu key=rpu_id item=_rpu}}
	    <div>
	      {{if count($_sejour->_back.rpu) > 1}}
	      <input name="{{$_rpu->_class_name}}-first" type="checkbox" value="{{$_rpu->_id}}" onclick="IdentitoVigilance.highlite(this);" />
	      <input name="{{$_rpu->_class_name}}-second" type="radio" value="{{$_rpu->_id}}" style="visibility: hidden;" onclick="IdentitoVigilance.merge(this);"/>
	      {{/if}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_rpu->_guid}}')">
          {{tr}}CRPU-msg-create{{/tr}}
        </span>
	    </div>
	    {{foreachelse}}
      <div class="warning">
        {{tr}}CRPU-msg-absent{{/tr}}
      </div>
	    {{/foreach}}
    </td>
       
    {{/foreach}}
  </tbody>
  {{/if}}

  {{/foreach}}
	

</table>

</form>
