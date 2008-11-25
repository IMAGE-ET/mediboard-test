function showQuestion(question){
  if(question){
    $('view'+question).show();
  }
}
function hideQuestion(question){
  if(question){
    var oForm = document.editFrmNyha;
    var oField = oForm[question];
    
    if(oField[0].checked || oField[1].checked){
      oField[2].checked = true;
      oField[0].onchange();
    }
    $('view'+question).hide();
  }
}

function changeValue(sField,sRepYes,sRepNo){
  var oForm = document.editFrmNyha;
  var oField = oForm[sField];
  if(oField[1].checked){
    showQuestion(sRepYes);
    hideQuestion(sRepNo);
  }else if(oField[0].checked){
    showQuestion(sRepNo);
    hideQuestion(sRepYes);
  }else{
    hideQuestion(sRepNo);
    hideQuestion(sRepYes);
  }
  calculClasseNyha();
}

function calculClasseNyha(){
  var nyha = "";
  var oForm = document.editFrmNyha;
  if(oForm.q1[1].checked){
    if(oForm.q2a[0].checked){
      nyha = "Classe III";
    }
    if(oForm.q2a[1].checked && oForm.q2b[0].checked){
      nyha = "Classe II";
    }
    if(oForm.q2a[1].checked && oForm.q2b[1].checked){
      nyha = "Classe I";
    }
  }
  if(oForm.q1[0].checked){
    if(oForm.q3a[0].checked){
      nyha = "Classe III";
    }
    if(oForm.q3a[1].checked && oForm.q3b[0].checked){
      nyha = "Classe IV";
    }
    if(oForm.q3a[1].checked && oForm.q3b[1].checked){
      nyha = "Classe III";
    }
  }
  $('classeNyha').innerHTML = nyha;
}