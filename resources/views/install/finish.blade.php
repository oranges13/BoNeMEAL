@extends('layouts.basic')

@section('content')
        <ul class="nav nav-fill wizard">
            <li class="nav-item nav-link">
                <span class="badge badge-default">1</span>
                <span class="d-none d-sm-inline">Welcome</span>
            </li>
            <li class="nav-item nav-link">
                <span class="badge badge-default">2</span>
                <span class="d-none d-sm-inline">Configuration</span>
            </li>
            <li class="nav-item nav-link">
                <span class="badge badge-default">3</span>
                <span class="d-none d-sm-inline">Installation</span>
            </li>
            <li class="nav-item nav-link active">
                <span class="badge badge-default">4</span>
                <span class="d-none d-sm-inline">Finished</span>
            </li>
        </ul>

        <div class="content">
            <h1>Done!</h1>
            <hr/>
            <p class="lead">Congratulations! The application is now installed.</p>

            <div class="d-flex justify-content-center">
                <a href="{{route('login')}}" class="btn btn-primary btn-lg">Let's Get Started!</a>
            </div>
        </div>
@endsection
