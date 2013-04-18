{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

{{if $revealed}}
    <script>
      prompt("Votre mot de passe :", "{{$revealed}}");
    </script>
{{/if}}