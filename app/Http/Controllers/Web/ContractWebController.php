<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Office;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractWebController extends Controller
{
    public function __construct(private ContractService $contracts) {}

    private function office(Request $request): Office
    {
        $office = $request->user()->office;
        abort_unless($office, 403, 'هذه المنطقة مخصّصة للمكاتب العقارية.');
        return $office;
    }

    public function index(Request $request)
    {
        $office = $this->office($request);

        return view('dashboard.contracts', [
            'office'    => $office,
            'contracts' => $office->contracts()->with('house.district')->latest()->paginate(10),
            'dueAlerts' => $this->contracts->dueAlerts($office, 30),
        ]);
    }

    public function create(Request $request)
    {
        $office = $this->office($request);

        return view('dashboard.contract-create', [
            'office' => $office,
            'houses' => $office->houses()->with('district')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $office = $this->office($request);

        $data = $request->validate([
            'house_id'          => ['required', 'exists:houses,id'],
            'type'              => ['required', 'in:rent,sale'],
            'party_name'        => ['required', 'string', 'max:120'],
            'party_phone'       => ['nullable', 'string', 'max:30'],
            'party_national_id' => ['nullable', 'string', 'max:40'],
            'amount'            => ['required', 'numeric', 'min:1'],
            'payment_cycle'     => ['required', 'in:once,monthly,quarterly,yearly'],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ]);

        // Ensure the house belongs to this office.
        abort_unless($office->houses()->where('id', $data['house_id'])->exists(), 403);

        $contract = $this->contracts->create($office, $data);

        return redirect()->route('dashboard.contracts')
            ->with('success', "تم إنشاء العقد {$contract->reference} وتوليد ملف PDF بنجاح ✅");
    }

    public function download(Request $request, Contract $contract)
    {
        $office = $this->office($request);
        abort_unless($contract->office_id === $office->id, 403);

        return $this->contracts->download($contract);
    }
}
