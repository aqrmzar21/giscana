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
        $aidRecipients = \App\Models\AidRecipient::with(['village'])->latest('date')->paginate($perPage)->withQueryString();
        return $this->partialView('admin.aid-recipients.index', compact('aidRecipients'));
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
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'description' => 'nullable|string',
        ]);

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
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'description' => 'nullable|string',
        ]);

        $aidRecipient->update($validated);

        return redirect()->route('admin.aid-recipients.index')->with('success', 'Data Penerima Bantuan berhasil diperbarui.');
    }

    public function destroy(\App\Models\AidRecipient $aidRecipient)
    {
        $aidRecipient->delete();
        return redirect()->route('admin.aid-recipients.index')->with('success', 'Data Penerima Bantuan berhasil dihapus.');
    }
}
