<!-- Affichage des techniques complementaire saisies en consult anesth -->
{{foreach from=$operation->_ref_anesth_perops item=_perop}}    	 
	<form name="editPerop{{$_perop->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshAnesthPerops.curry('{{$_perop->operation_id}}') } )">
    <input type="hidden" name="m" value="dPsalleOp" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_anesth_perop_aed" />
    <input type="hidden" name="anesth_perop_id" value="{{$_perop->_id}}" />
			
  	{{assign var=perop_id value=$_perop->_id}}
		<button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="$V(this.form.del, '1'); this.form.onsubmit(); ">
      {{tr}}Delete{{/tr}}
    </button>
  	{{mb_field object=$_perop field=datetime register=true form="editPerop$perop_id" onchange="this.form.onsubmit();"}}
		{{$_perop->libelle}}
  </form>
  <br />
{{/foreach}}
  