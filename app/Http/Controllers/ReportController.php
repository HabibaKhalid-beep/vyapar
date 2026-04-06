<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category; // 👈 ADD THIS

class ReportController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // 👈 ADD THIS

        return view('dashboard.report', compact('categories')); // 👈 PASS THIS
    }
}