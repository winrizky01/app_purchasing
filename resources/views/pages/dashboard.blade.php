@extends('layout.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Website Analytics -->
        <div class="col-lg-6 mb-4">
            <div class="swiper-container swiper-container-horizontal swiper swiper-card-advance-bg"
                id="swiper-with-pagination-cards">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-white mb-0 mt-2">Website Analytics</h5>
                                <small>Total 28.5% Conversion Rate</small>
                            </div>
                            <div class="row">
                                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                                    <h6 class="text-white mt-0 mt-md-3 mb-3">Traffic</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">28%</p>
                                                    <p class="mb-0">Sessions</p>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">1.2k</p>
                                                    <p class="mb-0">Leads</p>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">3.1k</p>
                                                    <p class="mb-0">Page Views</p>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">12%</p>
                                                    <p class="mb-0">Conversions</p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                    <img src="{{ asset('template/assets/img/illustrations/card-website-analytics-1.png') }}"
                                        alt="Website Analytics" width="170" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-white mb-0 mt-2">Website Analytics</h5>
                                <small>Total 28.5% Conversion Rate</small>
                            </div>
                            <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                                <h6 class="text-white mt-0 mt-md-3 mb-3">Spending</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex mb-4 align-items-center">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">12h</p>
                                                <p class="mb-0">Spend</p>
                                            </li>
                                            <li class="d-flex align-items-center mb-2">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">127</p>
                                                <p class="mb-0">Order</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex mb-4 align-items-center">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">18</p>
                                                <p class="mb-0">Order Size</p>
                                            </li>
                                            <li class="d-flex align-items-center mb-2">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">2.3k</p>
                                                <p class="mb-0">Items</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                <img src="{{ asset('template/assets/img/illustrations/card-website-analytics-2.png') }}"
                                    alt="Website Analytics" width="170" class="card-website-analytics-img" />
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-white mb-0 mt-2">Website Analytics</h5>
                                <small>Total 28.5% Conversion Rate</small>
                            </div>
                            <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                                <h6 class="text-white mt-0 mt-md-3 mb-3">Revenue Sources</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex mb-4 align-items-center">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">268</p>
                                                <p class="mb-0">Direct</p>
                                            </li>
                                            <li class="d-flex align-items-center mb-2">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">62</p>
                                                <p class="mb-0">Referral</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex mb-4 align-items-center">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">890</p>
                                                <p class="mb-0">Organic</p>
                                            </li>
                                            <li class="d-flex align-items-center mb-2">
                                                <p class="mb-0 fw-semibold me-2 website-analytics-text-bg">1.2k</p>
                                                <p class="mb-0">Campaign</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                <img src="{{ asset('template/assets/img/illustrations/card-website-analytics-3.png') }}"
                                    alt="Website Analytics" width="170" class="card-website-analytics-img" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <!--/ Website Analytics -->

        <!-- Sales Overview -->
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Sales Overview</small>
                        <p class="card-text text-success">+18.2%</p>
                    </div>
                    <h4 class="card-title mb-1">$42.5k</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <span class="badge bg-label-info p-1 rounded"><i
                                        class="ti ti-shopping-cart ti-xs"></i></span>
                                <p class="mb-0">Order</p>
                            </div>
                            <h5 class="mb-0 pt-1 text-nowrap">62.2%</h5>
                            <small class="text-muted">6,440</small>
                        </div>
                        <div class="col-4">
                            <div class="divider divider-vertical">
                                <div class="divider-text">
                                    <span class="badge-divider-bg bg-label-secondary">VS</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                                <p class="mb-0">Visits</p>
                                <span class="badge bg-label-primary p-1 rounded"><i
                                        class="ti ti-link ti-xs"></i></span>
                            </div>
                            <h5 class="mb-0 pt-1 text-nowrap ms-lg-n3 ms-xl-0">25.5%</h5>
                            <small class="text-muted">12,749</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                        <div class="progress w-100" style="height: 8px">
                            <div class="progress-bar bg-info" style="width: 70%" role="progressbar"
                                aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 30%"
                                aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Sales Overview -->
    </div>
</div>
@endsection