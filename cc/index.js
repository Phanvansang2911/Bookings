function toggleForm() {
  document.getElementById('login-form').classList.toggle('hidden');
  document.getElementById('register-form').classList.toggle('hidden');
}

function login(event) {
  event.preventDefault();
  alert("Đăng nhập thành công (giả lập)!");
  return false;
}

function register(event) {
  event.preventDefault();
  alert("Đăng ký thành công (giả lập)!");
  return false;
}