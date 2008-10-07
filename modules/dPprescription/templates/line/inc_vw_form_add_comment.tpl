{{if $line->_can_modify_comment}}
	{{mb_label object=$line field="commentaire"}}: 
	<input type="text" name="commentaire" size="60" value="{{$line->commentaire}}" 
         onchange="testPharma({{$line->_id}}); 
                   {{if $line->substitute_for && !$line->substitution_active}}submitAddCommentSubst{{else}}submitAddComment{{/if}}('{{$line->_class_name}}', '{{$line->_id}}', this.value);" />
{{else}}
  {{if $line->commentaire}}
    {{mb_label object=$line field="commentaire"}}: {{$line->commentaire}}
	{{else}}
	  Aucun commentaire
	{{/if}}
{{/if}}