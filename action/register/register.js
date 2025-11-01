function togglePassword() {
    const p = document.getElementById("password");
    p.type = (p.type === "password") ? "text" : "password";
}

function toggleConfirm() {
    const c = document.getElementById("confirm");
    c.type = (c.type === "password") ? "text" : "password";
}

function showPopup(type, message) {
    const popup = document.getElementById("popup");
    const title = document.getElementById("popup-title");
    const msg = document.getElementById("popup-message");

    popup.classList.remove("success", "error");
    popup.classList.add(type);

    title.textContent = (type === "success") ? "Berhasil ✅" : "Gagal ❌";
    msg.textContent = message;

    popup.style.display = "flex";
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}
