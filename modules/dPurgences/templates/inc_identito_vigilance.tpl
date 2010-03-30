{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
highlite = function(check) {
  // Hide and empty all inputs
  $$("input[type=radio]").each(function(e) {
    e.checked = false;
    e.setVisibility(false);
  } );

  // Uncheck and unselect all other inputs
  var checked = check.checked;
  $$("input[type=checkbox]").each(function(e) {
    e.checked = false;
    e.setVisibility(!checked);
  } )
  check.setVisibility(true);
	if (checked) {
	  check.checked = true;
	}

  // Show all possible radios
  var object_class = check.name.split('-')[0];
  var tbody = $(check).up(2);
  var inputFirst  = "input[name="+object_class+"-first]";
  var inputSecond = "input[name="+object_class+"-second]";

  if (checked) {
	  if (object_class == "CPatient") {
	    $$(inputSecond).each(function (e) {
	      e.setVisibility(!e.descendantOf(tbody));
	    } );
	  }
		else {
      var tr = $(check).up(1);
      $$(inputSecond).each(function (e) {
        e.setVisibility(e.descendantOf(tbody) && !e.descendantOf(tr));
      } );
		}
	}

  // Handle highlight
	$$(".merge-selected").each(function (e) {
    e.removeClassName("merge-selected")
	} )
	
	if (check.checked) {
    tbody.addClassName("merge-selected");
	}
}

merge = function(radio) {
  var object_class = radio.name.split('-')[0];
  Console.debug(object_class, "Object class");
  var first_id  = $V(document.Merger[object_class+"-first"])[0];
  var second_id = radio.value;
	url = new Url("system", "object_merger") .
	  addParam("objects_class", object_class) .
    addParam("objects_id", [first_id, second_id].join('-'));
	url.popup(900, 700);
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
	<tbody class="hoverable" class="CPatient">
  {{assign var=count_sejour value=$_patient->_ref_sejours|@count}}
  <tr>
    <td rowspan="{{$count_sejour}}">
      <input name="{{$_patient->_class_name}}-first" type="checkbox" value="{{$_patient->_id}}" onclick="highlite(this);" />
      <input name="{{$_patient->_class_name}}-second" type="radio" value="{{$_patient->_id}}" style="visibility: hidden;" onclick="merge(this);"/>
      <big onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">{{$_patient}}</big>
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
		
    {{foreach from=$_patient->_ref_sejours item=_sejour name=sejour}}
       
	  {{assign var=rpu value=$_sejour->_ref_rpu}}

    <td id="{{$_sejour->_guid}}">
      {{if $count_sejour > 1}} 
      <input name="{{$_sejour->_class_name}}-first" type="checkbox" value="{{$_sejour->_id}}" onclick="highlite(this);" />
      <input name="{{$_sejour->_class_name}}-second" type="radio" value="{{$_sejour->_id}}" style="visibility: hidden;" onclick="merge(this);"/>
      {{/if}}
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
