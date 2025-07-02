// Fungsi login sederhana simulasi (tanpa backend)
function login() {
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  const role = document.getElementById("role").value;

  if (!username || !password || !role) {
    showAlert("Semua field harus diisi!", "danger");
    return false;
  }

  // Simulasi validasi login
  // NOTE: Ini contoh, sebaiknya validasi dan autentikasi lewat backend
  if (password.length < 3) {
    showAlert("Password terlalu pendek!", "danger");
    return false;
  }

  // Simpan role ke sessionStorage
  sessionStorage.setItem("userRole", role);
  sessionStorage.setItem("username", username);

  // Simulasi loading
  showAlert("Login berhasil! Sedang mengalihkan...", "success");
  
  // Redirect sesuai role setelah delay
  setTimeout(() => {
    if (role === "siswa") {
      window.location.href = "siswa.html";
    } else if (role === "guru") {
      window.location.href = "guru.html";
    } else if (role === "admin") {
      window.location.href = "admin.html";
    }
  }, 1500);

  return false; // agar form tidak submit reload page
}

// Fungsi untuk menampilkan alert
function showAlert(message, type) {
  const alertBox = document.createElement("div");
  alertBox.className = `alert alert-${type} alert-dismissible fade show`;
  alertBox.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  
  // Cari container untuk alert
  const container = document.querySelector(".card-body");
  const form = document.querySelector("form");
  
  // Tambahkan alert sebelum form
  if (container && form) {
    container.insertBefore(alertBox, form);
    
    // Hilangkan alert setelah 3 detik
    setTimeout(() => {
      alertBox.classList.remove("show");
      setTimeout(() => alertBox.remove(), 300);
    }, 3000);
  }
}

// Fungsi proteksi halaman berdasarkan role
function protectPage(expectedRole) {
  const role = sessionStorage.getItem("userRole");
  if (role !== expectedRole) {
    showAlertAndRedirect("Anda tidak punya akses ke halaman ini!", "danger");
    setTimeout(() => {
      window.location.href = "index.php";
    }, 1500);
  }
}

// Fungsi untuk menampilkan alert dan redirect
function showAlertAndRedirect(message, type) {
  const alertBox = document.createElement("div");
  alertBox.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-4`;
  alertBox.style.zIndex = "9999";
  alertBox.innerHTML = message;
  
  document.body.appendChild(alertBox);
  
  setTimeout(() => {
    alertBox.remove();
  }, 1500);
}

// Fungsi logout
function setupLogout() {
  const btn = document.getElementById("logoutBtn");
  if (btn) {
    btn.addEventListener("click", () => {
      showAlertAndRedirect("Logout berhasil!", "success");
      
      setTimeout(() => {
        sessionStorage.clear();
        window.location.href = "login.html";
      }, 1500);
    });
  }
}

// Pasang event listener saat dokumen siap
document.addEventListener("DOMContentLoaded", function() {
  // Ambil path URL saat ini
  const currentPath = window.location.pathname;
  
  // Jika di halaman login dan sudah login, redirect ke halaman yang sesuai
  if (currentPath.includes("login.html") || currentPath === "/") {
    const role = sessionStorage.getItem("userRole");
    if (role) {
      if (role === "siswa") {
        window.location.href = "siswa.html";
      } else if (role === "guru") {
        window.location.href = "guru.html";
      } else if (role === "admin") {
        window.location.href = "admin.html";
      }
    }
  }
  
  // Tambahkan kelas tema sesuai dengan peran
  const role = sessionStorage.getItem("userRole");
  if (role && !document.body.classList.contains("bg-light")) {
    document.body.classList.add(`${role}-theme`);
  }
});