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
                                                    @if (class_exists(\admin\users\Models\User::class))
                                                        {{ $rating?->user?->full_name ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(class_exists(\admin\products\Models\Product::class))
                                                        {{ $rating?->product?->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{!! $rating->getStarRatingHtml() !!}</td>
                                                <td>
                                                    @if($rating->status === 'pending')
                                                        <button type="button" 
                                                                class="btn btn-warning btn-sm status-btn" 
                                                                data-id="{{ $rating->id }}"
                                                                data-status="pending"
                                                                data-url="{{ route('admin.ratings.updateStatus') }}">
                                                            Pending
                                                        </button>
                                                    @elseif($rating->status === 'approved')
                                                        <button type="button" 
                                                                class="btn btn-success btn-sm" 
                                                                disabled>
                                                            Approved
                                                        </button>
                                                    @elseif($rating->status === 'rejected')
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm status-btn" 
                                                                data-id="{{ $rating->id }}"
                                                                data-status="rejected"
                                                                data-url="{{ route('admin.ratings.updateStatus') }}">
                                                            Rejected
                                                        </button>
                                                    @endif
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
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();
        });

        $(document).on('click', '.status-btn', function () {
            const $this = $(this);
            const ratingId = $this.data('id');
            const currentStatus = $this.data('status');
            const updateUrl = $this.data('url');

            if (currentStatus === 'pending') {
                // Show SWAL with Approve, Reject, Cancel buttons for pending status
                Swal.fire({
                    title: 'Rating Status',
                    text: 'What would you like to do with this rating?',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Approve',
                    denyButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    denyButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        confirmButton: 'btn btn-outline-success',
                        denyButton: 'btn btn-outline-danger',
                        cancelButton: 'btn btn-outline-secondary',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Approve action
                        updateRatingStatus(ratingId, 'approved', updateUrl, $this);
                    } else if (result.isDenied) {
                        // Reject action
                        updateRatingStatus(ratingId, 'rejected', updateUrl, $this);
                         
                    }
                    // Cancel action - do nothing, just close the popup
                });
            } else if (currentStatus === 'rejected') {
                // Show SWAL with Approve and Cancel buttons for rejected status
                Swal.fire({
                    title: 'Rating Status',
                    text: 'Would you like to approve this rejected rating?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Approve',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        confirmButton: 'btn btn-outline-success',
                        cancelButton: 'btn btn-outline-secondary',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Approve action
                        updateRatingStatus(ratingId, 'approved', updateUrl, $this);
                    }
                    // Cancel action - do nothing, just close the popup
                });
            }
        });

        function updateRatingStatus(ratingId, newStatus, updateUrl, $button) {
            $.post(updateUrl, {
                _token: '{{ csrf_token() }}',
                id: ratingId,
                status: newStatus
            }, function (response) {
                Swal.fire('Updated!', response.message, 'success');
                
                // Update the button based on new status
                if (newStatus === 'approved') {
                    $button.replaceWith('<button type="button" class="btn btn-success btn-sm" disabled>Approved</button>');
                } else if (newStatus === 'rejected') {
                    $button.replaceWith('<button type="button" class="btn btn-danger btn-sm status-btn" data-id="' + ratingId + '" data-status="rejected" data-url="' + updateUrl + '">Rejected</button>');
                } else if (newStatus === 'pending') {
                    $button.replaceWith('<button type="button" class="btn btn-warning btn-sm status-btn" data-id="' + ratingId + '" data-status="pending" data-url="' + updateUrl + '">Pending</button>');
                }
            }).fail(function () {
                Swal.fire('Error!', 'Status could not be updated.', 'error');
            });
        }
    </script>
@endpush