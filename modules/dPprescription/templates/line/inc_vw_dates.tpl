<script type="text/javascript">
// Calcul de la date de debut lors de la modification de la fin
syncDate = function(oForm, curr_line_id, fieldName, type) {
  // Déclaration des div des dates
  oDivDebut = $('editDates-'+type+'-'+curr_line_id+'_debut_da');
  oDivFin = $('editDates-'+type+'-'+curr_line_id+'__fin_da');

  // Recuperation de la date actuelle
  var todayDate = new Date();
  var dToday = todayDate.toDATE();
  
  // Recuperation des dates des formulaires
  var sDebut = oForm.debut.value;
  var sFin = oForm._fin.value;
  var nDuree = parseInt(oForm.duree.value, 10);
  var sType = oForm.unite_duree.value;
  
  // Transformation des dates
  if(sDebut){
    var dDebut = Date.fromDATE(sDebut);  
  }
  if(sFin){
    var dFin = Date.fromDATE(sFin);  
  }
  
  // Modification de la fin en fonction du debut
  if(fieldName != "_fin" && sDebut && sType && nDuree) {
    dFin = dDebut;
    if(sType == "jour")      { dFin.addDays(nDuree-1);     }
    if(sType == "semaine")   { dFin.addDays(nDuree*7-1);   }
    if(sType == "quinzaine") { dFin.addDays(nDuree*14);  }
    if(sType == "mois")      { dFin.addDays(nDuree*30);  }
    if(sType == "trimestre") { dFin.addDays(nDuree*90);  }
    if(sType == "semestre")  { dFin.addDays(nDuree*180); }
    if(sType == "an")        { dFin.addDays(nDuree*365); }

  	oForm._fin.value = dFin.toDATE();
  	oDivFin.innerHTML = dFin.toLocaleDate();
  }
  
  //-- Lors de la modification de la fin --
  // Si debut, on modifie la duree
  if(sDebut && sFin && fieldName == "_fin"){
    var nDuree = (dFin - dDebut)/86400000;
    oForm.duree.value = nDuree+1;
    oForm.unite_duree.value = "jour";
  }
  
  // Si !debut et duree, on modifie le debut
  if(!sDebut && nDuree && sType && fieldName == "_fin"){
    dDebut = dFin;
    if(sType == "jour")      { dDebut.addDays(-nDuree);     }
    if(sType == "semaine")   { dDebut.addDays(-nDuree*7);   }
    if(sType == "quinzaine") { dDebut.addDays(-nDuree*14);  }
    if(sType == "mois")      { dDebut.addDays(-nDuree*30);  }
    if(sType == "trimestre") { dDebut.addDays(-nDuree*90);  }
    if(sType == "semestre")  { dDebut.addDays(-nDuree*180); }
    if(sType == "an")        { dDebut.addDays(-nDuree*365); }

  	oForm.debut.value = dDebut.toDATE();
  	oDivDebut.innerHTML = dDebut.toLocaleDate();
  }
  
  // Si !debut et !duree, on met le debut a aujourd'hui, et on modifie la duree
  if(!sDebut && !nDuree && fieldName == "_fin"){
    dDebut = todayDate;
    oForm.debut.value = todayDate.toDATE();
    oDivDebut.innerHTML = todayDate.toLocaleDate();
    var nDuree = parseInt((dFin - dDebut)/86400000,10);
    oForm.duree.value = nDuree;
    oForm.unite_duree.value = "jour";
  }
}

syncDateSubmit = function(oForm, curr_line_id, fieldName, type) {
  syncDate(oForm, curr_line_id, fieldName, type);
  if(!curr_line_id){
    return;
  }
  submitFormAjax(oForm, 'systemMsg');
}

</script>


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
         {{mb_field object=$line field=debut form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate');"}}
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
			     {{mb_field object=$line field=duree increment=1 min=1 form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate');" size="3" }}
			     {{mb_field object=$line field=unite_duree onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate');"}}
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
         {{mb_field object=$line field=_fin form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate');"}}
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