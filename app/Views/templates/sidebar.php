<?php
// Sidebar Template - يمكن استدعاؤه في أي صفحة
// استخدم: include_once __DIR__ . '/../templates/sidebar.php';
?>
<!-- start sideMenu -->
<div id="sideMenu">
    <div class="header flex flex-gap-15 flex-align-items-center">
        <div class="logoHolder">
            <a href="/">
                <img class="logo" src="">
            </a>
        </div>
        <div class="info">
            <div class="tenantName">
                LOGO
            </div>
            <div class="userName extra-small">
                <a href="/account/modify">
                    <i class="fa fa-user"></i> Super Administrator
                </a>
            </div>
        </div>
    </div>
    <i class="icon fa fa-bars toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
    <div id="menu-content" class="menu-content collapse out">
        
        <ul class="shortcuts">
            <li class="notifications">
                <a onclick="window.App.user.notifications.open(); return false;" href="">
                    <span class="count"></span>
                    <i class="icon fa fa-bell"></i>
                </a>
            </li>
            <li>
                <a href="/calendar" title="التقويم" data-toggle="tooltip">
                    <i class="icon fa fa-calendar"></i>
                </a>
            </li>
            <li>
                <a href="/account/modify" data-toggle="tooltip" data-placement="bottom" title="حسابي">
                    <i class="icon fa fa-user"></i>
                </a>
            </li>
            <li>
                <a href="/logout" data-toggle="tooltip" data-placement="bottom" title="تسجيل خروج">
                    <i class="icon fa fa-sign-out"></i>
                </a>
            </li>
        </ul>
        
        <form id="search-form" class="form-group">
            <div class="input-group box-shadow">
                <input type="text" class="form-control" placeholder="البحث عن أي شئ ..."/>
                <div class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="fa fa-search m-0"></i>
                    </button>
                </div>
            </div>
        </form>

        <ul class="menu-wrapper">
            <li class="menu-item no-sub-menu active">
                <a href="/" class="menu-item-title d-flex">
                    <span>
                        <i class="icon fa fa-home"></i>
                        <span class="title">البداية</span>
                    </span>
                </a>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_admission"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-filter fa-fw"></i>
                        <span class="title">التقديمات والقبول</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_admission">
                    <li class="sub-menu-item">
                        <a href="/admission/newPipeline" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">مراحل التقديم</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/admission/placementTests" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">إختبارات تحديد المستوى</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admission/waitingLists" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">قوائم الإنتظار</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_coordination"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-users fa-fw"></i>
                        <span class="title">تنسيق التدريب</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_coordination">
                    <li class="sub-menu-item">
                        <a href="/batches" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">المجموعات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/batches/create" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">بدء مجموعة جديدة</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/lectures" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">المحاضرات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/lectures/create" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">إضافة محاضرة جديدة</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/orders/enrollment" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">التسجيل</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/orders/retentionConfirmation" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">تأكيد الاستبقاء</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/courses" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الدورات ومسارات التدريب</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/training/timeSlots" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">المواعيد</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_orders"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-shopping-cart fa-fw"></i>
                        <span class="title">الطلبات</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_orders">
                    <li class="sub-menu-item">
                        <a href="/orders/allOrders" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">جميع الطلبات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/orders/installmentTracking" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">تتبع الأقساط</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/orders/batchProjection" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">توقعات المجموعات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/orders/insights" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">تحليلات</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_instructors"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-user-circle fa-fw"></i>
                        <span class="title">المدربين</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_instructors">
                    <li class="sub-menu-item">
                        <a href="/instructors" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">المدربين</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/instructors/availability" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الإتاحة والتوفر</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/instructors/utilization" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الإستفادة والتشغيل</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_clients"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-user fa-fw"></i>
                        <span class="title">العملاء</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_clients">
                    <li class="sub-menu-item">
                        <a href="/clients/create" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">تسجيل عميل جديد</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/clients/all" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">قاعدة بيانات العملاء</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/clients/companies" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الشركات والتعاقدات</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_lms"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-laptop fa-fw"></i>
                        <span class="title">إدارة التعليم</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_lms">
                    <li class="sub-menu-item">
                        <a href="/lms/management/coursePlans" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">خطط التدريب والمحتوى</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/lms/tc/management" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">مركز الاختبارات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/lms/training/virtualClassrooms/provider" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الفصول الإفتراضية</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item no-sub-menu ">
                <a href="/certificates" class="menu-item-title d-flex">
                    <span>
                        <i class="icon fa fa-certificate"></i>
                        <span class="title">الشهادات</span>
                    </span>
                </a>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_messaging"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-send fa-fw"></i>
                        <span class="title">المراسلة</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_messaging">
                    <li class="sub-menu-item">
                        <a href="/messaging/create" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">إرسال رسالة جديدة</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/messaging/log" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">سجل الرسائل</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_finances"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-usd fa-fw"></i>
                        <span class="title">الماليات</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_finances">
                    <li class="sub-menu-item">
                        <a href="/finances/transactions" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">المعاملات المالية</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/transactions/create" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">عملية دفع أو استلام جديدة</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/invoices" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الفواتير</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/invoices/create" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">إصدار فاتورة جديدة</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/finances/batches" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">ماليات المجموعات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/installments" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">متابعة الأقساط</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/instructors" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">ماليات المدربين</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/finances/paymentLinks" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">روابط الدفع</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/stats/transactionsReport" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">ملخص المعاملات المالية</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/finances/preferences" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الإعدادات</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_marketing"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-bullhorn fa-fw"></i>
                        <span class="title">التسويق والمبيعات</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_marketing">
                    <li class="sub-menu-item">
                        <a href="/marketing/leads" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">العملاء المحتملين</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/marketing/forms" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">النماذج</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/marketing/campaigns" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الحملات التسويقية</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/marketing/sa" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">اتمتة المبيعات</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item no-sub-menu ">
                <a href="/reports" class="menu-item-title d-flex">
                    <span>
                        <i class="icon fa fa-print"></i>
                        <span class="title">التقارير</span>
                    </span>
                </a>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_misc"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-th fa-fw"></i>
                        <span class="title">ادوات متنوعة</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_misc">
                    <li class="sub-menu-item">
                        <a href="/clients/remarks" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">مهام فريق العمل</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/misc/kb" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">قاعدة المعرفة</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/announcements" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الإعلانات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="#" target="_self" onclick="return window.App.sharedAssets.open()" class="sub-menu-item-title">
                            <span class="title">الملفات المتشاركة</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item has-sub-menu ">
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#menu_admin"
                   class="menu-item-title d-flex flex-gap-10 justify-content-between collapsed">
                    <span>
                        <i class="icon fa fa-sliders fa-fw"></i>
                        <span class="title">الإدارة</span>
                    </span>
                    <span>
                        <span class="arrow"></span>
                    </span>
                </a>
                <ul class="sub-menu collapse " id="menu_admin">
                    <li class="sub-menu-item">
                        <a href="/admin/subscription" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الإشتراك بالخدمة</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/subscription/ai" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الذكاء الاصطناعي</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/activityReport" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">تقرير النشاط الإجمالي</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/analytics" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">التحليلات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/logs" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">سجل العمليات Logs</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/admin/settings" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">الإعدادات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/identity" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">هوية المؤسسة</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/users" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">المستخدمون والصلاحيات</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/notifications" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">إدارة الإشعارات</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li class="sub-menu-item">
                        <a href="/admin/branches" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">إدارة الفروع</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/labs" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">قاعات التدريب</span>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a href="/admin/templates" target="_self" onclick="" class="sub-menu-item-title">
                            <span class="title">القوالب</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                </ul>
            </li>
        </ul>

        <div class="copyright visible-lg visible-md">
            <div class="flex flex-gap-15 align-items-center">
                <div>
                    <a href="" target="_blank" class="logo-wrapper">
                        <img class="logo" src="" />
                    </a>
                </div>
               
                <div class="text">
                    <a href="misc/about" class="text-strong">
                        FastWeb
                    </a>

                    v1.0.0
                </div>
            </div>
        </div> 

    </div>
</div>
<!-- end sideMenu -->
