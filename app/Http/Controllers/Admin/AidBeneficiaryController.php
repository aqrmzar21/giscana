<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidBeneficiary;
use App\Models\AidDisaster;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;

class AidBeneficiaryController extends Controller
{
    use PartialRenderable;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = AidBeneficiary::with(['district', 'village'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('identity_card_number', 'like', "%{$search}%")
                  ->orWhereHas('village', fn ($vq) => $vq->where('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('district', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('aid_status')) {
            $query->where('aid_status', $status);
        }

        $beneficiaries = $query->paginate($perPage)->withQueryString();
        return $this->partialView('admin.aid-beneficiaries.index', compact('beneficiaries'));
    }

    public function print(Request $request)
    {
        abort_if(!auth()->user()->can('export data'), 403);
        $query = AidBeneficiary::with(['district', 'village'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('identity_card_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('aid_status')) {
            $query->where('aid_status', $status);
        }

        $beneficiaries = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.aid-beneficiaries.pdf', compact('beneficiaries'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-penerima-bantuan.pdf');
    }

    public function create()
    {
        $districts = District::with('villages')->orderBy('name')->get();
        return $this->partialView('admin.aid-beneficiaries.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id'          => 'required|exists:districts,id',
            'village_id'           => 'required|exists:villages,id',
            'recipient_name'       => 'required|string|max:255',
            'identity_card_number' => 'nullable|string|max:50',
            'address_detail'       => 'nullable|string|max:500',
            'aid_status'           => 'nullable|in:pending,received',
        ]);

        $validated['aid_status'] = $validated['aid_status'] ?? 'pending';

        AidBeneficiary::create($validated);

        return redirect()->route('admin.aid-beneficiaries.index')
            ->with('success', 'Data warga penerima bantuan berhasil ditambahkan.');
    }

    public function show(AidBeneficiary $aidBeneficiary)
    {
        $aidBeneficiary->load(['district', 'village', 'distributions.aidInventory']);
        return $this->partialView('admin.aid-beneficiaries.show', compact('aidBeneficiary'));
    }

    public function edit(AidBeneficiary $aidBeneficiary)
    {
        abort_if(!auth()->user()->can('update data'), 403);
        $districts = District::with('villages')->orderBy('name')->get();
        return $this->partialView('admin.aid-beneficiaries.edit', compact('aidBeneficiary', 'districts'));
    }

    public function update(Request $request, AidBeneficiary $aidBeneficiary)
    {
        abort_if(!auth()->user()->can('update data'), 403);

        $validated = $request->validate([
            'district_id'          => 'required|exists:districts,id',
            'village_id'           => 'required|exists:villages,id',
            'recipient_name'       => 'required|string|max:255',
            'identity_card_number' => 'nullable|string|max:50',
            'address_detail'       => 'nullable|string|max:500',
            'aid_status'           => 'nullable|in:pending,received',
        ]);

        $aidBeneficiary->update($validated);

        return redirect()->route('admin.aid-beneficiaries.index')
            ->with('success', 'Data warga penerima bantuan berhasil diperbarui.');
    }

    public function destroy(AidBeneficiary $aidBeneficiary)
    {
        abort_if(!auth()->user()->can('delete data'), 403);
        $aidBeneficiary->delete();

        return redirect()->route('admin.aid-beneficiaries.index')
            ->with('success', 'Data warga penerima bantuan berhasil dihapus.');
    }
}

