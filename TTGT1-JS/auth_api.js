// Cấu hình đường dẫn API
const API_BASE_URL = 'http://127.0.0.1:8000/api/v1';

// 1. CHỨC NĂNG ĐĂNG NHẬP
const btnLogin = document.getElementById('btn-login-action');
if(btnLogin) {
    btnLogin.addEventListener('click', function(e) {
        // Chặn hành vi load lại trang mặc định của form
        e.preventDefault();

        // Lấy dữ liệu từ ô nhập
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-pass').value;

        // Gọi API Login
        fetch(`${API_BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.access_token) {
                // Thành công: Lưu token và user info
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('user_info', JSON.stringify(data.user)); // Lưu thông tin user nếu cần
                alert('Đăng nhập thành công!');
                window.location.href = 'index.html'; // Chuyển về trang chủ
            } else {
                // Thất bại
                alert('Lỗi: ' + (data.message || 'Đăng nhập thất bại'));
            }
        })
        .catch(error => console.error('Lỗi hệ thống:', error));
    });
}

// 2. CHỨC NĂNG ĐĂNG KÝ
const btnReg = document.getElementById('btn-reg-action');
if(btnReg) {
    btnReg.addEventListener('click', function(e) {
        e.preventDefault();

        // Lấy dữ liệu
        const ho = document.getElementById('reg-ho') ? document.getElementById('reg-ho').value : '';
        const ten = document.getElementById('reg-name').value;
        
        // Gộp Họ và Tên thành một chuỗi để gửi vào trường 'name' của Backend
        const fullName = `${ho} ${ten}`.trim(); 

        const email = document.getElementById('reg-email').value;
        const password = document.getElementById('reg-pass').value;
        const c_password = document.getElementById('reg-confirm').value;

        // Gọi API Register
        fetch(`${API_BASE_URL}/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: fullName, // Gửi tên đầy đủ
                email: email,
                password: password,
                password_confirmation: c_password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message && data.user) {
                alert('Đăng ký thành công! Vui lòng đăng nhập.');
                // Tự động chuyển tab sang đăng nhập (nếu cần thiết)
                // document.querySelector('.loginn').click(); 
            } else {
                // Hiển thị lỗi chi tiết từ Laravel (thường trả về dạng mảng)
                alert('Đăng ký thất bại. Vui lòng kiểm tra lại thông tin.');
                console.log(data); 
            }
        })
        .catch(error => console.error('Lỗi hệ thống:', error));
    });
}