@extends('layouts.app')
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>About page</title>

    <style>
        body {
            background: #fffefe;
        }

        .headline {
            max-width: 900px;
            margin: 2em auto;
            font-family: Helvetica, Arial, Verdana, "Verdana Ref", sans-serif;
        }

        .headline-title {
            margin: 0;
            font-size: 2rem;
            color: tomato;
        }

        .headline-text {
            margin: 0;
            color: #735e5a;
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

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

    </style>
</head>

@section('heading')
@endsection

@section('content')
    <div class="flex-center position-ref full-height">
        <div>
            <table border=2 width="900" align="center">
                <tr>
                    <td align="center">
                        <h1 class="headline-title">Ldap Test</h1>
                        <p class="headline-text">This is a test against a public LDAP server.</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

@endsection
