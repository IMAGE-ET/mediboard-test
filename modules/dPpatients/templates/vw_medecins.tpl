<!-- $Id$ -->

<script type="text/javascript">
function checkMedecin() {
  var form = document.editFrm;
    
  if (form.nom.value.length == 0) {
    alert("Nom manquant");
    form.nom.focus();
    return false;
  }
    
  if (form.prenom.value.length == 0) {
    alert("Pr�nom manquant");
    form.prenom.focus();
    return false;
  }
   
  return true;
}

function setClose() {
  window.opener.setMed(
    "{{$medecin->medecin_id}}",
    "{{$medecin->nom|escape:javascript}}",
    "{{$medecin->prenom|escape:javascript}}",
    "{{$type|escape:javascript}}");
  window.close();
}

</script>

<table class="main">
  <tr>
    <td class="greedyPane">
    
      <form name="find" action="./index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      {{if $dialog}}
      <input type="hidden" name="a" value="vw_medecins" />
      <input type="hidden" name="dialog" value="1" />
      {{else}}
      <input type="hidden" name="tab" value="{{$tab}}" />
      {{/if}}
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">Recherche d'un m�decin</th>
        </tr>
  
        <tr>
          <th><label for="medecin_nom" title="Nom complet ou partiel du m�decin recherch�">Nom</label></th>
          <td><input tabindex="1" type="text" name="medecin_nom" value="{{$nom}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_prenom" title="Pr�nom complet ou partiel du m�decin recherch�">Pr�nom</label></th>
          <td><input tabindex="2" type="text" name="medecin_prenom" value="{{$prenom}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_dept" title="D�partement du m�decin recherch�">D�partement (00 pour tous)</label></th>
          <td><input tabindex="3" type="text" name="medecin_dept" value="{{$departement}}" /></td>
        </tr>
        
        <tr>
          <td class="button" colspan="2"><button class="search" type="submit">Rechercher</button></td>
        </tr>
      </table>

      </form>

      {{if !$dialog}}
      <form name="fusion" action="index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_medecin" />
      {{/if}}
      
      <table class="tbl">
        <tr>
          {{if !$dialog}}
          <th><button type="submit" class="search">Fusion</button></th>
          {{/if}}
          <th>Nom - Pr�nom</th>
          {{if !$dialog}}
          <th>Adresse</th>
          {{/if}}
          <th>Ville</th>
          <th>CP</th>
          {{if !$dialog}}
          <th>Telephone</th>
          <th>Fax</th>
          {{/if}}
        </tr>

        {{foreach from=$medecins item=curr_medecin}}
        {{assign var="medecin_id" value=$curr_medecin->medecin_id"}}
        <tr>
          {{if !$dialog}}
            <td><input type="checkbox" name="fusion_{{$medecin_id}}" /></td>
          {{/if}}
          {{if $dialog}}
            {{assign var="href" value="?m=$m&amp;a=vw_medecins&amp;dialog=1&amp;medecin_id=$medecin_id"}}
            <td><a href="{{$href}}">{{$curr_medecin->_view}}</a></td>
            <td class="text"><a href="{{$href}}">{{$curr_medecin->ville}}</a></td>
            <td><a href="{{$href}}">{{$curr_medecin->cp}}</a></td>
          {{else}}
            {{assign var="href" value="?m=$m&amp;tab=$tab&amp;medecin_id=$medecin_id"}}
            <td><a href="{{$href}}">{{$curr_medecin->_view}}</a></td>
            <td class="text"><a href="{{$href}}">{{$curr_medecin->adresse}}</a></td>
            <td class="text"><a href="{{$href}}">{{$curr_medecin->ville}}</a></td>
            <td><a href="{{$href}}">{{$curr_medecin->cp}}</a></td>
            <td><a href="{{$href}}">{{$curr_medecin->tel}}</a></td>
            <td><a href="{{$href}}">{{$curr_medecin->fax}}</a></td>
          {{/if}}
        </tr>
        {{/foreach}}
        
      </table>

      {{if !$dialog}}
      </form>
      {{/if}}

    </td>

    <td class="pane">
      <form name="editFrm" action="index.php?m={{$m}}" method="post" onsubmit="return checkMedecin()">
      <input type="hidden" name="dosql" value="do_medecins_aed" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if !$dialog && $medecin->medecin_id}}
        <tr>
          <td colspan="2"><a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;new=1">Cr�er un nouveau m�decin</a></td>
        </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="2">
            {{if $medecin->medecin_id}}
	         <a style="float:right;" href="javascript:view_log('CMedecin',{{$medecin->medecin_id}})">
               <img src="images/history.gif" alt="historique" />
              </a>
              Modification du Dr. {{$medecin->_view}}
            {{else}}
              Cr�ation d'une fiche
            {{/if}}
          </th>
        </tr>

        <tr>
          <th><label for="nom" title="Nom du m�decin">Nom</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}><input type="text" {{if $dialog}} readonly {{/if}} name="nom" value="{{$medecin->nom}}" /></td>
        </tr>
        
        <tr>
          <th><label for="prenom" title="Pr�nom du m�decin">Pr�nom</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}><input type="text" {{if $dialog}} readonly {{/if}} name="prenom" value="{{$medecin->prenom}}" /></td>
        </tr>
        
        <tr>
          <th><label for="adresse" title="Adresse du cabinet du m�decin">Adresse</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}>
            <textarea {{if $dialog}} readonly {{/if}} name="adresse">{{$medecin->adresse}}</textarea>
          </td>
        </tr>
        
        <tr>
          <th><label for="cp" title="Code Postal du cabinet du m�decin">Code Postal</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}><input type="text" {{if $dialog}} readonly {{/if}} name="cp" value="{{$medecin->cp}}" /></td>
        </tr>
        
        <tr>
          <th><label for="ville" title="Ville du cabinet du m�decin">Ville</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}><input type="text" {{if $dialog}} readonly {{/if}} name="ville" value="{{$medecin->ville}}" /></td>
        </tr>
        
        <tr>
          <th><label for="_tel1" title="T�l�phone du m�decin">T�l</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}>
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_tel1" value="{{$medecin->_tel1}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_tel2" value="{{$medecin->_tel2}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_tel3" value="{{$medecin->_tel3}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_tel4" value="{{$medecin->_tel4}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_tel5" value="{{$medecin->_tel5}}" />
          </td>
        </tr>
        
        <tr>
          <th><label for="_fax1" title="Fax du m�decin">Fax</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}>
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_fax1" value="{{$medecin->_fax1}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_fax2" value="{{$medecin->_fax2}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_fax3" value="{{$medecin->_fax3}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_fax4" value="{{$medecin->_fax4}}" /> -
            <input type="text" {{if $dialog}} readonly {{/if}} size="2" maxlength="2" name="_fax5" value="{{$medecin->_fax5}}" />
          </td>
        </tr>
        
        <tr>
          <th><label for="email" title="Email du m�decin">Email</label></th>
          <td {{if $dialog}} class="readonly" {{/if}}><input type="text" {{if $dialog}} readonly {{/if}} name="email" value="{{$medecin->email}}" /></td>
        </tr>

        <tr>
          <td class="button" colspan="4">
          {{if $dialog}}
            <button type="button" onclick="setClose()">Selectionner ce medecin</button>
          {{else}}
            {{if $medecin->medecin_id}}
            <input type="hidden" name="medecin_id" value="{{$medecin->medecin_id}}" />
            <button class="modify" type="submit">Modifier</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'le m�decin',objName:'{{$medecin->_view|escape:javascript}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
      