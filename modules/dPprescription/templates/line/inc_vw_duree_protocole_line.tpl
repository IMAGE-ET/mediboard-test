{{assign var=line_id value=$line->_id}}
<tr>
  <td></td>
  <td colspan="5">
    <form name="editDuree-{{$typeDate}}-{{$line->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="{{$dosql}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
      
      <!-- Durée -->
      Durée de 
      {{mb_field object=$line field=duree increment=1 min=1 form=editDuree-$typeDate-$line_id size="3"
                 onchange="submitFormAjax(this.form, 'systemMsg');"}}
			jours
			
			<!-- Décalage -->
			à partir de J+ 
			{{mb_field object=$line field=decalage_line increment=1 min=1 form=editDuree-$typeDate-$line_id 
			           onchange="submitFormAjax(this.form, 'systemMsg');" size="3"}}
			
    </form>
    <script type="text/javascript">
      Main.add( function(){
        prepareForm(document.forms['editDuree-{{$typeDate}}-{{$line->_id}}']); 
      } );
    </script>
  </td>


</tr>