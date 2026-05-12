<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AidRecipientController extends Controller
{
    use \App\Http\Traits\PartialRenderable;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = \App\Models\AidRecipient::with(['village'])->latest('date');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('aid_type', 'like', "%{$search}%")
                  ->orWhereHas('village', function ($vq) use ($search) {
                      $vq->where('yard', 'like', "%{$search}%")
                         ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
        }
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }

        $aidRecipients = $query->paginate($perPage)->withQueryString();
        return $this->partialView('admin.aid-recipients.index', compact('aidRecipients'));
    }

    public function print(Request $request)
    {
        $query = \App\Models\AidRecipient::with(['village'])->latest('date');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('aid_type', 'like', "%{$search}%")
                  ->orWhereHas('village', function ($vq) use ($search) {
                      $vq->where('yard', 'like', "%{$search}%")
                         ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
        }
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }

        $aidRecipients = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.aid-recipients.pdf', compact('aidRecipients'))
               ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Penerima-Bantuan-' . date('Y-m-d') . '.pdf');
    }

    public function create()
    {
        $districts = \App\Models\District::with('villages')->get();
        return $this->partialView('admin.aid-recipients.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'aid_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'recipient_name' => 'required|string|max:255',
            'village_id' => 'required|exists:villages,id',
            'description' => 'nullable|string',
        ]);

        $village = \App\Models\Village::with('district')->find($validated['village_id']);
        if ($village && $village->district) {
            $aidDisaster = \App\Models\AidDisaster::where('district_name', $village->district->name)->first();
            $validated['aid_disaster_id'] = $aidDisaster ? $aidDisaster->id : null;
            $validated['name'] = $village->district->name;
        }

        \App\Models\AidRecipient::create($validated);

        return redirect()->route('admin.aid-recipients.index')->with('success', 'Data Penerima Bantuan berhasil ditambahkan.');
    }

    public function show(\App\Models\AidRecipient $aidRecipient)
    {
        $aidRecipient->load(['village']);
        return $this->partialView('admin.aid-recipients.show', compact('aidRecipient'));
    }

    public function edit(\App\Models\AidRecipient $aidRecipient)
    {
        $districts = \App\Models\District::with('villages')->get();
        return $this->partialView('admin.aid-recipients.edit', compact('aidRecipient', 'districts'));
    }

    public function update(Request $request, \App\Models\AidRecipient $aidRecipient)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'aid_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'recipient_name' => 'required|string|max:255',
            'village_id' => 'required|exists:villages,id',
            'description' => 'nullable|string',
        ]);

        $village = \App\Models\Village::with('district')->find($validated['village_id']);
        if ($village && $village->district) {
            $aidDisaster = \App\Models\AidDisaster::where('district_name', $village->district->name)->first();
            $validated['aid_disaster_id'] = $aidDisaster ? $aidDisaster->id : null;
            $validated['name'] = $village->district->name;
        }

        $aidRecipient->update($validated);

        return redirect()->route('admin.aid-recipients.index')->with('success', 'Data Penerima Bantuan berhasil diperbarui.');
    }

    public function destroy(\App\Models\AidRecipient $aidRecipient)
    {
        $aidRecipient->delete();
        return redirect()->route('admin.aid-recipients.index')->with('success', 'Data Penerima Bantuan berhasil dihapus.');
    }
}
