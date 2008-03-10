<script type="text/javascript">

{{assign var=nb_med value=$prescription->_ref_lines_med_comments.med|@count}}
{{assign var=nb_comment value=$prescription->_ref_lines_med_comments.comment|@count}}
{{assign var=nb_total value=$nb_med+$nb_comment}}

Prescription.refreshTabHeader("div_medicament","{{$nb_total}}");

</script>

<!-- Affichage des div des medicaments et autres produits -->
  <form action="?" method="get" name="searchProd" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.medicament item=curr_prod}}
      <option value="{{$curr_prod->code_cip}}">
        {{$curr_prod->libelle}}
      </option>
      {{/foreach}}
    </select>
    <button class="add" onclick="$('add_line_comment_med').show();">Ajouter une ligne de commentaire</button>
    <br />
	  <input type="text" name="produit" value=""/>
	  <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	  <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
	  <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
	  <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
	  <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
	  <script type="text/javascript">
		  if (MedSelector.oUrl) {
		    MedSelector.close();
		  }
		  MedSelector.init = function(onglet){
		    this.sForm = "searchProd";
		    this.sView = "produit";
		    this.sSearch = document.searchProd.produit.value;
		    this.sOnglet = onglet;
		    this.selfClose = false;
		    this.pop();
		  }
		  MedSelector.set = function(nom, code){
		    Prescription.addLine(code);
		  }
	</script>
  </form>
  <br />
  <div id="add_line_comment_med" style="display: none">
   <button class="remove notext" type="button" onclick="$('add_line_comment_med').hide();">Cacher</button>
   <form name="addLineCommentMed" method="post" action="">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
    
      <input type="hidden" name="chapitre" value="medicament" />
      <input name="commentaire" type="text" size="98" />
      <button class="submit notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">Ajouter</button>
    </form>
 </div> 

{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.med}}
<table class="tbl">
  {{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_lines_med_comments.comment|@count}} 
  <tr>
    <th colspan="4">Médicaments</th>
  </tr>
  {{/if}}
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
 
  <tbody class="hoverable">
  <tr>
    <td style="width: 25px">
      <button type="button" class="trash notext" onclick="Prescription.delLine({{$curr_line->_id}})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
    <td style="width: 10px">
    {{assign var="color" value=#ccc}}
      {{if $curr_line->_nb_alertes}}
        
        {{if $curr_line->_ref_alertes.IPC || $curr_line->_ref_alertes.profil}}
          {{assign var="image" value="note_orange.png"}}
          {{assign var="color" value=#fff288}}
        {{/if}}  
        {{if $curr_line->_ref_alertes.allergie || $curr_line->_ref_alertes.interaction}}
          {{assign var="image" value="note_red.png"}}
          {{assign var="color" value=#ff7474}}
        {{/if}}  
        <img src="images/icons/{{$image}}" title="" alt="" 
             onmouseover="$('line-{{$curr_line->_id}}').show();"
             onmouseout="$('line-{{$curr_line->_id}}').hide();" />
      {{/if}}
      <div id="line-{{$curr_line->_id}}" class="tooltip" style="display: none; background-color: {{$color}}; border-style: ridge; padding-right:5px; ">
      {{foreach from=$curr_line->_ref_alertes_text key=type item=curr_type}}
        {{if $curr_type|@count}}
          <ul>
          {{foreach from=$curr_type item=curr_alerte}}
            <li>
              <strong>{{tr}}CPrescriptionLine-alerte-{{$type}}-court{{/tr}} :</strong>
              {{$curr_alerte}}
            </li>
          {{/foreach}}
          </ul>
        {{/if}}
      {{/foreach}}
      </div>
    </td>
    <td>
      <a href="#produit{{$curr_line->_id}}" onclick="viewProduit({{$curr_line->_ref_produit->code_cip}})">
        <strong>{{$curr_line->_view}}</strong>
      </a>
      <form action="?m=dPprescription" method="post" name="editLine-{{$curr_line->_id}}" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_aed" />
        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}"/>
        <input type="hidden" name="del" value="0" />
        <select name="no_poso" onchange="submitFormAjax(this.form, 'systemMsg')">
          <option value="">&mdash; Choisir une posologie</option>
          {{foreach from=$curr_line->_ref_produit->_ref_posologies item=curr_poso}}
          <option value="{{$curr_poso->code_posologie}}"
            {{if $curr_poso->code_posologie == $curr_line->no_poso}}selected="selected"{{/if}}>
            {{$curr_poso->_view}}
          </option>
          {{/foreach}}
        </select>
      </form>
    </td>
    <td>
      <div style="float: right;">
        <button type="button" class="change notext" onclick="EquivSelector.init('{{$curr_line->_id}}','{{$curr_line->_ref_produit->code_cip}}');">
          Equivalents
        </button>
        <script type="text/javascript">
          if(EquivSelector.oUrl) {
            EquivSelector.close();
          }
          EquivSelector.init = function(line_id, code_cip){
            this.sForm = "searchProd";
            this.sView = "produit";
            this.sCodeCIP = code_cip
            this.sLine = line_id;
            this.selfClose = false;
            this.pop();
          }
          EquivSelector.set = function(code, line_id){
            Prescription.addEquivalent(code, line_id);
          }
        </script>
      </div>
      <form name="addCommentMedicament-{{$curr_line->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
        <input type="text" name="commentaire" value="{{$curr_line->commentaire}}" onchange="this.form.onsubmit();" />
      </form>
    </td>
   </tr>
  </tbody>
  
  {{/foreach}}
    <!-- Parcours des commentaires --> 
 {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
   <tbody class="hoverable">
    <tr>
      <td>
        <form name="delLineCommentMed-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'medicament') } } );">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
      </td>
      <td colspan="3">
        {{$_line_comment->commentaire}}
      </td>
    </tr>
  </tbody>
  {{/foreach}}
 </table> 
{{else}}
  <div class="big-info"> 
     Il n'y a aucun médicament dans cette prescription.
  </div>
{{/if}}