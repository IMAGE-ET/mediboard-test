{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$notes item="curr_note" name="notes"}}
  <table class="note">
    <tr>
      <th class="info {{$curr_note->degre}}">
        <span style="float: right;">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_note->_ref_user initials=true}}
        </span>
        
        <label title="{{$curr_note->date|date_format:$dPconfig.datetime}}">
          {{$curr_note->_date_relative.count}} {{tr}}{{$curr_note->_date_relative.unit}}{{if $curr_note->_date_relative.count > 1}}s{{/if}}{{/tr}}
        </label>
      </th>
    </tr>
    <tr>
      <td class="text">
        {{if $user == $curr_note->user_id}}
          <form name="delNoteFrm{{$curr_note}}" action="" method="post">
            <input type="hidden" name="m" value="system" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_note_aed" />
            {{mb_key object=$curr_note}}
            
            <button style="float: right;" class="cancel notext" type="button" onclick="confirmDeletion(this.form, {typeName:'cette note',ajax:1,target:'systemMsg'},{onComplete: function(){initNotes(true)} })"></button>
          </form>
        {{/if}}
        <strong>{{$curr_note->libelle}}</strong>
        <br />
        {{$curr_note->text|nl2br}}
      </td>
    </tr>
  </table>
  
  {{if !$smarty.foreach.notes.last}}
    <hr />
  {{/if}}
{{foreachelse}}
  Pas de note visible
{{/foreach}}