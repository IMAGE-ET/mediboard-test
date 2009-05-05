{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="ChoixStat" method="post" action="#">
  <label for="stat" title="Statistiques à afficher">Statistiques</label>
  <select name="stat" onchange="this.form.submit()">
  {{foreach from=$stats item=_stat}}
    <option value="{{$_stat}}" {{if $_stat == $stat}}selected="selected"{{/if}}> 
    	{{tr}}mod-dPboard-tab-{{$_stat}}{{/tr}}
    </option>
  {{/foreach}}
  </select>
</form>

{{if !$stat}}
<div class="big-info">
  Plusieurs statistiques sont disponibles pour le praticien.
  <br />Merci d'en <strong>sélectionner</strong> une dans la liste ci-dessus.
</div>
{{/if}}
