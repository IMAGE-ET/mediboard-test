{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
highlite = function(radio) {
  var div = $(radio).up();

  $$("div.merge-selected").each(function(e) {
	  e.removeClassName("merge-selected");
  } );

	if (radio.checked) {
    div.addClassName("merge-selected");
		$$("input[name=CPatient-second]").each(function (e) {
		  e.enable();
			e.checked = false;
	  } );
    div.select("input[name=CPatient-second]").each(Form.Element.disable)
	}
}

merge = function(radio) {
  var first_id = $V(document.Merger["CPatient-first"]);
  var second_id = $V(document.Merger["CPatient-second"]);
  Console.debug(first_id, "First Patient");
  Console.debug(second_id, "Second Patient");
  alert("Merging ");
}
	
</script>

<form name="Merger">
	
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

  {{foreach from=$sejours item=_sejour}}
  {{assign var=rpu value=$_sejour->_ref_rpu}}
  {{assign var=patient value=$_sejour->_ref_patient}}

  <tr>
    <td>
    	<div class="{{$patient->_guid}}" style="margin: 2 4px;">
        <input name="CPatient-first" type="radio" value="{{$patient->_id}}" onclick="highlite(this);" />
        <input name="CPatient-second" type="radio" value="{{$patient->_id}}" disabled="disabled" onclick="merge(this);"/>
	      <big onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</big>
      </div>
    </td>
    <td style="text-align: center">
      <strong>{{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}</strong>
    </td>
    <td>
      <big>{{mb_value object=$patient field=naissance}}</big> 
		</td>
    <td>
      {{mb_value object=$patient field=_age}} ans
    </td>
    <td>
    	{{mb_value object=$patient field=adresse}}
      {{mb_value object=$patient field=cp}}
      {{mb_value object=$patient field=ville}}
		</td>

    <td class="{{$_sejour->_guid}}">
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


    <td class="{{$rpu->_guid}}">
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
  </tr>

  {{/foreach}}
</table>

</form>
