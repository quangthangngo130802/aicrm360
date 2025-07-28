<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CRM360 - Quản lý khách hàng thông minh chuyên nghiệp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo_icon/SGOVN-VUONG.png') }}">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background-image: url('https://cdn.pixabay.com/photo/2020/01/23/19/15/confetti-4783793_1280.png');
            /* pháo hoa nhẹ */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Segoe UI", sans-serif;
        }

        .wrapper {
            background: linear-gradient(to bottom right, #ffffffcc, #f0f9ffcc);
            /* màu nền sáng nhẹ */
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .check-circle {
            background-color: #d1fae5;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .check-circle i {
            font-size: 28px;
            color: green;
        }

        .info-block {
            background-color: #e8f0fe;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .info-block.login {
            background-color: #d1fae5;
        }

        .info-block.password {
            background-color: #fef3c7;
        }

        .info-block .label {
            font-weight: 600;
            margin-bottom: 3px;
            font-size: 13px;
            color: #333;
            display: block;
            text-align: left;
        }

        .info-block .value {
            font-weight: bold;
        }

        .info-left {
            text-align: left;
        }

        .start-btn {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            font-weight: bold;
            font-size: 15px;
        }

        .store-badges {
            margin-top: 20px;
        }

        .store-badges img {
            height: 40px;
            margin: 0 5px;
        }

        .footer-text {
            font-size: 13px;
            color: #555;
            margin-top: 15px;
        }

        .bi-clipboard {
            font-size: 18px;
            cursor: pointer;
            color: #555;
        }

        .label {
            font-size: 12px !important;
        }
    </style>

</head>

<body>

    <div class="wrapper">
        <div class="check-circle">
            <i class="bi bi-check-lg"></i>
        </div>

        <h5 class="mb-4 fw-bold">CRM360 - Quản lý khách hàng thông minh chuyên nghiệp</h5>

        <div class="info-block">
            <div class="info-left">
                <span class="label">Link truy cập </span>
                <span class="value text-primary" id="shop-url"></span>
            </div>
            <i class="bi bi-clipboard" onclick="copyText(document.getElementById('shop-url').textContent, this)"></i>
        </div>

        <div class="info-block login">
            <div class="info-left">
                <span class="label">Email</span>
                <span class="value text-success" id="shop-email"></span>
            </div>
            <i class="bi bi-clipboard" onclick="copyText(document.getElementById('shop-email').textContent, this)"></i>
        </div>

        <div class="info-block password">
            <div class="info-left">
                <span class="label">Mật khẩu</span>
                <span class="value text-warning" id="shop-password"></span>
            </div>
            <i class="bi bi-clipboard"
                onclick="copyText(document.getElementById('shop-password').textContent, this)"></i>
        </div>

        <button class="btn btn-primary start-btn" onclick="goToStore()">Bắt đầu quản lý</button>

        {{-- <div class="footer-text">Bạn muốn quản lý mọi lúc mọi nơi? Tải ứng dụng trên điện thoại</div>
        <div class="store-badges">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                alt="Google Play">
        </div> --}}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <script>
        // Lưu thông tin từ Blade vào localStorage (chỉ cần thực hiện 1 lần)


        document.addEventListener("DOMContentLoaded", () => {
            const data = JSON.parse(localStorage.getItem("shopData"));
            console.log(data);
            if (data) {
                document.getElementById("shop-email").textContent = data.email;
                document.getElementById("shop-password").textContent = data.password;
                document.getElementById("shop-url").textContent = data.subdomain;
            }

            // Hiện wrapper mượt mà
            document.querySelector(".wrapper").style.opacity = "0";
            setTimeout(() => {
                document.querySelector(".wrapper").style.opacity = "1";
            }, 1000);
        });

        // Pháo hoa
        window.addEventListener("load", () => {
            const duration = 2 * 1200;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 10,
                    angle: 60,
                    spread: 80,
                    origin: {
                        x: 0,
                        y: 1
                    }
                });
                confetti({
                    particleCount: 10,
                    angle: 120,
                    spread: 80,
                    origin: {
                        x: 1,
                        y: 1
                    }
                });
                confetti({
                    particleCount: 10,
                    spread: 160,
                    origin: {
                        x: 0.5,
                        y: 0.3
                    }
                });

                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        });

        function copyText(text, element) {
            navigator.clipboard.writeText(text).then(() => {
                element.classList.remove("bi-clipboard");
                element.classList.add("bi-check-lg");
                element.style.color = "green";
                setTimeout(() => {
                    element.classList.remove("bi-check-lg");
                    element.classList.add("bi-clipboard");
                    element.style.color = "#555";
                }, 1500);
            });
        }

        function goToStore() {
            const data = JSON.parse(localStorage.getItem("shopData"));
            if (data && data.subdomain) {
                window.location.href = "https://" + data.subdomain;
            }
        }
    </script>


</body>

</html>
