@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit mr-2"></i>
                        Edit User: {{ $user->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Users
                        </a>
                    </div>
                </div>
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" 
                                           placeholder="Enter full name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           placeholder="Enter email address" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">User Role *</label>
                                    <select name="role" id="role" 
                                            class="form-control @error('role') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User (Submitter)</option>
                                        <option value="technical_reviewer" {{ old('role', $user->role) == 'technical_reviewer' ? 'selected' : '' }}>Technical Reviewer</option>
                                        <option value="content_reviewer" {{ old('role', $user->role) == 'content_reviewer' ? 'selected' : '' }}>Content Reviewer</option>
                                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" name="password" id="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Leave blank to keep current password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Minimum 8 characters. Leave empty to keep current password.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" 
                                           placeholder="Confirm new password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account Status</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge badge-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                                            {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            Member since: {{ $user->created_at->format('M j, Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Statistics -->
                        <div class="alert alert-secondary mt-3">
                            <h6><i class="fas fa-chart-bar mr-2"></i>User Activity:</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="small-box bg-light p-2">
                                        <div class="inner">
                                            <h3>{{ $user->items()->count() }}</h3>
                                            <p>Items Created</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="small-box bg-light p-2">
                                        <div class="inner">
                                            <h3>{{ $user->workflowActions()->count() }}</h3>
                                            <p>Workflow Actions</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="small-box bg-light p-2">
                                        <div class="inner">
                                            <h3>{{ $user->created_at->diffForHumans() }}</h3>
                                            <p>Account Age</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        @if($user->id !== auth()->id())
                        <div class="alert alert-danger mt-3">
                            <h6><i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone</h6>
                            <p class="mb-2">Once you delete a user, there is no going back. Please be certain.</p>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.')">
                                    <i class="fas fa-trash mr-1"></i> Delete User
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-info-circle mr-2"></i>Note</h6>
                            <p class="mb-0">You cannot delete your own account while logged in.</p>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-default ml-2">Cancel</a>
                        
                        @if($user->id !== auth()->id())
                        <div class="float-right">
                            <small class="text-muted">
                                Last updated: {{ $user->updated_at->format('M j, Y g:i A') }}
                            </small>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Password strength indicator for edit page
    document.getElementById('password')?.addEventListener('input', function() {
        const password = this.value;
        if (password === '') return;
        
        const strength = checkPasswordStrength(password);
        let feedback = document.getElementById('password-feedback');
        
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.id = 'password-feedback';
            feedback.className = 'mt-1 small';
            this.parentNode.appendChild(feedback);
        }
        
        feedback.innerHTML = `Strength: <span class="text-${strength.color}">${strength.text}</span>`;
    });

    function checkPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;

        const levels = [
            { text: 'Very Weak', color: 'danger' },
            { text: 'Weak', color: 'danger' },
            { text: 'Fair', color: 'warning' },
            { text: 'Good', color: 'info' },
            { text: 'Strong', color: 'success' },
            { text: 'Very Strong', color: 'success' }
        ];

        return levels[Math.min(score, levels.length - 1)];
    }

    // Confirm role change for admin users
    document.getElementById('role')?.addEventListener('change', function() {
        if (this.value === 'admin') {
            if (!confirm('Are you sure you want to assign Administrator role? This user will have full system access.')) {
                this.value = '{{ $user->role }}';
            }
        }
    });
</script>
@endsection