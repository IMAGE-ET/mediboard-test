{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="Insc_test_saisie" method="post" onsubmit="return Ccda.submitSaisieInsc(this);">
  <table class="form">
    <tr>
      <th class="title" colspan="4">{{tr}}Informations carte{{/tr}}</th>
    </tr>
      <tr>
        <th>{{tr}}firstName{{/tr}}</th>
        <td><input name="firstName" type="input" value="{{$firstName}}"></td>
      </tr>
      <tr>
        <th>{{tr}}birthDate{{/tr}}</th>
        <td><input name="birthDate" type="input" value="{{$birthDate}}"></td>
      </tr>
      <tr>
        <th>{{tr}}nir{{/tr}}</th>
        <td><input name="nir" type="input" value="{{$nir}}"></td>
      </tr>
      <tr>
        <th>{{tr}}nirKey{{/tr}}</th>
        <td><input name="nirKey" type="input" value="{{$nirKey}}"></td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          <button type="submit" class="submit">{{tr}}Send{{/tr}}</button>
        </td>
      </tr>
      {{if $insc}}
        <tr>
          <th>{{tr}}insc{{/tr}}</th>
          <td><input name="insc" type="input" value="{{$insc}}" readonly="true"></td>
        </tr>
      {{/if}}
  </table>
</form>

