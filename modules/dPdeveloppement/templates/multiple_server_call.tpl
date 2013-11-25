{{*
 * $Id$
 *  
 * @category dPdeveloppement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  function sendRequest(form) {
    new  Url("dPdeveloppement", "ajax_multiple_server_call")
      .addFormData(form)
      .requestUpdate("resultSend");

    return false;
  }
</script>

<form method="post" onsubmit="return sendRequest(this)">
  <table class="form">
    <tr>
      <th colspan="2" class="title">
        Paramètre de la requête à exécuter
      </th>
    </tr>
    <tr>
      <th class="section" style="text-align: center"><label for="getArea">GET</label></th>
      <th class="section" style="text-align: center"><label for="postArea">POST</label></th>
    </tr>
    <tr>
      <td>
        <textarea id="getArea" name="getArea">{{$get}}</textarea>
      </td>
      <td>
        <textarea id="postArea" name="postArea">{{$post}}</textarea>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="submit" class="send">{{tr}}Send{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<br/>
<div id="resultSend"></div>