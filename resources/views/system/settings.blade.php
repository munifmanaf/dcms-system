@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>
                        System Settings
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('system.settings.update') }}" method="POST">
                        @csrf @method('PUT')
                        
                        <!-- General Settings -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-globe mr-2"></i>
                                    General Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Site Name *</label>
                                            <input type="text" name="site_name" class="form-control" 
                                                   value="{{ $settings['general']['site_name'] }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Email *</label>
                                            <input type="email" name="contact_email" class="form-control" 
                                                   value="{{ $settings['general']['contact_email'] }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Site Description</label>
                                            <textarea name="site_description" class="form-control" rows="2">{{ $settings['general']['site_description'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Items Per Page *</label>
                                            <input type="number" name="items_per_page" class="form-control" 
                                                   value="{{ $settings['general']['items_per_page'] }}" min="5" max="100" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Timezone *</label>
                                    <select name="timezone" class="form-control" required>
                                        @foreach($timezones as $tz)
                                        <option value="{{ $tz }}" {{ $settings['general']['timezone'] == $tz ? 'selected' : '' }}>
                                            {{ $tz }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Workflow Settings -->
                        <div class="card card-info card-outline mt-4">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-project-diagram mr-2"></i>
                                    Workflow Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="auto_assign_reviewers" class="form-check-input" id="auto_assign" 
                                                {{ $settings['workflow']['auto_assign_reviewers'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_assign">Auto-assign reviewers</label>
                                            <small class="form-text text-muted">Automatically assign technical and content reviewers</small>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="notify_submitter_on_approval" class="form-check-input" id="notify_approval" 
                                                {{ $settings['workflow']['notify_submitter_on_approval'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="notify_approval">Notify submitter on approval</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="allow_quick_approval" class="form-check-input" id="quick_approval" 
                                                {{ $settings['workflow']['allow_quick_approval'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="quick_approval">Allow quick approval</label>
                                            <small class="form-text text-muted">Managers can bypass workflow steps</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Max Review Days *</label>
                                            <input type="number" name="max_review_days" class="form-control" 
                                                   value="{{ $settings['workflow']['max_review_days'] }}" min="1" max="30" required>
                                            <small class="form-text text-muted">Send reminders after this many days</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- More sections for notifications, backup, etc. -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save All Settings
                            </button>
                            <a href="{{ route('system.settings') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection