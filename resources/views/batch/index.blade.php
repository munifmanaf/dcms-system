@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Operations</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-export"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Export Data</span>
                                    <a href="{{ route('batch.export') }}" class="btn btn-sm btn-info">Go to Export</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-file-import"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Import Data</span>
                                    <a href="{{ route('batch.import') }}" class="btn btn-sm btn-success">Go to Import</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection