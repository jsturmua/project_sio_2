// password_strength.js
function checkPasswordStrength(password) {
    const result = zxcvbn(password); // Use zxcvbn to evaluate password strength
    const meter = document.getElementById('password-strength');

    // Update the meter with password strength
    meter.style.width = (result.score * 20) + '%';
    meter.style.backgroundColor = getColor(result.score);
}

// Function to set color based on strength
function getColor(score) {
    switch(score) {
        case 0: return 'red';
        case 1: return 'orange';
        case 2: return 'yellow';
        case 3: return 'green';
        case 4: return 'darkgreen';
        default: return 'black';
    }
}
