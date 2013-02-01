{{*
  * list of tests for context
  *  
  * @category Context
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<table class="tbl" style="width:30%">
  <tr><th>Libelle</th><th>Action</th></tr>
  <tr><td>Vue non définie</td><td class="button"><a class="button" href="?m=context&a=call&dialog=1">TESTER</a></td></tr>
  <tr><td>Vue non existante</td><td class="button"><a class="button" href="?m=context&a=call&dialog=1&view=titi">TESTER</a></td></tr>
  <tr><td>Dossier patient par IPP</td>
    <td class="button">
      <form action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}"/>
        <input type="hidden" name="a" value="call"/>
        <input type="hidden" name="view" value="patient"/>
        <input type="text" placeholder="num. IPP" name="ipp" value=""/><br/>
        <input type="submit" value="TESTER"/>
      </form>
    </td>
  </tr>
  <tr><td>Dossier Patient (par recherche)</td>
    <td class="button">
      <form action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}"/>
        <input type="hidden" name="a" value="call"/>
        <input type="hidden" name="view" value="patient"/>
        <input type="text" placeholder="nom" name="name" value=""/><br/>
        <input type="text" placeholder="prenom" name="firstname" value=""/><br/>
        <input type="text" placeholder="date_naiss" name="birthdate" value=""/><br/>
        <input type="submit" value="TESTER"/>
      </form>
    </td>
  </tr>
  <tr><td>Dossier Patient (par recherche imprecise)</td><td class="button"><a class="button" href="?m=context&a=call&dialog=1&view=patient&name=test">TESTER</a></td></tr>
  <tr><td>Dossier Soins</td>
    <td class="button">
      <form action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}"/>
        <input type="hidden" name="a" value="call"/>
        <input type="hidden" name="view" value="soins"/>
        <input type="text" placeholder="num. NDA" name="nda" value=""/><br/>
        <input type="submit" value="TESTER"/>
      </form>
    </td>
  </tr>
  <tr>
    <td>Doss Soin (sans NDA)</td>
    <td class="button"><a class="button" href="?m=context&a=call&dialog=1&view=soins">TESTER</a></td></tr>
  <tr><td>Résultats Labo (Nda)</td>
    <td class="button">
      <form action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}"/>
        <input type="hidden" name="a" value="call"/>
        <input type="hidden" name="view" value="labo"/>
        <input type="text" placeholder="num. NDA" name="nda" value=""/><br/>
        <input type="submit" value="TESTER"/>
      </form>
    </td>
  </tr>
  <tr><td>Labo (sans NDA)</td>
    <td class="button">
      <a class="button" href="?m=context&a=call&dialog=1&view=labo">TESTER</a>
    </td>
  </tr>
</table>