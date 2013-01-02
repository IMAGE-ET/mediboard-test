{{*
  * Find candidates
  *
  * @category sip
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  findCandidates = function(form) {
    return Url.update(form, "find_candidates");
  }
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="find" action="?m=sip&a=ajax_find_candidates" method="post" onsubmit="return findCandidates(this)" class="prepared">
        <table class="form">
          <tr>
            <th class="category" colspan="4">Recherche d'un dossier patient sur le SIP</th>
          </tr>

          <tr>
            <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
            <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>

            <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
            <td><input tabindex="4" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
          </tr>

          <tr>
            <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
            <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>

            <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
            <td><input tabindex="5" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
          </tr>

          <tr>
            <th><label for="nom_jeune_fille" title="Nom de naissance">Nom de naissance</label></th>
            <td><input tabindex="3" type="text" name="nom_jeune_fille" value="{{$nom_jeune_fille|stripslashes}}" /></td>

            <td colspan="2" />
          </tr>

          <tr>
            <th colspan="2">
              <label for="Date_Day" title="Date de naissance du patient à rechercher">
                Date de naissance
              </label>
            </th>
            <td colspan="2">
            {{mb_include module=patients template=inc_select_date date="--" tabindex=7}}
            </td>
          </tr>

          <tr>
            <td class="button" colspan="4">
              <button class="search singleclick"> {{tr}}Search{{/tr}} </button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td id="find_candidates">

    </td>
  </tr>
</table>