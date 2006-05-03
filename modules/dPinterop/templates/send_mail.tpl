<table class="main">
  <tr>
    <td class="halfPane">
      <form name="mailFrm" action="?m={$m}" method="post">
        <input type="hidden" name="m" value="{$m}" />
        <input type="hidden" name="dosql" value="do_send" />
        <table class="form">
          <tr>
            <th class="title" colspan="2">Envoie d'emails</th>
          <tr>
            <th class="category" colspan="2">Headers</th>
          </tr>
          <tr>
            <th>From:</th>
            <td class="greedyPane"><input type="text" name="from" value="rhum1@mbservertest.local" /></td>
          </tr>
          <tr>
            <th>To:</th>
            <td><input type="text" name="to" value="ollivier@openxtrem.com" /></td>
          </tr>
          <tr>
            <th>Subject:</th>
            <td><input type="text" name="subject" value="test" /></td>
          </tr>
          <tr>
            <th class="category" colspan="2">Message</th>
          </tr>
          <tr>
            <td colspan="2" style="height: 300px">
              <textarea id="htmlarea" name="body">Test</textarea>
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2"><button type="submit">Send</button></td>
          </tr>
        </table>
      </form>
    </td>
    <td class="halfPane">
      <table>
        <tr>
          <th class="title">Reception d'emails</th>
        </tr>
      </table>
    </td>
  </tr>
</table>