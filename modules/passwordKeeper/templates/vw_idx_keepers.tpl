{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

{{mb_script module="passwordKeeper" script="keeper"}}

<script type="text/javascript">
  Main.add(function() {
    Keeper.showListKeeper();
  })
</script>

<table class="main">
  <tr>
    <td>
      <button type="button" class="new" onclick="Keeper.showKeeper('0')">{{tr}}CPasswordKeeper-title-create{{/tr}}</button>
      <button type="button" class="hslip" onclick="Keeper.popupImport()">{{tr}}CPasswordKeeper-import{{/tr}}</button>
    </td>
  </tr>
  <tr>
    <td style="width: 30%" id="vw_list_keeper">
    </td>
    <td id="vw_edit_keeper">
      &nbsp;
    </td>
  </tr>
</table>

<div id="modalPassphrase" class="modal" style="display: none">
  <form name="passphrase" action="?m={{$m}}" method="post" onsubmit="return Keeper.getPassphrase(this, $V(this.password_keeper_id), $V(this.deletion), $V(this.passphraseInputExport))">
    <input type="hidden" name="password_keeper_id" />
    <input type="hidden" name="deletion" />
    <table class="tbl">
      <tr>
        <td><label for="passphraseInput">{{tr}}Passphrase{{/tr}} :</label></td>
        <td><input type="password" name="passphraseInput" /></td>
      </tr>
      <tbody id="passphrase2" style="display: none">
        <tr>
          <td><label for="passphraseInputExport">{{tr}}PassphraseExport{{/tr}} :</label></td>
          <td><input type="password" name="passphraseInputExport" /></td>
        </tr>
      </tbody>
      <tr>
        <td class="button" colspan="2">
          <button class="cancel" type="button" onclick="$V(form.passphraseInput, ''); Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
          <button class="tick" type="submit">{{tr}}Validate{{/tr}}</button>
        </td>
      </tr>
    </table>
   </form>
</div>