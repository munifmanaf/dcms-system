@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Export Data</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.export.process') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="data_type">Select Data to Export:</label>
                            <select name="data_type" id="data_type" class="form-control">
                                <option value="collections">Collections</option>
                                <option value="items">Items</option>
                                <option value="users">Users</option>
                                <option value="statistics">Statistics</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="format">Export Format:</label>
                            <select name="format" id="format" class="form-control">
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection