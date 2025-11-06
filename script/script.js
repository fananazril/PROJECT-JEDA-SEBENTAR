var addModal = document.getElementById("addJurnalModal");
var editModal = document.getElementById("editJurnalModal");
var closeButtons = document.querySelectorAll(".close-btn");
var editLinks = document.querySelectorAll(".edit-link");

// Tombol Tambah Jurnal
var addBtn = document.getElementById("addJurnalBtn");
if (addBtn) {
    addBtn.addEventListener("click", function() {
        if (addModal) addModal.style.display = "flex";
    });
}

// Modal Edit
editLinks.forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();
        const card = this.closest('.jurnal-card');
        if (!card) return;

        const id = card.dataset.id;
        const judul = card.dataset.judul;
        const tanggal = card.dataset.tanggal;
        const isi = card.dataset.isi;

        const editForm = editModal.querySelector('form');
        editForm.querySelector('#edit-jurnal-id').value = id;
        editForm.querySelector('#edit-judul').value = judul;
        editForm.querySelector('#edit-tanggal').value = tanggal;
        editForm.querySelector('#edit-isi').value = isi;

        editModal.style.display = "flex";
    });
});

// Tutup Modal
closeButtons.forEach(button => {
    button.onclick = function() {
        let modal = this.closest('.modal');
        if (modal) modal.style.display = "none";
    }
});

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) event.target.style.display = "none";
}

// Dropdown ⋮
const actionButtons = document.querySelectorAll('.jurnal-actions-btn');
actionButtons.forEach(button => {
    button.addEventListener('click', function(e) {
        const dropdown = this.nextElementSibling;
        closeAllDropdowns(dropdown);
        if (dropdown) dropdown.classList.toggle('show');
        e.stopPropagation();
    });
});
function closeAllDropdowns(exceptThis) {
    document.querySelectorAll('.dropdown-menu.show').forEach(d => {
        if (d !== exceptThis) d.classList.remove('show');
    });
}
window.addEventListener('click', function(e){
    if (!e.target.matches('.jurnal-actions-btn') && !e.target.closest('.jurnal-actions-btn')) {
        closeAllDropdowns();
    }
});

// Mode Gelap
(function(){
    const toggleBtn = document.getElementById('dark-toggle');
    const body = document.body;
    const KEY = 'dark_mode';
    if (localStorage.getItem(KEY) === '1') body.classList.add('dark');
    toggleBtn.onclick = function(){
        body.classList.toggle('dark');
        localStorage.setItem(KEY, body.classList.contains('dark')?'1':'0');
        toggleBtn.textContent = body.classList.contains('dark') ? '☀️' : '🌙';
    }
})();
