@extends('admin::admin.layouts.master')

@section('title', 'Ratings Management')

@section('page-title', 'Rating Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a href="{{ route('admin.ratings.index') }}">Manage Ratings</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Rating Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header with Back button -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title mb-0">Rating #{{ $rating->rating ?? 'N/A' }}</h4>
                            <div>
                                <a href="{{ route('admin.ratings.index') }}" class="btn btn-secondary ml-2">
                                    Back
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Rating Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Rating Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">User:</label>
                                                    <p>
                                                        @if (class_exists(\admin\users\Models\User::class))
                                                            {{ $rating?->user?->full_name ?? 'N/A' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">
                                                        @if (class_exists(\admin\products\Models\Product::class))
                                                            Product:
                                                        @elseif(class_exists(\admin\courses\Models\Course::class))
                                                            Course:
                                                        @else
                                                            Item:
                                                        @endif
                                                    </label>
                                                    <p>
                                                        @if (class_exists(\admin\products\Models\Product::class))
                                                            {{ $rating?->product?->name ?? 'N/A' }}
                                                        @elseif(class_exists(\admin\courses\Models\Course::class))
                                                            {{ $rating?->course?->title ?? 'N/A' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Status:</label>
                                                    <p>{!! config('rating.constants.aryStatusLabel.' . $rating->status, 'N/A') !!}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Created At:</label>
                                                    <p>
                                                        {{ $rating->created_at ? $rating->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s') : '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Rating:</label>
                                                    <p>{!! $rating->getStarRatingHtml() !!}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Review:</label>
                                                    <p>{!! $rating->review !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column">
                                            @admincan('ratings_manager_edit')
                                                <a href="{{ route('admin.ratings.edit', $rating) }}"
                                                    class="btn btn-warning mb-2">
                                                    <i class="mdi mdi-pencil"></i> Edit Rating
                                                </a>
                                            @endadmincan

                                            @admincan('ratings_manager_delete')
                                                <button type="button" class="btn btn-danger delete-btn delete-record"
                                                    title="Delete this record"
                                                    data-url="{{ route('admin.ratings.destroy', $rating) }}"
                                                    data-redirect="{{ route('admin.ratings.index') }}"
                                                    data-text="Are you sure you want to delete this rating?"
                                                    data-method="DELETE">
                                                    <i class="mdi mdi-delete"></i> Delete Rating
                                                </button>
                                            @endadmincan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- row end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Container fluid  -->
@endsection
