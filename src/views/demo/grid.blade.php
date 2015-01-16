@extends('coffee::demo.master')

@section('content')
    <style>
        th.btn {
            border: 0 none;
            border-radius: 0;
            display: table-cell;
            font-weight: bold;
            /*width: 100%;*/
        }
    </style>

    {{ $grid }}

    <table class="table table-bordered table-striped">
        <colgroup class="data" span="4"/>
        <colgroup class="actions" span="1"/>
        <thead>
        <tr>

            <th class="btn">Title</th>
            <th class="btn">Views</th>
            <th class="btn">Time <i class="dropdown-toggle"></i></th>
            <th class="btn">Interest</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Row number 1</td>
            <td>4343</td>
            <td>6556</td>
            <td>3232</td>
            <td>
                <div>
                    <span class="btn btn-primary btn-xs">Edit</span>
                    <span class="btn btn-danger btn-xs">Delete</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Row number 2</td>
            <td>3423</td>
            <td>541</td>
            <td>765</td>
            <td>
                <span class="btn btn-primary btn-xs">Edit</span>
                <span class="btn btn-danger btn-xs">Delete</span>
            </td>
        </tr>
        <tr>
            <td>Row number 3</td>
            <td>4546</td>
            <td>2342</td>
            <td>766545</td>
            <td>
                <span class="btn btn-primary btn-xs">Edit</span>
                <span class="btn btn-danger btn-xs">Delete</span>
            </td>
        </tr>
        <tr>
            <td>Row number 4</td>
            <td>4545</td>
            <td>432</td>
            <td>3465</td>
            <td>
                <span class="btn btn-primary btn-xs">Edit</span>
                <span class="btn btn-danger btn-xs">Delete</span>
            </td>
        </tr>
        <tr>
            <td>Row number 5</td>
            <td>45</td>
            <td>75643</td>
            <td>12</td>
            <td>
                <span class="btn btn-primary btn-xs">Edit</span>
                <span class="btn btn-danger btn-xs">Delete</span>
            </td>
        </tr>
        <tr>
            <td>Row number 6</td>
            <td>34</td>
            <td>1</td>
            <td>67</td>
            <td>
                <span class="btn btn-primary btn-xs">Edit</span>
                <span class="btn btn-danger btn-xs">Delete</span>
            </td>
        </tr>
        </tbody>
    </table>

@stop
