{{*
 * $Id$
 *  
 * @category Outils
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  (function() {
    document.location.href =
      document.location.origin   +
      document.location.pathname +
      "?m={{$m}}&tab={{$tab}}&session_id={{$session_id}}&timeout={{$timeout}}";
  }).delay('{{$timeout}}');
</script>

<div class="small-info">
  Pour réduire ou augmenter le délai de rafraîchissement, ajoutez le paramètre dans l'url <strong>timeout=[nb de secondes]</strong>. Par défaut, le délai est de 30 secs.
</div>

<h2>Cookie de session : {{$session_id}}</h2>
<h2>IP du serveur : {{$ip_server}}</h2>