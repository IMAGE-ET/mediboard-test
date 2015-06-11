{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    Grossesse.formFrom = getForm("bindFormGrossesse");
    // Après création d'une grossesse, si l'objet concerné n'est relié à aucune grossesse,
    // alors
    {{if $show_checkbox && !$object->grossesse_id && $grossesses|@count == 1}}
      Grossesse.formFrom.unique_grossesse_id.checked = true;
    {{/if}}
    Grossesse.editGrossesse($V(Grossesse.formFrom.unique_grossesse_id));
  });
</script>

<form name="bindFormGrossesse" method="get">
  <table class="tbl">
    <tr>
      <th colspan="4" class="category">
        Liste des grossesses
      </th>
    </tr>
    {{foreach from=$grossesses item=_grossesse}}
      <tr>
        <td class="narrow">
          {{if $show_checkbox}}
            <input type="radio" name="unique_grossesse_id"
                   data-active="{{$_grossesse->active}}"
                   data-view_grossesse="{{$_grossesse}}"
                   data-date="{{$_grossesse->terme_prevu}}"
                   {{if $_grossesse->_id == $object->grossesse_id}}checked{{/if}} value="{{$_grossesse->_id}}" />
          {{/if}}
        </td>
        <td>
          <a href="#1" onclick="Grossesse.editGrossesse('{{$_grossesse->_id}}')">{{$_grossesse}}</a>
        </td>
        <td class="compact">
          {{if $_grossesse->_count.sejours}}
            <div>
              {{$_grossesse->_count.sejours}} {{tr}}CGrossesse-back-sejours{{/tr}}
            </div>
          {{/if}}
          {{if $_grossesse->_count.consultations}}
            <div>
              {{$_grossesse->_count.consultations}} {{tr}}CGrossesse-back-consultations{{/tr}}
            </div>
          {{/if}}
          {{if $_grossesse->_count.naissances}}
            <div>
              {{$_grossesse->_count.naissances}} {{tr}}CGrossesse-back-naissances{{/tr}}
            </div>
          {{/if}}
        </td>

        {{if "forms"|module_active}}
          <td class="narrow">
            <button class="forms notext compact" type="button" {{if $_grossesse->_count.consultations == 0}}disabled{{/if}}
                    onclick="ExObject.loadExObjects('{{$_grossesse->_class}}', '{{$_grossesse->_id}}', 'edit_grossesse', 0.5)">
              Formulaires
              {{if $_grossesse->_count.consultations == 0}}
                (la grossesse doit être liée à une consultation pour accèder aux formulaires)
              {{/if}}
            </button>
          </td>
        {{/if}}
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="4">{{tr}}CGrossesse.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>