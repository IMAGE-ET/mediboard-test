{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>
            Liste des fonctions disponibles
            <a href="{{$exchange_source->host}}" title="Accès direct">
              (accéder directement au serveur)
            </a>
          </th>
        </tr>
        {{foreach from=$functions item=_function}}
        <tr>
          <td>
            {{$_function}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>