{{* $Id: vw_agenda.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision: 6228 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<script>
function buildUrl(form){
	if (!checkForm(form)){
		return false;
	}
	var url = '{{$url|smarty:nodefaults}}';
	url += '&'+ form.serialize().replace(/%5B%5D/g, '[]');
	prompt("Copier puis coller cette adresse dans votre gestionnaire d'agenda",url);
	return false;
} 	
</script>
<form name="creerurl" action="?m={{$m}}" method="get" onsubmit="return buildUrl(this);">
	<input type="hidden" name="username" value="{{$login}}"/>
  <input type="hidden" name="login" value="1"/>
  <input type="hidden" name="prat_id" value="{{$prat_id}}"/>
	<table class="main form">
		<col style="width:50%;"/>
	  <tr>
	    <th class="title" colspan="2" >Génération de lien agenda</th>
	  </tr>
	  
	  <tr>
	    <th>Votre login:</th>
			<td>{{$login}}</td>
		</tr>
	  
	  <tr>
	    <th><label  for="password" title="Votre mot de passe">Votre mot de passe</label></th>
			<td><input type="password" name="password" value="" class="password notNull"/></td>
		</tr>
		<tr>
	    <th>Vous souhaitez exporter l'agenda:</th>
			<td><input type="checkbox" name="export[]" value="interv" checked="checked"/>Interventions</td>
	  </tr>
	  <tr>
	  	<th></th>
	  	<td><input type="checkbox" name="export[]" value="consult"/>Consultations</td>
	  </tr>
	  <tr >
	  	<th><label for="weeks_before">Nombre de semaines avant la date actuelle</label></th>
	  	<td>
    			<select name="weeks_before">
           <option value="0">0</option>
           <option value="1" selected="selected">1</option>
           <option value="2">2</option>
          </select>
			</td>
	  </tr>
	  <tr>
	  	<th><label for="weeks_after">Nombre de semaines après la date actuelle</label></th>
	  	<td >
          <select name="weeks_after">
           <option value="0">0</option>
           <option value="1">1</option>
           <option value="2" selected="selected">2</option>
           <option value="3">3</option>
           <option value="4">4</option>
           <option value="5">5</option>
          </select>
      </td>
	  </tr>
	  <tr>
	  	<th>Souhaitez vous avoir les détails des taches?</th>
	  	<td><input type="radio" name="details" value="1" checked="checked"/> Oui <input type="radio" name="details" value="0" /> Non </td>
	  </tr>
		<tr>
			<th></th>
			<td><button type="submit" class="submit">Générer</button></td>
		</tr>
	</table>
</form>