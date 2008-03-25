<tbody class="hoverable">
    <tr>
      <td style="width: 25px">
        <form name="delLineComment{{$element}}-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'{{$element}}') } } );">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
      </td>
      <td colspan="3">
        {{$_line_comment->commentaire}}
      </td>
      <td>
        {{assign var=category value=$_line_comment->_ref_category_prescription}}
        {{$category->_view}}
      </td>
     <td>
      {{assign var=category_id value=$category->_id}}
      {{if @$executants.$category_id}}
      <form name="addExecutant-{{$_line_comment->_id}}" method="post" action="">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
        <!-- Selection d'un executant -->
        <select class="executant-{{$category_id}}" name="executant_prescription_line_id" onchange="submitFormAjax(this.form, 'systemMsg');">
          <option value="">&mdash; Sélection d'un exécutant</option>
          {{foreach from=$executants.$category_id item=executant}}
          <option value="{{$executant->_id}}" {{if $executant->_id == $_line_comment->executant_prescription_line_id}}selected="selected"{{/if}}>{{$executant->_view}}</option>
          {{/foreach}}
        </select>
      </form>
      
      <a href="#" style="display:inline" 
         onclick="preselectExecutant(document.forms['addExecutant-'+{{$_line_comment->_id}}].executant_prescription_line_id.value,'{{$category_id}}');">
		    <img src="images/icons/updown.gif" alt="Préselectionner" border="0" />
			</a>
			
      {{else}}
        Aucun exécutant disponible
      {{/if}}
    </td>
      <td style="width: 25px">
        <form name="lineCommentALD{{$element}}-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          {{mb_field object=$_line_comment field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg')"}}
          {{mb_label object=$_line_comment field="ald" typeEnum="checkbox"}}
        </form>
      </td>
    </tr>
  </tbody>
