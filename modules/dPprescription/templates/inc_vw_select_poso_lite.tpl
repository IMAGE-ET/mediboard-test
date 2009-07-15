{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var oFormTP = document.editLineTP;
   
addToTokenPoso = function(){
  var nb_checkbox = 0;
	$('checkboxPoso').select('input').each(function(e){
	  if(e.checked){
	    tokenPosologie.add(oFormTP.quantite.value+"_"+oFormTP.unite_prise.value+"_"+e.name, true);
	    nb_checkbox++;
	  }
	});
	// Aucune checkbox selectionnee
	if(!nb_checkbox){
	  tokenPosologie.add(oFormTP.quantite.value+"_"+oFormTP.unite_prise.value+"_", true);
	}
	$('checkboxPoso').select('input').each(function(e){
	  e.checked = false;
	  oFormTP.quantite.value = '1';
	});
}

Main.add(function () {
  updateTokenPoso = function(v){
  	if(v){
	    var i, codes = v.split("|");
		  for (i = 0; i < codes.length; i++) {
		  	codes_without_underscore = codes[i].gsub("_"," ");
		    codes[i] = '<button class="remove notext" type="button" onclick="tokenPosologie.remove(\''+codes[i]+'\')"></button> '+codes_without_underscore;
		  }
		  $("list_poso").update(codes.join("<br />"));
	  } else {
	    $("list_poso").update("");
	  }
  }
  
  // initialisation du tokenField  
  tokenPosologie = new TokenField(oFormTP.token_poso, { 
    onChange : updateTokenPoso
  });
});

</script>

<input type="hidden" name="token_poso" size="50" />
        
{{mb_field object=$prise field=quantite size=3 increment=1 min=1 form=editLineTP}}
  
<select name="unite_prise" style="width: 100px;">
{{foreach from=$unites_prise item=_prise}}
  <option value="{{$_prise}}">{{$_prise}}</option>
{{/foreach}}
</select>
<br />

<span id="checkboxPoso">
	<label><input type="checkbox" name="matin" /> Matin</label>
	<label><input type="checkbox" name="midi" /> Midi</label>
  <label><input type="checkbox" name="soir" /> Soir</label>
</span>

<button type="button" class="add notext" onclick="addToTokenPoso()">Ajouter</button>

<div id='list_poso'></div>