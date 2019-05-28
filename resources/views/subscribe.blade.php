@extends('layouts.app')

@section('content')
    @if(!empty($status))
        <p class="alert" style="text-align: center">Dear user we got your subscription request. All are in review and approval state. </p>
    @endif
    <div class="container">
        <div class="row">
            @if(!empty($hrefs))
                @foreach($hrefs as $href)
                    <a href="{{$href}}">click to approve</a>
                @endforeach
            @endif

            <form action="/home" method="post">
                {!! csrf_field() !!}
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Subscription plans</div>

                    <div class="panel-body">


                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card text-white bg-primary" style="border: black 1px solid;padding: 10px;">
                                                <h5 class="card-header">
                                                    <input type="checkbox" name="plan[]" value="P-8NK56774469222946LTV3TJQ"> Video Streaming Service<br>
                                                </h5>
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        Card content
                                                    </p>
                                                </div>
                                                <div class="card-footer">
                                                    Card footer
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card text-white bg-success" style="border: black 1px solid; padding: 10px;">
                                                <h5 class="card-header">
                                                    <input type="checkbox" name="plan[]" value="P-1B685724E24186004LTV3XAI"> Blog
                                                </h5>
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        Card content
                                                    </p>
                                                </div>
                                                <div class="card-footer">
                                                    Card footer
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card text-white bg-info" style="border: black 1px solid; padding: 10px;">
                                                <h5 class="card-header">
                                                    <input type="checkbox" name="plan[]" value="P-7AA56909AG4292057LTV3XSY"> AudioBooks<br>
                                                </h5>
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        Card content
                                                    </p>
                                                </div>
                                                <div class="card-footer">
                                                    Card footer
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-info " name="subscribe_button"> Pay </button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            </form>

        </div>
    </div>
@endsection
