{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $praticiens|@count > 1 || !$user->_is_praticien}}
  <form name="ChoixPraticien" method="get" action="?">
    <input type="hidden" name="m" value="{{$m}}"/>
    <input type="hidden" name="tab" value="{{$tab}}" />
    <label for="praticien_id" title="Praticien pour lequel on affiche les statistiques">Praticien</label>
    <select name="praticien_id" onchange="this.form.submit()">
    <option value="">&mdash; Choix d'un praticien</option>
    {{mb_include module=mediusers template=inc_options_mediuser selected=$prat->_id list=$praticiens}}
    </select>
  </form>


  {{if !$prat->_id}}
    <div class="small-warning">
      Les vues du tableau de bord sont spécifiques à chaque praticien.
      <br />Merci d'en <strong>sélectionner</strong> un dans la liste ci-dessus.
    </div>
  {{/if}}
{{/if}}
