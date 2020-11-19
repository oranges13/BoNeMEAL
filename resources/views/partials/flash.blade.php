@if ($message = Session::get('success'))
    <div class="alert alert-success alert-block my-1 mb-2 alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ $message }}
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block my-1 mb-2 alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ $message }}
    </div>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-block my-1 mb-2 alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ $message }}
    </div>
@endif

@if ($message = Session::get('info'))
    <div class="alert alert-info alert-block my-1 mb-2 alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ $message }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger my-1 mb-2 alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Please check the form below for errors</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif
