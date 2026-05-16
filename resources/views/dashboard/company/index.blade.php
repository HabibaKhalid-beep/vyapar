@extends('layouts.app')

@section('content')
<style>
    /* Hide sidebar and navbar injected by components.js */
    #sidebar { display: none !important; }
    #topNavbar { display: none !important; }
    #topbar { display: none !important; }
    
    /* Make content full width */
    #mainContent { 
        margin-left: 0 !important; 
        width: 100% !important; 
        padding: 0 !important; 
    }
    
    body { background-color: #f1f3f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .company-card:hover { background-color: #f8f9fa !important; cursor: pointer; transition: 0.2s; }
    .btn-vyapar-primary { background-color: #007bff; color: white; border-radius: 4px; font-weight: 500; border: none; }
    .btn-vyapar-primary:hover { background-color: #0056b3; color: white; }
    .btn-vyapar-outline { border: 1px solid #007bff; color: #007bff; border-radius: 4px; background: white; transition: 0.3s; }
    .btn-vyapar-outline:hover { background-color: #007bff; color: white; }
    .search-input::placeholder { color: rgba(255,255,255,0.6); }
    .nav-tabs .nav-link.active { 
        background: transparent !important; 
        color: white !important; 
        border-bottom: 3px solid white !important; 
    }
</style>

<div class="company-selection-wrapper" style="min-height: 100vh; padding-top: 50px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
                
                <div class="card border-0 shadow-sm" style="border-radius: 4px; overflow: hidden;">
                    <div class="card-header d-flex justify-content-between align-items-center px-4 py-3" 
                         style="background-color: #1a2233; color: white; border: none;">
                        <h5 class="mb-0" style="font-weight: 600; font-size: 1.1rem;">Company List</h5>
                        <div class="search-container" style="position: relative; width: 300px;">
                            <i class="fa fa-search" style="position: absolute; left: 15px; top: 12px; color: #adb5bd; z-index: 10;"></i>
                            <input type="text" class="form-control search-input" id="companySearch" placeholder="Search Company" 
                                   style="background: rgba(255,255,255,0.1); border: none; border-radius: 20px; padding-left: 40px; color: white; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <ul class="nav nav-tabs px-3" style="background-color: #1a2233; border: none;">
                            <li class="nav-item">
                                <a class="nav-link px-4 {{ $tab == 'shared' ? 'active' : '' }}" href="?tab=shared" 
                                   style="color: #adb5bd; border: none; font-size: 0.85rem;">Companies Shared with Me</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4 {{ $tab != 'shared' ? 'active' : '' }}" href="?tab=my-companies" 
                                   style="color: #adb5bd; border: none; font-size: 0.85rem;">My Companies</a>
                            </li>
                        </ul>

                        <div class="p-4" style="background-color: #f1f3f6; min-height: 380px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                    {{ $tab == 'shared' ? 'List of companies others shared with you' : 'Below are the company that are created by you' }}
                                </p>
                                <div class="d-flex align-items-center gap-3">
                                    <a href="#" class="text-decoration-none" style="font-size: 0.85rem; color: #007bff;">Browse Files (.vyp)</a>
                                    <button class="btn btn-sm bg-white border shadow-sm" onclick="location.reload()" title="Refresh List">
                                        <i class="fa fa-sync-alt text-primary"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="companyListContainer">
                                @if($tab != 'shared')
                                    @forelse($companies as $company)
                                    <div class="company-card d-flex align-items-center bg-white mb-2 shadow-sm p-3" 
                                         style="border-radius: 4px; border-left: 4px solid #f39c12;">
                                        <div class="flex-grow-1">
                                            <span class="company-name" style="font-weight: 600; font-size: 0.95rem;">{{ $company->name }}</span>
                                            @if(session('current_company_id') == $company->id)
                                            <span style="color: #f39c12; font-size: 0.75rem; margin-left: 10px; font-weight: 600;">
                                                <i class="fa fa-circle" style="font-size: 8px; vertical-align: middle;"></i> Current Company
                                            </span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="sync-status px-3 d-flex align-items-center" style="border-right: 1px solid #eee; margin-right: 15px;">
                                                <i class="fa fa-laptop-code text-muted me-2"></i>
                                                <small style="color: #adb5bd; font-weight: 800; font-size: 0.65rem; letter-spacing: 0.5px;">SYNC OFF</small>
                                            </div>

                                            <form action="{{ route('company.switch', $company->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-vyapar-outline px-4 py-1 me-2">Open</button>
                                            </form>

                                            <div class="dropdown">
                                                <button class="btn text-muted p-0" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                               <ul class="dropdown-menu dropdown-menu-end shadow border-0">
    <li>
        <form action="{{ route('company.switch', $company->id) }}" method="POST">
            @csrf
            <button class="dropdown-item py-2"><i class="fa fa-folder-open me-2"></i> Open</button>
        </form>
    </li>
    <li>
        <a class="dropdown-item py-2" href="#" onclick="openRenameModal({{ $company->id }}, '{{ $company->name }}')">
            <i class="fa fa-edit me-2"></i> Rename Company
        </a>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
        <form action="{{ route('company.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Delete this company?')">
            @csrf @method('DELETE')
            <button class="dropdown-item py-2 text-danger"><i class="fa fa-trash-can me-2"></i> Delete</button>
        </form>
    </li>
</ul>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-5">
                                        <p class="text-muted">No companies found. Create one to get started!</p>
                                    </div>
                                    @endforelse
                                @else
                                    <div class="text-center py-5 mt-4">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" style="width: 100px; opacity: 0.5; filter: grayscale(1);">
                                        <p class="text-muted mt-3" style="font-weight: 500;">No Shared Companies to Show</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Currently logged in with Phone:</small>
                           <span style="font-weight: 600; font-size: 0.9rem;">{{ auth()->user()->phone ?? auth()->user()->email }}</span>
                        </div>
                        <div class="d-flex gap-3">
                            <button class="btn btn-vyapar-outline px-4" onclick="alert('Select a backup file to restore')">Restore backup</button>
                            <button class="btn btn-vyapar-primary px-4" data-bs-toggle="modal" data-bs-target="#newCompanyModal">New Company</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between px-2 align-items-start">
                    <p style="color: #adb5bd; font-size: 0.72rem; line-height: 1.4; max-width: 60%">
                        Note: These companies are not owned by you and are only available for working when internet connection is available.
                    </p>
                    <div class="text-end">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                           class="text-primary text-decoration-none d-block" style="font-weight: 600; font-size: 0.85rem;">Logout</a>
                        <small class="text-muted" style="font-size: 0.65rem;">Logging out will stop syncing data</small>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.company.modals')

<script>
    // 1. Working Search Functionality
    document.getElementById('companySearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let cards = document.querySelectorAll('.company-card');
        
        cards.forEach(card => {
            let name = card.querySelector('.company-name').textContent;
            if (name.toUpperCase().indexOf(filter) > -1) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    });

    // 2. Open Rename Modal Logic
 function openRenameModal(id, name) {
    const form = document.getElementById('renameForm');
    form.action = `/dashboard/company/${id}/rename`;
    document.getElementById('rename_input_field').value = name;
    const modal = new bootstrap.Modal(document.getElementById('renameCompanyModal'));
    modal.show();
}
</script>
@endsection