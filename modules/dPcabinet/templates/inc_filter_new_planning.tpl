{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>Afficher les plages libres</th>
    <td>
      <select name="show_free">
        <option value="1" selected="selected">Oui</option>
        <option value="0">Non</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Afficher les plages annulées</th>
    <td>
      <select name="cancelled">
        <option value="1">Oui</option>
        <option value="0" selected="selected">Non</option>
      </select>
    </td>
  </tr>


  <tr>
    <th>Statut des consultations facturées</th>
    <td>
      <select name="facturated">
        <option value="">Tout voir</option>
        <option value="1">Seulement les facturés</option>
        <option value="0">Seulement les non facturés</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Statut des rdv</th>
    <td>
      <select name="finished">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        <option value="16">Planifié</option>
        <option value="32">Patient arrivé</option>
        <option value="48">En cours</option>
        <option value="64">Terminé</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Actes {{if $conf.ref_pays == 2}}tarmed{{/if}}</th>
    <td>
      <select name="actes">
        <option value="">&mdash; Tous</option>
        <option value="1">Seulement les cotés</option>
        <option value="0">Seulement les non cotés</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Affichage en fonction des congés</th>
    <td>
      <select name="hide_in_conge">
        <option value="0">Tout afficher</option>
        <option value="1">Cacher tout si congés</option>
      </select>
    </td>
  </tr>

  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="Control.Modal.close();">{{tr}}OK{{/tr}}</button>

    </td>
  </tr>
</table>
