<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index()
    {
        $dashboard = $this->reportService->dashboardData();

        return view('admin.dashboard', compact('dashboard'));
    }
}
