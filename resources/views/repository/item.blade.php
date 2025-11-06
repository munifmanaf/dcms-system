@extends('layouts.app')

@section('title', $item->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $item->title }}</h3>
                </div>
                <div class="card-body">
                    <!-- Dublin Core Metadata -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                @if(isset($item->metadata['dc_creator']))
                                <tr>
                                    <th class="text-muted">Authors:</th>
                                    <td>{{ implode(', ', $item->metadata['dc_creator']) }}</td>
                                </tr>
                                @endif
                                @if(isset($item->metadata['dc_subject']))
                                <tr>
                                    <th class="text-muted">Subjects:</th>
                                    <td>
                                        @foreach($item->metadata['dc_subject'] as $subject)
                                        <span class="badge bg-light text-dark">{{ $subject }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                @if(isset($item->metadata['dc_date_issued']))
                                <tr>
                                    <th class="text-muted">Date:</th>
                                    <td>{{ $item->metadata['dc_date_issued'][0] }}</td>
                                </tr>
                                @endif
                                @if(isset($item->metadata['dc_publisher']))
                                <tr>
                                    <th class="text-muted">Publisher:</th>
                                    <td>{{ $item->metadata['dc_publisher'][0] }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Description -->
                    @if(isset($item->metadata['dc_description']))
                    <div class="mt-3">
                        <h5>Description</h5>
                        <p>{{ $item->metadata['dc_description'][0] }}</p>
                    </div>
                    @endif

                    <!-- File Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h5>
                            <i class="fas fa-file-pdf text-danger"></i> 
                            Document File
                        </h5>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection