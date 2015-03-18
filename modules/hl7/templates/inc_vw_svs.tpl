{{*
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="find_value_set" action="?" method="post" onsubmit="return TestHL7.findValueSet(this)">
  <input type="hidden" name="value_set_type" value="" />

  <table class="form">
    <tr>
      <th class="category">Recherche d'un value set</th>
    </tr>

    <tr>
      <td>
        <fieldset>
          <legend>Retrieve value set (ITI-48)</legend>

          <table class="form">
            <tr>
              <th> <label class="notNull">OID</label></th>
              <td> <input tabindex="10" type="text" name="OID" value="" size="25" class="notNull"/> </td>
            </tr>

            <tr>
              <th>Version</th>
              <td> <input tabindex="20" type="text" name="version" value="" size="25" /> </td>
            </tr>

            <tr>
              <th>Langue</th>
              <td> <input tabindex="30" type="text" name="language" value="" size="25" placeholder="fr-FR" /> </td>
            </tr>
          </table>

        </fieldset>
      </td>
    </tr>

    <tr>
      <td class="button">
        <button class="search singleclick" onclick="$V(this.form.value_set_type, 'RetrieveValueSet')">
          {{tr}}Search{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>

<div id="search_value_set"></div>