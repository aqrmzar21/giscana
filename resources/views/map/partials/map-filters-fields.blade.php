<div class="flex flex-wrap items-end gap-4">
    <div class="filter-group mb-0 min-w-[160px]">
        <label for="disaster_type" class="block text-xs font-medium text-gray-600">Jenis Bencana</label>
        <select id="disaster_type" name="disaster_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="all" {{ $disasterType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
            <option value="longsor" {{ $disasterType === 'longsor' ? 'selected' : '' }}>Longsor</option>
            <option value="banjir" {{ $disasterType === 'banjir' ? 'selected' : '' }}>Banjir</option>
        </select>
    </div>

    <div class="filter-group mb-0 min-w-[160px]">
        <label for="risk_level" class="block text-xs font-medium text-gray-600">Tingkat Risiko</label>
        <select id="risk_level" name="risk_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="all" {{ $riskLevel === 'all' ? 'selected' : '' }}>Semua Tingkat</option>
            <option value="low" {{ $riskLevel === 'low' ? 'selected' : '' }}>Rendah</option>
            <option value="medium" {{ $riskLevel === 'medium' ? 'selected' : '' }}>Sedang</option>
            <option value="high" {{ $riskLevel === 'high' ? 'selected' : '' }}>Tinggi</option>
            <option value="very_high" {{ $riskLevel === 'very_high' ? 'selected' : '' }}>Sangat Tinggi</option>
        </select>
    </div>
</div>
