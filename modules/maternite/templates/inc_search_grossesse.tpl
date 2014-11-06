{{*
 * $Id$
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    var oform = getForm('search_grossesse_modal');
    Calendar.regField(oform.terme_date);
    Calendar.regField(oform.terme_start);
    Calendar.regField(oform.terme_end);

    {{if $lastname}}
      oform.onsubmit();
    {{/if}}
  });
</script>

<form method="get" name="search_grossesse_modal" onsubmit="return onSubmitFormAjax(this, null, 'result_search_grossesse')">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="a" value="ajax_search_grossesse" />

  <fieldset style="display: inline-block;">
    <legend>{{mb_title class=CGrossesse field=parturiente_id}}</legend>
    <label>Nom <input type="text" name="lastname" value="{{$lastname}}"/> </label>
    <label>Prénom <input type="text" name="firstname" /> </label>
  </fieldset>

  <fieldset style="display: inline-block;">
    <legend>{{mb_title class=CGrossesse field=terme_prevu}}</legend>
    <label>
      Exactement : <input type="text" name="terme_date" style="display: none;" />
       ou
      Après <input type="text" name="terme_start" style="display: none;" /> , Avant <input type="text" name="terme_end" style="display: none;" />
    </label>
  </fieldset>

  <fieldset style="display: inline-block;">
    <legend>Autres options</legend>
    <label>
      {{mb_title class=CGrossesse field=multiple}}
      <select name="multiple">
        <option value="">&mdash;</option>
        <option value="0">Non</option>
        <option value="1">Oui</option>
      </select>
    </label>

    <label>
      {{mb_title class=CGrossesse field=fausse_couche}}
      <select name="fausse_couche">
        <option value="">&mdash;</option>
        <option value="0">Non</option>
        <option value="1">Oui</option>
      </select>
    </label>
  </fieldset>

  <p style="text-align: center;"><button class="search">{{tr}}Search{{/tr}}</button></p>
</form>

<table class="tbl">
  <thead>
    <tr>
      <th>{{mb_title class=CGrossesse field=parturiente_id}}</th>
      <th>{{mb_title class=CGrossesse field=terme_prevu}}</th>
      <th class="narrow">{{mb_title class=CGrossesse field=multiple}}</th>
      <th>{{mb_title class=CGrossesse field=fausse_couche}}</th>
      <th class="narrow">action</th>
    </tr>
  </thead>
  <tbody id="result_search_grossesse">
    <tr>
      <td colspan="5">Effectuer une recherche avec les critères ci-dessus</td>
    </tr>
  </tbody>
</table>