<!DOCTYPE html>
<html lang="en">
    <head id="Starter-Site">
        <meta charset="UTF-8">
        <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>
            @section('title')
            Administration
            @show
        </title>
        <!--  Mobile Viewport Fix -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <!-- This is the traditional favicon.
         - size: 16x16 or 32x32
         - transparency is OK
         - see wikipedia for info on browser support: http://mky.be/favicon/ -->
        <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">

        <!-- CSS -->
        {{ HTML::style('css/bootstrap.min.css') }}

        <style>
            body {
                padding: 50px 0;
                background:#ddd;
            }
            #main
            {
                padding:100px 50px;
                background: #efefef;
            }
        </style>

        @yield('styles')

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    </head>

    <body>
        <!-- Container -->
        <div class="container">
            <!-- Navbar -->
            <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    {{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@getIndex', 'Coffee demo', [], ['class'=>'navbar-brand']) }}
                    {{--<a class="navbar-brand" href="/">Coffee test</a>--}}
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                        <li>{{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@getIndex', 'Home') }}</li>
                        <li>
                            {{--<a href="/setup">Setup</a>--}}
                            {{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@getSetup', 'Setup') }}
                        </li>
                        <li>{{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@getWipe', 'Wipe') }}</li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav>
            <!-- ./ navbar -->

            <!-- Content -->
            <div class="container" id="main">
                @yield('content')

                <br /><br /><br /><br />

                {{ $demo->source() }}
            </div>
            <!-- ./ content -->

            <!-- Footer -->
            <footer class="clearfix">
                @yield('footer')
            </footer>
            <!-- ./ Footer -->

        </div>
        <!-- ./ container -->

        <!-- Javascripts -->
        <script src="/js/jquery-1.10.2.min.js"></script>
        @yield('scripts')


    </body>

</html>
