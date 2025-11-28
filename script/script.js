const addModal = document.getElementById("addJurnalModal");
const editModal = document.getElementById("editJurnalModal");
const detailModal = document.getElementById("detailJurnalModal");
const addBtn = document.getElementById("addJurnalBtn");

let currentDetailId = null;

function formatTanggalIndonesia(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const tanggal = date.getDate();
    const bulanNama = bulan[date.getMonth()];
    const tahun = date.getFullYear();
    const jam = String(date.getHours()).padStart(2, '0');
    const menit = String(date.getMinutes()).padStart(2, '0');
    
    return `${tanggal} ${bulanNama} ${tahun}, ${jam}:${menit}`;
}

//fungsi tombol tambah
if (addBtn) {
    addBtn.addEventListener("click", function(e) {
        e.preventDefault();
        if (addModal) {
            addModal.style.display = "flex";
        }
    });
}

//fungsi tombol close
document.querySelectorAll(".close-btn").forEach(btn => {
    btn.addEventListener("click", function() {
        const modal = this.closest(".modal");
        if (modal) {
            modal.style.display = "none";
        }
    });
});

//fungsi klik di luar modal
document.querySelectorAll(".modal").forEach(modal => {
    modal.addEventListener("click", function(e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});

//fungsi tombol dropdown
document.querySelectorAll(".jurnal-actions-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const menu = this.nextElementSibling;
        const currentCard = this.closest('.jurnal-card');
        const isCurrentlyOpen = menu.classList.contains("show");
        
        document.querySelectorAll(".dropdown-menu").forEach(m => {
            m.classList.remove("show");
        });
        document.querySelectorAll(".jurnal-card").forEach(card => {
            card.classList.remove("dropdown-active");
        });
        
        if (!isCurrentlyOpen) {
            menu.classList.add("show");
            currentCard.classList.add("dropdown-active");
        }
    });
});

//fungsi klik dropdown
document.addEventListener("click", function(e) {
    if (!e.target.closest(".jurnal-actions")) {
        document.querySelectorAll(".dropdown-menu").forEach(menu => {
            menu.classList.remove("show");
        });
        document.querySelectorAll(".jurnal-card").forEach(card => {
            card.classList.remove("dropdown-active");
        });
    }
});

//edit modal aktif
document.addEventListener("click", function(e) {
    if (e.target.closest(".edit-link")) {
        e.preventDefault();
        e.stopPropagation();
        
        const editLink = e.target.closest(".edit-link");
        const card = editLink.closest(".jurnal-card");
        
        if (card) {
            const id = card.dataset.id;
            const judul = card.dataset.judul;
            const tanggal = card.dataset.tanggal;
            const isi = card.dataset.isi;
            
            document.getElementById("edit-jurnal-id").value = id;
            document.getElementById("edit-judul").value = judul;
            document.getElementById("edit-tanggal").value = tanggal;
            document.getElementById("edit-isi").value = isi;
            
            document.querySelectorAll(".dropdown-menu").forEach(menu => {
                menu.classList.remove("show");
            });
            document.querySelectorAll(".jurnal-card").forEach(c => {
                c.classList.remove("dropdown-active");
            });
            
            if (editModal) {
                editModal.style.display = "flex";
            }
        }
    }
});

//detail modal aktif
document.querySelectorAll(".jurnal-card").forEach(card => {
    card.addEventListener("click", function(e) {

        if (e.target.closest(".jurnal-actions")) {
            return;
        }
        
        const id = this.dataset.id;
        const judul = this.dataset.judul;
        const tanggal = this.dataset.tanggal;
        const isi = this.dataset.isi;
        const dibuat = this.dataset.dibuat;
        const diupdate = this.dataset.diupdate;
    
        currentDetailId = id;
        document.getElementById("detail-judul").textContent = judul;
        
        const date = new Date(tanggal);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = date.toLocaleDateString('id-ID', options);
        document.getElementById("detail-tanggal").innerHTML = `<i class="far fa-calendar-alt"></i> ${formattedDate}`;
        
        document.getElementById("detail-isi").textContent = isi;
        

        const dibuatElement = document.getElementById("detail-dibuat");
        if (dibuatElement && dibuat) {
            dibuatElement.innerHTML = `<i class="far fa-clock"></i> Dibuat: ${formatTanggalIndonesia(dibuat)}`;
        } else if (dibuatElement) {
            dibuatElement.innerHTML = '';
        }

        const diupdateElement = document.getElementById("detail-diupdate");
        if (diupdateElement) {
            if (diupdate && diupdate !== dibuat && diupdate !== '0000-00-00 00:00:00' && diupdate !== '') {
                diupdateElement.innerHTML = `<i class="fas fa-sync-alt"></i> Diperbarui: ${formatTanggalIndonesia(diupdate)}`;
                diupdateElement.style.display = 'block';
            } else {
                diupdateElement.innerHTML = '';
                diupdateElement.style.display = 'none';
            }
        }

        if (detailModal) {
            detailModal.style.display = "flex";
        }
    });
});

// Edit di Detail Modal
const detailEditBtn = document.getElementById("detailEditBtn");
if (detailEditBtn) {
    detailEditBtn.addEventListener("click", function(e) {
        e.preventDefault();
        
        if (!currentDetailId) return;
        
        const card = document.querySelector(`.jurnal-card[data-id="${currentDetailId}"]`);
        if (card) {
            document.getElementById("edit-jurnal-id").value = card.dataset.id;
            document.getElementById("edit-judul").value = card.dataset.judul;
            document.getElementById("edit-tanggal").value = card.dataset.tanggal;
            document.getElementById("edit-isi").value = card.dataset.isi;
            
            if (detailModal) detailModal.style.display = "none";
            if (editModal) editModal.style.display = "flex";
        }
    });
}

// Delete di Detail Modal
const detailDeleteBtn = document.getElementById("detailDeleteBtn");
if (detailDeleteBtn) {
    detailDeleteBtn.addEventListener("click", function(e) {
        e.preventDefault();
        
        if (!currentDetailId) return;
        
        if (confirm('Apakah Anda yakin ingin menghapus jurnal ini?')) {
            window.location.href = `../action/jurnalaction/hapusjurnal.php?id=${currentDetailId}`;
        }
    });
}

document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        document.querySelectorAll(".modal").forEach(modal => {
            modal.style.display = "none";
        });
        
        document.querySelectorAll(".dropdown-menu").forEach(menu => {
            menu.classList.remove("show");
        });
        document.querySelectorAll(".jurnal-card").forEach(card => {
            card.classList.remove("dropdown-active");
        });
    }
});

// Set Tanggal di Modal
if (addModal) {
    const tanggalInput = addModal.querySelector("#tanggal");
    if (tanggalInput && !tanggalInput.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalInput.value = today;
    }
}

console.log("Script loaded successfully!");