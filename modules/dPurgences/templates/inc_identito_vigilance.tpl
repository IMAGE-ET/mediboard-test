{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
highlite = function(radio) {
  var first_id = $V(document.Merger["CPatient-first"]);
	var tbody = $("CPatient-" + first_id);
  var inputFirst = "input[name=CPatient-first]";
  var inputSecond = "input[name=CPatient-second]";

  $$(inputFirst).each(function (e) {
    e.setVisibility(!radio.checked || e.descendantOf(tbody));
  } );

  $$(inputSecond).each(function (e) {
    e.setVisibility(radio.checked && !e.descendantOf(tbody));
  } );

  $$(".merge-selected").each(function(e) {
    e.removeClassName("merge-selected");
  } );

	if (radio.checked) {
    tbody.addClassName("merge-selected");
	}
}

merge = function(radio) {
  var first_id = $V(document.Merger["CPatient-first"])[0];
  var second_id = $V(document.Merger["CPatient-second"]);
  Console.debug(first_id, "First Patient");
  Console.debug(second_id, "Second Patient");
	
}
	
</script>

<form name="Merger" action="?">
	
<table class="tbl">
  <tr>
    <th colspan="5" class="title">{{tr}}CPatient{{/tr}}</th>
    <th colspan="2" class="title">{{tr}}CSejour{{/tr}}</th>
    <th rowspan="2" class="title">{{tr}}CRPU{{/tr}}</th>
  </tr>

  <tr>
    <th>{{mb_label class=CPatient field=nom}}</th>
    <th>{{mb_title class=CPatient field=_IPP}}</th>
    <th>{{mb_title class=CPatient field=naissance}}</th>
    <th>{{mb_title class=CPatient field=_age}}</th>
    <th>{{mb_label class=CPatient field=adresse}}</th>
    <th>{{mb_label class=CSejour field=_entree}}</th>
    <th>{{mb_title class=CSejour field=_num_dossier}}</th>
  </tr>

  {{foreach from=$patients item=_patient}}
	<tbody class="hoverable" id="{{$_patient->_guid}}">
  {{assign var=count_sejour value=$_patient->_ref_sejours|@count}}
  <tr>
    <td rowspan="{{$count_sejour}}">
      <input name="CPatient-first" type="checkbox" value="{{$_patient->_id}}" onclick="highlite(this);" />
      <input name="CPatient-second" type="radio" value="{{$_patient->_id}}" style="visibility: hidden;" onclick="merge(this);"/>
      <big onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">{{$_patient}}</big>
    </td>
    <td rowspan="{{$count_sejour}}" style="text-align: center">
      <strong>{{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}</strong>
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
		
    {{foreach from=$_patient->_ref_sejours item=_sejour name=sejour}}
       
	  {{assign var=rpu value=$_sejour->_ref_rpu}}

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


    <td {{if $rpu->_id}}id="{{$rpu->_guid}}"{{/if}}>
    	{{if $rpu->_id}} 
	      <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}')">
	        {{tr}}CRPU-msg-create{{/tr}}
	      </span>
    	{{else}}
        <div class="warning">
          {{tr}}CRPU-msg-absent{{/tr}}
       </div>
    	{{/if}}
    </td>

    {{if !$smarty.foreach.sejour.last}} 
    </tr><tr>       
    {{/if}}
		
    {{/foreach}}
  </tr>

  {{/foreach}}
	
  </tbody>

</table>

</form>
