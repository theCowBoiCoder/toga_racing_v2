@if($errors->any())<div class="form-errors"><b>Please check the form:</b><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
