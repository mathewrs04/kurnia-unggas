 @if ($errors->any())
     <div class="alert alert-{{ $type }}">
         <ul>
             @foreach ($errors->all() as $error)
                 <small class="d-block">{{ $error }}</small>
             @endforeach
         </ul>
     </div>
 @endif
