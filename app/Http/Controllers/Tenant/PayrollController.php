<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Services\Tenant\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(protected PayrollService $payrollService)
    {}

    /**
     * Display a listing of payroll periods.
     */
    public function index(): View
    {
        $periods = PayrollPeriod::latest()->paginate(10);
        return view('tenant.payroll.index', compact('periods'));
    }

    /**
     * Show form to create a new payroll period.
     */
    public function create(): View
    {
        return view('tenant.payroll.create');
    }

    /**
     * Store a new payroll period.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        PayrollPeriod::create($validated);

        return redirect()->route('tenant.payroll.index')
            ->with('success', 'Payroll period created.');
    }

    /**
     * Show the detailed payroll slips for a period.
     */
    public function show(PayrollPeriod $period): View
    {
        $period->load('payrolls.user');
        return view('tenant.payroll.show', compact('period'));
    }

    /**
     * Trigger computation for a period.
     */
    public function generate(PayrollPeriod $period): RedirectResponse
    {
        $this->payrollService->generateForPeriod($period);

        return back()->with('success', 'Payroll slips generated successfully.');
    }

    /**
     * Release payments for a period.
     */
    public function release(PayrollPeriod $period): RedirectResponse
    {
        $this->payrollService->releasePeriod($period);

        return back()->with('success', 'Payroll released and marked as paid.');
    }
}
