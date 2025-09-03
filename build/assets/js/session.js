// /qlvt/build/assets/js/session.js
(function () {
  const BASE       = '/qlvt/build/';
  const LOGIN_PAGE = BASE + 'pages/dang_nhap.html';
  const USER_HOME  = BASE + 'pages/trang_chu.html';

  function isLoginOrRegister() {
    const p = location.pathname;
    return p.endsWith('/dang_nhap.html') || p.endsWith('/dang_ky.html');
  }
  // Chỉ cho phép user vào trang chủ, admin vào được cả 3 trang
  function isAdminAllowedPage() {
    return (
      location.pathname.endsWith('/trang_chu.html') ||
      location.pathname.endsWith('/bang_thong_tin.html') ||
      location.pathname.endsWith('/quan_ly.html')
    );
  }
  function isUserAllowedPage() {
    return location.pathname.endsWith('/trang_chu.html');
  }

  function ensureAuthAndRole() {
    const u = localStorage.getItem('username');
    const r = (localStorage.getItem('role') || 'user').toLowerCase();
    if (!u && !isLoginOrRegister()) return location.replace(LOGIN_PAGE);
    if (u && r === 'admin' && !isAdminAllowedPage()) return location.replace(USER_HOME);
    if (u && r !== 'admin' && !isUserAllowedPage()) return location.replace(USER_HOME);
  }

  // Chặn truy cập ngay
  ensureAuthAndRole();

  // Chống quay lại bằng Back sau khi logout (bfcache)
  addEventListener('pageshow', (e) => { if (e.persisted) ensureAuthAndRole(); });

  // Xử lý Đăng xuất cho mọi phần tử có data-logout
  function attachLogout() {
    document.querySelectorAll('[data-logout]').forEach(el => {
      el.addEventListener('click', (e) => {
        e.preventDefault();
        localStorage.removeItem('username');
        localStorage.removeItem('role');
        try { sessionStorage.clear(); } catch(_) {}
        location.replace(LOGIN_PAGE);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachLogout);
  } else {
    attachLogout();
  }
})();
