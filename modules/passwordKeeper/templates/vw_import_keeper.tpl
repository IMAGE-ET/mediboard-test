{{*
 * $Id$
 *  
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<form action="?m=passwordKeeper&amp;a=vw_import_keeper&amp;dialog=1" enctype="multipart/form-data" method="post">
  <input type="hidden" name="dosql" value="do_keeper_import" />
  <h3>{{tr}}XML-Import{{/tr}}</h3>

  <div style="text-align: center;">
    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
    <input type="file" name="datafile" size="40" accept="application/xml">
    <button type="submit" class="submit">{{tr}}Import{{/tr}}</button>
    <br />
    <label for="passphrase">Phrase de passe :</label><br />
    <input type="password" name="passphrase"/>
  </div>
</form>