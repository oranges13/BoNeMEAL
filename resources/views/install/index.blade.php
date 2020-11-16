@extends('layouts.basic')

@push('scripts')
    <script type="text/javascript">
        window.addEventListener('load', function() {
            // Ensure javascript and extensions are loaded before enabling button
            if (
                {{extension_loaded('bcmath')}} &&
                {{extension_loaded('ctype')}} &&
                {{extension_loaded('fileinfo')}} &&
                {{extension_loaded('json')}} &&
                {{extension_loaded('mbstring')}} &&
                {{extension_loaded('openssl')}} &&
                {{extension_loaded('pdo')}} &&
                {{extension_loaded('tokenizer')}} &&
                {{extension_loaded('xml')}} &&
                {{extension_loaded('pdo_sqlite')}} &&
                {{extension_loaded('pdo_mysql')}}
            ) {
                $("#start").removeClass("disabled");
            }
        });
    </script>
@endpush

@push('styles')
@endpush

@section('content')
<div class="container">
    <ul class="nav nav-fill wizard">
            <li class="nav-item nav-link active">
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
            <li class="nav-item nav-link">
                <span class="badge badge-default">4</span>
                <span class="d-none d-sm-inline">Finished</span>
            </li>
    </ul>

    <div class="content">
        <h2>Welcome to the Ban-Management of tomorrow!</h2><hr/>
        <p class="lead">This Installer will guide you through the installation of the <b><u>B</u>a<u>N</u> <u>M</u>anagement w<u>E</u>b <u>A</u>pp<u>L</u>ication</b> (BoNeMEAL).</p>

        <noscript>
            <p class="bg-warning"><strong><i class="fa fa-exclamation-triangle"></i> Javascript deactivated!</strong> The installer requires Javascript to run - it will not work without it! Please enable Javascript.</p>
            <br />
        </noscript>

        <h3>Requirements</h3>
        <p>First of all be sure to have the Ban-Management plugin installed on your Minecraft server and connected to a MySQL database.</p>
        <p>This application is currently based on Laravel 6.x (LTS), and has a few <a href="http://laravel.com/docs/6.x#installation" target="_blank">server requirements</a>.</p>
        <ul>
            <li>PHP >= 7.3 {!! version_compare(PHP_VERSION, '7.3') >= 0 ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-exclamation-triangle text-danger"></i>' !!}</li>
            <li>BCMath PHP Extension {!! extension_check('bcmath') !!}</li>
            <li>Ctype PHP Extension {!! extension_check('ctype') !!}</li>
            <li>Fileinfo PHP extension {!! extension_check('fileinfo') !!}</li>
            <li>JSON PHP Extension {!! extension_check('json') !!}</li>
            <li>Mbstring PHP Extension {!! extension_check('mbstring') !!}</li>
            <li>OpenSSL PHP Extension {!! extension_check('openssl') !!}</li>
            <li>PDO PHP Extension {!! extension_check('pdo') !!}</li>
            <li>Tokenizer PHP Extension {!! extension_check('tokenizer') !!}</li>
            <li>XML PHP Extension {!! extension_check('xml') !!}</li>
        </ul>
        <h4>Database Requirements</h4>
        <ul>
            <li>SQLite Extension {!! extension_check('pdo_sqlite') !!}</li>
            <li>MySQL Extension {!! extension_check('pdo_mysql') !!}</li>
        </ul>

        <hr>
        <h3>Terms and Conditions</h3>
        <p><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">BoNeMEAL</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/ftbastler/BoNeMEAL" property="cc:attributionName" rel="cc:attributionURL">ftbastler and contributors</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a> (see <a href="https://github.com/ftbastler/BoNeMEAL/blob/master/LICENSE.md">LICENSE</a>).</p>
        <p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
        <p><i>By downloading and/or installing the software you agree with these terms and conditions.</i></p>
    </div>

    <div class="buttons">
        <a href="{{ route('install.config') }}" class="btn btn-primary disabled" id="start">Agree &amp; Continue <i class="fa fa-chevron-right"></i></a>
    </div>
</div>
@endsection
