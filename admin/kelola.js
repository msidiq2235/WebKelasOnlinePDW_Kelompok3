// Fungsi login sederhana simulasi (tanpa backend)
function login() {
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  const role = document.getElementById("role").value;

  if (!username || !password || !role) {
    alert("Semua field harus diisi!");
    return false;
  }

  // Simulasi validasi login
  // NOTE: Ini contoh, sebaiknya validasi dan autentikasi lewat backend
  if (password.length < 3) {
    alert("Password terlalu pendek!");
    return false;
  }

  // Check against stored users if any
  const users = JSON.parse(localStorage.getItem('users')) || [];
  const foundUser = users.find(user => user.username === username && user.password === password && user.role === role);

  if (!foundUser) {
    // Fallback for default users if no users are stored in localStorage
    // This is for initial testing if localStorage is empty
    if (username === "siswa" && password === "123" && role === "siswa") {
      // Proceed
    } else if (username === "guru" && password === "123" && role === "guru") {
      // Proceed
    } else if (username === "admin" && password === "123" && role === "admin") {
      // Proceed
    } else {
      alert("Username, password, atau peran tidak sesuai!");
      return false;
    }
  }


  // Simpan role ke sessionStorage
  sessionStorage.setItem("userRole", role);
  sessionStorage.setItem("username", username);

  // Redirect sesuai role
  if (role === "siswa") {
    window.location.href = "siswa.html";
  } else if (role === "guru") {
    window.location.href = "guru.html";
  } else if (role === "admin") {
    window.location.href = "admin.html";
  }

  return false; // agar form tidak submit reload page
}

// Fungsi proteksi halaman berdasarkan role
function protectPage(expectedRole) {
  const role = sessionStorage.getItem("userRole");
  if (role !== expectedRole) {
    alert("Anda tidak punya akses ke halaman ini!");
    window.location.href = "/index.php";
  }
}

// Fungsi logout
function setupLogout() {
  const btn = document.getElementById("logoutBtn");
  if (btn) {
    btn.addEventListener("click", () => {
      sessionStorage.clear();
      window.location.href = "/index.php";
    });
  }
}

// saveQuizResult function (moved to kuis.html for direct relevance)
// function saveQuizResult(quizName, score, maxScore) {
//   const quizResults = JSON.parse(localStorage.getItem('quizResults')) || [];

//   quizResults.push({
//     quizName,
//     score,
//     maxScore,
//     timestamp: new Date().getTime()
//   });

//   localStorage.setItem('quizResults', JSON.stringify(quizResults));
// }