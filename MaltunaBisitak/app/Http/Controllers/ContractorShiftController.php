<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractorShiftRequest;
use App\Models\Contractor;
use App\Models\ContractorShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractorShiftController extends Controller
{
    public function index(): View
    {
        return view('contractor-shifts.index', [
            'activeShifts' => ContractorShift::active()->with('contractor')->orderBy('checked_in_at')->get(),
            'finishedShifts' => ContractorShift::whereNotNull('checked_out_at')
                ->with('contractor')
                ->orderByDesc('checked_in_at')
                ->paginate(25),
        ]);
    }

    /**
     * Check-in form, prefilled with a returning contractor when ?langilea={id} is given.
     */
    public function create(Request $request): View
    {
        return view('contractor-shifts.create', [
            'contractor' => Contractor::find($request->query('langilea')) ?? new Contractor,
            'now' => now()->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(StoreContractorShiftRequest $request): RedirectResponse
    {
        $contractor = Contractor::updateOrCreate(
            $request->safe()->only(['first_name', 'last_name', 'phone']),
            $request->safe()->only(['company']),
        );

        $signaturePath = 'signatures/'.Str::uuid().'.png';
        Storage::disk('local')->put($signaturePath, $request->signatureImage());

        $shift = new ContractorShift([
            'checked_in_at' => $request->validated('checked_in_at'),
            'needs_key' => $request->boolean('needs_key'),
            'signature_path' => $signaturePath,
        ]);
        $shift->contractor()->associate($contractor);
        $shift->registeredBy()->associate($request->user());
        $shift->save();

        return redirect()
            ->route('contractor-shifts.index')
            ->with('status', "{$contractor->full_name}: sarrera erregistratuta.");
    }

    public function checkOut(Request $request, ContractorShift $contractorShift): RedirectResponse
    {
        if ($contractorShift->checked_out_at) {
            return back()->with('error', 'Lanaldi honen irteera markatuta zegoen jada.');
        }

        $request->validate(['key_returned' => ['boolean']]);

        $contractorShift->update([
            'checked_out_at' => now(),
            'key_returned' => $contractorShift->needs_key ? $request->boolean('key_returned') : null,
        ]);

        return redirect()
            ->route('contractor-shifts.index')
            ->with('status', "{$contractorShift->contractor->full_name}: irteera erregistratuta.");
    }

    public function signature(ContractorShift $contractorShift): StreamedResponse
    {
        abort_unless(
            $contractorShift->signature_path && Storage::disk('local')->exists($contractorShift->signature_path),
            404,
        );

        return Storage::disk('local')->response($contractorShift->signature_path, headers: [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
