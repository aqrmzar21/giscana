
<script>
(function() {
function initAidRecipientForm() {
    const districtsData = @json($districts);
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');
    
    if (!districtSelect || !villageSelect) return;
    
    const selectedVillageId = "{{ old('village_id', $aidRecipient->village_id ?? '') }}";

    function populateVillages(districtId) {
        villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
        if(!districtId) return;

        const district = districtsData.find(d => d.id == districtId);
        if(district && district.villages) {
            district.villages.forEach(village => {
                const option = document.createElement('option');
                option.value = village.id;
                option.text = village.name;
                if(village.id == selectedVillageId) {
                    option.selected = true;
                }
                villageSelect.add(option);
            });
        }
    }

    // Hindari event listener ganda pada re-render PJAX
    districtSelect.removeEventListener('change', window._onDistrictChangeHandler);
    window._onDistrictChangeHandler = function() {
        populateVillages(this.value);
    };
    districtSelect.addEventListener('change', window._onDistrictChangeHandler);

    if(districtSelect.value) {
        populateVillages(districtSelect.value);
    }
}

// Jalankan saat script dirender (cocok untuk PJAX)
initAidRecipientForm();
})();
</script>