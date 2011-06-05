{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$notes item="_note" name="notes"}}
  <table class="note">
    <tr>
      <th class="info {{$_note->degre}}">
        <span style="float: right;">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_note->_ref_user initials=block}}
        </span>
        
        <label title="{{$_note->date|date_format:$conf.datetime}}">
          {{$_note->_date_relative.count}} 
          {{tr}}{{$_note->_date_relative.unit}}{{if $_note->_date_relative.count > 1}}s{{/if}}{{/tr}}
        </label>
      </th>
    </tr>
    <tr>
      <td class="text">
        {{if !$_note->user_id || $user == $_note->user_id}}
          <form name="Del-{{$_note->_guid}}" action="" method="post">
            <input type="hidden" name="m" value="system" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_note_aed" />
            {{mb_key object=$_note}}
            
            <button style="float: right;" class="cancel notext" type="button" 
              onclick="confirmDeletion(this.form, { typeName: 'cette note', ajax: 1 }, { onComplete: Note.refresh.curry(true, '{{$object->_guid}}') })">
              {{tr}}Delete{{/tr}}
            </button>
          </form>
        {{/if}}
        <strong>{{$_note->libelle}}</strong>
        <br />
        {{$_note->text|nl2br}}
      </td>
    </tr>
  </table>
  
  {{if !$smarty.foreach.notes.last}}
    <hr />
  {{/if}}
{{foreachelse}}
  Pas de note visible
{{/foreach}}