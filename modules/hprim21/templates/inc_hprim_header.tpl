{{*
  * header of hprim medecin
  *  
  * @category Hprim21
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<table class="main form">
  <tr>
    <th colspan="4" class="title">{{$header.1}} {{$header.2}} &ndash; {{$header.6}} [{{$header.8}}]</th>
  </tr>
  <tr>
    <th>Expéditeur</th>
    <td><small>[{{$header.10.0}}]</small> {{$header.10.1}}</td>

    <th>Destinataire</th>
    <td><small>[{{$header.11.0}}]</small> {{$header.11.1}}</td>
  </tr>
  <tr>
    <td colspan="4"><hr /></td>
  </tr>
  <tr>
    <th>Adresse</th>
    <td>
    {{$header.3}}<br />
    {{$header.4}}<br />
    {{$header.5}}
    </td>

    <th>Numéro de sécurité sociale</th>
    <td>{{$header.7}}</td>
  </tr>
</table>