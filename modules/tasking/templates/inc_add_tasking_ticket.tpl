{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main tbl">
  <tr>
    <td class="narrow">
      <img src="style/mediboard/images/icons/help.png" alt="{{tr}}Help{{/tr}}" onclick="Tasking.showHelp()"/>
    </td>
    <td>
      <form name="add-task" action="?" method="post" onsubmit="return Tasking.smartAddTaskingTicket(this);" >
        <input type="hidden" name="list_id" value="" />
        <input type="hidden" name="task_action" value="add" />
        <table class="form">
          <tr>
            <td>
              <button type="button" class="add notext" onclick="this.form.elements.task_multiple.up('tr').toggle()">{{tr}}Multiple{{/tr}}</button>
            </td>
            <td style="width: 100%;">
              <input type="text" id="task_smart" name="task_smart" style="width: 100%;" />
            </td>
            <td>
              <button type="submit" class="tick notext">{{tr}}Validate{{/tr}}</button>
            </td>
          </tr>
          <tr style="display: none;">
            <td colspan="3">
              <textarea name="task_multiple" rows="6" style="width: 100%" /></textarea>
              <div class="big-info">
                <strong>Nouveaux raccourcis pour les tâches multiples</strong>
                <br />
                <ul>
                  <li>Parenthèses ou crochets autorisés : (raccourci) ou [raccourci]</li>
                  <li>Insensible à la casse : (P1) ou (p1)</li>
                  <li><code>OK</code> : Tâche réalisée </li>
                  <li><code>nH</code> : Tâche évaluée à 'n' heures</li>
                  <li><code>Pn</code> : Tâche de priorité 'n'. Si 'n' > 3, pas de priorité</li>
                  <li><code>--</code> : Sans OK, tâche abandonnée : tag:cancelled, et réalisée; avec OK tâche à 0h</li>
                  <li><code>??</code> : Tâche à définir : tag:definir, et priorité !3</li>
                  <li><em>Prénom</em> : Tâche assignée au Mediuser correspondant</li>
                  <li><em>Autre chaîne</em> : Tâche avec une note 'Autre chaîne'</li>
                  <li><em>Chaîne vide</em> : Ne rien faire</li>
                </ul>
              </div>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
