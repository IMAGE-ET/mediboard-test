{{*
 * test
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="3">Synthèse</th>
  </tr>
  <tr>
    <th>
      Total des tests
    </th>
    <th>
      Nombre de succès
    </th>
    <th>
      Classe en erreur
    </th>
  </tr>
  <tr>
    <td>
      {{$resultsynth.total}}
    </td>
    <td>
      {{$resultsynth.succes}}
    </td>
    <td>
      {{foreach from=$resultsynth.erreur item=_classerror}}
        <a href="#{{$_classerror}}">{{$_classerror}}</a>
      {{/foreach}}
    </td>
  </tr>
</table>
<br/>
<br/>

{{mb_include template="inc_testdatatype"}}