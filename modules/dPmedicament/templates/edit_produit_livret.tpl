<script type="text/javascript">

function submitProduitLivret(){
  var oForm = document.editProduitLivret;
  submitFormAjax(oForm, 'systemMsg', { onComplete : window.opener.Livret.reload });
}

function pageMain() {
  regFieldCalendar("editProduitLivret", "date_prix_hopital");
  regFieldCalendar("editProduitLivret", "date_prix_ville");
}


</script>

<form name="editProduitLivret" action="?" method="post">
  <input type="hidden" name="m" value="dPmedicament" />
  <input type="hidden" name="dosql" value="do_produit_livret_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="produit_livret_id" value="{{$produit_livret->_id}}" />
	<table class="tbl">
		<tr>
		  <th colspan="2">{{$produit_livret->_ref_produit->libelle}}</th>
		</tr>
		<tr>
		  <td>
		    {{mb_label object=$produit_livret field="prix_hopital"}}
		  </td>
		  <td>
		    {{mb_field object=$produit_livret field="prix_hopital"}}
		  </td>
		</tr>
		<tr>
		  <td>
		    {{mb_label object=$produit_livret field="prix_ville"}}
		  </td>
		  <td>
		    {{mb_field object=$produit_livret field="prix_ville"}}
		  </td>
		</tr>
		<tr>
		  <td>
		    {{mb_label object=$produit_livret field="date_prix_hopital"}}
		  </td>
		  <td class="date">
		    {{mb_field object=$produit_livret field="date_prix_hopital" form="editProduitLivret"}}
		  </td>
		</tr>
		<tr>
		  <td>
		    {{mb_label object=$produit_livret field="date_prix_ville"}}
		  </td>
		  <td class="date">
		    {{mb_field object=$produit_livret field="date_prix_ville" form="editProduitLivret"}}
		  </td>
		</tr>
		<tr>
		  <td>
		    {{mb_label object=$produit_livret field="code_interne"}}
		  </td>
		  <td>
		    {{mb_field object=$produit_livret field="code_interne"}}
		  </td>
		</tr>
		<tr>
		  <td>
		    {{mb_label object=$produit_livret field="commentaire"}}
		  </td>
		  <td>
		    {{mb_field object=$produit_livret field="commentaire"}}
		  </td>
		</tr>
		<tr>
		  <td colspan="2" style="text-align: center">
		    <button type="button" class="submit" onclick="submitProduitLivret();">Enregistrer</button>
		  </td>
		</tr>
	</table>
</form>

