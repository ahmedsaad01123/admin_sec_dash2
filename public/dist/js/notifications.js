/**
 * Notifications JavaScript Module
 * Handles all notification-related functionality
 */

// تهيئة كائن التطبيق إذا لم يكن موجوداً
if (!window.App) {
  window.App = {};
}
if (!window.App.user) {
  window.App.user = {};
}
if (!window.App.user.notifications) {
  window.App.user.notifications = {};
}

// دالة إخفاء الإشعارات
window.App.user.notifications.holder = {
  hide: function () {
    const notificationsDiv = document.getElementById("notifications");
    if (notificationsDiv) {
      notificationsDiv.style.display = "none";
      // إعادة تعيين الأنماط
      notificationsDiv.style.position = "";
      notificationsDiv.style.top = "";
      notificationsDiv.style.left = "";
      notificationsDiv.style.right = "";
      notificationsDiv.style.zIndex = "";
      notificationsDiv.style.width = "";
      notificationsDiv.style.maxHeight = "";
      notificationsDiv.style.overflow = "";
      notificationsDiv.style.background = "";
      notificationsDiv.style.border = "";
      notificationsDiv.style.borderRadius = "";
      notificationsDiv.style.boxShadow = "";
    }
  },
  show: function () {
    const notificationsDiv = document.getElementById("notifications");
    if (notificationsDiv) {
      notificationsDiv.style.display = "block";
    }
  },
  toggle: function () {
    const notificationsDiv = document.getElementById("notifications");
    if (notificationsDiv) {
      if (
        notificationsDiv.style.display === "none" ||
        notificationsDiv.style.display === ""
      ) {
        window.App.user.notifications.open();
      } else {
        this.hide();
      }
    }
  },
};

// دالة فتح الإشعارات (للزر في الشريط الجانبي)
window.App.user.notifications.open = function () {
  const notificationsDiv = document.getElementById("notifications");
  if (notificationsDiv) {
    console.log("Opening notifications...");

    // تحديد الموضع المناسب للإشعارات من اليسار
    notificationsDiv.style.position = "fixed";
    notificationsDiv.style.left = "20px";
    notificationsDiv.style.zIndex = "9999";
    notificationsDiv.style.display = "block";
    notificationsDiv.style.width = "300px";
    notificationsDiv.style.overflow = "auto";
    notificationsDiv.style.background = "white";
    notificationsDiv.style.border = "1px solid #ddd";
    notificationsDiv.style.borderRadius = "8px";
    notificationsDiv.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";

    // إضافة تأثير حركي من اليسار
    notificationsDiv.style.opacity = "0";
    notificationsDiv.style.transform = "translateX(-10px)";

    setTimeout(() => {
      notificationsDiv.style.transition = "all 0.3s ease";
      notificationsDiv.style.opacity = "1";
      notificationsDiv.style.transform = "translateX(0)";
    }, 10);
  }
};

// دالة تعيين جميع الإشعارات كمقروءة
window.App.user.notifications.markAllRead = function () {
  console.log("Marking all notifications as read...");
  // هنا يمكن إضافة منطق تحديث الخادم
  const markAllReadLink = document.querySelector(".markAllRead");
  if (markAllReadLink) {
    markAllReadLink.style.display = "none";
  }

  // إزالة أي شعارات غير مقروءة
  const unreadBadges = document.querySelectorAll(".notification-badge");
  unreadBadges.forEach((badge) => {
    badge.style.display = "none";
  });
};

// تهيئة مستمعي الأحداث
document.addEventListener("DOMContentLoaded", function () {
  // إضافة مستمع حدث لزر الإغلاق
  const closeButton = document.querySelector("#notifications .close");
  if (closeButton) {
    closeButton.addEventListener("click", function (e) {
      e.preventDefault();
      window.App.user.notifications.holder.hide();
    });
  }

  // إغلاق الإشعارات عند النقر خارجها
  document.addEventListener("click", function (e) {
    const notificationsDiv = document.getElementById("notifications");
    if (
      notificationsDiv &&
      notificationsDiv.style.display === "block" &&
      !notificationsDiv.contains(e.target) &&
      !e.target.closest(".fa-bell") &&
      !e.target.closest(".notifications")
    ) {
      window.App.user.notifications.holder.hide();
    }
  });

  // إغلاق الإشعارات عند الضغط على ESC
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const notificationsDiv = document.getElementById("notifications");
      if (notificationsDiv && notificationsDiv.style.display === "block") {
        window.App.user.notifications.holder.hide();
      }
    }
  });
});
