<button class="edit notext" type="button" onclick="window.location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}'">
  Modifier
</button>
{{$patient->_view}}
<br />
Age : {{$patient->_age}} ans
<br />
<a class="button search" href="{{$patient->_dossier_cabinet_url}}">
  Dossier complet
</a>
<br />
<button class="search" onclick="showAll({{$patient->_id}})">
  Résumé
</button>