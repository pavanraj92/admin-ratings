@extends('admin::admin.layouts.master')

@section('title', 'Ratings Management')

@section('page-title', 'Rating Manager')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Rating Manager</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start category Content -->
        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <h4 class="card-title">Filter</h4>
                    <form action="{{ route('admin.ratings.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">Keyword</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control"
                                        value="{{ app('request')->query('keyword') }}" placeholder="Enter user or product">                                   
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach(config('rating.constants.status') as $key => $label)
                                            <option value="{{ $key }}" {{ app('request')->query('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                            
                            <div class="col-auto mt-1 text-right">
                                <div class="form-group">
                                    <label for="created_at">&nbsp;</label>
                                    <button type="submit" form="filterForm" class="btn btn-primary mt-4">Filter</button>
                                    <a href="{{ route('admin.ratings.index') }}" class="btn btn-secondary mt-4">Reset</a>
                                </div>
                            </div>
                        </div>                       
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">                   
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">S. No.</th>
                                        <th scope="col">@sortablelink('user_id', 'User', [], ['style' => 'color: #4F5467; text-decoration: none;'])</th>
                                        <th scope="col">@sortablelink('product_id', 'Product', [], ['style' => 'color: #4F5467; text-decoration: none;'])</th>
                                        <th scope="col">@sortablelink('rating', 'Rating', [], ['style' => 'color: #4F5467; text-decoration: none;'])</th>
                                        <th scope="col">@sortablelink('status', 'Status', [], ['style' => 'color: #4F5467; text-decoration: none;'])</th>
                                        <th scope="col">@sortablelink('created_at', 'Created At', [], ['style' => 'color: #4F5467; text-decoration: none;'])</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($ratings) && $ratings->count() > 0)
                                        @php
                                            $i = ($ratings->currentPage() - 1) * $ratings->perPage() + 1;
                                        @endphp
                                        @foreach ($ratings as $rating)
                                            <tr>
                                                <th scope="row">{{ $i }}</th>
                                                <td>
                                                    @if($rating->user)
                                                        {{ $rating?->user?->full_name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($rating->product)
                                                        {{ $rating?->product?->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{!! $rating->getStarRatingHtml() !!}</td>
                                                <td>
                                                    <!-- create update status functionality-->
                                                    @php
                                                        $statusOptions = config('rating.constants.status');
                                                    @endphp

                                                    <select class="form-control status-dropdown"
                                                            data-id="{{ $rating->id }}"
                                                            data-url="{{ route('admin.ratings.updateStatus') }}">
                                                        @foreach ($statusOptions as $value => $label)
                                                            <option value="{{ $value }}" {{ $rating->status === $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </td>
                                                <td>
                                                    {{ $rating->created_at
                                                        ? $rating->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                                        : '—' }}
                                                </td>
                                                <td style="width: 10%;">
                                                    @admincan('ratings_manager_view')
                                                    <a href="{{ route('admin.ratings.show', $rating) }}" 
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="View this record"
                                                        class="btn btn-warning btn-sm"><i class="mdi mdi-eye"></i></a>
                                                    @endadmincan
                                                    @admincan('ratings_manager_delete')
                                                    <a href="javascript:void(0)" 
                                                        data-toggle="tooltip" 
                                                        data-placement="top"
                                                        title="Delete this record" 
                                                        data-url="{{ route('admin.ratings.destroy', $rating) }}"
                                                        data-text="Are you sure you want to delete this record?"                                                    
                                                        data-method="DELETE"
                                                        class="btn btn-danger btn-sm delete-record" ><i class="mdi mdi-delete"></i></a>
                                                    @endadmincan
                                                </td>
                                            </tr>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <!--pagination move the right side-->
                            @if ($ratings->count() > 0)
                                {{ $ratings->links('admin::pagination.custom-admin-pagination') }}
                            @endif                        
                            
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- End rating Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom CSS for the page -->
    <link rel="stylesheet" href="{{ asset('backend/custom.css') }}">           
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).on('change', '.status-dropdown', function () {
            const $this = $(this);
            const newStatus = $this.val();
            const oldStatus = $this.data('original') || $this.find('option[selected]').val(); // Optional
            const ratingId = $this.data('id');
            const updateUrl = $this.data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to change the status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(updateUrl, {
                        _token: '{{ csrf_token() }}',
                        id: ratingId,
                        status: newStatus
                    }, function (response) {
                        Swal.fire('Updated!', response.message, 'success');
                    }).fail(function () {
                        Swal.fire('Error!', 'Status could not be updated.', 'error');
                    });
                } else {
                    // Optionally reset to previous value
                    $this.val(oldStatus);
                }
            });
        });
    </script>
@endpush