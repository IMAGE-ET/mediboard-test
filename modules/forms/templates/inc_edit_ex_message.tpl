{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  getForm("editMessage").elements.title.select();
});
</script>

<div class="small-info">
	Les Titres/Textes sont des zones d'information à placer sur la grille (Disposition du formulaire).<br />
	<strong>Le libellé n'a pas nécessairement besoin d'être placé sur la grille</strong>.
</div>

<form name="editMessage" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_message->_ref_ex_group->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_message_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_message}}
  {{mb_field object=$ex_message field=ex_group_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_message colspan="4"}}
    
    <tr>
      <th>{{mb_label object=$ex_message field=title}}</th>
      <td>{{mb_field object=$ex_message field=title}}</td>
			
      <th>{{mb_label object=$ex_message field=type}}</th>
      <td>{{mb_field object=$ex_message field=type emptyLabel="Normal" onchange="\$('text-preview').className='small-'+\$V(this)"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_message field=text}}</th>
      <td colspan="3">
      	<div id="text-preview" class="small-{{$ex_message->type}}">
      	  {{mb_field object=$ex_message field=text}}
				</div>
			</td>
    </tr>
      
    <tr>
      <th></th>
      <td colspan="3">
        {{if $ex_message->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le message ',objName:'{{$ex_message->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
