@extends('layouts.backend.master')
@section('title', 'Schedules')
@section('page', 'Schedules')
@section('breadcrumb')
    <!--begin::Breadcrumb-->
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('backend.dashboard') }}" class="text-muted text-hover-primary">
                Home </a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-300 w-5px h-2px"></span>
        </li>
        <!--end::Item-->

        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('backend.schedules.index') }}" class="text-muted text-hover-primary">Schedules</a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-300 w-5px h-2px"></span>
        </li>
        <!--end::Item-->

        <!--begin::Item-->
        <li class="breadcrumb-item text-dark">
            All Schedules
        </li>
        <!--end::Item-->
    </ul>
    <!--end::Breadcrumb-->
@endsection
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <div class="card card-flush">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                        rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                    <path
                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <input type="text" name="keywords" class="form-control form-control-solid w-250px ps-15"
                                placeholder="Cari data..." />
                        </div>
                        <!--end::Search-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add schedule-->
                            <a href="{{ route('backend.schedules.create') }}" class="btn btn-primary">
                                Add Schedule
                            </a>
                            <!--end::Add schedule-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="datatables">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-50px">No</th>
                                        <th class="min-w-125px">Driver</th>
                                        <th class="min-w-125px">Route</th>
                                        <th class="min-w-125px">Departure Time</th>
                                        <th class="min-w-125px">Arrival Time</th>
                                        <th class="min-w-125px">Price</th>
                                        <th class="min-w-70px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-bold text-gray-600">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#datatables').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('backend.schedules.index') }}',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        defaultContent: '',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'driver_name',
                        name: 'driver_name',
                    },
                    {
                        data: 'route',
                        name: 'route'
                    },
                    {
                        data: 'departure_time',
                        name: 'departure_time'
                    },
                    {
                        data: 'arrival_time',
                        name: 'arrival_time'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            // set number for datatables
            $('#datatables').on('draw.dt', function() {
                var info = $('#datatables').DataTable().page.info();
                $('#datatables').DataTable().column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + info.start;
                });
            });

            $('input[name=keywords]').on('keyup', function() {
                console.log($(this).val());
                $('#datatables').DataTable().search($(this).val()).draw();
            });

            $('select[name=route]').on('change', function() {
                console.log($(this).val());
                $('#datatables').DataTable().column(2).search($(this).val()).draw();
            });
        });
    </script>
@endpush
