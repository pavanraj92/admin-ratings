@extends('admin::admin.layouts.master')

@section('title', 'Ratings Management')

@section('page-title', 'Rating Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.ratings.index') }}">Rating Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rating Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Email Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">                    
                    <div class="table-responsive">
                         <div class="card-body">      
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">User</th>
                                        <td scope="col">
                                            @if ($rating->user)
                                                {{ $rating?->user?->name ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>                      
                                    <tr>
                                        <th scope="row">Product</th>
                                        <td scope="col">
                                            @if ($rating->product)
                                                {{ $rating?->product?->name ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td scope="col"> {!! config('rating.constants.aryStatusLabel.' . $rating->status, 'N/A') !!}</td>
                                    </tr>    
                                    <tr>
                                        <th scope="row">Rating</th>
                                        <td scope="col">{!! $rating->getStarRatingHtml() !!}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Review</th>
                                        <td scope="col">{!! $rating->review !!}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Created At</th>
                                        <td scope="col"> {{ $rating->created_at
                                            ? $rating->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                            : '—' }}</td>
                                    </tr>                                
                                </tbody>
                            </table>   
                                             
                            <a href="{{ route('admin.ratings.index') }}" class="btn btn-secondary">Back</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End rating Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
