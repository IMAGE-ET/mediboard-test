<!-- Affichage des techniques complementaire saisies en consult anesth -->

<table class="main layout">
{{foreach from=$operation->_ref_anesth_perops item=_perop}}
  <tr> 
    <td>
    	<form name="editPerop{{$_perop->_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshAnesthPerops.curry('{{$_perop->operation_id}}') } )">
        <input type="hidden" name="m" value="dPsalleOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_anesth_perop_aed" />
        {{mb_key object=$_perop}}
    			
        {{assign var=perop_id value=$_perop->_id}}
        <button class="trash notext" type="button" onclick="$V(this.form.del, '1'); this.form.onsubmit(); ">
          {{tr}}Delete{{/tr}}
        </button>
        {{mb_field object=$_perop field=datetime register=true form="editPerop$perop_id" onchange="this.form.onsubmit();"}}
      </form>
    </td>
    <td class="text greedyPane" {{if $_perop->incident}}style="background-color: #f88 !important;"{{/if}}>
      {{if $_perop->incident}}
      <strong>Incident</strong>
      <br />
      {{/if}}
      {{mb_value object=$_perop field=libelle}}
    </td>
  </tr>
{{/foreach}}
</table>
  