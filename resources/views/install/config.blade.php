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

            @open(['route' => 'install.run', 'autocomplete' => 'off', 'id' => 'configForm'])
            <div class="form-row">
                <div class="col-md-6 pr-md-3">
                    <h2>System Settings</h2>
                    <p>Basic settings for the application</p>
                    @text('host', __('Host'), old('host'), ['help' => 'Note: No trailing slash!', 'required' => true])
                    @select('locale', __('Locale'), $langs, old('locale') ?? 'en', ['help' => 'Don\'t see your language? <a tabindex=99 href="https://github.com/ftbastler/BoNeMEAL/blob/master/CONTRIBUTING.md" target="_blank">Help us translate BoNeMEAL!</a>', 'required' => true])
                    @select('timezone', __('Timezone'), $timezones, old('timezone') ?? 'UTC', ['help' => 'Choosing the correct timezone will ensure that timestamps are accurate', 'required' => true])
                </div>
                <div class="col-md-6 pl-md-3">
                    <h2>Administrator Account</h2>
                    <p>Create your superuser account for the admin panel.</p>
                    @text('name', __('Username'), old('name'), ['required' => true])
                    @email('email', __('E-Mail Address'), old('email'), ['required' => true])
                    @password('password', __('Password'), ['required' => true])
                    @password('password_confirmation', __('Confirm Password'), ['required' => true])
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
                                    @select('database_type', __('validation.attributes.database_type'), ['sqlite' => 'SQLite', 'mysql' => 'MySQL'], old('database_type'), ['help' => 'Database configuration is only required if you select MySQL', 'id' => 'database_type'])
                                </div>
                                <div class="col-md-6 pr-md-3 showMysql @if(old('database_type') == 'sqlite') d-none @endif">
                                    @url('db_host', __('validation.attributes.db_host'), old('db_host'))
                                    @text('db_port', __('validation.attributes.db_port'), old('db_port'), ['pattern' => '\d+'])
                                    @text('db_prefix', __('validation.attributes.db_prefix'), old('db_prefix'))
                                </div>
                                <div class="col-md-6 pl-md-3 showMysql @if(old('database_type') == 'sqlite') d-none @endif">
                                    @text('db_database', __('validation.attributes.db_database'), old('db_database'))
                                    @text('db_username', __('validation.attributes.db_username'), old('db_username'))
                                    @password('db_password', __('validation.attributes.db_password'))
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row mt-2">
                @submit(__('Submit'), ['class'=> 'btn btn-primary btn-lg'])
            </div>
            @close
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        window.addEventListener('load', function() {
            if ($("#database_type").val() === 'sqlite') {
                // Hide database connection fields by default
                $(".showMysql").addClass('d-none');
            }
            $("#database_type").change(function () {
                if ($(this).val() === 'mysql') {
                    $(".showMysql").removeClass('d-none');
                } else {
                    $(".showMysql input").each(function() {
                       $(this).val('');
                    });
                    $(".showMysql").addClass('d-none');
                }
            });
            $('#configForm').validate({
                rules: {
                    host: {
                        url: true,
                    },
                    db_host: {
                        requiredIfMysql: true,
                    },
                    db_port: {
                        requiredIfMysql: true,
                    },
                    db_database: {
                        requiredIfMysql: true,
                    },
                    db_username: {
                        requiredIfMysql: true,
                    },
                    db_password: {
                        requiredIfMysql: true,
                    }
                }
            });
        });
    </script>
@endpush
