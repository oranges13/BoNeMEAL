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

            {!! Form::open(['route' => 'install.run']) !!}
            <div class="form-row">
                <div class="col-md-6 pr-md-3">
                    <h2>System Settings</h2>
                    <p>Basic settings for the application</p>
                    <div class="form-group">
                        <label for="host">Host</label>
                        <input type="url" class="form-control @error('host') is-invalid @enderror" name="host" value="{{old('host')}}" placeholder="https://awesome-site.com" aria-describedby="hostHelp" required>
                        <small class="form-text text-muted" id="hostHelp">Note: No trailing slash!</small>
                        @error('host')
                        <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="locale">Locale</label>
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
                        <label for="timezone">Timezone</label>
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
                        <label for="name">Username</label>
                        <input type="text" name="name" value="{{old('name')}}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail Address</label>
                        <input type="email" name="email" value="{{old('email')}}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="{{old('password')}}" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Password Confirmation</label>
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-row">
                {!! Form::submit() !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
