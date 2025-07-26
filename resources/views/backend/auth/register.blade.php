<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký dùng thử CRM360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('global/css/toastr.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1f3b8c, #5f72be, #9f98e8);
            background-size: 300% 300%;
            animation: gradientBG 10s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .form-container {
            background: #ffffffcc;
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 24px 20px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .form-subtitle {
            text-align: center;
            font-size: 13px;
            margin-bottom: 20px;
            color: #555;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #333;
        }

        .form-control,
        .form-select,
        textarea {
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 0 0.15rem rgba(108, 99, 255, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(to right, #6c63ff, #63a4ff);
            color: white;
            font-weight: 600;
            padding: 10px;
            font-size: 14px;
            border: none;
            border-radius: 10px;
            width: 100%;
            transition: 0.3s ease-in-out;
        }

        .btn-gradient:hover {
            background: linear-gradient(to right, #63a4ff, #6c63ff);
            box-shadow: 0 6px 18px rgba(108, 99, 255, 0.3);
        }

        .form-check-label {
            font-size: 13px;
        }

        a {
            color: #6c63ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <div class="form-title">🚀 Đăng ký dùng thử MiniCRM</div>
        <div class="form-subtitle">Nhanh chóng & miễn phí</div>

        <form action="{{ url('/dang-ky') }}" method="POST" enctype="multipart/form-data" id="myForm">
            @csrf

            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="Nguyễn Văn A">
                </div>

                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" name="phone" placeholder="0912xxx...">
                </div>

                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="example@email.com">
                </div>

                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                    <input type="text" name="subdomain" class="form-control" placeholder="Tên đăng nhập">
                </div>

                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>

                    <div class="position-relative">
                        <input type="password" name="password" id="password" class="form-control pe-5"
                            placeholder="Mật khẩu">

                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 " onclick="togglePassword()"
                            style="cursor: pointer;">
                            <i class="fa-solid fa-eye text-muted" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                </div>



                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Công ty</label>
                    <input type="text" class="form-control" name="company" placeholder="Tên công ty">
                </div>

                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Giới tính</label>
                    <select class="form-select" name="gender">
                        <option selected disabled>Chọn giới tính</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>


                <div class="col-12 col-md-6 mb-2">
                    <label class="form-label">Lĩnh vực</label>
                    <select class="form-select" name="field">
                        <option selected disabled>Chọn lĩnh vực</option>
                        <option>Bán lẻ</option>
                        <option>Dịch vụ</option>
                        <option>Giáo dục</option>
                        <option>Khác</option>
                    </select>
                </div>

                <div class="col-12 col-md-12 mb-2">
                    <label class="form-label">Nhu cầu</label>
                    <textarea class="form-control" rows="2" name="demand" placeholder="Bạn cần gì?"></textarea>
                </div>

                <div class="col-12 col-md-12 mb-2 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree">
                        <label class="form-check-label" for="agree">
                            Tôi đồng ý với <a href="#">điều khoản sử dụng</a>.
                        </label>
                    </div>
                </div>

                <div class="col-12 mb-2">
                    <button type="submit" class="btn-gradient">Gửi đăng ký</button>
                </div>
            </div>
        </form>

    </div>
    <script src="{{ asset('assets/backend/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('global/js/toastr.js') }}"></script>
    <script src="{{ asset('global/js/helpers.js') }}"></script>
    <script src="{{ asset('global/js/validate.js') }}"></script>


    <script>
        submitForm("#myForm", function(response) {
            aicrm?.success("TĐăng ký thành công!");
        }, "/dang-ky");
    </script>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const icon = document.getElementById("togglePasswordIcon");
            const isPassword = passwordInput.type === "password";

            passwordInput.type = isPassword ? "text" : "password";
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }
    </script>

</body>


</html>
