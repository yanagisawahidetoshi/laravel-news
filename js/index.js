function check() {
  if (window.confirm("投稿してよろしいですか？")) {
    return true;
  } else {
    alert("キャンセルしました。");
    return false;
  }
}
