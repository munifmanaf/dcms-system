@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import Data</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Select File:</label>
                            <input type="file" name="file" id="file" class="form-control-file" accept=".csv,.xlsx,.xls">
                            <small class="form-text text-muted">Supported formats: CSV, Excel</small>
                        </div>
                        <div class="form-group">
                            <label for="data_type">Import Type:</label>
                            <select name="data_type" id="data_type" class="form-control">
                                <option value="collections">Collections</option>
                                <option value="items">Items</option>
                                <option value="users">Users</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection