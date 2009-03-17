<script type="text/javascript">

function addFtpParams() {
  oForm = document.paramsFtp;
  url = new Url();
  url = new Url();
  url.setModuleAction("dPinterop", "do_test_ftp");
  url.addElement(oForm.hostname);
  url.addElement(oForm.username);
  url.addElement(oForm.userpass);
  url.addElement(oForm.port);
  url.addElement(oForm.timeout);
  url.addElement(oForm.passif_mode);
  return url;
}

function testSocket() {
  url = addFtpParams();
  url.addParam("testType", "socket");
  url.requestUpdate("test_socket");
}

function testFtp() {
  addFtpParams(url);
  url.addParam("testType", "ftp");
  url.requestUpdate("test_ftp");
}

</script>

<form name="paramsFtp">
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Paramètres
    </th>
  </tr>
  <tr>
    <th><label for="hostname">hostname</label></th>
    <td><input name="hostname" type="text" value="hostname" /></td>
  </tr>
  <tr>
    <th><label for="username">username</label></th>
    <td><input name="username" type="text" value="username" /></td>
  </tr>
  <tr>
    <th><label for="userpass">userpass</label></th>
    <td><input name="userpass" type="text" value="userpass" /></td>
  </tr>
  <tr>
    <th><label for="port">port</label></th>
    <td><input name="port" type="text" value="21" /></td>
  </tr>
  <tr>
    <th><label for="timeout">timeout</label></th>
    <td><input name="timeout" type="text" value="90" /></td>
  </tr>
  <tr>
    <th>Mode</th>
    <td>
      <input name="passif_mode" type="radio" value="false" checked="checked" /> actif
      <br />
      <input name="passif_mode" type="radio" value="true" /> passif
    </td>
  </tr>
</table>
</form>

<table class="tbl">
  <tr>
    <th>Fonction / paramètres</th>
    <th>Résultat</th>
  </tr>
  <tr>
    <td>
      <button type="button" onclick="testSocket()">Test socket</button>
    </td>
    <td>
      <div id="test_socket">
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <button type="button" onclick="testFtp()">Test ftp</button>
    </td>
    <td>
      <div id="test_ftp">
      </div>
    </td>
  </tr>
</table>