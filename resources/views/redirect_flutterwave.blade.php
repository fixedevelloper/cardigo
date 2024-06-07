@extends("base")
@section('content')
    <div class="nk-wrap nk-wrap-nosidebar">
        <!-- content @s -->
        <div class="nk-content ">
            <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                <div class="card">
                    <div class="card-inner card-inner-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title center">{!! config('app.name') !!}</h4>
                                <div class="nk-block-des">
                                </div>
                            </div>
                        </div>
                        @if($status=="cancelled")
                            <div class="text-center">
                                <img width="250" src="{!! asset("assets/images/error_.png") !!}">
                                <div class="nk-block-head-lg center">
                                    Payement Rejected
                                </div>
                            </div>

                        @else
                            <img src="{!! asset("assets/images/success_icon.png") !!}">
                            <div class="nk-block-head-lg center">
                                Payement success
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            <div class="nk-footer nk-auth-footer-full">
                <div class="container wide-lg">
                    <div class="row g-3">

                    </div>
                </div>
            </div>
        </div>
        <!-- wrap @e -->
    </div>
@endsection

