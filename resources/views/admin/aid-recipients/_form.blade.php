  
<script>
document.getElementById('district_id').addEventListener('change', function() {
    let districtId = this.value;
    let villageSelect = document.getElementById('village_id');
    villageSelect.innerHTML = '<option value="">Loading...</option>';

    if (districtId) {
        fetch('/villages/' + districtId)
            .then(response => response.json())
            .then(data => {
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                data.forEach(function(village) {
                    villageSelect.innerHTML += `<option value="${village.id}">${village.name}</option>`;
                });
            });
    } else {
        villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
    }
});
</script>
