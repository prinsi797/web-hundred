function getFileExtension(fileType) {
    // Create a mapping of common MIME types to file extensions
    const mimeToExt = {
        "text/csv": "csv",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
            "xlsx",
        "application/vnd.ms-excel": "xls",
    };

    // Check if the provided fileType is in the mapping
    if (fileType && mimeToExt[fileType]) {
        return mimeToExt[fileType];
    }

    // If no mapping is found, return null or an empty string
    return null;
}

function display_overlay_modal(title, body) {
    $(".overlay_modal").modal({
        backdrop: "static",
        keyboard: false,
    });
    $(".overlay_modal .modal-title").html(title);
    $(".overlay_modal .modal-body").html(body);
}
function hide_overlay_modal() {
    $(".overlay_modal").modal("hide");
}
function restrictNonNumericInput(event) {
    // Allow: backspace, delete, tab, escape, enter, and numeric keys
    if (
        [46, 8, 9, 27, 13].indexOf(event.keyCode) !== -1 ||
        // Allow: Ctrl/cmd+A
        (event.keyCode == 65 &&
            (event.ctrlKey === true || event.metaKey === true)) ||
        // Allow: Ctrl/cmd+C
        (event.keyCode == 67 &&
            (event.ctrlKey === true || event.metaKey === true)) ||
        // Allow: Ctrl/cmd+X
        (event.keyCode == 88 &&
            (event.ctrlKey === true || event.metaKey === true)) ||
        // Allow: home, end, left, right
        (event.keyCode >= 35 && event.keyCode <= 39)
    ) {
        // Let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if (
        (event.shiftKey || event.keyCode < 48 || event.keyCode > 57) &&
        (event.keyCode < 96 || event.keyCode > 105)
    ) {
        event.preventDefault();
    }
}
function generateStrongPassword() {
    const uppercaseLetters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const lowercaseLetters = "abcdefghijklmnopqrstuvwxyz";
    const numbers = "0123456789";
    const specialChars = "!@#$%^&*()_-+=<>?";

    function getRandomCharacter(characters) {
        return characters.charAt(Math.floor(Math.random() * characters.length));
    }

    let password = "";

    // Ensure at least one character from each character set
    password += getRandomCharacter(uppercaseLetters);
    password += getRandomCharacter(lowercaseLetters);
    password += getRandomCharacter(numbers);
    password += getRandomCharacter(specialChars);

    // Generate the rest of the password
    const requiredLength = 16;
    const remainingLength = requiredLength - 4;

    for (let i = 0; i < remainingLength; i++) {
        const characterSet =
            uppercaseLetters + lowercaseLetters + numbers + specialChars;
        password += getRandomCharacter(characterSet);
    }

    // Shuffle the characters in the password for randomness
    password = password
        .split("")
        .sort(() => Math.random() - 0.5)
        .join("");

    return password;
}
