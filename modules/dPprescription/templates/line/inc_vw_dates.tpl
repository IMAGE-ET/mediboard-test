<form name="editDates-{{$typeDate}}-{{$line->_id}}" action="?" method="post">
   <input type="hidden" name="m" value="dPprescription" />
   <input type="hidden" name="dosql" value="{{$dosql}}" />
   <input type="hidden" name="del" value="0" />
   <input type="hidden" name="{{$line->_tbl_key}}" value="{{$line->_id}}" />
   <table>
     <tr>
       {{assign var=line_id value=$line->_id}}
       <td style="border:none">
         {{mb_label object=$line field=debut}}
       </td>    
       {{if $perm_edit}}
       <td class="date" style="border:none;">
         {{mb_field object=$line field=debut form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, $line_id, this.name, '$typeDate');"}}
       </td>
       {{else}}
       <td style="border:none">
         {{if $line->debut}}
           {{$line->debut|date_format:"%d/%m/%Y"}}
         {{else}}
          -
         {{/if}}				   
       </td>
       {{/if}}
       <td style="border:none;">
         {{mb_label object=$line field=duree}}
       </td>
       <td style="border:none">
	       {{if $perm_edit}}
			     {{mb_field object=$line field=duree onchange="syncDateSubmit(this.form, $line_id, this.name, '$typeDate');" size="3" }}
			     {{mb_field object=$line field=unite_duree onchange="syncDateSubmit(this.form, $line_id, this.name, '$typeDate');"}}
			   {{else}}
			     {{if $line->duree}}
			       {{$line->duree}}
			     {{else}}
			       -
			     {{/if}}
			     {{if $line->unite_duree}}
			       {{tr}}CPrescriptionLineMedicament.unite_duree.{{$line->unite_duree}}{{/tr}}	      
			     {{/if}}
			   {{/if}}
       </td>
       <td style="border:none">
         {{mb_label object=$line field=_fin}} 
       </td>
       {{if $perm_edit}}
       <td class="date" style="border:none;">
         {{mb_field object=$line field=_fin form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, $line_id, this.name, '$typeDate');"}}
       </td>
       {{else}}
       <td style="border:none">
	       {{if $line->_fin}}
	         {{$line->_fin|date_format:"%d/%m/%Y"}}
	       {{else}}
	        -
	       {{/if}}				   
       </td>
       {{/if}}
    </tr>
  </table>
</form>