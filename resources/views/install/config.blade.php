@extends('layouts.basic')

@section('content')
    <div class="container">
        <ul class="nav nav-fill wizard">
            <li class="nav-item nav-link">
                <span class="badge badge-default">1</span>
                <span class="d-none d-sm-inline">Welcome</span>
            </li>
            <li class="nav-item nav-link active">
                <span class="badge badge-default">2</span>
                <span class="d-none d-sm-inline">Configuration</span>
            </li>
            <li class="nav-item nav-link">
                <span class="badge badge-default">3</span>
                <span class="d-none d-sm-inline">Installation</span>
            </li>
            <li class="nav-item nav-link">
                <span class="badge badge-default">4</span>
                <span class="d-none d-sm-inline">Finished</span>
            </li>
        </ul>

        <div class="content">
            <h1>
                Configuration
                <small class="text-muted">All fields are required!</small>
            </h1>
            <hr>

            {!! Form::open(['route' => 'install.run', 'autocomplete' => 'off', 'novalidate' => true]) !!}
            <div class="form-row">
                <div class="col-md-6 pr-md-3">
                    <h2>System Settings</h2>
                    <p>Basic settings for the application</p>
                    <div class="form-group">
                        <label for="host">{{__('Host')}}</label>
                        <input type="url" class="form-control @error('host') is-invalid @enderror" name="host" value="{{old('host')}}" placeholder="https://awesome-site.com" aria-describedby="hostHelp" required>
                        <small class="form-text text-muted" id="hostHelp">Note: No trailing slash!</small>
                        @error('host')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="locale">{{__('Locale')}}</label>
                        <select class="form-control @error('locale') is-invalid @enderror" name="locale" id="locale" required>
                            @foreach($langs as $lang)
                                <option @if($lang == 'en') selected @endif value="{{$lang}}">{{$lang}}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Don't see your language? <a href="https://github.com/ftbastler/BoNeMEAL/blob/master/CONTRIBUTING.md" target="_blank">Help us translate BoNeMEAL!</a></small>
                        @error('locale')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="timezone">{{__('Timezone')}}</label>
                        <select name="timezone" id="timezone" class="form-control @error('timezone') is-invalid @enderror" required>
                            @foreach($timezones as $zone => $offset)
                                <option value="{{$zone}}" @if($zone == old('timezone')) selected @endif>{{$offset}}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Choosing your time zone will ensure that timestamps are accurate.</small>
                        @error('timezone')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 pl-md-3">
                    <h2>Administrator Account</h2>
                    <p>Create your superuser account for the admin panel.</p>
                    <div class="form-group">
                        <label for="name">{{__('Username')}}</label>
                        <input type="text" name="name" value="{{old('name')}}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">{{__('E-mail Address')}}</label>
                        <input type="email" name="email" value="{{old('email')}}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">{{__('Password')}}</label>
                        <input type="password" name="password" value="{{old('password')}}" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">{{__('Password Confirmation')}}</label>
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="card w-100">
                    <h5 class="card-header">
                        <a class="@if(old('database_type') == 'sqlite') collapsed @endif d-block" data-toggle="collapse" href="#database-install" aria-expanded="true" aria-controls="database-install" id="heading-db">
                            <i class="fa fa-chevron-down pull-right"></i>
                            Database Setup <small>(optional)</small>
                        </a>
                    </h5>
                    <div id="database-install" class="collapse @if(old('database_type') == 'mysql') show @endif" aria-labelledby="heading-db">
                        <div class="card-body">
                            <p class="lead">By default, BoNeMEAL will use a local database for it's user management and
                                storage. If you would like to use a remote database, you can set that up here.
                                <span class="text-danger">This is not your Ban Management server set up. That is in the
                                    next step. If you do not understand these settings, leave them blank!</span>
                            </p>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="database_type">{{__('validation.attributes.database_type')}}</label>
                                        <select name="database_type" id="database_type" class="form-control @error('database_type') is-invalid @enderror" required>
                                            <option value="sqlite">SQLite (default)</option>
                                            <option value="mysql">MySQL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 pr-md-3 showMysql d-none">
                                    <div class="form-group">
                                        {!! Form::label('db_host', __('validation.attributes.db_host')) !!}
                                        {!! Form::text('db_host', old('db_host'), ['class'=>'form-control', 'required' => true]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('db_port', __('validation.attributes.db_port')) !!}
                                        {!! Form::text('db_port', old('db_port'), ['class'=>'form-control', 'required' => true, 'pattern' => "\d"]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('db_prefix', __('validation.attributes.db_prefix')) !!}
                                        {!! Form::text('db_prefix', old('db_prefix'), ['class'=>'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 pl-md-3 showMysql d-none">
                                    <div class="form-group">
                                        {!! Form::label('db_database', __('validation.attributes.db_database')) !!}
                                        {!! Form::text('db_database', old('db_database'), ['class'=>'form-control', 'required' => true]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('db_username', __('validation.attributes.db_username')) !!}
                                        {!! Form::text('db_username', old('db_username'), ['class'=>'form-control', 'required' => true]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('db_password', __('validation.attributes.db_password')) !!}
                                        {!! Form::password('db_password', ['class'=>'form-control', 'required' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row mt-2">
                {!! Form::submit('Submit', ['class'=> 'btn btn-primary btn-lg']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        window.addEventListener('load', function() {
            $("#database_type").change(function () {
                if ($(this).val() === 'mysql') {
                    $(".showMysql").removeClass('d-none');
                } else {
                    $(".showMysql").addClass('d-none');
                }
            });
        });
    </script>
@endpush
