{{-- Header dengan toggle switch master --}}
<div class="hazard-panel-header">
    <div class="hazard-panel-title">
        <svg xmlns="http://www.w3.org/2000/svg" class="hazard-panel-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
        </svg>
        <span>Sebaran Kawasan Rawan Bencana</span>
    </div>
    {{-- Master Toggle Switch --}}
    <label class="hazard-master-toggle" id="master_toggle_label" title="Aktifkan/nonaktifkan semua layer">
        <input type="checkbox" id="toggle_all_hazard_layers">
        <span class="hazard-toggle-track">
            <span class="hazard-toggle-thumb"></span>
        </span>
        <span class="hazard-toggle-text" id="master_toggle_text">Semua</span>
    </label>
</div>

{{-- Hint teks --}}
<p class="hazard-panel-hint">Pilih satu jenis bencana untuk ditampilkan di peta</p>

{{-- Badge grid per bencana --}}
<div class="hazard-badges-grid" id="hazard_layer_checkboxes">
    <span class="hazard-loading">
        <svg class="hazard-loading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"/>
            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity="0.75"/>
        </svg>
        Memuat layer bencana...
    </span>
</div>