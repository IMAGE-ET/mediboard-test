{{if $perm_edit}}
	{{mb_label object=$line field="commentaire"}}: 
	<form name="addComment{{$line->_class_name}}-{{$line->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="dosql" value="{{$dosql}}" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
	  <input type="text" name="commentaire" size="60" value="{{$line->commentaire}}" onchange="testPharma({{$line->_id}}); this.form.onsubmit();" />
	</form>
{{else}}
  {{if $line->commentaire}}
    {{mb_label object=$line field="commentaire"}}: {{$line->commentaire}}
	{{else}}
	  Aucun commentaire
	{{/if}}
{{/if}}