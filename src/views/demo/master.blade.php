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
    {{ HTML::style('css/demo.css') }}

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
        <div class="container">
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                {{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@anyIndex', 'Coffee demo', [], ['class'=>'navbar-brand']) }}
                {{--<a class="navbar-brand" href="/">Coffee test</a>--}}
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>{{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@anyIndex', 'Home') }}</li>
                    <li>{{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@anyIndex', 'Custom view',['view'=>'custom']) }}</li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <li>
                        {{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@getSetup', 'Setup') }}
                    </li>
                    <li>{{ link_to_action('Tacone\Coffee\Demo\Controllers\DemoController@getWipe', 'Wipe') }}</li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <!-- ./ navbar -->



    <!-- Content -->
    <div class="container" id="main">

        <div class="row">
            <div class="col-xs-7">

                @yield('content')

            </div>
            <div class="col-xs-5">
                @section('debug')
                    <code>$form->toArray()</code>
                    <pre class="prettyprint">{{ json_encode($form->toArray(), JSON_PRETTY_PRINT ) }}</pre>
                    <code>$model->toArray()</code>
                    <pre class="prettyprint">{{ json_encode($form->source->unwrap()->toArray(), JSON_PRETTY_PRINT ) }}</pre>
                @show
            </div>
        </div>


        <br/><br/><br/><br/>

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
<script src="/js/prettify.js"></script>
<script>
    prettyPrint();
</script>
<div class="demo-form-debug">
@if ($form)
    {{--{{ \Kint::dump($form) }}--}}
@endif
</div>
<script>
    $(function(){
        $('.demo-form-debug').insertAfter('nav');
    });
</script>

@yield('scripts')


</body>

</html>
