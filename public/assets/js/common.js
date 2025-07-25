// ログアウト処理
function logout() {
  if (confirm("ログアウトしますか？")) {
    window.location.href = "/auth/logout";
  }
}

// Cookie関連のユーティリティ関数
function setCookie(name, value, days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(";");
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == " ") c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}

// 削除確認
function confirmDelete(message) {
  return confirm(message || "本当に削除しますか？");
}

// フィルターオプションのクリックハンドラー
function toggleFilter(checkbox) {
  // チェックボックスの状態変更を処理
  return true;
}
