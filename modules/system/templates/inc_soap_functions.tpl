{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  getEvenementName = function(button, func) {
	  var method = func.split(/[ \(]/i)[1];
	  $V(button.up('.source').down('form').evenement_name, method);
	}
</script>

<table class="main">
  <tr>
    <th colspan="2">
      Liste des fonctions disponibles
      <a href="{{$exchange_source->host}}" title="Accès direct">
        (accéder directement au serveur)
      </a>
    </th>
  </tr>
  {{foreach from=$functions item=_function}}
  <tr>
  	<td style="width:0.1%">
  		<button class="tick notext" onclick="getEvenementName($(this), '{{$_function}}')"></button>
	  </td>
    <td class="text">
      {{$_function}}
    </td>
  </tr>
  {{/foreach}}
</table>