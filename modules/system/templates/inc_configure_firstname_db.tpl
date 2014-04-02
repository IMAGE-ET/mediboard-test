{{*
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2>Import de la base de données de correspondance Prénoms / sexe</h2>

<script>
  function updateCountsFirstName(start, count) {
    var form = getForm("do_firstnames");
    $V(form.elements.start, start);
    $V(form.elements.count, count);

    if ($V(form.elements.auto)) {
      form.onsubmit();
    }
  }
</script>

<table class="tbl">
  <tr>
    <th class="narrow">{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  <tr>
    <td style="vertical-align: top">
      <form method="post" name="do_firstnames" onsubmit="return onSubmitFormAjax(this, null, 'action_firstname_db')">
        <input type="hidden" name="m" value="system"/>
        <input type="hidden" name="dosql" value="do_import_firstnames"/>
        <input type="hidden" name="callback" value="updateCountsFirstName"/>
        <table class="form">
          <tr>
            <th>Début</th>
            <td><input type="text" name="start" value="{{$conf.system.import_firstname.start}}" style="width:5em;"/></td>
          </tr>
          <tr>
            <th>Nombre à traiter</th>
            <td><input type="text" name="step" value="{{$conf.system.import_firstname.step}}" style="width:5em;"/></td>
          </tr>
          <tr>
            <th>Automatique</th>
            <td><input type="checkbox" name="auto" /></td>
          </tr>
          <tr>
            <td colspan="2">
              <button class="tick">
                Importer la table de prénoms
              </button>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td id="action_firstname_db"></td>
  </tr>
</table>