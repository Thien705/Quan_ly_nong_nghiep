// /qlvt/build/assets/js/session.js
(function () {
  const BASE       = '/qlvt/build/';
  const LOGIN_PAGE = BASE + 'pages/dang_nhap.html';
  const USER_HOME  = BASE + 'pages/trang_chu.html';

  function isLoginOrRegister() {
    const p = location.pathname;
    return p.endsWith('/dang_nhap.html') || p.endsWith('/dang_ky.html');
  }
  
  // Admin có thể vào tất cả 3 trang
  function isAdminAllowedPage() {
    return (
      location.pathname.endsWith('/trang_chu.html') ||
      location.pathname.endsWith('/bang_thong_tin.html') ||
      location.pathname.endsWith('/quan_ly.html')
    );
  }
  
  // User chỉ có thể vào trang chủ và bảng thông tin
  function isUserAllowedPage() {
    return (
      location.pathname.endsWith('/trang_chu.html') ||
      location.pathname.endsWith('/bang_thong_tin.html')
    );
  }

  function ensureAuthAndRole() {
    const u = localStorage.getItem('username');
    const r = (localStorage.getItem('role') || 'user').toLowerCase();
    
    // Debug thông tin
    console.log('Current path:', location.pathname);
    console.log('Username:', u);
    console.log('Role:', r);
    console.log('Is login page:', isLoginOrRegister());
    console.log('Is user allowed:', isUserAllowedPage());
    
    if (!u && !isLoginOrRegister()) {
      console.log('Redirecting to login - no username');
      return location.replace(LOGIN_PAGE);
    }
    
    if (u && r === 'admin' && !isAdminAllowedPage()) {
      console.log('Admin redirect to home');
      return location.replace(USER_HOME);
    }
    
    if (u && r !== 'admin' && !isUserAllowedPage()) {
      console.log('User redirect to home - not allowed page');
      
      // Hiển thị thông báo khi user cố truy cập trang quản lý
      if (location.pathname.endsWith('/quan_ly.html')) {
        alert('Bạn không có quyền truy cập trang này!');
      }
      
      return location.replace(USER_HOME);
    }
    
    console.log('Access granted');
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
