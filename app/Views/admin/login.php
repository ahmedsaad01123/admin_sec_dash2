<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\Security::e($title) ?></title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f4f6f8;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .box {
            background: #fff;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            width: 100%;
            max-width: 380px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 24px;
            text-align: center;
            font-size: 20px;
            color: #111827;
        }

        label {
            display: block;
            margin: 14px 0 6px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #111827;
        }

        button[type="submit"] {
            margin-top: 22px;
            width: 100%;
            padding: 12px;
            background: #111827;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: .2s;
        }

        button[type="submit"]:hover {
            background: #1f2937;
        }

        .error-box {
            background: #fef2f2;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
            text-align: center;
        }

        /* Password */
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding: 10px 45px 10px 12px;
        }

        .toggle-password {
            position: absolute;
            left: 12px; /* في RTL تظهر يسار الحقل */
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
            color: #6b7280;
        }

        .toggle-password:hover {
            color: #111827;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>

<body>

<div class="box">

    <h1><?= \App\Core\Security::e(__('admin_login')) ?></h1>

    <?php if (!empty($error)): ?>
        <div class="error-box">
            <?= \App\Core\Security::e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/login">

        <input type="hidden"
               name="_csrf_token"
               value="<?= \App\Core\Security::e($csrf_token) ?>">

        <label><?= \App\Core\Security::e(__('username')) ?></label>
        <input type="text"
               name="username"
               required
               autofocus>

        <label><?= \App\Core\Security::e(__('password')) ?></label>

        <div class="password-wrapper">

            <input
                type="password"
                name="password"
                id="password"
                required
            >

            <button
                type="button"
                class="toggle-password"
                onclick="togglePassword()"
                aria-label="إظهار أو إخفاء كلمة المرور">

                <!-- Eye -->
                <svg id="eye-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0"/>

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>

                <!-- Eye Off -->
                <svg id="eye-off-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2"
                     style="display:none;">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>

            </button>

        </div>

        <button type="submit">
            <?= \App\Core\Security::e(__('login')) ?>
        </button>

    </form>

</div>

<script>
function togglePassword() {

    const password = document.getElementById("password");
    const eye = document.getElementById("eye-icon");
    const eyeOff = document.getElementById("eye-off-icon");

    if (password.type === "password") {
        password.type = "text";
        eye.style.display = "none";
        eyeOff.style.display = "block";
    } else {
        password.type = "password";
        eye.style.display = "block";
        eyeOff.style.display = "none";
    }

}
</script>

</body>
</html>