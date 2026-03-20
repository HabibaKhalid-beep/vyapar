// POS keyboard shortcuts
$(function () {
  $(document).on("keydown", function (e) {
    var key = e.key;
    var code = e.code;
    var ctrl = e.ctrlKey || e.metaKey;

    var handled = false;

    switch (code) {
      case "F2":
        console.log("Shortcut triggered: F2");
        handled = true;
        break;
      case "F3":
        console.log("Shortcut triggered: F3");
        handled = true;
        break;
      case "F4":
        console.log("Shortcut triggered: F4");
        handled = true;
        break;
      case "F6":
        console.log("Shortcut triggered: F6");
        handled = true;
        break;
      case "F8":
        console.log("Shortcut triggered: F8");
        handled = true;
        break;
      case "F9":
        console.log("Shortcut triggered: F9");
        handled = true;
        break;
      case "F10":
        console.log("Shortcut triggered: F10");
        handled = true;
        break;
      case "F11":
        console.log("Shortcut triggered: F11");
        handled = true;
        break;
      case "F12":
        console.log("Shortcut triggered: F12");
        handled = true;
        break;
      default:
        break;
    }

    if (ctrl && (key === "p" || key === "P")) {
      console.log("Shortcut triggered: Ctrl+P");
      handled = true;
    } else if (ctrl && (key === "m" || key === "M")) {
      console.log("Shortcut triggered: Ctrl+M");
      handled = true;
    } else if (ctrl && (key === "f" || key === "F")) {
      console.log("Shortcut triggered: Ctrl+F");
      handled = true;
    } else if (ctrl && (key === "t" || key === "T")) {
      console.log("Shortcut triggered: Ctrl+T");
      handled = true;
    }

    if (handled) {
      e.preventDefault();
    }
  });
});
