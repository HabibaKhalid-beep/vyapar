<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'my-companies');
        $companies = Auth::user()->companies; 
        return view('dashboard.company.index', compact('companies', 'tab'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $company = Auth::user()->companies()->create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Company created!');
    }

    public function switchCompany(Company $company)
    {
        // Security check: ensure user owns the company
        if ($company->user_id !== Auth::id()) { abort(403); }

        session(['current_company_id' => $company->id]);
        session(['current_company_name' => $company->name]);

        return redirect()->route('dashboard'); 
    }

    public function rename(Request $request, Company $company)
    {
        if ($company->user_id !== Auth::id()) { abort(403); }

        $request->validate(['name' => 'required|string|max:255']);
        $company->update(['name' => $request->name]);

        if (session('current_company_id') == $company->id) {
            session(['current_company_name' => $company->name]);
        }
          if ($request->expectsJson()) {
        return response()->json(['success' => true]);
    }

        return redirect()->back()->with('success', 'Company renamed!');
    }

    public function destroy(Company $company)
    {
        if ($company->user_id !== Auth::id()) { abort(403); }
        
        $company->delete();

        if (session('current_company_id') == $company->id) {
            session()->forget(['current_company_id', 'current_company_name']);
        }

        return redirect()->back();
    }
    
}