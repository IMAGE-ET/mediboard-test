{{mb_script module="dPccam" script="code_ccam" ajax="true"}}

<script type="text/javascript">
  afterActeDentaire = function(id, obj) {
    refreshListActesDentaires(obj.devenir_dentaire_id);
    var form = getForm('editActeDentaire');
    $V(form.code, '');
    $V(form._codes_ccam, '');
    $V(form.commentaire, '');
    refreshListDevenirs('{{$consult->patient_id}}', obj.devenir_dentaire_id);
  }
  
  chooseEtudiant = function(devenir_dentaire_id) {
    var url = new Url('dPcabinet', 'ajax_choose_etudiant');
    url.addParam("devenir_dentaire_id", devenir_dentaire_id);
    url.modal();
  }
  
  selectEtudiant = function(etudiant_id) {
    var form = getForm('editDevenir');
    $V(form.etudiant_id, etudiant_id);
    Control.Modal.close();
    form.onsubmit();
  }
  
  editProjet = function(devenir_dentaire_id, obj) {
    var url = new Url('dPcabinet', 'ajax_edit_devenir_dentaire');
    url.addParam('patient_id', '{{$consult->patient_id}}');
    url.addParam('devenir_dentaire_id', devenir_dentaire_id);
    url.requestUpdate('devenir_area');
    if (!Object.isUndefined(obj)) {
      refreshListDevenirs('{{$consult->patient_id}}', obj.devenir_dentaire_id);
    }
  }
  
  refreshListDevenirs = function(patient_id, devenir_dentaire_id) {
    var url = new Url('dPcabinet', 'ajax_list_devenirs_dentaires');
    url.addParam('patient_id', '{{$consult->patient_id}}');
    url.addParam('devenir_dentaire_id', devenir_dentaire_id);
    url.requestUpdate('list_devenirs');
  }
  
  refreshListActesDentaires = function(devenir_dentaire_id) {
    var url = new Url('dPcabinet', 'ajax_list_actes_dentaires');
    url.addParam('devenir_dentaire_id', devenir_dentaire_id);
    url.requestUpdate('list_actes_dentaires');
  }
  
  refreshSelected = function(tr) {
    $('list_devenirs').select('tr').each(function(elt) {
      elt.removeClassName('selected');
    });
    tr.addClassName('selected');
  }

  dragOptions = {
    starteffect : function(element) { 
      new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 }); 
    },
    constraint: "vertical",
    revert: true,
    ghosting: false
  }
  
  orderActeDentaire = function(acte_dentaire_id, rank) {
    var form = getForm('reorderActe');
    $V(form.acte_dentaire_id, acte_dentaire_id);
    $V(form.rank, rank);
    return onSubmitFormAjax(form, { onComplete: function() {
      refreshListActesDentaires($V(getForm('editDevenir').devenir_dentaire_id));
    }});
  }
  
  updateRank = function(increment) {
    var form = getForm('editActeDentaire');
    $V(form.rank, parseInt($V(form.rank))+increment);
  }
</script>

<!-- Formulaire de changement d'ordre d'acte dentaire -->
<form name="reorderActe" action="?" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_rank_acte_dentaire_aed" />
  <input type="hidden" name="acte_dentaire_id" value="" />
  <input type="hidden" name="rank" value="" />
</form>

<table class="main">
  <tr>
    <td>
      <button type="button" class="new" onclick="editProjet(0);">Nouveau projet thérapeutique</button>
      <table class="form">
        <tr>
          <th class="title" colspan="3">Liste des projets thérapeutiques</th>
        </tr>
        <tr>
          <th class="category">{{mb_label class=CDevenirDentaire field=description}}</th>
          <th class="category">Nombre d'actes</th>
          <th class="category">Etudiant</th>
        </tr>
        <tbody id="list_devenirs">
          {{mb_include module=cabinet template=inc_list_devenirs_dentaires}}
        </tbody>
      </table>
    </td>
  </tr>
  <tr>
    <td id="devenir_area">
    </td>
  </tr>
</table>

