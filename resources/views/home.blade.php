@extends('layouts.app')

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Loan Review</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
            text-align: center;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
            font-weight: bold;
        }
    </style>
</head>

@section('heading')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div style="width:1000px;">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="content">
                        <table width="100%" border-spacing: 20px 0;>
                            <tr>
                                <td>
                                    <div class="title m-b-md" style="text-align:center">
                                        Loan Review
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="links" style="text-align:center">
                                        <a href="https://laravel.com/docs">Documentation</a>
                                        <a href="https://laracasts.com">Laracasts</a>
                                        <a href="https://laravel-news.com">News</a>
                                        <a href="https://nova.laravel.com">Nova</a>
                                        <a href="https://forge.laravel.com">Forge</a>
                                        <a href="https://github.com/laravel/laravel">GitHub</a>
                                        <a href="/about">About</a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection



