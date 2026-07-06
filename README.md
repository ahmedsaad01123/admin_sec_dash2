# هيكل PHP MVC مخصص — بدون Composer

فريموورك MVC بسيط ومبني بالكامل من الصفر بـ **PHP 8.4**، من غير أي Dependencies خارجية (من غير composer حتى). مبني عشان يدير منصة جمعية خيرية، وفيه 3 مناطق وصول منفصلة: **زوار عاديين**، **مستخدمين مسجلين (متبرعين)**، و**أدمن**.

---

## 1) فكرة المشروع باختصار

كل طلب بيدخل من `public/index.php` (Front Controller)، وبيتوجّه لـ `App\Core\App` اللي بيشغّل الـ Router، يطبّق الـ Middleware المناسب، وبعدين يستدعي الـ Controller/Method المطلوبين.

```
طلب المستخدم → public/index.php → App::run() → Router::resolve()
                                                     ↓
                                          Middleware (لو موجود)
                                                     ↓
                                            Controller → View
```

---

## 2) هيكل المجلدات

```
project/
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php, AboutController.php...     → صفحات عامة (Guest)
│   │   ├── AuthController.php                               → لوجين/تسجيل/لوجاوت المستخدم
│   │   ├── User/                                             → صفحات المستخدم المسجل
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   └── DonationController.php
│   │   └── Admin/                                            → صفحات الأدمن
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       └── ProfileController.php
│   │
│   ├── Models/                    → UserModel, AdminUserModel...
│   │
│   ├── Views/
│   │   ├── home/, about.php...    → Views عامة
│   │   ├── admin/                 → Views الأدمن
│   │   ├── errors/                → 404 / 500
│   │   └── layouts/               → header/footer/main/app
│   │
│   └── Core/                      → قلب الفريموورك (الشرح بالتفصيل تحت)
│       ├── App.php, Router.php, Controller.php, Model.php
│       ├── Database.php, Session.php, Security.php
│       ├── Validator.php, RateLimiter.php, PasswordPolicy.php
│       ├── Config.php, Env.php, Language.php, Autoloader.php
│       └── Middleware/
│           ├── GuestOnlyMiddleware.php   → لصفحات اللوجين/التسجيل
│           ├── UserAuthMiddleware.php    → لصفحات المستخدم المسجل
│           └── AdminAuthMiddleware.php   → لصفحات الأدمن
│
├── config/config.php              → كل الإعدادات بترجع من هنا (مبنية على .env)
├── database/schema.sql            → الجدول الأساسي
├── routes/
│   ├── web.php                    → روتات عامة + مستخدم
│   └── admin.php                  → روتات الأدمن
├── lang/ar.php, lang/en.php       → الترجمات
├── public/                        → Document Root
│   ├── index.php                  → نقطة الدخول الوحيدة
│   ├── install/                   → معالج التثبيت (يتحذف بعد التركيب)
│   ├── devtools/                  → أداة توليد صفحات سريعة (بيئة تطوير فقط)
│   └── pages-manager/             → إدارة/حذف الصفحات (بيئة تطوير فقط)
├── storage/logs/                  → لوج الأخطاء
└── .env                           → إعداداتك (متترفعش على Git)
```

---

## 3) المناطق الثلاثة (Guest / User / Admin)

الفكرة إن كل منطقة عندها **Session key** مختلف و**Middleware** مختلف، فمفيش تداخل بينهم خالص:

| المنطقة | مين يدخلها | مفتاح الـ Session | الـ Middleware |
|---|---|---|---|
| **Guest** | أي حد (مسجل أو لأ) | — | من غيره |
| **Guest-Only** | لازم يكون *مش* مسجل | يتفحص غياب `user_id` | `GuestOnlyMiddleware` |
| **User (متبرع)** | لازم `user_id` في السيشن | `user_id` | `UserAuthMiddleware` |
| **Admin** | لازم `admin_id` في السيشن | `admin_id` | `AdminAuthMiddleware` |

**ملاحظة مهمة:** نظام الأدمن منفصل بالكامل عن نظام المستخدمين — جدول مختلف (`admin_users` مقابل `users`)، Session key مختلف، وMiddleware مختلف. متتوقعش إن يوزر عادي بـ `role=admin` (لو موجود) يقدر يدخل لوحة تحكم الأدمن؛ الدخول للأدمن بيتم بس عن طريق `admin_id`.

### إزاي الـ Middleware بيشتغل؟

كل Route ممكن يكون ليه Middleware خاص بيه (لو صفحة واحدة)، أو Middleware على **Group** كامل من الروتات (زي الأدمن). مثال من `routes/admin.php`:

```php
$router->group(Config::get('app.admin_prefix', 'admin'), function ($router): void {

    // مفتوحة لأي حد (بدون تسجيل دخول)
    $router->get('/login', 'Admin\AuthController@showLogin');

    // كل حاجة جوه المجموعة دي محمية أوتوماتيك
    $router->group('', function ($router): void {
        $router->get('/', 'Admin\DashboardController@index');
    }, [AdminAuthMiddleware::class]);
});
```

أو Middleware على Route واحد بس، زي ما هو في `routes/web.php`:

```php
$router->get('/dashboard', 'User\DashboardController@index', [
    'middleware' => [UserAuthMiddleware::class],
]);
```

---

## 4) إزاي تضيف صفحة جديدة؟

### أ) صفحة عامة (Guest) — متاحة للجميع

**١. Controller** في `app/Controllers/`:
```php
<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class ServicesController extends Controller
{
    public function index(): void
    {
        $this->view('services', [
            'title' => 'خدماتنا',
        ], 'layouts.app');
    }
}
```

**٢. View** في `app/Views/services.php` (استخدم `e()` دايمًا لطباعة أي بيانات):
```php
<h1><?= e($title) ?></h1>
```

**٣. Route** في `routes/web.php` (فوق قسم "AUTO-GENERATED ROUTES"):
```php
$router->get('/services', 'ServicesController@index');
```

> 💡 بديل أسرع: أداة `public/devtools` بتعمل الخطوات دي أوتوماتيك (Controller + View + Route) لصفحات Guest عادية. متستخدمهاش لصفحات User/Admin لأنها لسه معملتش تدعم الـ Middleware بتاعنا، وأصلاً هي أداة بيئة تطوير بس وبتقفل نفسها تلقائي في `APP_ENV=production`.

---

### ب) صفحة محمية لمستخدم مسجل (متبرع)

**١. Controller** تحت `app/Controllers/User/`:
```php
<?php
declare(strict_types=1);

namespace App\Controllers\User;

use App\Core\Controller;
use App\Core\Session;
use App\Models\UserModel;

final class DonationController extends Controller
{
    public function index(): void
    {
        $userId = (int) Session::get('user_id');
        $user = (new UserModel())->find($userId);

        $this->view('user.donations', [
            'title' => 'تبرعاتي',
            'user'  => $user,
        ], 'layouts.app');
    }
}
```

**٢. View** في `app/Views/user/donations.php`.

**٣. Route** في `routes/web.php`، لازم يتحط جوه Group بـ `UserAuthMiddleware`:
```php
$router->group('', function ($router): void {
    $router->get('/dashboard', 'User\DashboardController@index');
    $router->get('/donations', 'User\DonationController@index'); // ← الصفحة الجديدة
}, [UserAuthMiddleware::class]);
```

كده أي صفحة تضيفها جوه الـ Group ده بتبقى محمية أوتوماتيك من غير أي كود إضافي — لازم اليوزر يكون عامل لوجين، وإلا هيتحول لـ `/login` تلقائيًا.

---

### ج) صفحة جديدة في لوحة تحكم الأدمن

نفس الفكرة بالظبط، لكن في `routes/admin.php` جوه الـ Group المحمي بـ `AdminAuthMiddleware`:

**١. Controller** تحت `app/Controllers/Admin/`:
```php
<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class DonorsController extends Controller
{
    public function index(): void
    {
        $this->view('admin.donors', ['title' => 'المتبرعين']);
    }
}
```

**٢. View** في `app/Views/admin/donors.php`.

**٣. Route** في `routes/admin.php`:
```php
$router->group(Config::get('app.admin_prefix', 'admin'), function ($router): void {
    $router->get('/login', 'Admin\AuthController@showLogin');
    $router->post('/login', 'Admin\AuthController@login');
    $router->post('/logout', 'Admin\AuthController@logout');

    $router->group('', function ($router): void {
        $router->get('/', 'Admin\DashboardController@index');
        $router->get('/donors', 'Admin\DonorsController@index'); // ← الصفحة الجديدة
    }, [AdminAuthMiddleware::class]);
});
```

الرابط النهائي هيبقى `/{ADMIN_PREFIX}/donors` — و`ADMIN_PREFIX` مضبوط في `.env` (افتراضيًا `admin`، وممكن تغيّره لأي حاجة تانية زي `adminlogin` عشان تخبي مكان اللوحة).

---

## 5) إزاي تضيف Model جديد؟

كل Model لازم يورّث من `App\Core\Model` ويحدد اسم الجدول، والأهم: **`$fillable`** — الأعمدة المسموح إنشاؤها/تعديلها. لو نسيت تحددها، أي `create()`/`update()` هيرفض يشتغل (حماية من Mass Assignment).

```php
<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class DonationModel extends Model
{
    protected string $table = 'donations';
    protected array $fillable = ['user_id', 'amount', 'campaign_id', 'status'];

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId);
    }
}
```

الدوال الجاهزة في `Model`: `find($id)`, `all()`, `where($column, $value)`, `create($data)`, `update($id, $data)`, `delete($id)`. كلها بتستخدم Prepared Statements فمحمية من SQL Injection تلقائيًا.

---

## 6) نقاط أمان مهمة لازم تلتزم بيها وإنت بتضيف صفحات

- ✅ **CSRF**: أي Form بـ POST لازم يحتوي على `_csrf_token`، وفي الـ Controller تتحقق بـ `$this->verifyCsrf()` قبل أي تعديل في الداتابيز.
- ✅ **الطباعة في الـ View**: استخدم `e($value)` دايمًا مع أي بيانات جاية من اليوزر أو الداتابيز. البيانات الراجعة من `input()`/`all()` خام من غير Escaping، فالـ escaping مسؤولية الـ View وقت الطباعة بس.
- ✅ **الفورمات الحساسة (لوجين/باسورد)**: استخدم `RateLimiter::attempt()` قبل التحقق من الباسورد، و`PasswordPolicy::validate()` عند إنشاء/تغيير أي باسورد.
- ✅ **اللوجاوت لازم يكون POST مش GET** (زي أدمن) عشان يتفادى CSRF بسيط.
- ✅ **الفورمات عمومًا**: استخدم `$this->validate([...])` من `Controller` بدل التحقق اليدوي.

---

## 7) الإعدادات (`.env`)

أهم المتغيرات:

| المتغير | الوظيفة |
|---|---|
| `APP_ENV` | `local`/`development` وقت التطوير، `production` وقت النشر (بيقفل devtools وpages-manager تلقائيًا) |
| `APP_DEBUG` | `true` لعرض تفاصيل الأخطاء، خليه `false` في الإنتاج |
| `ADMIN_PREFIX` | مسار لوحة تحكم الأدمن (افتراضيًا `admin`) |
| `SESSION_SECURE` | خليه `true` لو الموقع شغال على HTTPS |
| `PASSWORD_*` | إعدادات سياسة قوة الباسورد |
| `RATE_LIMIT_*` | إعدادات الحد من محاولات الدخول |

---

## 8) التشغيل محليًا

```bash
cd public
php -S localhost:8000
```

أو Apache: خلّي Document Root يشاور على `public/` مباشرة.

بعد رفع المشروع لأول مرة: افتح `/install` مرة واحدة لعمل الجداول، وبعدها **احذف مجلد `install` بالكامل** أو أقفله. كذلك احذف/أقفل `devtools` و`pages-manager` قبل النشر النهائي (بيقفلوا نفسهم تلقائيًا لو `APP_ENV=production`، لكن الأفضل تحذفهم فعليًا من السيرفر).

---

## 9) الخطوات الجاية

- بناء `AuthController` و`User\DashboardController/ProfileController/DonationController` (لسه الروتات والـ Middleware بس جاهزين).
- REST API layer منفصلة (`ApiController` بيرجع JSON فقط، Auth بـ API Key بدل Session).
- تكامل WhatsApp Business API + AI Assistant (RAG).